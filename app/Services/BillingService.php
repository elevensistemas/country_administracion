<?php

namespace App\Services;

use App\Models\BillingPeriod;
use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\AccountMovement;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\FunctionalUnit;
use App\Models\Lot;
use App\Models\SystemSetting;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BillingService
{
    /**
     * Generate expenses for all active functional units in a billing period.
     */
    public function generateExpensesForPeriod(BillingPeriod $period)
    {
        $units = FunctionalUnit::with('lot')->get();
        $interestRate = (float) SystemSetting::where('key', 'interest_rate_monthly')->value('value') ?? 3.5;
        
        $dueDay = (int) SystemSetting::where('key', 'due_day')->value('value') ?? 10;
        $secondDueDay = (int) SystemSetting::where('key', 'second_due_day')->value('value') ?? 20;

        $yearMonth = explode('-', $period->period);
        $dueDate = Carbon::create($yearMonth[0], $yearMonth[1], $dueDay)->addMonth(); // Due next month
        $secondDueDate = Carbon::create($yearMonth[0], $yearMonth[1], $secondDueDay)->addMonth();

        $generatedCount = 0;

        foreach ($units as $unit) {
            DB::transaction(function () use ($unit, $period, $dueDate, $secondDueDate, $interestRate, &$generatedCount) {
                // Check if expense already generated for this unit and period
                $exists = Expense::where('billing_period_id', $period->id)
                    ->where('functional_unit_id', $unit->id)
                    ->exists();

                if ($exists) {
                    return;
                }

                // 1. Calculate Interest on previous overdue balance
                $interestAmount = 0.00;
                $previousBalance = $unit->balance;

                if ($previousBalance > 0) {
                    // Check if there are overdue unpaid debits
                    // Overdue interest calculation: previousBalance * rate / 100
                    $interestAmount = round($previousBalance * ($interestRate / 100), 2);
                }

                // 2. Base general expense amounts (hardcoded or configured - let's set a realistic base)
                $baseCapital = 15000.00; // Base expensa general
                $baseReserve = 3000.00;  // Base fondo de reserva
                
                $totalNewCharges = $baseCapital + $baseReserve + $interestAmount;
                $totalExpenseAmount = $previousBalance + $totalNewCharges;

                // 3. Create Expense Record
                $expense = Expense::create([
                    'billing_period_id' => $period->id,
                    'functional_unit_id' => $unit->id,
                    'issue_date' => now()->toDateString(),
                    'due_date' => $dueDate->toDateString(),
                    'second_due_date' => $secondDueDate->toDateString(),
                    'previous_balance' => $previousBalance,
                    'capital_amount' => $baseCapital + $baseReserve,
                    'interest_amount' => $interestAmount,
                    'adjustments_amount' => 0.00,
                    'discount_amount' => 0.00,
                    'total_amount' => $totalNewCharges,
                    'status' => 'draft',
                ]);

                // Create items
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'concept' => 'Expensas Ordinarias del Período',
                    'amount' => $baseCapital,
                    'category' => 'general',
                ]);

                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'concept' => 'Fondo de Reserva Colectivo',
                    'amount' => $baseReserve,
                    'category' => 'reserve_fund',
                ]);

                if ($interestAmount > 0) {
                    ExpenseItem::create([
                        'expense_id' => $expense->id,
                        'concept' => 'Intereses por Mora acumulados',
                        'amount' => $interestAmount,
                        'category' => 'general',
                    ]);

                    // Add debit movement for interest in Account
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
                $unit->balance += ($baseCapital + $baseReserve);
                $unit->save();

                // Update Lot balance too
                $lot = $unit->lot;
                $lot->balance = $unit->balance;
                $lot->save();

                AccountMovement::create([
                    'functional_unit_id' => $unit->id,
                    'type' => 'debit',
                    'date' => now()->toDateString(),
                    'amount' => $baseCapital + $baseReserve,
                    'balance_after' => $unit->balance,
                    'description' => "Facturación Expensas Período {$period->period}",
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
                    'description' => "Se facturaron $ " . number_format($baseCapital + $baseReserve, 2, ',', '.') . " en conceptos de expensas, y $ " . number_format($interestAmount, 2, ',', '.') . " en intereses por mora.",
                    'event_date' => now(),
                    'visibility' => 'public',
                ]);

                $generatedCount++;
            });
        }

        return $generatedCount;
    }

    /**
     * Allocate a payment to the account, settling interest first and then capital, oldest first.
     */
    /**
     * Allocate a payment to the account, settling interest first and then capital, oldest first.
     * Supports manual custom allocations and full auditing.
     */
    public function allocatePayment(Payment $payment, ?array $customAllocations = null, $userId = null, $method = 'automatic', $notes = null)
    {
        return DB::transaction(function () use ($payment, $customAllocations, $userId, $method, $notes) {
            $unit = FunctionalUnit::lockForUpdate()->findOrFail($payment->functional_unit_id);
            $lot = Lot::lockForUpdate()->findOrFail($unit->lot_id);

            // 1. Post Credit Movement and Sync Balances
            $this->syncBalances($unit, $lot, $payment->amount);
            $this->createCreditMovement($payment, $unit);

            // 2. Imputation
            if ($customAllocations !== null) {
                // Manual Imputation: loop through user-selected allocations
                $this->validatePaymentAllocation($payment, $customAllocations);

                foreach ($customAllocations as $debitId => $allocatedAmount) {
                    if ($allocatedAmount <= 0) continue;

                    $debit = AccountMovement::lockForUpdate()->findOrFail($debitId);
                    
                    // Calculate remaining amount
                    $currentAllocated = PaymentAllocation::where('account_movement_id', $debit->id)
                        ->where('status', 'active')
                        ->sum('allocated_amount');
                    $remaining = (float) number_format($debit->amount - $currentAllocated, 2, '.', '');

                    if ($allocatedAmount > ($remaining + 0.01)) {
                        throw new \Exception("No se puede imputar $allocatedAmount a una deuda con saldo pendiente de $remaining.");
                    }

                    $prevBalance = $remaining;
                    $postBalance = (float) number_format($remaining - $allocatedAmount, 2, '.', '');

                    PaymentAllocation::create([
                        'payment_id' => $payment->id,
                        'account_movement_id' => $debit->id,
                        'allocated_amount' => $allocatedAmount,
                        'user_id' => $userId,
                        'method' => $method,
                        'previous_balance' => $prevBalance,
                        'posterior_balance' => $postBalance,
                        'notes' => $notes,
                        'status' => 'active',
                    ]);

                    if ($debit->related_model_type === Expense::class) {
                        $this->updateExpenseStatus($debit->related_model_id);
                    }
                }
            } else {
                // Auto Imputation (FIFO)
                $remainingPayment = $payment->amount;

                // Step A: Find all unpaid/partially paid debits (interest first, then capital)
                $debits = AccountMovement::where('functional_unit_id', $unit->id)
                    ->where('type', 'debit')
                    ->get()
                    ->map(function ($debit) {
                        $allocated = PaymentAllocation::where('account_movement_id', $debit->id)
                            ->where('status', 'active')
                            ->sum('allocated_amount');
                        $debit->remaining_amount = (float) number_format($debit->amount - $allocated, 2, '.', '');
                        return $debit;
                    })
                    ->filter(function ($debit) {
                        return $debit->remaining_amount > 0;
                    });

                // Split debits into Interest and Capital
                $interestDebits = $debits->filter(function ($d) {
                    return str_contains(strtolower($d->description), 'interes') || str_contains(strtolower($d->description), 'mora');
                })->sortBy('date');

                $capitalDebits = $debits->filter(function ($d) {
                    return !str_contains(strtolower($d->description), 'interes') && !str_contains(strtolower($d->description), 'mora');
                })->sortBy('date');

                // Apply to Interest first
                $this->allocateToDebits($payment, $interestDebits, $remainingPayment, $userId, $method, $notes);

                // Apply to Capital next
                $this->allocateToDebits($payment, $capitalDebits, $remainingPayment, $userId, $method, $notes);
            }

            // Log approved payment in lot history
            $evType = LotHistoryEventType::where('name', 'payment_approved')->first();
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
                'user_id' => $userId ?? auth()->id(),
                'title' => "Pago Aprobado e Imputado",
                'description' => "Se concilió pago de $ " . number_format($payment->amount, 2, ',', '.') . " con operación N°: {$payment->operation_number}. Se imputó a deudas del lote. Saldo actual: $ " . number_format($unit->balance, 2, ',', '.'),
                'event_date' => now(),
                'visibility' => 'public',
            ]);

            return true;
        });
    }

    /**
     * Helper to allocate remaining payment to a list of debits.
     */
    private function allocateToDebits(Payment $payment, $debits, &$remainingPayment, $userId, $method, $notes)
    {
        foreach ($debits as $debit) {
            if ($remainingPayment <= 0) break;

            $allocatedAmount = min($remainingPayment, $debit->remaining_amount);
            
            $prevBalance = $debit->remaining_amount;
            $postBalance = (float) number_format($debit->remaining_amount - $allocatedAmount, 2, '.', '');

            PaymentAllocation::create([
                'payment_id' => $payment->id,
                'account_movement_id' => $debit->id,
                'allocated_amount' => $allocatedAmount,
                'user_id' => $userId,
                'method' => $method,
                'previous_balance' => $prevBalance,
                'posterior_balance' => $postBalance,
                'notes' => $notes,
                'status' => 'active',
            ]);

            $remainingPayment -= $allocatedAmount;

            if ($debit->related_model_type === Expense::class) {
                $this->updateExpenseStatus($debit->related_model_id);
            }
        }
    }

    /**
     * Validate manual custom allocations.
     */
    private function validatePaymentAllocation(Payment $payment, array $allocations)
    {
        $total = 0.00;
        foreach ($allocations as $amount) {
            $total += (float)$amount;
        }

        $totalStr = number_format($total, 2, '.', '');
        $paymentAmountStr = number_format($payment->amount, 2, '.', '');

        if ($totalStr > $paymentAmountStr) {
            throw new \Exception("El importe total imputado ($totalStr) supera el importe del pago ($paymentAmountStr).");
        }
    }

    /**
     * Create credit account movement for payment.
     */
    private function createCreditMovement(Payment $payment, FunctionalUnit $unit)
    {
        return AccountMovement::create([
            'functional_unit_id' => $unit->id,
            'type' => 'credit',
            'date' => $payment->payment_date->toDateString(),
            'amount' => $payment->amount,
            'balance_after' => $unit->balance,
            'description' => "Acreditación Pago Informado - Transf. / Op: {$payment->operation_number}",
            'related_model_type' => Payment::class,
            'related_model_id' => $payment->id,
        ]);
    }

    /**
     * Update functional unit and lot balances.
     */
    private function syncBalances(FunctionalUnit $unit, Lot $lot, $paymentAmount)
    {
        $unit->balance -= $paymentAmount;
        $unit->save();

        $lot->balance = $unit->balance;
        $lot->save();
    }

    /**
     * Update Expense status based on active allocations.
     */
    public function updateExpenseStatus($expenseId)
    {
        $expense = Expense::find($expenseId);
        if (!$expense) return;

        $totalAllocatedOnExpense = DB::table('payment_allocations')
            ->join('account_movements', 'payment_allocations.account_movement_id', '=', 'account_movements.id')
            ->where('account_movements.related_model_type', Expense::class)
            ->where('account_movements.related_model_id', $expense->id)
            ->where('payment_allocations.status', 'active')
            ->sum('payment_allocations.allocated_amount');

        if ($totalAllocatedOnExpense >= $expense->total_amount) {
            $expense->update(['status' => 'paid']);
        } else if ($totalAllocatedOnExpense > 0) {
            $expense->update(['status' => 'partial']);
        } else {
            $status = (Carbon::parse($expense->due_date)->isPast()) ? 'overdue' : 'published';
            $expense->update(['status' => $status]);
        }
    }
}
