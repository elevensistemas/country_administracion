<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FunctionalUnit;
use App\Models\AccountMovement;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    /**
     * Display a listing of current balances.
     */
    public function index(Request $request)
    {
        $query = FunctionalUnit::with(['lot.owner', 'lot.tenant']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('lot', function ($lq) use ($search) {
                      $lq->where('number', 'like', "%{$search}%")
                         ->orWhereHas('owner', function ($oq) use ($search) {
                             $oq->where('name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                         });
                  });
        }

        if ($request->filled('balance_status')) {
            $status = $request->input('balance_status');
            if ($status === 'debt') {
                $query->where('balance', '>', 0);
            } elseif ($status === 'surplus') {
                $query->where('balance', '<', 0);
            } else {
                $query->where('balance', 0);
            }
        }

        $units = $query->paginate(10)->withQueryString();

        return view('admin.accounting.index', compact('units'));
    }

    /**
     * Display detailed account current statement for a functional unit.
     */
    public function showUnit(FunctionalUnit $functionalUnit)
    {
        $functionalUnit->load(['lot.owner', 'lot.tenant']);

        $movements = AccountMovement::where('functional_unit_id', $functionalUnit->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.accounting.show', compact('functionalUnit', 'movements'));
    }

    /**
     * Store an administrative adjustment movement.
     */
    public function storeAdjustment(Request $request, FunctionalUnit $functionalUnit)
    {
        $request->validate([
            'type' => 'required|string|in:debit,credit',
            'amount' => 'required|numeric|gt:0',
            'description' => 'required|string|max:255',
        ]);

        $amount = (float) $request->input('amount');
        $type = $request->input('type');

        DB::transaction(function () use ($functionalUnit, $type, $amount, $request) {
            // Update balance
            if ($type === 'debit') {
                $functionalUnit->balance += $amount;
            } else {
                $functionalUnit->balance -= $amount;
            }
            $functionalUnit->save();

            // Sync Lot balance too
            $lot = $functionalUnit->lot;
            $lot->balance = $functionalUnit->balance;
            $lot->save();

            // Create Movement record
            $movement = AccountMovement::create([
                'functional_unit_id' => $functionalUnit->id,
                'type' => $type,
                'date' => now()->toDateString(),
                'amount' => $amount,
                'balance_after' => $functionalUnit->balance,
                'description' => "Ajuste Contable: " . $request->description,
            ]);

            // Log event in Lot History
            $evType = LotHistoryEventType::where('name', 'note_added')->first(); // generic note
            $evCat = LotHistoryCategory::where('name', 'finance')->first();

            LotHistoryEvent::create([
                'lot_id' => $lot->id,
                'functional_unit_id' => $functionalUnit->id,
                'event_type_id' => $evType ? $evType->id : 1,
                'category_id' => $evCat ? $evCat->id : 1,
                'related_model_type' => AccountMovement::class,
                'related_model_id' => $movement->id,
                'owner_id' => $lot->current_owner_id,
                'tenant_id' => $lot->current_tenant_id,
                'user_id' => auth()->id(),
                'title' => "Ajuste Contable Registrado",
                'description' => "Se registró un movimiento de tipo " . ($type === 'debit' ? 'débito' : 'crédito') . " por $ " . number_format($amount, 2, ',', '.') . " en concepto de ajuste contable. Detalle: {$request->description}.",
                'event_date' => now(),
                'visibility' => 'internal', // internal note
            ]);
        });

        return back()->with('success', 'Ajuste contable registrado correctamente en la cuenta corriente.');
    }
}
