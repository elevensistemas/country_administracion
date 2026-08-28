<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FunctionalUnit;
use App\Models\Lot;
use App\Models\Owner;
use Illuminate\Http\Request;

class FunctionalUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FunctionalUnit::with(['lot', 'owners']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('lot', function ($lq) use ($search) {
                      $lq->where('number', 'like', "%{$search}%");
                  });
        }

        $units = $query->paginate(10)->withQueryString();

        return view('admin.functional-units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lots = Lot::orderBy('number')->get();
        $owners = Owner::orderBy('last_name')->get();
        return view('admin.functional-units.create', compact('lots', 'owners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'code' => 'required|string|unique:functional_units,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'balance' => 'required|numeric',
            'owner_ids' => 'nullable|array',
            'owner_ids.*' => 'exists:owners,id',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $unit = FunctionalUnit::create([
                'lot_id' => $request->lot_id,
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'balance' => $request->balance,
            ]);

            if ($request->filled('owner_ids')) {
                foreach ($request->owner_ids as $ownerId) {
                    $unit->owners()->attach($ownerId, ['share_percentage' => 100]);
                }
            }
        });

        return redirect()->route('admin.functional-units.index')->with('success', 'Unidad funcional creada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FunctionalUnit $functionalUnit)
    {
        $lots = Lot::orderBy('number')->get();
        $owners = Owner::orderBy('last_name')->get();
        $associatedOwners = $functionalUnit->owners->pluck('id')->toArray();
        return view('admin.functional-units.edit', compact('functionalUnit', 'lots', 'owners', 'associatedOwners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FunctionalUnit $functionalUnit)
    {
        $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'code' => 'required|string|unique:functional_units,code,' . $functionalUnit->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'balance' => 'required|numeric',
            'owner_ids' => 'nullable|array',
            'owner_ids.*' => 'exists:owners,id',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $functionalUnit) {
            $functionalUnit->update([
                'lot_id' => $request->lot_id,
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'balance' => $request->balance,
            ]);

            // Sync owners
            $syncData = [];
            if ($request->filled('owner_ids')) {
                foreach ($request->owner_ids as $ownerId) {
                    $syncData[$ownerId] = ['share_percentage' => 100];
                }
            }
            $functionalUnit->owners()->sync($syncData);
        });

        return redirect()->route('admin.functional-units.index')->with('success', 'Unidad funcional actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FunctionalUnit $functionalUnit)
    {
        $functionalUnit->delete();
        return redirect()->route('admin.functional-units.index')->with('success', 'Unidad funcional eliminada correctamente.');
    }
}
