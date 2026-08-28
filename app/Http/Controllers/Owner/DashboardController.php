<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\LotHistoryEvent;
use App\Models\News;
use App\Models\Lot;
use App\Models\Ticket;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display owner portal homepage.
     */
    public function index()
    {
        $user = auth()->user();
        $user->load(['functionalUnits.lot.owner']);

        $lots = $user->functionalUnits->map(function ($unit) {
            return $unit->lot;
        })->unique('id');

        // Context Lot selector
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);

        if (!$activeLot) {
            $activeLot = $lots->first();
            $activeLotId = $activeLot?->id;
            session(['active_lot_id' => $activeLotId]);
        }

        // Active Lot Balance
        $activeLotBalance = $activeLot ? $activeLot->balance : 0.00;

        // Next due date from settings
        $dueDaySetting = SystemSetting::where('key', 'due_day')->first();
        $dueDay = $dueDaySetting ? intval($dueDaySetting->value) : 10;
        $nextDue = now()->day($dueDay);
        if (now()->day > $dueDay) {
            $nextDue = $nextDue->addMonth();
        }

        // Recent news / announcements
        $announcements = News::where('is_published', true)
            ->orderBy('publish_date', 'desc')
            ->take(3)
            ->get();

        // Recent public events on their active lot
        $recentHistory = [];
        if ($activeLot) {
            $recentHistory = LotHistoryEvent::where('lot_id', $activeLot->id)
                ?->where('visibility', 'public')
                ->orderBy('event_date', 'desc')
                ->take(3)
                ->get() ?: [];
        }

        // Active tickets count
        $activeTicketsCount = Ticket::where('user_id', $user->id)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        // Upcoming reservation on active lot
        $upcomingReservation = null;
        if ($activeLot) {
            $upcomingReservation = Reservation::where('lot_id', $activeLot->id)
                ->where('reservation_date', '>=', now()->toDateString())
                ->where('status', 'confirmed')
                ->orderBy('reservation_date')
                ->orderBy('start_time')
                ->first();
        }

        // Latest reported payment
        $latestPayment = null;
        if ($activeLot) {
            $latestPayment = Payment::where('functional_unit_id', $activeLot->functionalUnits()->first()?->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return view('owner.dashboard', compact(
            'user', 'lots', 'activeLot', 'activeLotBalance', 'nextDue', 
            'announcements', 'recentHistory', 'activeTicketsCount', 
            'upcomingReservation', 'latestPayment'
        ));
    }

    /**
     * Display public history timeline of their lot.
     */
    public function lotHistory(Request $request)
    {
        $user = auth()->user();
        $lots = $user->functionalUnits->map(function ($unit) {
            return $unit->lot;
        })->unique('id');

        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);
        if (!$activeLot) {
            $activeLot = $lots->first();
            $activeLotId = $activeLot?->id;
            session(['active_lot_id' => $activeLotId]);
        }

        $events = [];
        if ($activeLot) {
            $events = LotHistoryEvent::where('lot_id', $activeLot->id)
                ->where('visibility', 'public')
                ->with(['eventType', 'category', 'user'])
                ->orderBy('event_date', 'desc')
                ->paginate(15);
        }

        return view('owner.history', compact('lots', 'activeLot', 'events'));
    }
}
