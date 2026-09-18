<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Expense;
use App\Models\SupplierInvoice;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display reporting dashboard.
     */
    public function index(Request $request)
    {
        // Selected year filter (default current year)
        $selectedYear = (int) $request->input('year', date('Y'));

        // 1. KPIs Generales
        $totalLots = Lot::count();
        $totalOwners = Owner::count();
        $totalDebt = Lot::where('balance', '>', 0)->sum('balance');
        $totalSurplus = abs(Lot::where('balance', '<', 0)->sum('balance'));
        $totalCollectedYear = Payment::where('status', 'approved')
            ->whereYear('payment_date', $selectedYear)
            ->sum('amount');
        $totalInvoicedYear = Expense::whereYear('issue_date', $selectedYear)
            ->sum('total_amount');
        $totalSupplierExpensesYear = SupplierInvoice::whereYear('issue_date', $selectedYear)
            ->sum('amount');
        $openTicketsCount = Ticket::whereIn('status', ['open', 'in_progress', 'pending'])->count();

        // 2. Ranking de Deudores / Morosidad (Lotes con saldo deudor)
        $debtors = Lot::where('balance', '>', 0)
            ->with(['owner', 'tenant', 'functionalUnits'])
            ->orderBy('balance', 'desc')
            ->get();

        // 3. Padrón General de Lotes con Saldos
        $lotsSummary = Lot::with(['owner', 'tenant'])
            ->orderBy('number', 'asc')
            ->get();

        // 4. Recaudación Mensual (Cobros aprobados en los últimos 12 meses)
        $monthlyCollection = Payment::where('status', 'approved')
            ->select(
                DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as month"),
                DB::raw('SUM(amount) as total_collected'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        // 5. Facturación de Expensas Mensual
        $monthlyExpenses = Expense::select(
            DB::raw("DATE_FORMAT(issue_date, '%Y-%m') as month"),
            DB::raw('SUM(total_amount) as total_billed'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        // 6. Gastos por Proveedor
        $supplierTotals = SupplierInvoice::with('supplier')
            ->select('supplier_id', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('supplier_id')
            ->orderBy('total_amount', 'desc')
            ->get();

        $recentSupplierInvoices = SupplierInvoice::with('supplier')
            ->orderBy('issue_date', 'desc')
            ->take(10)
            ->get();

        // 7. Estadísticas de Reclamos (por Categoría y por Estado)
        $ticketsByCategory = Ticket::select('category_id', DB::raw('count(*) as count'))
            ->with('category')
            ->groupBy('category_id')
            ->get();

        $ticketsByStatus = Ticket::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // 8. Distribución de Lotes por Estado Físico
        $lotsByStatus = Lot::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(balance) as total_debt'))
            ->groupBy('status')
            ->get();

        return view('admin.reports.index', compact(
            'totalLots',
            'totalOwners',
            'totalDebt',
            'totalSurplus',
            'totalCollectedYear',
            'totalInvoicedYear',
            'totalSupplierExpensesYear',
            'openTicketsCount',
            'debtors',
            'lotsSummary',
            'monthlyCollection',
            'monthlyExpenses',
            'supplierTotals',
            'recentSupplierInvoices',
            'ticketsByCategory',
            'ticketsByStatus',
            'lotsByStatus',
            'selectedYear'
        ));
    }

    /**
     * Export report data to CSV/Excel format.
     */
    public function export(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'debtors');
        $fileName = "reporte_" . $type . "_" . date('Y-m-d_His') . ".csv";

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($type === 'debtors') {
                fputcsv($handle, ['Nro Lote', 'Código', 'Propietario', 'Teléfono', 'Email', 'Saldo Deudor ($)', 'Estado Lote']);
                $records = Lot::where('balance', '>', 0)->with('owner')->orderBy('balance', 'desc')->get();
                foreach ($records as $r) {
                    fputcsv($handle, [
                        $r->number,
                        $r->code,
                        $r->owner?->full_name ?? 'Sin asignar',
                        $r->owner?->phone ?? 'N/A',
                        $r->owner?->email ?? 'N/A',
                        number_format($r->balance, 2, '.', ''),
                        ucfirst(str_replace('_', ' ', $r->status)),
                    ]);
                }
            } elseif ($type === 'lots') {
                fputcsv($handle, ['Nro Lote', 'Código', 'Dirección Interna', 'Propietario', 'Inquilino', 'Saldo Actual ($)', 'Estado']);
                $records = Lot::with(['owner', 'tenant'])->orderBy('number', 'asc')->get();
                foreach ($records as $r) {
                    fputcsv($handle, [
                        $r->number,
                        $r->code,
                        $r->internal_address ?? "Lote {$r->number}",
                        $r->owner?->full_name ?? 'Sin asignar',
                        $r->tenant?->full_name ?? 'N/A',
                        number_format($r->balance, 2, '.', ''),
                        ucfirst(str_replace('_', ' ', $r->status)),
                    ]);
                }
            } elseif ($type === 'payments') {
                fputcsv($handle, ['ID Pago', 'Fecha Pago', 'Nro Lote', 'Propietario', 'Banco', 'Método', 'Nro Operación', 'Monto ($)', 'Estado']);
                $records = Payment::with(['lot', 'owner'])->orderBy('payment_date', 'desc')->get();
                foreach ($records as $r) {
                    fputcsv($handle, [
                        $r->id,
                        $r->payment_date?->format('Y-m-d') ?? 'N/A',
                        $r->lot?->number ?? 'N/A',
                        $r->owner?->full_name ?? 'N/A',
                        $r->bank ?? 'N/A',
                        $r->payment_method ?? 'Transferencia',
                        $r->operation_number ?? 'N/A',
                        number_format($r->amount, 2, '.', ''),
                        ucfirst($r->status),
                    ]);
                }
            } elseif ($type === 'suppliers') {
                fputcsv($handle, ['ID Factura', 'Proveedor', 'CUIT', 'Nro Comprobante', 'Concepto', 'Fecha Emisión', 'Fecha Vencimiento', 'Monto ($)', 'Estado']);
                $records = SupplierInvoice::with('supplier')->orderBy('issue_date', 'desc')->get();
                foreach ($records as $r) {
                    fputcsv($handle, [
                        $r->id,
                        $r->supplier?->name ?? 'N/A',
                        $r->supplier?->tax_id ?? 'N/A',
                        $r->invoice_number ?? 'N/A',
                        $r->concept ?? 'N/A',
                        $r->issue_date?->format('Y-m-d') ?? 'N/A',
                        $r->due_date?->format('Y-m-d') ?? 'N/A',
                        number_format($r->amount, 2, '.', ''),
                        ucfirst($r->status),
                    ]);
                }
            } elseif ($type === 'tickets') {
                fputcsv($handle, ['ID Reclamo', 'Fecha', 'Lote', 'Categoría', 'Título', 'Prioridad', 'Estado', 'Calificación']);
                $records = Ticket::with(['lot', 'category'])->orderBy('created_at', 'desc')->get();
                foreach ($records as $r) {
                    fputcsv($handle, [
                        $r->id,
                        $r->created_at?->format('Y-m-d H:i') ?? 'N/A',
                        $r->lot?->number ?? 'N/A',
                        $r->category?->display_name ?? 'General',
                        $r->title,
                        ucfirst($r->priority),
                        ucfirst(str_replace('_', ' ', $r->status)),
                        $r->rating ?? 'Sin calificar',
                    ]);
                }
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
