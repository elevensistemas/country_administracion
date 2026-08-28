<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('cuit', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $suppliers = $query->orderBy('business_name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show form to create a new supplier.
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store new supplier in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'cuit' => 'required|string|regex:/^\d{2}-\d{8}-\d{1}$|^\d{11}$/|unique:suppliers,cuit',
            'category' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:100',
            'cbu_alias' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ], [
            'cuit.required' => 'El CUIT es obligatorio.',
            'cuit.regex' => 'El CUIT debe tener el formato XX-XXXXXXXX-X o ser numérico de 11 dígitos.',
            'cuit.unique' => 'Ya existe un proveedor registrado con este CUIT.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
        ]);

        Supplier::create($request->all());

        return redirect()->route('admin.suppliers.index')->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Display the specified supplier details and history.
     */
    public function show(Supplier $supplier)
    {
        $invoices = $supplier->invoices()
            ->orderBy('due_date', 'desc')
            ->paginate(10);

        $totalPaid = $supplier->invoices()->where('status', 'paid')->sum('amount');
        $totalPending = $supplier->invoices()->whereIn('status', ['pending', 'scheduled'])->sum('amount');

        return view('admin.suppliers.show', compact('supplier', 'invoices', 'totalPaid', 'totalPending'));
    }

    /**
     * Show edit form for specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update specified supplier details.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'cuit' => 'required|string|regex:/^\d{2}-\d{8}-\d{1}$|^\d{11}$/|unique:suppliers,cuit,' . $supplier->id,
            'category' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:100',
            'cbu_alias' => 'nullable|string|max:50',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string',
        ], [
            'cuit.required' => 'El CUIT es obligatorio.',
            'cuit.regex' => 'El CUIT debe tener el formato XX-XXXXXXXX-X o ser numérico de 11 dígitos.',
            'cuit.unique' => 'Ya existe otro proveedor registrado con este CUIT.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
        ]);

        $supplier->update($request->all());

        return redirect()->route('admin.suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Remove specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Check if supplier has invoices
        if ($supplier->invoices()->exists()) {
            return back()->with('error', 'No se puede eliminar un proveedor que posee facturas registradas. Anula o elimina sus facturas primero.');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Proveedor eliminado correctamente.');
    }
}
