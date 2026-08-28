<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CommonArea;
use App\Models\Reservation;
use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Role;
use App\Mail\ReservationMail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);

        if (!$activeLot) {
            $activeLot = $lots->first();
            $activeLotId = $activeLot?->id;
            session(['active_lot_id' => $activeLotId]);
        }

        // List their reservations on active lot
        $reservations = [];
        if ($activeLot) {
            $reservations = Reservation::where('lot_id', $activeLot->id)
                ->with(['commonArea'])
                ->orderBy('reservation_date', 'desc')
                ->orderBy('start_time', 'desc')
                ->paginate(10);
        }

        // Available common areas for booking
        $commonAreas = CommonArea::where('is_active', true)->orderBy('name')->get();

        return view('owner.reservations.index', compact('reservations', 'commonAreas', 'activeLot'));
    }

    public function create(Request $request, CommonArea $commonArea)
    {
        $user = Auth::user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);

        if (!$activeLot) {
            $activeLot = $lots->first();
            $activeLotId = $activeLot?->id;
            session(['active_lot_id' => $activeLotId]);
        }

        // Date selection (defaults to tomorrow)
        $dateStr = $request->input('date', Carbon::tomorrow()->toDateString());
        $selectedDate = Carbon::parse($dateStr);

        // Check if date day is in maintenance blocked days (e.g. "Monday")
        $dayOfWeek = $selectedDate->format('l'); // e.g. "Monday"
        $isMaintenanceDay = false;
        if ($commonArea->maintenance_blocked_days) {
            $blockedDays = is_array($commonArea->maintenance_blocked_days) 
                ? $commonArea->maintenance_blocked_days 
                : json_decode($commonArea->maintenance_blocked_days, true);
            
            if (is_array($blockedDays) && in_array($dayOfWeek, $blockedDays)) {
                $isMaintenanceDay = true;
            }
        }

        // Fetch already reserved ranges for this area on selected date to display them
        $existingBookings = [];
        if (!$isMaintenanceDay) {
            $existingBookings = Reservation::where('common_area_id', $commonArea->id)
                ->where('reservation_date', $dateStr)
                ->whereIn('status', ['confirmed', 'pending'])
                ->orderBy('start_time')
                ->get();
        }

        return view('owner.reservations.create', compact('commonArea', 'activeLot', 'selectedDate', 'existingBookings', 'isMaintenanceDay'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'common_area_id' => 'required|exists:common_areas,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string', // Format: "HH:MM"
            'end_time' => 'required|string', // Format: "HH:MM"
            'is_exclusive' => 'nullable|boolean',
            'accept_rules' => 'required|accepted',
            'charge_to_expenses' => 'nullable|boolean'
        ]);

        $user = Auth::user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);

        if (!$activeLot) {
            return redirect()->back()->with('error', 'Lote no seleccionado.');
        }

        $commonArea = CommonArea::findOrFail($request->common_area_id);

        $startTime = trim($request->start_time) . ':00';
        $endTime = trim($request->end_time) . ':00';

        if ($startTime >= $endTime) {
            return redirect()->back()->with('error', 'La hora de inicio debe ser anterior a la de fin.')->withInput();
        }

        // Check range bounds matching common area schedules
        $allowedStart = $commonArea->schedule_start;
        $allowedEnd = $commonArea->schedule_end;
        if ($startTime < $allowedStart || $endTime > $allowedEnd) {
            return redirect()->back()->with('error', "El horario seleccionado debe estar dentro del rango permitido del espacio (" . substr($allowedStart, 0, 5) . " hs a " . substr($allowedEnd, 0, 5) . " hs).")->withInput();
        }

        // Check if overlaps with existing bookings
        $collision = Reservation::where('common_area_id', $commonArea->id)
            ->where('reservation_date', $request->reservation_date)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        if ($collision) {
            return redirect()->back()->with('error', 'El horario seleccionado se solapa con una reserva ya registrada.')->withInput();
        }

        $isExclusive = $request->input('is_exclusive') == '1';

        // Exclusivity rules: force status pending, price = 0.00 (admin will set it)
        $price = $isExclusive ? 0.00 : $commonArea->price;
        $status = ($isExclusive || $commonArea->requires_approval) ? 'pending' : 'confirmed';
        $chargeToExpenses = $isExclusive ? false : $request->has('charge_to_expenses');

        // Create reservation
        $res = Reservation::create([
            'common_area_id' => $commonArea->id,
            'lot_id' => $activeLot->id,
            'user_id' => $user->id,
            'reservation_date' => $request->reservation_date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $price,
            'charge_to_expenses' => $chargeToExpenses,
            'status' => $status,
            'is_exclusive' => $isExclusive,
            'notes' => $request->notes
        ]);

        // Integration accounting: if auto-confirmed (no approval required), price > 0 and charged to expenses:
        if ($res->status === 'confirmed' && $res->price > 0 && $res->charge_to_expenses) {
            $fu = $activeLot->functionalUnits()->first();
            if ($fu) {
                // Update balances
                $fu->balance += $res->price;
                $fu->save();

                $activeLot->balance = $fu->balance;
                $activeLot->save();

                // Create Account Movement
                \App\Models\AccountMovement::create([
                    'functional_unit_id' => $fu->id,
                    'type' => 'debit',
                    'date' => now()->toDateString(),
                    'amount' => $res->price,
                    'balance_after' => $fu->balance,
                    'description' => "Reserva Auto-Confirmada: {$commonArea->name} (Fecha: " . $res->reservation_date->format('d/m/Y') . ")",
                    'related_model_type' => get_class($res),
                    'related_model_id' => $res->id
                ]);

                // Create Lot History Event
                $evType = \App\Models\LotHistoryEventType::where('name', 'expense_generated')->first();
                $evCat = \App\Models\LotHistoryCategory::where('name', 'finance')->first();

                \App\Models\LotHistoryEvent::create([
                    'lot_id' => $activeLot->id,
                    'category_id' => $evCat ? $evCat->id : null,
                    'event_type_id' => $evType ? $evType->id : null,
                    'title' => 'Cargo por Reserva de Espacio',
                    'description' => "Débito automático de $" . number_format($res->price, 2) . " por reserva de {$commonArea->name} el día " . $res->reservation_date->format('d/m/Y') . ".",
                    'visibility' => 'public',
                    'created_at' => now(),
                ]);
            }
        }
        // Trigger notifications and email alerts
        try {
            $notifyEmail = SystemSetting::where('key', 'notify_reservation_email')->value('value') ?? '1';
            $notifySystem = SystemSetting::where('key', 'notify_reservation_system')->value('value') ?? '1';
            $notifyOwnerEmail = SystemSetting::where('key', 'notify_reservation_owner_email')->value('value') ?? '1';

            $exclTag = $res->is_exclusive ? ' [CON EXCLUSIVIDAD]' : '';

            // 1. System Notification for Admin
            if ($notifySystem === '1') {
                $admins = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['superadmin', 'admin', 'operator']);
                })->get();

                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title' => 'Nueva Reserva Registrada' . $exclTag,
                        'message' => "Lote {$activeLot->number} reservó {$commonArea->name} para el " . $res->reservation_date->format('d/m/Y') . " (" . substr($res->start_time, 0, 5) . " - " . substr($res->end_time, 0, 5) . " hs)" . ($res->is_exclusive ? ' (Requiere Presupuestar Exclusividad)' : ''),
                        'type' => 'reservation',
                        'link' => route('admin.reservations.index'),
                    ]);
                }
            }

            // 2. Email Notification to Admin
            if ($notifyEmail === '1') {
                $admins = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['superadmin', 'admin', 'operator']);
                })->get();

                foreach ($admins as $admin) {
                    if ($admin->email) {
                        Mail::to($admin->email)->send(new ReservationMail($res, 'admin_new'));
                    }
                }
            }

            // 3. Email Notification to Owner/Resident
            if ($notifyOwnerEmail === '1' && $user->email) {
                Mail::to($user->email)->send(new ReservationMail($res, 'owner_status'));
            }
        } catch (\Exception $e) {
            \Log::warning('Error triggering reservation notifications: ' . $e->getMessage());
        }

        $successMsg = $isExclusive
            ? 'Tu reserva CON EXCLUSIVIDAD ha sido solicitada. Queda en estado pendiente hasta que la administración presupueste y apruebe el costo final.'
            : ($commonArea->requires_approval 
                ? 'Tu reserva ha sido solicitada. Quedará en estado pendiente hasta la aprobación de la administración.'
                : '¡Tu reserva ha sido confirmada y registrada exitosamente!');

        return redirect()->route('owner.reservations.index')->with('success', $successMsg);
    }

    public function cancel(Reservation $reservation)
    {
        $user = Auth::user();
        $userLotIds = $user->functionalUnits->pluck('lot_id')->toArray();

        if (!in_array($reservation->lot_id, $userLotIds)) {
            abort(403, 'No tienes permiso para cancelar esta reserva.');
        }

        // Logic check: only pending or confirmed reservations in the future can be canceled
        if ($reservation->reservation_date->isBefore(now()->toDateString())) {
            return redirect()->back()->with('error', 'No puedes cancelar reservas pasadas.');
        }

        // If it was confirmed and charged to expenses, we should reverse the charge!
        if ($reservation->status === 'confirmed' && $reservation->price > 0 && $reservation->charge_to_expenses) {
            $fu = $reservation->lot->functionalUnits()->first();
            if ($fu) {
                // Refund / Credit
                $fu->balance -= $reservation->price;
                $fu->save();

                $lot = $reservation->lot;
                $lot->balance = $fu->balance;
                $lot->save();

                // Create reverse Account Movement
                \App\Models\AccountMovement::create([
                    'functional_unit_id' => $fu->id,
                    'type' => 'credit',
                    'date' => now()->toDateString(),
                    'amount' => $reservation->price,
                    'balance_after' => $fu->balance,
                    'description' => "Reversión de Reserva Cancelada: {$reservation->commonArea->name} (Fecha: " . $reservation->reservation_date->format('d/m/Y') . ")",
                    'related_model_type' => get_class($reservation),
                    'related_model_id' => $reservation->id
                ]);

                // Log in Lot History
                $evType = \App\Models\LotHistoryEventType::where('name', 'payment_received')->first(); // financial credit
                $evCat = \App\Models\LotHistoryCategory::where('name', 'finance')->first();

                \App\Models\LotHistoryEvent::create([
                    'lot_id' => $lot->id,
                    'category_id' => $evCat ? $evCat->id : null,
                    'event_type_id' => $evType ? $evType->id : null,
                    'title' => 'Crédito por Cancelación de Reserva',
                    'description' => "Se acreditó la reversión de $" . number_format($reservation->price, 2) . " por la reserva cancelada de {$reservation->commonArea->name}.",
                    'visibility' => 'public',
                    'created_at' => now(),
                ]);
            }
        }

        $reservation->status = 'canceled';
        $reservation->save();

        return redirect()->route('owner.reservations.index')->with('success', 'La reserva ha sido cancelada.');
    }
}
