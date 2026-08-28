<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AccountMovement;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    /**
     * Display current account statement ledger.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $user->load('functionalUnits.lot');

        $unitIds = $user->functionalUnits->pluck('id')->toArray();

        $query = AccountMovement::whereIn('functional_unit_id', $unitIds)
            ->with(['functionalUnit.lot']);

        if ($request->filled('functional_unit_id')) {
            $query->where('functional_unit_id', $request->input('functional_unit_id'));
        }

        $movements = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('owner.accounting.index', compact('user', 'movements'));
    }
}
