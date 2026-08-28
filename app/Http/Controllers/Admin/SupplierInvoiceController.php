<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SupplierInvoiceController extends Controller
{
    /**
     * Display the weekly payment planner (index view).
     */
    public function index(Request $request)
    {
        $query = SupplierInvoice::with('supplier');

        // Apply filters
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // By default, exclude voided invoices from calculation or list unless selected
            $query->where('status', '!=', 'voided');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('concept', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('business_name', 'like', "%{$search}%")
                        ->orWhere('cuit', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('due_date', 'asc')->get();

        // Group invoices weekly by due_date
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();
        $endOfNextWeek = $endOfWeek->copy()->addWeek();

        $overdueInvoices = collect();
        $thisWeekInvoices = collect();
        $nextWeekInvoices = collect();
        $futureInvoices = collect();
        $paidInvoices = collect();

        foreach ($invoices as $invoice) {
            if ($invoice->status === 'paid') {
                $paidInvoices->push($invoice);
                continue;
            }

            $dueDate = Carbon::parse($invoice->due_date);

            if ($dueDate->lt($today)) {
                $overdueInvoices->push($invoice);
            } elseif ($dueDate->between($startOfWeek, $endOfWeek)) {
                $thisWeekInvoices->push($invoice);
            } elseif ($dueDate->between($endOfWeek->copy()->addSecond(), $endOfNextWeek)) {
                $nextWeekInvoices->push($invoice);
            } else {
                $futureInvoices->push($invoice);
            }
        }

        // Totals for planning
        $totalOverdue = $overdueInvoices->sum('amount');
        $totalThisWeek = $thisWeekInvoices->sum('amount');
        $totalNextWeek = $nextWeekInvoices->sum('amount');
        $totalFuture = $futureInvoices->sum('amount');

        $suppliers = Supplier::orderBy('business_name')->get();

        return view('admin.supplier-invoices.index', compact(
            'overdueInvoices', 'thisWeekInvoices', 'nextWeekInvoices', 'futureInvoices', 'paidInvoices',
            'totalOverdue', 'totalThisWeek', 'totalNextWeek', 'totalFuture', 'suppliers'
        ));
    }

    /**
     * Show form to create new invoice.
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('business_name')->get();
        return view('admin.supplier-invoices.create', compact('suppliers'));
    }

    /**
     * Store new invoice.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:100',
            'concept' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'status' => 'required|string|in:pending,scheduled,paid,voided',
            'notes' => 'nullable|string',
        ], [
            'supplier_id.required' => 'Debes seleccionar un proveedor.',
            'invoice_number.required' => 'El número de factura es obligatorio.',
            'concept.required' => 'El concepto es obligatorio.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un valor numérico.',
            'due_date.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la de emisión.',
            'file.mimes' => 'El archivo adjunto debe ser de tipo PDF, JPG, JPEG o PNG.',
            'file.max' => 'El archivo no debe pesar más de 5MB.',
        ]);

        $data = $request->except('file');

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('supplier_invoices', 'public');
            $data['file_path'] = $path;
        }

        SupplierInvoice::create($data);

        return redirect()->route('admin.supplier-invoices.index')->with('success', 'Factura cargada correctamente.');
    }

    /**
     * Show invoice details and bank deposit info.
     */
    public function show(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load('supplier');
        return view('admin.supplier-invoices.show', compact('supplierInvoice'));
    }

    /**
     * Show edit form for invoice.
     */
    public function edit(SupplierInvoice $supplierInvoice)
    {
        $suppliers = Supplier::orderBy('business_name')->get();
        return view('admin.supplier-invoices.edit', compact('supplierInvoice', 'suppliers'));
    }

    /**
     * Update invoice details.
     */
    public function update(Request $request, SupplierInvoice $supplierInvoice)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|max:100',
            'concept' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'status' => 'required|string|in:pending,scheduled,paid,voided',
            'notes' => 'nullable|string',
        ], [
            'supplier_id.required' => 'Debes seleccionar un proveedor.',
            'invoice_number.required' => 'El número de factura es obligatorio.',
            'concept.required' => 'El concepto es obligatorio.',
            'amount.required' => 'El monto es obligatorio.',
            'due_date.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la de emisión.',
            'file.mimes' => 'El archivo adjunto debe ser de tipo PDF, JPG, JPEG o PNG.',
        ]);

        $data = $request->except('file');

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($supplierInvoice->file_path) {
                Storage::disk('public')->delete($supplierInvoice->file_path);
            }
            $path = $request->file('file')->store('supplier_invoices', 'public');
            $data['file_path'] = $path;
        }

        $supplierInvoice->update($data);

        return redirect()->route('admin.supplier-invoices.index')->with('success', 'Factura actualizada correctamente.');
    }

    /**
     * Remove invoice.
     */
    public function destroy(SupplierInvoice $supplierInvoice)
    {
        if ($supplierInvoice->file_path) {
            Storage::disk('public')->delete($supplierInvoice->file_path);
        }

        $supplierInvoice->delete();

        return redirect()->route('admin.supplier-invoices.index')->with('success', 'Factura eliminada correctamente.');
    }

    /**
     * Printable view of weekly invoices.
     */
    public function print(Request $request)
    {
        $query = SupplierInvoice::with('supplier')->where('status', '!=', 'voided');

        // Allow printing specific statuses or filters
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $invoices = $query->orderBy('due_date', 'asc')->get();

        // Classify
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();
        $endOfNextWeek = $endOfWeek->copy()->addWeek();

        $overdueInvoices = collect();
        $thisWeekInvoices = collect();
        $nextWeekInvoices = collect();
        $futureInvoices = collect();
        $paidInvoices = collect();

        foreach ($invoices as $invoice) {
            if ($invoice->status === 'paid') {
                $paidInvoices->push($invoice);
                continue;
            }

            $dueDate = Carbon::parse($invoice->due_date);

            if ($dueDate->lt($today)) {
                $overdueInvoices->push($invoice);
            } elseif ($dueDate->between($startOfWeek, $endOfWeek)) {
                $thisWeekInvoices->push($invoice);
            } elseif ($dueDate->between($endOfWeek->copy()->addSecond(), $endOfNextWeek)) {
                $nextWeekInvoices->push($invoice);
            } else {
                $futureInvoices->push($invoice);
            }
        }

        return view('admin.supplier-invoices.print', compact(
            'overdueInvoices', 'thisWeekInvoices', 'nextWeekInvoices', 'futureInvoices', 'paidInvoices', 'today'
        ));
    }
}
