<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use App\Models\BillingPeriod;
use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\FunctionalUnit;
use App\Models\Lot;
use App\Models\Owner;
use App\Models\AccountMovement;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryCategory;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PdfExpenseParserService
{
    /**
     * Parse the PDF text and extract all 131 lot rows.
     */
    public function parsePdf(string $pdfPath): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $pages = $pdf->getPages();

        $allRows = [];

        foreach ($pages as $pIndex => $page) {
            $text = $page->getText();
            $rawLines = explode("\n", $text);
            $lines = array_values(array_filter(array_map('trim', $rawLines), fn($l) => $l !== ''));
            
            $i = 0;
            $n = count($lines);
            
            while ($i < $n) {
                $line = $lines[$i];
                
                // Fix numbers concatenated without spaces by the PDF text extractor (e.g. 753.984,00100.000,00 or 21.730.499,27127)
                $line = preg_replace('/(\,\d{2})(\d+)/', '$1 $2', $line);
                
                // Case 1: Standard single or concatenated line starting with UF number
                if (preg_match('/^(\d{1,3})\s+(.+?)\s*(\d+[\,\.]\d{2})\s+([\d\.\,\-]+(?:\s+[\d\.\,\-]+){7,13})/u', $line, $m)) {
                    $uf = intval($m[1]);
                    if ($uf >= 1 && $uf <= 150) {
                        $prop = trim($m[2]);
                        $pct = $this->cleanNum($m[3]);
                        $numTokens = preg_split('/\s+/', trim($m[4]));
                        $allRows[$uf] = $this->parseTokens($uf, $prop, $pct, $numTokens);
                        $i++;
                        continue;
                    }
                }
                
                // Case 2: Broken name across 2 lines (e.g. accented characters generating linebreaks)
                if ($i + 1 < $n && preg_match('/^(\d{1,3})\s+([^0-9\n\r]+)$/u', $line, $m1)) {
                    $ufCandidate = intval($m1[1]);
                    if ($ufCandidate >= 1 && $ufCandidate <= 150) {
                        $nextLine = preg_replace('/(\,\d{2})(\d+)/', '$1 $2', $lines[$i + 1]);
                        if (preg_match('/^(.*?)\s*(\d+[\,\.]\d{2})\s+([\d\.\,\-]+(?:\s+[\d\.\,\-]+){7,13})/u', $nextLine, $m2)) {
                            $prop = trim($m1[2] . ' ' . $m2[1]);
                            $pct = $this->cleanNum($m2[2]);
                            $numTokens = preg_split('/\s+/', trim($m2[3]));
                            $allRows[$ufCandidate] = $this->parseTokens($ufCandidate, $prop, $pct, $numTokens);
                            $i += 2;
                            continue;
                        }
                    }
                }
                
                // Case 3: Vertical multi-line format (e.g. table cells wrapping in PDF)
                if (preg_match('/^(\d{1,3})$/', $line, $mUf)) {
                    $uf = intval($mUf[1]);
                    if ($uf >= 1 && $uf <= 150 && $i + 1 < $n) {
                        $propCandidate = $lines[$i + 1];
                        if (!preg_match('/^[\d\.\,\-]+$/', $propCandidate) && !str_contains($propCandidate, 'SOCIEDAD') && !str_contains($propCandidate, 'SALDOS')) {
                            $collectedNumbers = [];
                            $j = $i + 2;
                            while ($j < $n && preg_match('/^[\d\.\,\-]+$/', $lines[$j])) {
                                $cLine = preg_replace('/(\,\d{2})(\d+)/', '$1 $2', $lines[$j]);
                                foreach (preg_split('/\s+/', trim($cLine)) as $tok) {
                                    if ($tok !== '') $collectedNumbers[] = $tok;
                                }
                                $j++;
                            }
                            if (count($collectedNumbers) >= 8) {
                                $pct = $this->cleanNum($collectedNumbers[0]);
                                $numTokens = array_slice($collectedNumbers, 1);
                                $allRows[$uf] = $this->parseTokens($uf, $propCandidate, $pct, $numTokens);
                                $i = $j;
                                continue;
                            }
                        }
                    }
                }
                
                $i++;
            }
        }

        ksort($allRows);
        return array_values($allRows);
    }

    private function parseTokens(int $uf, string $prop, float $pct, array $tokens): array
    {
        // Clean trailing repeated UF if present at the end of the row
        if (count($tokens) > 0 && intval($tokens[count($tokens) - 1]) === $uf) {
            array_pop($tokens);
        }
        
        // Clean name from PDF encoding artifacts / mojibake
        $prop = str_replace(
            ['ÃƒÂ‰', 'ÃƒÂ', 'Ã¯Â¿Â½', 'Ã‘', 'Â´', 'Ã', '‰', '¿', '½', 'Â', '´', '`'],
            ['E', 'A', 'E', 'N', '', 'I', '', '', '', '', '', ''],
            $prop
        );
        $prop = trim(preg_replace('/\s+/', ' ', $prop));
        
        $c = count($tokens);
        
        $salAnt = $this->cleanNum($tokens[0] ?? 0);
        $ajustes = $this->cleanNum($tokens[1] ?? 0);
        $pagos = $this->cleanNum($tokens[2] ?? 0);
        $impMora = $this->cleanNum($tokens[3] ?? 0);
        $intMora = $this->cleanNum($tokens[4] ?? 0);
        $expOrd = $this->cleanNum($tokens[5] ?? 0);
        
        if ($c >= 11) {
            $expExt = $this->cleanNum($tokens[6] ?? 0);
            $canon = $this->cleanNum($tokens[7] ?? 0);
            $redondeo = $this->cleanNum($tokens[8] ?? 0);
            $totalPronto = $this->cleanNum($tokens[9] ?? 0);
            $totalPagar = $this->cleanNum($tokens[10] ?? 0);
        } elseif ($c == 10) {
            $expExt = 0;
            $canon = $this->cleanNum($tokens[6] ?? 0);
            $redondeo = $this->cleanNum($tokens[7] ?? 0);
            $totalPronto = $this->cleanNum($tokens[8] ?? 0);
            $totalPagar = $this->cleanNum($tokens[9] ?? 0);
        } else {
            $expExt = 0;
            $canon = 0;
            $redondeo = $this->cleanNum($tokens[$c - 3] ?? 0);
            $totalPronto = $this->cleanNum($tokens[$c - 2] ?? 0);
            $totalPagar = $this->cleanNum($tokens[$c - 1] ?? 0);
        }
        
        return [
            'uf' => $uf,
            'propietario' => $prop,
            'porcentaje' => $pct,
            'saldo_anterior' => $salAnt,
            'ajustes' => $ajustes,
            'pagos' => $pagos,
            'importe_mora' => $impMora,
            'interes_mora' => $intMora,
            'expensas_ordinarias' => $expOrd,
            'expensas_extraordinarias' => $expExt,
            'canon_construccion' => $canon,
            'redondeo' => $redondeo,
            'total_pronto_pago' => $totalPronto,
            'total_a_pagar' => $totalPagar,
        ];
    }

    /**
     * Import parsed PDF data into database for a given BillingPeriod.
     */
    public function importExpenses(BillingPeriod $period, array $rows, string $storedRelativePdfPath): int
    {
        $evType = LotHistoryEventType::firstOrCreate(['name' => 'expense_published'], ['name' => 'expense_published', 'label' => 'Expensa Publicada']);
        $evCat = LotHistoryCategory::firstOrCreate(['name' => 'finance'], ['name' => 'finance', 'label' => 'Finanzas']);

        $count = 0;

        foreach ($rows as $row) {
            $ufNum = intval($row['uf']);
            $lotCode = 'UF' . str_pad($ufNum, 3, '0', STR_PAD_LEFT);

            $lot = Lot::firstOrCreate(
                ['number' => $ufNum],
                [
                    'code' => $lotCode,
                    'name' => 'Lote ' . $ufNum,
                    'internal_address' => 'Calle Principal ' . $ufNum,
                    'status' => 'active',
                    'balance' => $row['total_a_pagar']
                ]
            );

            if (!empty($row['propietario'])) {
                $propStr = $row['propietario'];
                $parts = explode(',', $propStr, 2);
                $lastName = trim($parts[0]);
                $firstName = isset($parts[1]) ? trim($parts[1]) : '';

                if ($lot->owner) {
                    $lot->owner->update([
                        'name' => $firstName ?: $lastName,
                        'last_name' => $firstName ? $lastName : ''
                    ]);
                } else {
                    $owner = Owner::firstOrCreate(
                        ['email' => 'propietario' . $ufNum . '@laranita.com'],
                        [
                            'name' => $firstName ?: $lastName,
                            'last_name' => $firstName ? $lastName : '',
                            'phone' => '11-0000-00' . str_pad($ufNum, 2, '0', STR_PAD_LEFT),
                            'status' => 'active'
                        ]
                    );
                    $lot->current_owner_id = $owner->id;
                }
            }

            $lot->balance = $row['total_a_pagar'];
            $lot->save();

            $unit = FunctionalUnit::firstOrCreate(
                ['lot_id' => $lot->id],
                [
                    'code' => $lotCode,
                    'name' => 'Lote ' . $ufNum,
                    'balance' => $row['total_a_pagar']
                ]
            );
            $unit->balance = $row['total_a_pagar'];
            $unit->save();

            $discountAmount = round(max(0, $row['total_a_pagar'] - $row['total_pronto_pago']), 2);
            $capitalAmount = $row['expensas_ordinarias'] + ($row['expensas_extraordinarias'] ?? 0) + $row['canon_construccion'];

            $expense = Expense::updateOrCreate(
                [
                    'billing_period_id' => $period->id,
                    'functional_unit_id' => $unit->id,
                ],
                [
                    'issue_date' => $period->end_date ? $period->end_date->toDateString() : now()->toDateString(),
                    'due_date' => $period->end_date ? $period->end_date->copy()->addDays(10)->toDateString() : now()->addDays(10)->toDateString(),
                    'second_due_date' => $period->end_date ? $period->end_date->copy()->addDays(30)->toDateString() : now()->addDays(30)->toDateString(),
                    'previous_balance' => $row['importe_mora'],
                    'capital_amount' => $capitalAmount,
                    'interest_amount' => $row['interes_mora'],
                    'adjustments_amount' => $row['ajustes'],
                    'discount_amount' => $discountAmount,
                    'total_amount' => $row['total_a_pagar'],
                    'status' => 'published',
                    'attachment_path' => $storedRelativePdfPath,
                ]
            );

            $expense->items()->delete();

            ExpenseItem::create([
                'expense_id' => $expense->id,
                'concept' => 'Expensas Ordinarias Período ' . $period->period,
                'amount' => $row['expensas_ordinarias'],
                'category' => 'general',
            ]);

            if (!empty($row['expensas_extraordinarias']) && $row['expensas_extraordinarias'] > 0) {
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'concept' => 'Expensas Extraordinarias (Reparación Calle Exterior)',
                    'amount' => $row['expensas_extraordinarias'],
                    'category' => 'extraordinary',
                ]);
            }

            if (!empty($row['canon_construccion']) && $row['canon_construccion'] > 0) {
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'concept' => 'Canon de Construcción / Obra',
                    'amount' => $row['canon_construccion'],
                    'category' => 'construction',
                ]);
            }

            if (!empty($row['interes_mora']) && $row['interes_mora'] > 0) {
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'concept' => 'Intereses por Mora acumulados',
                    'amount' => $row['interes_mora'],
                    'category' => 'interest',
                ]);
            }

            if (!empty($row['ajustes']) && $row['ajustes'] != 0) {
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'concept' => 'Ajuste de Saldo / Crédito anterior',
                    'amount' => $row['ajustes'],
                    'category' => 'adjustments',
                ]);
            }

            AccountMovement::updateOrCreate(
                [
                    'functional_unit_id' => $unit->id,
                    'related_model_type' => Expense::class,
                    'related_model_id' => $expense->id,
                ],
                [
                    'type' => 'debit',
                    'date' => $expense->issue_date->toDateString(),
                    'amount' => $row['total_a_pagar'],
                    'balance_after' => $row['total_a_pagar'],
                    'description' => "Liquidación Expensas {$period->period} (Vto Pronto Pago: " . $expense->due_date->format('d/m') . " - $ " . number_format($row['total_pronto_pago'], 2, ',', '.') . ")",
                ]
            );

            LotHistoryEvent::updateOrCreate(
                [
                    'lot_id' => $lot->id,
                    'related_model_type' => Expense::class,
                    'related_model_id' => $expense->id,
                ],
                [
                    'functional_unit_id' => $unit->id,
                    'event_type_id' => $evType->id,
                    'category_id' => $evCat->id,
                    'owner_id' => $lot->current_owner_id,
                    'tenant_id' => $lot->current_tenant_id,
                    'title' => "Liquidación de Expensas Publicada ({$period->period})",
                    'description' => "Liquidación período {$period->period} disponible. Pronto pago: $ " . number_format($row['total_pronto_pago'], 2, ',', '.') . " | Total regular: $ " . number_format($row['total_a_pagar'], 2, ',', '.'),
                    'event_date' => $expense->issue_date->toDateString(),
                    'visibility' => 'public',
                ]
            );

            $count++;
        }

        // Register document in repository
        $docCat = DocumentCategory::firstOrCreate(
            ['name' => 'expensas'],
            ['name' => 'expensas', 'display_name' => 'Expensas y Liquidaciones']
        );

        $doc = Document::firstOrCreate(
            ['name' => "Boletín y Liquidación de Expensas {$period->period}"],
            [
                'category_id' => $docCat->id,
                'description' => "Boletín informativo y resumen financiero de expensas del período {$period->period}.",
                'visibility' => 'public',
                'is_archived' => false
            ]
        );

        $fullStoragePath = storage_path('app/public/' . $storedRelativePdfPath);
        DocumentVersion::firstOrCreate(
            ['document_id' => $doc->id, 'version' => '1.0'],
            [
                'file_path' => $storedRelativePdfPath,
                'file_name' => "boletin_y_liquidacion_{$period->period}.pdf",
                'file_size' => file_exists($fullStoragePath) ? filesize($fullStoragePath) : 0,
                'uploaded_by' => auth()->id() ?? 1,
                'notes' => "Liquidación oficial período {$period->period}"
            ]
        );

        return $count;
    }

    private function cleanNum($val): float
    {
        if (!$val) return 0.0;
        $clean = str_replace([' ', '.'], '', (string)$val);
        $clean = str_replace(',', '.', $clean);
        return (float) $clean;
    }
}
