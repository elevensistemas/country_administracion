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
            $lines = explode("\n", $text);

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Match table lines starting with UF number 1..150
                // Pattern: UF (digits) + Owner Name + % (0,76) + numbers...
                if (preg_match('/^(\d{1,3})\s+(.+?)\s+(\d+[\,\.]\d+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)\s+([\d\.\,\-]+)/u', $line, $m)) {
                    $uf = intval($m[1]);
                    if ($uf >= 1 && $uf <= 150) {
                        $allRows[$uf] = [
                            'uf' => $uf,
                            'propietario' => trim($m[2]),
                            'porcentaje' => $this->cleanNum($m[3]),
                            'saldo_anterior' => $this->cleanNum($m[4]),
                            'ajustes' => $this->cleanNum($m[5]),
                            'pagos' => $this->cleanNum($m[6]),
                            'importe_mora' => $this->cleanNum($m[7]),
                            'interes_mora' => $this->cleanNum($m[8]),
                            'expensas_ordinarias' => $this->cleanNum($m[9]),
                            'canon_construccion' => $this->cleanNum($m[10]),
                            'redondeo' => $this->cleanNum($m[11]),
                            'total_pronto_pago' => $this->cleanNum($m[12]),
                            'total_a_pagar' => $this->cleanNum($m[13]),
                        ];
                    }
                }
            }
        }

        ksort($allRows);
        return array_values($allRows);
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
                    'capital_amount' => $row['expensas_ordinarias'] + $row['canon_construccion'],
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

            if ($row['canon_construccion'] > 0) {
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'concept' => 'Canon de Construcción / Obra',
                    'amount' => $row['canon_construccion'],
                    'category' => 'construction',
                ]);
            }

            if ($row['interes_mora'] > 0) {
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'concept' => 'Intereses por Mora acumulados',
                    'amount' => $row['interes_mora'],
                    'category' => 'interest',
                ]);
            }

            if ($row['ajustes'] != 0) {
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
