<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\AccountMovement;
use App\Models\FunctionalUnit;
use App\Models\Lot;
use App\Models\Expense;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReconciliationService
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Calculate candidate matches for a payment against outstanding debits.
     * Returns the best candidate match details, score, explanations and all potential candidates.
     */
    public function calculateMatch(Payment $payment)
    {
        // 1. Fetch active debits with remaining outstanding amounts
        $query = AccountMovement::with(['functionalUnit.lot.owner'])
            ->where('type', 'debit');

        // If unit is already specified, narrow search to that unit
        if ($payment->functional_unit_id) {
            $query->where('functional_unit_id', $payment->functional_unit_id);
        }

        $debits = $query->get();

        // Load allocations in bulk to avoid N+1
        $allocations = PaymentAllocation::whereIn('account_movement_id', $debits->pluck('id'))
            ->where('status', 'active')
            ->get()
            ->groupBy('account_movement_id');

        $activeDebits = $debits->map(function ($debit) use ($allocations) {
            $allocated = isset($allocations[$debit->id]) ? $allocations[$debit->id]->sum('allocated_amount') : 0.00;
            // Use safe subtraction with bcsub or formatting
            $debit->remaining_amount = (float) number_format($debit->amount - $allocated, 2, '.', '');
            return $debit;
        })->filter(function ($debit) {
            return $debit->remaining_amount > 0;
        });

        $candidates = [];

        foreach ($activeDebits as $debit) {
            $score = 0;
            $reasons = [];
            $hasStructuralMatch = false;

            $unit = $debit->functionalUnit;
            $lot = $unit->lot;
            $owner = $lot ? $lot->owner : null;

            // --- 1. LOTE / UNIDAD (Max +40) ---
            if ($payment->functional_unit_id && $payment->functional_unit_id == $unit->id) {
                $score += 40;
                $reasons[] = "Lote/Unidad coincide de forma estructurada (+40)";
                $hasStructuralMatch = true;
            } elseif ($lot && $payment->notes) {
                // Infer from notes/concept text
                if (preg_match('/\blote\s*' . $lot->number . '\b/i', $payment->notes) ||
                    preg_match('/\bl' . $lot->number . '\b/i', $payment->notes)) {
                    $score += 20;
                    $reasons[] = "Lote {$lot->number} detectado en el concepto (+20)";
                }
            }

            // --- 2. PROPIETARIO (Max +25) ---
            if ($owner) {
                if ($payment->owner_id && $payment->owner_id == $owner->id) {
                    $score += 25;
                    $reasons[] = "Propietario coincide de forma estructurada (+25)";
                    $hasStructuralMatch = true;
                } else {
                    // Check CUIT or DNI in payment notes
                    if ($owner->cuit && $payment->notes && str_contains(str_replace('-', '', $payment->notes), str_replace('-', '', $owner->cuit))) {
                        $score += 25;
                        $reasons[] = "CUIT del propietario coincide (+25)";
                        $hasStructuralMatch = true;
                    } elseif ($owner->dni && $payment->notes && str_contains($payment->notes, $owner->dni)) {
                        $score += 25;
                        $reasons[] = "DNI del propietario coincide (+25)";
                        $hasStructuralMatch = true;
                    } else {
                        // Fuzzy check for owner name in notes
                        $nameMatched = false;
                        if ($payment->notes) {
                            $notesLower = mb_strtolower($payment->notes);
                            $lastNameLower = mb_strtolower($owner->last_name);
                            $firstNameLower = mb_strtolower($owner->name);

                            if (!empty($owner->last_name) && str_contains($notesLower, $lastNameLower)) {
                                $score += 10;
                                $reasons[] = "Apellido del propietario detectado en concepto (+10)";
                                $nameMatched = true;
                            }
                            if (!empty($owner->name) && str_contains($notesLower, $firstNameLower) && !$nameMatched) {
                                $score += 5;
                                $reasons[] = "Nombre del propietario detectado en concepto (+5)";
                            }
                        }
                    }
                }
            }

            // --- 3. IMPORTE EXACTO (+25) ---
            // Use safe comparison for currency
            if (abs($payment->amount - $debit->remaining_amount) < 0.01) {
                $score += 25;
                $reasons[] = "Importe coincide exactamente con el saldo pendiente (+25)";
            }

            // --- 4. CONCEPTO / PERIODO (+10) ---
            if ($debit->related_model_type === Expense::class && $payment->notes) {
                $expense = Expense::find($debit->related_model_id);
                if ($expense && $expense->billingPeriod) {
                    $period = $expense->billingPeriod->period; // e.g., "2026-08"
                    $parts = explode('-', $period);
                    if (count($parts) === 2) {
                        $monthNum = $parts[1];
                        $monthsMap = [
                            '01' => ['enero', 'ene'],
                            '02' => ['febrero', 'feb'],
                            '03' => ['marzo', 'mar'],
                            '04' => ['abril', 'abr'],
                            '05' => ['mayo', 'may'],
                            '06' => ['junio', 'jun'],
                            '07' => ['julio', 'jul'],
                            '08' => ['agosto', 'ago'],
                            '09' => ['septiembre', 'sep', 'setiembre'],
                            '10' => ['octubre', 'oct'],
                            '11' => ['noviembre', 'nov'],
                            '12' => ['diciembre', 'dic'],
                        ];

                        $matchedPeriod = false;
                        if (isset($monthsMap[$monthNum])) {
                            foreach ($monthsMap[$monthNum] as $term) {
                                if (str_contains(mb_strtolower($payment->notes), $term)) {
                                    $matchedPeriod = true;
                                    break;
                                }
                            }
                        }

                        if (str_contains($payment->notes, $parts[0])) { // Year check
                            $matchedPeriod = true;
                        }

                        if ($matchedPeriod) {
                            $score += 10;
                            $reasons[] = "Período del débito ({$period}) mencionado en concepto (+10)";
                        }
                    }
                }
            }

            $candidates[] = [
                'debit' => $debit,
                'score' => min($score, 100),
                'reasons' => $reasons,
                'has_structural_match' => $hasStructuralMatch,
            ];
        }

        // Sort candidates by score descending
        usort($candidates, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $bestMatch = null;
        $score = 0;
        $reasons = [];
        $isAutoReconcilable = false;

        if (count($candidates) > 0) {
            $bestCandidate = $candidates[0];
            $bestMatch = $bestCandidate['debit'];
            $score = $bestCandidate['score'];
            $reasons = $bestCandidate['reasons'];

            // Auto-reconcile rules:
            // 1. score >= 95
            // 2. No second candidate is close (diff >= 15 points)
            // 3. Strong structural match (lot_id, functional_unit_id, owner_id or CUIT/DNI)
            $isAutoReconcilable = ($score >= 95) && $bestCandidate['has_structural_match'];

            if ($isAutoReconcilable && count($candidates) > 1) {
                $secondScore = $candidates[1]['score'];
                if (($score - $secondScore) < 15) {
                    $isAutoReconcilable = false; // Ambiguity!
                }
            }
        }

        return [
            'best_match' => $bestMatch,
            'score' => $score,
            'reasons' => $reasons,
            'candidates' => $candidates,
            'is_auto_reconcilable' => $isAutoReconcilable,
        ];
    }

    /**
     * Simulate bulk auto-reconciliation for all pending payments.
     */
    public function simulateAutoReconciliation()
    {
        $pendingPayments = Payment::with(['owner', 'lot', 'functionalUnit'])
            ->whereIn('status', ['pending', 'review'])
            ->get();

        $autoReconcilable = [];
        $requiresReview = [];

        foreach ($pendingPayments as $payment) {
            $matchResult = $this->calculateMatch($payment);

            if ($matchResult['is_auto_reconcilable']) {
                $payment->suggested_debit = $matchResult['best_match'];
                $payment->suggested_score = $matchResult['score'];
                $payment->suggested_reasons = $matchResult['reasons'];
                $autoReconcilable[] = $payment;
            } else {
                $payment->suggested_debit = $matchResult['best_match'];
                $payment->suggested_score = $matchResult['score'];
                $payment->suggested_reasons = $matchResult['reasons'];
                $requiresReview[] = $payment;
            }
        }

        return [
            'auto_reconcilable' => $autoReconcilable,
            'requires_review' => $requiresReview,
            'total_pending' => $pendingPayments->count(),
        ];
    }

    /**
     * Apply bulk auto-reconciliation.
     */
    public function applyAutoReconciliation(array $paymentIds, $userId)
    {
        return DB::transaction(function () use ($paymentIds, $userId) {
            $processedCount = 0;

            foreach ($paymentIds as $id) {
                // Lock payment for concurrency safety
                $payment = Payment::lockForUpdate()->findOrFail($id);

                // Double check status inside transaction
                if ($payment->status !== 'pending' && $payment->status !== 'review') {
                    continue; // Skip already processed
                }

                $matchResult = $this->calculateMatch($payment);

                if ($matchResult['is_auto_reconcilable']) {
                    $debit = $matchResult['best_match'];

                    // Ensure payment has unit linked
                    if (!$payment->functional_unit_id) {
                        $payment->update([
                            'owner_id' => $debit->functionalUnit->lot->current_owner_id,
                            'lot_id' => $debit->functionalUnit->lot_id,
                            'functional_unit_id' => $debit->functional_unit_id,
                        ]);
                    }

                    // Form custom allocation array (pay matching debit first)
                    // If payment is superior, the BillingService will handle allocating the rest or surplus.
                    $allocations = [
                        $debit->id => min($payment->amount, $debit->remaining_amount)
                    ];

                    $this->reconcile($payment, $allocations, $userId, 'automatic', "Conciliación automática masiva (Match: {$matchResult['score']}%)");
                    $processedCount++;
                }
            }

            return $processedCount;
        });
    }

    /**
     * Reconcile a payment manually or automatically with transactional lock.
     */
    public function reconcile(Payment $payment, array $allocations, $userId, $method, $notes = null)
    {
        return DB::transaction(function () use ($payment, $allocations, $userId, $method, $notes) {
            // Lock payment, functional unit, and lot for update to prevent concurrent race conditions
            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === 'approved') {
                throw new \Exception("Este pago ya ha sido conciliado anteriormente.");
            }

            // Ensure allocations sum + credit_surplus = payment.amount
            $totalAllocated = 0.00;
            foreach ($allocations as $debitId => $amount) {
                $totalAllocated += (float)$amount;
            }

            // Math check (format to float string to bypass float precision issues)
            $totalAllocatedStr = number_format($totalAllocated, 2, '.', '');
            $paymentAmountStr = number_format($payment->amount, 2, '.', '');

            if ($totalAllocatedStr > $paymentAmountStr) {
                throw new \Exception("El importe total imputado ($totalAllocatedStr) no puede superar el importe del pago ($paymentAmountStr).");
            }

            // Update payment record status
            $payment->update([
                'status' => 'approved',
                'user_id' => $userId,
                'reconciliation_method' => $method,
                'reconciled_at' => now(),
                'notes' => $notes ?? $payment->notes,
            ]);

            // Call modified BillingService to execute financial movements
            $this->billingService->allocatePayment($payment, $allocations, $userId, $method, $notes);

            return true;
        });
    }

    /**
     * Revert a previously approved payment reconciliation safely.
     */
    public function revert(Payment $payment, $userId, $reason)
    {
        return DB::transaction(function () use ($payment, $userId, $reason) {
            // Lock payment and functional unit for update
            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status !== 'approved') {
                throw new \Exception("Solo se pueden revertir pagos conciliados (aprobados).");
            }

            $unit = FunctionalUnit::lockForUpdate()->findOrFail($payment->functional_unit_id);
            $lot = Lot::lockForUpdate()->findOrFail($unit->lot_id);

            // 1. Get active allocations for this payment
            $activeAllocations = PaymentAllocation::where('payment_id', $payment->id)
                ->where('status', 'active')
                ->get();

            // 2. Mark allocations as reverted
            foreach ($activeAllocations as $alloc) {
                $alloc->update([
                    'status' => 'reverted',
                    'reverted_at' => now(),
                    'reverted_by' => $userId,
                    'reversion_reason' => $reason,
                ]);

                // Recalculate Expense status if the debit was an Expense
                $debit = $alloc->accountMovement;
                if ($debit && $debit->related_model_type === Expense::class) {
                    $this->billingService->updateExpenseStatus($debit->related_model_id);
                }
            }

            // 3. Post Reversal Debit Movement (Contra-asiento) in Account Current
            // This restores the balance without deleting the credit history.
            $unit->balance += $payment->amount;
            $unit->save();

            $lot->balance = $unit->balance;
            $lot->save();

            AccountMovement::create([
                'functional_unit_id' => $unit->id,
                'type' => 'debit',
                'date' => now()->toDateString(),
                'amount' => $payment->amount,
                'balance_after' => $unit->balance,
                'description' => "REVERSIÓN PAGO - Op: {$payment->operation_number} - Motivo: {$reason}",
                'related_model_type' => Payment::class, // Reference payment
                'related_model_id' => $payment->id,
            ]);

            // 4. Update Payment record to pending again, but saving reversion log
            $payment->update([
                'status' => 'pending',
                'reverted_at' => now(),
                'reverted_by' => $userId,
                'reversion_reason' => $reason,
            ]);

            // 5. Log reversion in Lot History
            $evType = LotHistoryEventType::where('name', 'note_added')->first(); // generic administrative note
            $evCat = LotHistoryCategory::where('name', 'finance')->first();

            LotHistoryEvent::create([
                'lot_id' => $lot->id,
                'functional_unit_id' => $unit->id,
                'event_type_id' => $evType ? $evType->id : 1,
                'category_id' => $evCat ? $evCat->id : 1,
                'related_model_type' => Payment::class,
                'related_model_id' => $payment->id,
                'owner_id' => $lot->current_owner_id,
                'tenant_id' => $lot->current_tenant_id,
                'user_id' => $userId,
                'title' => "Conciliación de Pago Revertida",
                'description' => "Se anuló la conciliación del pago de $ " . number_format($payment->amount, 2, ',', '.') . " (Op: {$payment->operation_number}). Motivo: {$reason}. Saldo actual: $ " . number_format($unit->balance, 2, ',', '.'),
                'event_date' => now(),
                'visibility' => 'internal', // internal note
            ]);

            return true;
        });
    }
}
