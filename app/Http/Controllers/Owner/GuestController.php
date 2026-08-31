<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\GuestAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
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

        $guests = GuestAuthorization::whereNull('id')->paginate(10);
        if ($activeLot) {
            $guests = GuestAuthorization::where('lot_id', $activeLot->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('owner.guests.index', compact('guests', 'activeLot'));
    }

    public function create()
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

        return view('owner.guests.create', compact('activeLot'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);

        if (!$activeLot) {
            return redirect()->back()->with('error', 'Lote no seleccionado.');
        }

        $request->validate([
            'type' => 'required|string|in:individual,frequent,list',
            'name' => 'required_if:type,individual,frequent|string|max:255|nullable',
            'last_name' => 'required_if:type,individual,frequent|string|max:255|nullable',
            'dni' => 'required_if:type,individual,frequent|string|max:20|nullable',
            'license_plate' => 'nullable|string|max:20',
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_time' => 'nullable',
            'notes' => 'nullable|string',
            'guest_names_list' => 'required_if:type,list|string|nullable'
        ]);

        $data = [
            'lot_id' => $activeLot->id,
            'user_id' => $user->id,
            'type' => $request->type,
            'status' => 'active',
            'qr_code' => 'RANITA-' . strtoupper(Str::random(12))
        ];

        if ($request->type === 'list') {
            $data['name'] = 'Lista de Invitados';
            $data['last_name'] = '';
            $data['notes'] = $request->guest_names_list;
            $data['visit_date'] = $request->visit_date ?: now()->toDateString();
        } else {
            $data['name'] = $request->name;
            $data['last_name'] = $request->last_name;
            $data['dni'] = $request->dni;
            $data['license_plate'] = $request->license_plate;
            $data['visit_date'] = $request->visit_date;
            $data['visit_time'] = $request->visit_time;
            $data['notes'] = $request->notes;
        }

        GuestAuthorization::create($data);

        return redirect()->route('owner.guests.index')->with('success', 'Autorización de ingreso creada exitosamente.');
    }

    public function destroy(GuestAuthorization $guest)
    {
        // Security check: must belong to user's lots
        $user = Auth::user();
        $userLotIds = $user->functionalUnits->pluck('lot_id')->toArray();

        if (!in_array($guest->lot_id, $userLotIds)) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $guest->delete();

        return redirect()->route('owner.guests.index')->with('success', 'Autorización cancelada con éxito.');
    }

    public function showQr(GuestAuthorization $guest)
    {
        $user = Auth::user();
        $userLotIds = $user->functionalUnits->pluck('lot_id')->toArray();

        if (!in_array($guest->lot_id, $userLotIds)) {
            abort(403, 'No tienes permiso para ver esta autorización.');
        }

        return view('owner.guests.qr', compact('guest'));
    }
}
