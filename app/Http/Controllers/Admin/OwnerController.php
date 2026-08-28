<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Lot;
use App\Models\User;
use App\Models\FunctionalUnit;
use App\Models\AccountMovement;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Owner::with('lots')->withCount('lots');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('cuit', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $owners = $query->orderBy('last_name')->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'dni' => 'nullable|string|regex:/^[0-9]{7,9}$/',
            'cuit' => 'nullable|string|regex:/^\d{2}-\d{8}-\d{1}$|^\d{11}$/',
            'email' => 'required|email|unique:owners,email',
            'email_alternate' => 'nullable|email',
            'phone' => 'nullable|string|regex:/^\+?[0-9\s-]{8,18}$/',
            'phone_alternate' => 'nullable|string|regex:/^\+?[0-9\s-]{8,18}$/',
            'address' => 'nullable|string',
            'preferred_channel' => 'required|string|in:email,whatsapp,both,portal',
            'notes' => 'nullable|string',
        ], [
            'dni.regex' => 'El DNI debe contener entre 7 y 9 dígitos numéricos sin puntos.',
            'cuit.regex' => 'El CUIT debe tener el formato XX-XXXXXXXX-X o ser numérico de 11 dígitos.',
            'phone.regex' => 'El formato del teléfono no es válido (Ej: +5491133334444 o 1133334444).',
            'phone_alternate.regex' => 'El formato del teléfono alternativo no es válido.',
            'email.email' => 'El correo electrónico principal no tiene un formato válido.',
            'email_alternate.email' => 'El correo electrónico alternativo no tiene un formato válido.',
        ]);

        Owner::create($request->all());

        return redirect()->route('admin.owners.index')->with('success', 'Propietario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Owner $owner)
    {
        // Load relationships
        $owner->load(['lots', 'functionalUnits.lot']);

        // Users associated with owner's email, phone, or functional units
        $fuIds = $owner->functionalUnits->pluck('id')->toArray();
        $associatedUsersQuery = User::where('email', $owner->email)
            ->orWhereHas('functionalUnits', function ($q) use ($fuIds) {
                $q->whereIn('functional_unit_id', $fuIds);
            });

        if (!empty($owner->phone)) {
            $associatedUsersQuery->orWhere('phone', $owner->phone);
        }

        $associatedUsers = $associatedUsersQuery->get();

        // Get functional units IDs
        $fuIds = $owner->functionalUnits->pluck('id')->toArray();

        // Account movements (cuenta corriente) for owner's functional units
        $movements = AccountMovement::whereIn('functional_unit_id', $fuIds)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'movements_page');

        // Payments reported by owner
        $payments = Payment::where('owner_id', $owner->id)
            ->orderBy('payment_date', 'desc')
            ->paginate(5, ['*'], 'payments_page');

        // Tickets created by the owner's users
        $user_ids = $associatedUsers->pluck('id')->toArray();
        $tickets = Ticket::whereIn('user_id', $user_ids)
            ->orWhereIn('lot_id', $owner->lots->pluck('id')->toArray())
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'tickets_page');

        return view('admin.owners.show', compact('owner', 'associatedUsers', 'movements', 'payments', 'tickets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Owner $owner)
    {
        return view('admin.owners.edit', compact('owner'));
    }

    public function update(Request $request, Owner $owner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'dni' => 'nullable|string|regex:/^[0-9]{7,9}$/',
            'cuit' => 'nullable|string|regex:/^\d{2}-\d{8}-\d{1}$|^\d{11}$/',
            'email' => 'required|email|unique:owners,email,' . $owner->id,
            'email_alternate' => 'nullable|email',
            'phone' => 'nullable|string|regex:/^\+?[0-9\s-]{8,18}$/',
            'phone_alternate' => 'nullable|string|regex:/^\+?[0-9\s-]{8,18}$/',
            'address' => 'nullable|string',
            'preferred_channel' => 'required|string|in:email,whatsapp,both,portal',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string',
        ], [
            'dni.regex' => 'El DNI debe contener entre 7 y 9 dígitos numéricos sin puntos.',
            'cuit.regex' => 'El CUIT debe tener el formato XX-XXXXXXXX-X o ser numérico de 11 dígitos.',
            'phone.regex' => 'El formato del teléfono no es válido (Ej: +5491133334444 o 1133334444).',
            'phone_alternate.regex' => 'El formato del teléfono alternativo no es válido.',
            'email.email' => 'El correo electrónico principal no tiene un formato válido.',
            'email_alternate.email' => 'El correo electrónico alternativo no tiene un formato válido.',
        ]);

        $owner->update($request->all());

        return redirect()->route('admin.owners.index')->with('success', 'Propietario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Owner $owner)
    {
        // Check if owner owns lots
        if ($owner->lots()->exists()) {
            return back()->with('error', 'No se puede eliminar un propietario que posee lotes activos. Reasigna los lotes primero.');
        }

        $owner->delete();
        return redirect()->route('admin.owners.index')->with('success', 'Propietario eliminado correctamente.');
    }
}
