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
        if ($expense->attachment_path && file_exists(storage_path('app/public/' . $expense->attachment_path))) {
            return response()->file(storage_path('app/public/' . $expense->attachment_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="liquidacion_expensas_' . $expense->billingPeriod->period . '.pdf"'
            ]);
        }

        if ($expense->attachment_path && file_exists(public_path('storage/' . $expense->attachment_path))) {
            return response()->file(public_path('storage/' . $expense->attachment_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="liquidacion_expensas_' . $expense->billingPeriod->period . '.pdf"'
            ]);
        }

        $expense->load(['billingPeriod', 'functionalUnit.lot.owner', 'items']);
        return view('admin.expenses.pdf', compact('expense'));
    }

    /**
     * Upload and import monthly Expense PDF liquidation and bulletin.
     */
    public function importPdf(Request $request, \App\Services\PdfExpenseParserService $parserService)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:30720',
            'period' => 'required|string',
        ], [
            'pdf_file.required' => 'Debe seleccionar el archivo PDF oficial de liquidación.',
            'pdf_file.mimes' => 'El archivo debe ser un documento PDF válido.',
            'period.required' => 'Debe indicar el período a liquidar (ej: 2026-08).',
        ]);

        $file = $request->file('pdf_file');
        $periodInput = trim($request->input('period'));
        
        // Normalize period to YYYY-MM
        if (preg_match('/^(\d{4})-(\d{2})$/', $periodInput, $pm)) {
            $periodStr = $periodInput;
            $year = intval($pm[1]);
            $month = intval($pm[2]);
        } elseif (preg_match('/^(\d{2})[-\/](\d{4})$/', $periodInput, $pm)) {
            $periodStr = $pm[2] . '-' . $pm[1];
            $year = intval($pm[2]);
            $month = intval($pm[1]);
        } else {
            return back()->with('error', 'El formato del período debe ser AAAA-MM (Ej: 2026-09).');
        }

        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();

        $billingPeriod = BillingPeriod::firstOrCreate(
            ['period' => $periodStr],
            [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'status' => 'closed',
            ]
        );
        $billingPeriod->status = 'closed';
        $billingPeriod->save();

        // Save PDF in storage
        $fileName = 'boletin_y_liquidacion_' . str_replace('-', '_', $periodStr) . '.pdf';
        $destinationDir = storage_path('app/public/expenses');
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }
        $file->move($destinationDir, $fileName);
        $relativePdfPath = 'expenses/' . $fileName;

        // Copy to public/storage
        $pubDir = public_path('storage/expenses');
        if (!file_exists($pubDir)) {
            mkdir($pubDir, 0755, true);
        }
        @copy($destinationDir . '/' . $fileName, $pubDir . '/' . $fileName);

        $fullPdfPath = $destinationDir . '/' . $fileName;

        try {
            $rows = $parserService->parsePdf($fullPdfPath);

            if (empty($rows)) {
                return back()->with('error', 'No se pudieron extraer los datos de la tabla de liquidación del PDF. Verifique que el archivo sea el boletín oficial con las páginas de liquidación.');
            }

            $importedCount = $parserService->importExpenses($billingPeriod, $rows, $relativePdfPath);
            $totalSum = array_sum(array_column($rows, 'total_a_pagar'));

            return redirect()->route('admin.expenses.index', ['billing_period_id' => $billingPeriod->id])
                ->with('success', "¡Liquidación importada con éxito! Se procesaron {$importedCount} lotes para el período {$periodStr}. Monto total: $ " . number_format($totalSum, 2, ',', '.'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo PDF: ' . $e->getMessage());
        }
    }
}
