<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reporting dashboard.
     */
    public function index()
    {
        // 1. Debt distribution by lot status
        $debtByLotStatus = Lot::select('status', DB::raw('SUM(balance) as total_debt'))
            ->groupBy('status')
            ->get();

        // 2. Collection statistics (approved payments in last 6 months)
        $monthlyCollection = Payment::where('status', 'approved')
            ->select(DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as month"), DB::raw('SUM(amount) as total_collected'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        // 3. Claims statistics (avg response time simulated or count by categories)
        $ticketsByCategory = Ticket::select('ticket_category_id', DB::raw('count(*) as count'))
            ->with('category')
            ->groupBy('ticket_category_id')
            ->get();

        // 4. Overdue expenses count
        $overdueExpensesAmount = Lot::where('balance', '>', 0)->sum('balance');
        $surplusAmount = Lot::where('balance', '<', 0)->sum('balance');

        return view('admin.reports.index', compact(
            'debtByLotStatus', 'monthlyCollection', 'ticketsByCategory', 
            'overdueExpensesAmount', 'surplusAmount'
        ));
    }
}
