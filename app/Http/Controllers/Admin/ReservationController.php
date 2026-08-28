<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\AccountMovement;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryCategory;
use App\Models\LotHistoryEventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['commonArea', 'lot.owner', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('common_area_id')) {
            $query->where('common_area_id', $request->input('common_area_id'));
        }

        $reservations = $query->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        $commonAreas = \App\Models\CommonArea::orderBy('name')->get();

        return view('admin.reservations.index', compact('reservations', 'commonAreas'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|string|in:confirmed,rejected,canceled,completed',
            'notes' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0'
        ]);

        $newStatus = $request->input('status');
        $oldStatus = $reservation->status;

        DB::transaction(function () use ($reservation, $newStatus, $oldStatus, $request) {
            $reservation->status = $newStatus;
            if ($request->filled('notes')) {
                $reservation->notes = $request->input('notes');
            }
            if ($request->filled('price')) {
                $reservation->price = $request->input('price');
            }
            $reservation->save();

            // Financial integration: if confirmed and not previously confirmed
            if ($newStatus === 'confirmed' && $oldStatus !== 'confirmed' && $reservation->price > 0 && $reservation->charge_to_expenses) {
                $fu = $reservation->lot->functionalUnits()->first();
                if ($fu) {
                    // 1. Update balances
                    $fu->balance += $reservation->price;
                    $fu->save();

                    $lot = $reservation->lot;
                    $lot->balance = $fu->balance;
                    $lot->save();

                    // 2. Create Account Movement
                    AccountMovement::create([
                        'functional_unit_id' => $fu->id,
                        'type' => 'debit',
                        'date' => now()->toDateString(),
                        'amount' => $reservation->price,
                        'balance_after' => $fu->balance,
                        'description' => "Reserva Confirmada: {$reservation->commonArea->name} (Fecha: " . $reservation->reservation_date->format('d/m/Y') . ")",
                        'related_model_type' => get_class($reservation),
                        'related_model_id' => $reservation->id
                    ]);

                    // 3. Log History Event in Lot History
                    $evType = LotHistoryEventType::where('name', 'expense_generated')->first(); // financial debit
                    $evCat = LotHistoryCategory::where('name', 'finance')->first();

                    LotHistoryEvent::create([
                        'lot_id' => $lot->id,
                        'category_id' => $evCat ? $evCat->id : null,
                        'event_type_id' => $evType ? $evType->id : null,
                        'title' => 'Cargo por Reserva de Espacio',
                        'description' => "Se imputó un débito de $" . number_format($reservation->price, 2) . " por la reserva confirmada de: {$reservation->commonArea->name} el día " . $reservation->reservation_date->format('d/m/Y') . ".",
                        'visibility' => 'public',
                        'event_date' => now(),
                        'created_at' => now(),
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'El estado de la reserva ha sido actualizado.');
    }
}
