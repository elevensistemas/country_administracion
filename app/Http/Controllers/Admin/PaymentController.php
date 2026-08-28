<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\FunctionalUnit;
use App\Models\AccountMovement;
use App\Models\PaymentAllocation;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryCategory;
use App\Services\ReconciliationService;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    protected $reconciliationService;
    protected $billingService;

    public function __construct(ReconciliationService $reconciliationService, BillingService $billingService)
    {
        $this->reconciliationService = $reconciliationService;
        $this->billingService = $billingService;
    }

    /**
     * Display a listing of reported payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['owner', 'lot', 'functionalUnit']);

        // 1. Apply status filters
        $status = $request->input('status', 'pending');
        if ($status === 'pending') {
            $query->whereIn('status', ['pending', 'review']);
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        } elseif ($status === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($status === 'review') {
            $query->where('status', 'review');
        } elseif ($status === 'unmatched') {
            $query->whereIn('status', ['pending', 'review'])->whereNull('functional_unit_id');
        } elseif ($status === 'excess') {
            // Reconciled with surplus
            $query->where('status', 'approved')
                  ->whereHas('allocations', function($q) {
                      $q->where('status', 'active');
                  })
                  ->where(function($q) {
                      // Remaining balance or total allocations < payment amount
                      $q->whereRaw('(SELECT SUM(allocated_amount) FROM payment_allocations WHERE payment_id = payments.id AND status = "active") < amount');
                  });
        } elseif ($status === 'partial') {
            // Reconciled but some debt remains on unit (or partially allocated)
            $query->where('status', 'approved');
        } elseif ($status !== 'all') {
            $query->where('status', 'pending');
        }

        // 2. Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('operation_number', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('dni', 'like', "%{$search}%")
                         ->orWhere('cuit', 'like', "%{$search}%");
                  })
                  ->orWhereHas('lot', function ($lq) use ($search) {
                      $lq->where('number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('functionalUnit', function ($fuq) use ($search) {
                      $fuq->where('code', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15)->withQueryString();

        // Dynamically calculate matching info for pending/review payments in current page
        foreach ($payments as $pay) {
            if ($pay->status === 'pending' || $pay->status === 'review') {
                $match = $this->reconciliationService->calculateMatch($pay);
                $pay->match_score = $match['score'];
                $pay->is_auto_reconcilable = $match['is_auto_reconcilable'];
                $pay->suggested_debit = $match['best_match'];
            }
        }

        // 3. Top indicators (Dashboard stats)
        $today = Carbon::today();
        $stats = [
            'pending_count' => Payment::whereIn('status', ['pending', 'review'])->count(),
            'reconciled_today_count' => Payment::where('status', 'approved')->whereDate('reconciled_at', $today)->count(),
            'review_count' => Payment::where('status', 'review')->count(),
            'unidentified_count' => Payment::whereIn('status', ['pending', 'review'])->whereNull('functional_unit_id')->count(),
            'reconciled_today_amount' => Payment::where('status', 'approved')->whereDate('reconciled_at', $today)->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Display the specified reported payment details.
     */
    public function show(Payment $payment)
    {
        $payment->load(['owner', 'lot', 'functionalUnit', 'receipts', 'user']);

        // Duplicates check
        $potentialDuplicates = [];
        if ($payment->operation_number) {
            $potentialDuplicates = Payment::where('operation_number', $payment->operation_number)
                ->where('id', '!=', $payment->id)
                ->get();
        }

        if ($payment->status === 'pending' || $payment->status === 'review') {
            // Get candidate deudas
            $matchResult = $this->reconciliationService->calculateMatch($payment);
            $suggestedDebit = $matchResult['best_match'];
            $matchingScore = $matchResult['score'];
            $matchingReasons = $matchResult['reasons'];
            $candidates = $matchResult['candidates'];

            // Get outstanding debits if unit is set
            $debits = collect();
            if ($payment->functional_unit_id) {
                // Fetch all unpaid/partially paid debits of this unit
                $allDebits = AccountMovement::where('functional_unit_id', $payment->functional_unit_id)
                    ->where('type', 'debit')
                    ->get();
                
                $allocations = PaymentAllocation::whereIn('account_movement_id', $allDebits->pluck('id'))
                    ->where('status', 'active')
                    ->get()
                    ->groupBy('account_movement_id');

                $debits = $allDebits->map(function($d) use ($allocations) {
                    $allocated = isset($allocations[$d->id]) ? $allocations[$d->id]->sum('allocated_amount') : 0.00;
                    $d->remaining_amount = (float) number_format($d->amount - $allocated, 2, '.', '');
                    return $d;
                })->filter(function($d) {
                    return $d->remaining_amount > 0;
                });
            }

            // List of functional units for manual matching
            $units = FunctionalUnit::with('lot.owner')->get();

            return view('admin.payments.show', compact(
                'payment', 'potentialDuplicates', 'suggestedDebit', 
                'matchingScore', 'matchingReasons', 'candidates', 'debits', 'units'
            ));
        } else {
            // Already reconciled or rejected
            $allocations = PaymentAllocation::with('accountMovement')
                ->where('payment_id', $payment->id)
                ->get();

            return view('admin.payments.show', compact('payment', 'potentialDuplicates', 'allocations'));
        }
    }

    /**
     * Reconcile payment manually.
     */
    public function reconcile(Request $request, Payment $payment)
    {
        $request->validate([
            'functional_unit_id' => 'required_without:payment_functional_unit_id|exists:functional_units,id',
            'allocations' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function() use ($request, $payment) {
                // Lock payment
                $payment = Payment::lockForUpdate()->findOrFail($payment->id);

                if ($payment->status === 'approved') {
                    throw new \Exception("Este pago ya se encuentra conciliado.");
                }

                // Match unit manually if not already matched
                $unitId = $request->input('functional_unit_id') ?? $payment->functional_unit_id;
                if (!$unitId) {
                    throw new \Exception("Debes seleccionar una unidad funcional.");
                }

                $unit = FunctionalUnit::findOrFail($unitId);

                $payment->update([
                    'functional_unit_id' => $unit->id,
                    'lot_id' => $unit->lot_id,
                    'owner_id' => $unit->lot->current_owner_id,
                ]);

                $allocations = $request->input('allocations', []);
                // Clean allocations
                $allocations = array_filter($allocations, fn($v) => (float)$v > 0);

                // If no allocations provided, we fallback to default auto FIFO in BillingService
                $allocationsParam = count($allocations) > 0 ? $allocations : null;

                $this->reconciliationService->reconcile(
                    $payment,
                    $allocationsParam ?? [],
                    auth()->id(),
                    'manual',
                    $request->input('notes')
                );
            });

            return redirect()->route('admin.payments.index')->with('success', 'Pago conciliado e imputado en la cuenta corriente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al conciliar el pago: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Revert payment reconciliation.
     */
    public function revert(Request $request, Payment $payment)
    {
        $request->validate([
            'reversion_reason' => 'required|string|max:500',
        ]);

        try {
            $this->reconciliationService->revert($payment, auth()->id(), $request->input('reversion_reason'));
            return redirect()->route('admin.payments.show', $payment)->with('success', 'Conciliación revertida de manera segura. El pago vuelve a estar pendiente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al revertir: ' . $e->getMessage());
        }
    }

    /**
     * Process simple rejection.
     */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            DB::transaction(function() use ($payment, $request) {
                $payment = Payment::lockForUpdate()->findOrFail($payment->id);

                if ($payment->status !== 'pending' && $payment->status !== 'review') {
                    throw new \Exception("Este pago ya ha sido procesado anteriormente.");
                }

                $payment->update([
                    'status' => 'rejected',
                    'user_id' => auth()->id(),
                    'notes' => $request->input('notes'),
                ]);

                // Log rejected payment in lot history
                if ($payment->lot) {
                    $lot = $payment->lot;
                    $evType = LotHistoryEventType::where('name', 'payment_rejected')->first();
                    $evCat = LotHistoryCategory::where('name', 'finance')->first();

                    LotHistoryEvent::create([
                        'lot_id' => $lot->id,
                        'functional_unit_id' => $payment->functional_unit_id,
                        'event_type_id' => $evType ? $evType->id : 1,
                        'category_id' => $evCat ? $evCat->id : 1,
                        'related_model_type' => Payment::class,
                        'related_model_id' => $payment->id,
                        'owner_id' => $lot->current_owner_id,
                        'tenant_id' => $lot->current_tenant_id,
                        'user_id' => auth()->id(),
                        'title' => "Pago Rechazado",
                        'description' => "Se rechazó el pago informado de $ " . number_format($payment->amount, 2, ',', '.') . " (Operación N°: {$payment->operation_number}). Motivo: " . $request->notes,
                        'event_date' => now(),
                        'visibility' => 'public',
                    ]);
                }
            });

            return redirect()->route('admin.payments.index')->with('success', 'Pago rechazado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al rechazar: ' . $e->getMessage());
        }
    }

    /**
     * Mark payment as review.
     */
    public function markReview(Payment $payment)
    {
        try {
            DB::transaction(function() use ($payment) {
                $payment = Payment::lockForUpdate()->findOrFail($payment->id);
                if ($payment->status !== 'pending') {
                    throw new \Exception("Solo se pueden enviar a revisión pagos pendientes.");
                }
                $payment->update(['status' => 'review']);
            });
            return back()->with('success', 'Pago enviado a revisión manual.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Simulate bulk auto-reconciliation.
     */
    public function autoReconcileSimulate()
    {
        $simulation = $this->reconciliationService->simulateAutoReconciliation();
        return view('admin.payments.auto-reconcile-confirm', compact('simulation'));
    }

    /**
     * Apply bulk auto-reconciliation.
     */
    public function autoReconcileApply(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array',
        ]);

        $ids = $request->input('payment_ids');

        try {
            $count = $this->reconciliationService->applyAutoReconciliation($ids, auth()->id());
            return redirect()->route('admin.payments.index')->with('success', "Se conciliaron automáticamente {$count} pagos de forma segura.");
        } catch (\Exception $e) {
            return redirect()->route('admin.payments.index')->with('error', 'Error en conciliación masiva: ' . $e->getMessage());
        }
    }
}
