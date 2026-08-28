<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * List published expenses for the owner's functional units.
     */
    public function index()
    {
        $user = auth()->user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);
        
        if (!$activeLot) {
            $activeLot = $lots->first();
            $activeLotId = $activeLot?->id;
            session(['active_lot_id' => $activeLotId]);
        }

        $unitIds = $activeLot ? $activeLot->functionalUnits->pluck('id')->toArray() : [];

        $expenses = Expense::whereIn('functional_unit_id', $unitIds)
            ->where('status', '!=', 'draft') // Drafts are hidden from owners
            ->with(['billingPeriod', 'functionalUnit.lot'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('owner.expenses.index', compact('expenses', 'activeLot'));
    }

    /**
     * Download or view invoice PDF.
     */
    public function downloadPdf(Expense $expense)
    {
        // Security check: must belong to user
        $user = auth()->user();
        $associatedUnits = $user->functionalUnits->pluck('id')->toArray();

        if (!in_array($expense->functional_unit_id, $associatedUnits)) {
            abort(403, 'No tienes permiso para ver esta liquidación.');
        }

        $expense->load(['billingPeriod', 'functionalUnit.lot.owner', 'items']);
        
        // Renders simulated print layout
        return view('admin.expenses.pdf', compact('expense'));
    }
}
