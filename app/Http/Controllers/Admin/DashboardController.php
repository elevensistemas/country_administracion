<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Owner;
use App\Models\Lot;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Communication;
use App\Models\CommunicationDelivery;
use App\Models\AccountMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // 1. Tickets (Reclamos) counts
        $ticketsNew = Ticket::where('status', 'new')->count();
        $ticketsInProgress = Ticket::whereIn('status', ['assigned', 'in_progress', 'waiting_response'])->count();
        $ticketsResolved = Ticket::where('status', 'resolved')->count();
        $ticketsClosed = Ticket::where('status', 'closed')->count();

        // 2. Payments (Pagos) counts
        $paymentsPending = Payment::where('status', 'pending')->count();
        $paymentsApproved = Payment::where('status', 'approved')->count();
        $paymentsRejected = Payment::where('status', 'rejected')->count();

        // 3. Financial aggregates
        $totalDebt = Lot::where('balance', '>', 0)->sum('balance');
        $totalSurplus = abs(Lot::where('balance', '<', 0)->sum('balance'));

        // 4. User Adoption metrics
        $totalUsers = User::whereNotIn('relationship_type', ['superadmin', 'admin', 'operator', 'accounting'])->count();
        $activeUsers = User::whereNotIn('relationship_type', ['superadmin', 'admin', 'operator', 'accounting'])
            ->where('status', 'active')
            ->whereNotNull('last_login_at')
            ->count();
        $neverLoggedIn = User::whereNotIn('relationship_type', ['superadmin', 'admin', 'operator', 'accounting'])
            ->whereNull('last_login_at')
            ->count();
        $pendingInvite = User::where('status', 'pending_invite')->count();
        
        $adoptionRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;

        // 5. Communications stats
        $commsSent = Communication::count();
        $emailsFailed = CommunicationDelivery::where('channel', 'email')->where('status', 'failed')->count();
        $whatsappFailed = CommunicationDelivery::where('channel', 'whatsapp')->where('status', 'failed')->count();

        // 6. Top lots with claims
        $topLotsWithClaims = Lot::withCount('tickets')
            ->orderBy('tickets_count', 'desc')
            ->take(5)
            ->get();

        // 7. Recent activity (movements/actions)
        $recentPayments = Payment::with(['owner', 'lot'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentTickets = Ticket::with(['lot', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 8. Chart Data
        // Claims by category
        $claimsByCategory = DB::table('tickets')
            ->join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->select('ticket_categories.display_name as category', DB::raw('count(tickets.id) as total'))
            ->groupBy('ticket_categories.display_name')
            ->get();

        // Claims by status
        $claimsByStatus = DB::table('tickets')
            ->select('status', DB::raw('count(id) as total'))
            ->groupBy('status')
            ->get();

        // Operation channel counts
        $channelUsage = DB::table('tickets')
            ->select('source_channel as channel', DB::raw('count(id) as total'))
            ->groupBy('source_channel')
            ->get();

        return view('admin.dashboard', compact(
            'ticketsNew', 'ticketsInProgress', 'ticketsResolved', 'ticketsClosed',
            'paymentsPending', 'paymentsApproved', 'paymentsRejected',
            'totalDebt', 'totalSurplus',
            'totalUsers', 'activeUsers', 'neverLoggedIn', 'pendingInvite', 'adoptionRate',
            'commsSent', 'emailsFailed', 'whatsappFailed',
            'topLotsWithClaims', 'recentPayments', 'recentTickets',
            'claimsByCategory', 'claimsByStatus', 'channelUsage'
        ));
    }
}
