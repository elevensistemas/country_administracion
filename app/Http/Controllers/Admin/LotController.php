<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Owner;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lot::with(['owner', 'tenant', 'functionalUnits']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $lots = $query->orderBy(DB::raw('CAST(number AS UNSIGNED)'), 'asc')->paginate(10)->withQueryString();

        return view('admin.lots.index', compact('lots'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $owners = Owner::orderBy('last_name')->get();
        $tenants = Tenant::orderBy('last_name')->get();
        return view('admin.lots.create', compact('owners', 'tenants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|unique:lots,number',
            'code' => 'required|string|unique:lots,code',
            'name' => 'nullable|string',
            'internal_address' => 'nullable|string',
            'status' => 'required|string|in:active,under_construction,vacant',
            'current_owner_id' => 'nullable|exists:owners,id',
            'current_tenant_id' => 'nullable|exists:tenants,id',
            'balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        Lot::create($request->all());

        return redirect()->route('admin.lots.index')->with('success', 'Lote creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lot $lot)
    {
        return redirect()->route('admin.lots.history', $lot);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lot $lot)
    {
        $owners = Owner::orderBy('last_name')->get();
        $tenants = Tenant::orderBy('last_name')->get();
        return view('admin.lots.edit', compact('lot', 'owners', 'tenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lot $lot)
    {
        $request->validate([
            'number' => 'required|string|unique:lots,number,' . $lot->id,
            'code' => 'required|string|unique:lots,code,' . $lot->id,
            'name' => 'nullable|string',
            'internal_address' => 'nullable|string',
            'status' => 'required|string|in:active,under_construction,vacant',
            'current_owner_id' => 'nullable|exists:owners,id',
            'current_tenant_id' => 'nullable|exists:tenants,id',
            'balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $oldOwnerId = $lot->current_owner_id;
        $oldTenantId = $lot->current_tenant_id;

        DB::transaction(function () use ($request, $lot, $oldOwnerId, $oldTenantId) {
            $lot->update($request->all());

            // Handle Ownership History trigger manually if changed (or via event, let's log it)
            if ($oldOwnerId != $request->current_owner_id) {
                // Terminate previous owner date
                if ($oldOwnerId) {
                    \App\Models\OwnershipHistory::where('lot_id', $lot->id)
                        ->where('owner_id', $oldOwnerId)
                        ->whereNull('end_date')
                        ->update(['end_date' => now()->toDateString()]);
                }

                // Create new ownership history entry
                if ($request->current_owner_id) {
                    \App\Models\OwnershipHistory::create([
                        'lot_id' => $lot->id,
                        'owner_id' => $request->current_owner_id,
                        'start_date' => now()->toDateString(),
                        'reason' => 'Modificación administrativa',
                        'user_id' => auth()->id(),
                    ]);

                    // Fire history event
                    \App\Models\LotHistoryEvent::create([
                        'lot_id' => $lot->id,
                        'event_type_id' => \App\Models\LotHistoryEventType::where('name', 'owner_changed')->first()?->id ?? 1,
                        'category_id' => \App\Models\LotHistoryCategory::where('name', 'admin')->first()?->id ?? 1,
                        'owner_id' => $request->current_owner_id,
                        'user_id' => auth()->id(),
                        'title' => 'Cambio de Propietario',
                        'description' => 'Se actualizó el propietario titular del lote administrativamente.',
                        'event_date' => now(),
                        'visibility' => 'public',
                    ]);
                }
            }

            // Handle Tenant History trigger
            if ($oldTenantId != $request->current_tenant_id) {
                if ($oldTenantId) {
                    \App\Models\TenancyHistory::where('lot_id', $lot->id)
                        ->where('tenant_id', $oldTenantId)
                        ->whereNull('end_date')
                        ->update(['end_date' => now()->toDateString()]);
                }

                if ($request->current_tenant_id) {
                    \App\Models\TenancyHistory::create([
                        'lot_id' => $lot->id,
                        'tenant_id' => $request->current_tenant_id,
                        'start_date' => now()->toDateString(),
                        'owner_id' => $request->current_owner_id ?? 1,
                    ]);

                    \App\Models\LotHistoryEvent::create([
                        'lot_id' => $lot->id,
                        'event_type_id' => \App\Models\LotHistoryEventType::where('name', 'tenant_changed')->first()?->id ?? 1,
                        'category_id' => \App\Models\LotHistoryCategory::where('name', 'admin')->first()?->id ?? 1,
                        'owner_id' => $request->current_owner_id,
                        'tenant_id' => $request->current_tenant_id,
                        'user_id' => auth()->id(),
                        'title' => 'Cambio de Inquilino',
                        'description' => 'Se registró un nuevo inquilino ocupante en el lote.',
                        'event_date' => now(),
                        'visibility' => 'public',
                    ]);
                }
            }
        });

        return redirect()->route('admin.lots.index')->with('success', 'Lote actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lot $lot)
    {
        $lot->delete();
        return redirect()->route('admin.lots.index')->with('success', 'Lote eliminado correctamente.');
    }
}
