<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingPeriod;
use App\Models\Expense;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Display a listing of resources.
     */
    public function index(Request $request)
    {
        $periods = BillingPeriod::orderBy('period', 'desc')->get();
        
        $query = Expense::with(['billingPeriod', 'functionalUnit.lot.owner']);

        if ($request->filled('billing_period_id')) {
            $query->where('billing_period_id', $request->input('billing_period_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $expenses = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.expenses.index', compact('periods', 'expenses'));
    }

    /**
     * Show form to create period.
     */
    public function createPeriod()
    {
        return view('admin.expenses.create-period');
    }

    /**
     * Store a billing period.
     */
    public function storePeriod(Request $request)
    {
        $request->validate([
            'period' => 'required|string|regex:/^\d{4}-\d{2}$/|unique:billing_periods,period',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'period.regex' => 'El formato del período debe ser AAAA-MM (Ej: 2026-09)',
            'period.unique' => 'Este período ya se encuentra registrado.',
        ]);

        BillingPeriod::create([
            'period' => $request->period,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'draft',
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Período de facturación creado correctamente en borrador.');
    }

    /**
     * Trigger batch expense generation.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'billing_period_id' => 'required|exists:billing_periods,id',
        ]);

        $period = BillingPeriod::findOrFail($request->billing_period_id);
        
        if ($period->status !== 'draft') {
            return back()->with('error', 'Solo se pueden generar expensas en períodos en estado borrador.');
        }

        $count = $this->billingService->generateExpensesForPeriod($period);

        return redirect()->route('admin.expenses.index')
            ->with('success', "Se generaron {$count} liquidaciones de expensas para el período {$period->period}.");
    }

    /**
     * Publish expenses for a period.
     */
    public function publish(Request $request, Expense $expense)
    {
        if ($expense->status !== 'draft') {
            return back()->with('error', 'Esta expensa ya se encuentra publicada.');
        }

        $expense->update(['status' => 'published']);

        // Fire lot history event
        $lot = $expense->functionalUnit->lot;
        \App\Models\LotHistoryEvent::create([
            'lot_id' => $lot->id,
            'functional_unit_id' => $expense->functional_unit_id,
            'event_type_id' => \App\Models\LotHistoryEventType::where('name', 'expense_published')->first()?->id ?? 1,
            'category_id' => \App\Models\LotHistoryCategory::where('name', 'finance')->first()?->id ?? 1,
            'related_model_type' => Expense::class,
            'related_model_id' => $expense->id,
            'owner_id' => $lot->current_owner_id,
            'tenant_id' => $lot->current_tenant_id,
            'title' => "Expensa Publicada",
            'description' => "Se ha publicado la expensa correspondiente al período {$expense->billingPeriod->period}. Total facturado: $ " . number_format($expense->total_amount, 2, ',', '.'),
            'event_date' => now(),
            'visibility' => 'public',
        ]);

        return back()->with('success', 'Expensa publicada correctamente. Ya es visible para el propietario.');
    }

    /**
     * Download simulated PDF.
     */
    public function downloadPdf(Expense $expense)
    {
        $expense->load(['billingPeriod', 'functionalUnit.lot.owner', 'items']);
        return view('admin.expenses.pdf', compact('expense'));
    }
}
