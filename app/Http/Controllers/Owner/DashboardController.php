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
        $announcementsQuery = News::where(function ($q) {
                $q->where('is_published', true)
                  ->orWhere('status', 'published');
            })
            ->where('visibility', '!=', 'internal');

        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            $userLotIds = $lots->pluck('id')->filter()->unique()->toArray();
            $ownerLotIds = Lot::whereHas('owner', function ($oq) use ($user) {
                $oq->where('email', $user->email);
            })->pluck('id')->toArray();
            $myLotIds = array_unique(array_merge($userLotIds, $ownerLotIds));

            $announcementsQuery->where(function ($targetQuery) use ($myLotIds) {
                $targetQuery->where('recipients_type', 'all')
                    ->orWhere(function ($sq) use ($myLotIds) {
                        $sq->where('recipients_type', 'specific_lots')
                           ->whereHas('lots', function ($lq) use ($myLotIds) {
                               $lq->whereIn('lots.id', $myLotIds);
                           });
                    });
            });
        }

        $announcements = $announcementsQuery
            ->orderByRaw('COALESCE(published_at, publish_date, created_at) DESC')
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
