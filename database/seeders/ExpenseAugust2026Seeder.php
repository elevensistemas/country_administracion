<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

class ExpenseAugust2026Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/expenses_08_2026.json');
        if (!File::exists($jsonPath)) {
            $this->command->error("expenses_08_2026.json not found in database/seeders");
            return;
        }

        $jsonData = json_decode(File::get($jsonPath), true);
        $destRelPath = "expenses/boletin_y_liquidacion_08_2026.pdf";

        // 1. Ensure Period
        $period = BillingPeriod::firstOrCreate(
            ['period' => '2026-08'],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2026-08-31',
                'status' => 'published'
            ]
        );
        $period->update(['status' => 'published']);

        $evType = LotHistoryEventType::firstOrCreate(['name' => 'expense_published'], ['name' => 'expense_published', 'label' => 'Expensa Publicada']);
        $evCat = LotHistoryCategory::firstOrCreate(['name' => 'finance'], ['name' => 'finance', 'label' => 'Finanzas']);

        foreach ($jsonData as $row) {
            $ufNum = intval($row['uf']);
            $lotCode = 'UF' . str_pad($ufNum, 3, '0', STR_PAD_LEFT);

            // Lot
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

            // Functional Unit
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

            // Expense
            $expense = Expense::updateOrCreate(
                [
                    'billing_period_id' => $period->id,
                    'functional_unit_id' => $unit->id,
                ],
                [
                    'issue_date' => '2026-08-31',
                    'due_date' => '2026-09-10',
                    'second_due_date' => '2026-09-30',
                    'previous_balance' => $row['importe_mora'],
                    'capital_amount' => $row['expensas_ordinarias'] + $row['canon_construccion'],
                    'interest_amount' => $row['interes_mora'],
                    'adjustments_amount' => $row['ajustes'],
                    'discount_amount' => $discountAmount,
                    'total_amount' => $row['total_a_pagar'],
                    'status' => 'published',
                    'attachment_path' => $destRelPath,
                ]
            );

            $expense->items()->delete();

            ExpenseItem::create([
                'expense_id' => $expense->id,
                'concept' => 'Expensas Ordinarias Período 08-2026',
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
                    'date' => '2026-08-31',
                    'amount' => $row['total_a_pagar'],
                    'balance_after' => $row['total_a_pagar'],
                    'description' => "Liquidación Expensas 08-2026 (Vto Pronto Pago: 10/09 - $ " . number_format($row['total_pronto_pago'], 2, ',', '.') . ")",
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
                    'title' => "Liquidación de Expensas Publicada (08-2026)",
                    'description' => "Liquidación período 08-2026 disponible. Pronto pago (hasta 10/09): $ " . number_format($row['total_pronto_pago'], 2, ',', '.') . " | Total después del 10: $ " . number_format($row['total_a_pagar'], 2, ',', '.'),
                    'event_date' => '2026-08-31',
                    'visibility' => 'public',
                ]
            );
        }

        // Register Document
        $docCat = DocumentCategory::firstOrCreate(
            ['name' => 'expensas'],
            ['name' => 'expensas', 'display_name' => 'Expensas y Liquidaciones']
        );

        $doc = Document::firstOrCreate(
            ['name' => 'Boletín y Liquidación de Expensas 08-2026'],
            [
                'category_id' => $docCat->id,
                'description' => 'Boletín informativo y resumen financiero de expensas del período 08-2026 con vencimiento 10/09/2026.',
                'visibility' => 'public',
                'is_archived' => false
            ]
        );

        DocumentVersion::firstOrCreate(
            ['document_id' => $doc->id, 'version' => '1.0'],
            [
                'file_path' => $destRelPath,
                'file_name' => 'boletin_y_liquidacion_08_2026.pdf',
                'file_size' => 1580067,
                'uploaded_by' => 1,
                'notes' => 'Liquidación oficial período 08-2026'
            ]
        );

        $this->command->info("SUCCESS: 131 lot expenses and balances seeded successfully.");
    }
}
