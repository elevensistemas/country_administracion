<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\ImportRow;
use App\Models\Lot;
use App\Models\Owner;
use App\Models\BillingPeriod;
use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\AccountMovement;
use App\Models\SystemSetting;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ImportController extends Controller
{
    /**
     * Display listing of imports.
     */
    public function index()
    {
        $imports = Import::orderBy('created_at', 'desc')->paginate(10);
        $draftPeriods = BillingPeriod::where('status', 'draft')->orderBy('period', 'desc')->get();
        return view('admin.imports.index', compact('imports', 'draftPeriods'));
    }

    /**
     * Upload an import file.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:lots,owners,expenses',
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'billing_period_id' => 'required_if:type,expenses|nullable|exists:billing_periods,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('imports');

        $import = Import::create([
            'type' => $request->type,
            'filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending',
            'total_rows' => 0,
            'valid_rows' => 0,
            'invalid_rows' => 0,
            'user_id' => auth()->id(),
        ]);

        // Parse CSV and save rows in database
        $filePath = storage_path('app/' . $path);
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle, 1000, ',');
        
        $rowCount = 0;
        $invalidCount = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Protect against row column counts mismatches
            if (count($header) !== count($row)) {
                $row = array_pad($row, count($header), '');
            }
            $data = array_combine($header, $row);
            
            if ($request->type === 'expenses') {
                $data['billing_period_id'] = $request->billing_period_id;
            }

            // Run pre-validations
            $rowErrors = [];
            if ($request->type === 'expenses') {
                if (empty($data['lote'])) {
                    $rowErrors[] = 'La columna "lote" es obligatoria.';
                } else {
                    $lot = Lot::where('number', $data['lote'])->first();
                    if (!$lot) {
                        $rowErrors[] = "El lote {$data['lote']} no existe en la base de datos.";
                    } else if (!$lot->functionalUnits()->exists()) {
                        $rowErrors[] = "El lote {$data['lote']} no tiene unidades funcionales asociadas.";
                    }
                }
                
                if (!isset($data['monto_expensa']) || !is_numeric($data['monto_expensa'])) {
                    $rowErrors[] = 'La columna "monto_expensa" es obligatoria y debe ser numérica.';
                }
                if (!isset($data['monto_fondo_reserva']) || !is_numeric($data['monto_fondo_reserva'])) {
                    $rowErrors[] = 'La columna "monto_fondo_reserva" es obligatoria y debe ser numérica.';
                }
            } elseif ($request->type === 'lots') {
                if (empty($data['number'])) {
                    $rowErrors[] = 'La columna "number" es obligatoria.';
                } elseif (Lot::where('number', $data['number'])->exists()) {
                    $rowErrors[] = "El lote número {$data['number']} ya está registrado.";
                }
                if (empty($data['code'])) {
                    $rowErrors[] = 'La columna "code" es obligatoria.';
                }
            } elseif ($request->type === 'owners') {
                if (empty($data['name'])) {
                    $rowErrors[] = 'La columna "name" es obligatoria.';
                }
                if (empty($data['last_name'])) {
                    $rowErrors[] = 'La columna "last_name" es obligatoria.';
                }
                if (empty($data['email'])) {
                    $rowErrors[] = 'La columna "email" es obligatoria.';
                } elseif (Owner::where('email', $data['email'])->exists()) {
                    $rowErrors[] = "El email {$data['email']} ya está registrado.";
                }
            }

            $status = count($rowErrors) > 0 ? 'invalid' : 'pending';
            if ($status === 'invalid') {
                $invalidCount++;
            }

            ImportRow::create([
                'import_id' => $import->id,
                'row_number' => ++$rowCount,
                'data' => $data,
                'errors' => count($rowErrors) > 0 ? $rowErrors : null,
                'status' => $status,
            ]);
        }
        fclose($handle);

        $import->update([
            'total_rows' => $rowCount,
            'invalid_rows' => $invalidCount,
            'valid_rows' => $rowCount - $invalidCount
        ]);

        return redirect()->route('admin.imports.validate', $import)->with('success', 'Archivo cargado. Revisa las validaciones antes de confirmar la importación.');
    }

    /**
     * Show validation errors.
     */
    public function showValidation(Import $import)
    {
        $import->load('rows');
        return view('admin.imports.validate', compact('import'));
    }

    /**
     * Process validated rows.
     */
    public function process(Import $import)
    {
        if ($import->status !== 'pending') {
            return redirect()->route('admin.imports.index')->with('error', 'Esta importación ya ha sido procesada.');
        }

        $import->update(['status' => 'processing']);

        $rows = ImportRow::where('import_id', $import->id)->where('status', 'pending')->get();
        $processed = 0;
        $failed = 0;

        foreach ($rows as $row) {
            DB::transaction(function () use ($import, $row, &$processed, &$failed) {
                try {
                    $data = $row->data;

                    if ($import->type === 'lots') {
                        // Create lot
                        Lot::create([
                            'number' => $data['number'],
                            'code' => $data['code'],
                            'name' => $data['name'] ?? null,
                            'internal_address' => $data['internal_address'] ?? null,
                            'status' => $data['status'] ?? 'active',
                            'balance' => $data['balance'] ?? 0.00,
                        ]);
                    } elseif ($import->type === 'owners') {
                        // Create owner
                        Owner::create([
                            'name' => $data['name'],
                            'last_name' => $data['last_name'],
                            'email' => $data['email'],
                            'phone' => $data['phone'] ?? null,
                            'dni' => $data['dni'] ?? null,
                            'cuit' => $data['cuit'] ?? null,
                            'preferred_channel' => $data['preferred_channel'] ?? 'email',
                        ]);
                    } elseif ($import->type === 'expenses') {
                        // Create custom expense
                        $lot = Lot::where('number', $data['lote'])->firstOrFail();
                        $unit = $lot->functionalUnits()->firstOrFail();
                        $billingPeriodId = $data['billing_period_id'];
                        $period = BillingPeriod::findOrFail($billingPeriodId);

                        // Calculate interest on previous balance
                        $interestRate = (float) SystemSetting::where('key', 'interest_rate_monthly')->value('value') ?? 3.5;
                        $previousBalance = $unit->balance;
                        $interestAmount = 0.00;

                        if ($previousBalance > 0) {
                            $interestAmount = round($previousBalance * ($interestRate / 100), 2);
                        }

                        $capitalAmount = (float) $data['monto_expensa'];
                        $reserveAmount = (float) $data['monto_fondo_reserva'];
                        $totalNewCharges = $capitalAmount + $reserveAmount + $interestAmount;

                        $dueDay = (int) SystemSetting::where('key', 'due_day')->value('value') ?? 10;
                        $secondDueDay = (int) SystemSetting::where('key', 'second_due_day')->value('value') ?? 20;
                        $yearMonth = explode('-', $period->period);
                        $dueDate = Carbon::create($yearMonth[0], $yearMonth[1], $dueDay)->addMonth();
                        $secondDueDate = Carbon::create($yearMonth[0], $yearMonth[1], $secondDueDay)->addMonth();

                        $expense = Expense::create([
                            'billing_period_id' => $billingPeriodId,
                            'functional_unit_id' => $unit->id,
                            'issue_date' => now()->toDateString(),
                            'due_date' => $dueDate->toDateString(),
                            'second_due_date' => $secondDueDate->toDateString(),
                            'previous_balance' => $previousBalance,
                            'capital_amount' => $capitalAmount + $reserveAmount,
                            'interest_amount' => $interestAmount,
                            'adjustments_amount' => 0.00,
                            'discount_amount' => 0.00,
                            'total_amount' => $totalNewCharges,
                            'status' => 'draft',
                        ]);

                        ExpenseItem::create([
                            'expense_id' => $expense->id,
                            'concept' => $data['concepto_expensa'] ?? 'Expensas Ordinarias del Período',
                            'amount' => $capitalAmount,
                            'category' => 'general',
                        ]);

                        ExpenseItem::create([
                            'expense_id' => $expense->id,
                            'concept' => $data['concepto_fondo_reserva'] ?? 'Fondo de Reserva Colectivo',
                            'amount' => $reserveAmount,
                            'category' => 'reserve_fund',
                        ]);

                        if ($interestAmount > 0) {
                            ExpenseItem::create([
                                'expense_id' => $expense->id,
                                'concept' => 'Intereses por Mora acumulados',
                                'amount' => $interestAmount,
                                'category' => 'general',
                            ]);

                            $unit->balance += $interestAmount;
                            $unit->save();

                            AccountMovement::create([
                                'functional_unit_id' => $unit->id,
                                'type' => 'debit',
                                'date' => now()->toDateString(),
                                'amount' => $interestAmount,
                                'balance_after' => $unit->balance,
                                'description' => "Intereses por Mora - Expensa {$period->period}",
                                'related_model_type' => Expense::class,
                                'related_model_id' => $expense->id,
                            ]);
                        }

                        // Add debit movement for base capital in Account
                        $unit->balance += ($capitalAmount + $reserveAmount);
                        $unit->save();

                        // Update Lot balance too
                        $lot->balance = $unit->balance;
                        $lot->save();

                        AccountMovement::create([
                            'functional_unit_id' => $unit->id,
                            'type' => 'debit',
                            'date' => now()->toDateString(),
                            'amount' => $capitalAmount + $reserveAmount,
                            'balance_after' => $unit->balance,
                            'description' => "Facturación Expensas Período {$period->period} (Importación)",
                            'related_model_type' => Expense::class,
                            'related_model_id' => $expense->id,
                        ]);

                        // Register event in Lot History
                        $evType = LotHistoryEventType::where('name', 'expense_generated')->first();
                        $evCat = LotHistoryCategory::where('name', 'finance')->first();

                        LotHistoryEvent::create([
                            'lot_id' => $lot->id,
                            'functional_unit_id' => $unit->id,
                            'event_type_id' => $evType ? $evType->id : 1,
                            'category_id' => $evCat ? $evCat->id : 1,
                            'related_model_type' => Expense::class,
                            'related_model_id' => $expense->id,
                            'owner_id' => $lot->current_owner_id,
                            'tenant_id' => $lot->current_tenant_id,
                            'title' => "Expensa Generada Período {$period->period}",
                            'description' => "Se facturaron $ " . number_format($capitalAmount + $reserveAmount, 2, ',', '.') . " en conceptos de expensas (Importación), y $ " . number_format($interestAmount, 2, ',', '.') . " en intereses por mora.",
                            'event_date' => now(),
                            'visibility' => 'public',
                        ]);
                    }

                    $row->update(['status' => 'processed']);
                    $processed++;
                } catch (\Exception $e) {
                    $row->update([
                        'status' => 'failed',
                        'errors' => [$e->getMessage()],
                    ]);
                    $failed++;
                }
            });
        }

        // Recalculate totals
        $import->update([
            'status' => 'completed',
            'valid_rows' => $processed,
            'invalid_rows' => $import->invalid_rows + $failed,
        ]);

        return redirect()->route('admin.imports.index')->with('success', 'Importación finalizada. Creados: ' . $processed . ' registros. Fallidos: ' . ($import->invalid_rows + $failed));
    }
}
