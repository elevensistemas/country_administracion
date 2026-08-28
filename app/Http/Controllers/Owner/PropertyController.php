<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryCategory;
use App\Models\LotHistoryEventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['functionalUnits.lot.residents', 'functionalUnits.lot.vehicles', 'functionalUnits.lot.owner']);

        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);

        if (!$activeLot) {
            $activeLot = $lots->first();
            $activeLotId = $activeLot?->id;
            session(['active_lot_id' => $activeLotId]);
        }

        return view('owner.property.index', compact('activeLot', 'user'));
    }

    public function requestChange(Request $request)
    {
        $request->validate([
            'change_details' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);

        if (!$activeLot) {
            return redirect()->back()->with('error', 'Lote no seleccionado.');
        }

        // Fetch administrative ticket category
        $cat = TicketCategory::where('name', 'admin')->first();

        // 1. Create a support ticket for this request
        $ticket = Ticket::create([
            'lot_id' => $activeLot->id,
            'user_id' => $user->id,
            'ticket_category_id' => $cat ? $cat->id : null,
            'title' => "Solicitud de Cambio en Propiedad - Lote {$activeLot->number}",
            'description' => "El propietario ha solicitado cambios en la declaración de su propiedad:\n\n" . $request->input('change_details'),
            'status' => 'new',
            'priority' => 'medium',
        ]);

        // 2. Log event in Lot History
        $evType = LotHistoryEventType::where('name', 'ticket_created')->first();
        $evCat = LotHistoryCategory::where('name', 'admin')->first();

        LotHistoryEvent::create([
            'lot_id' => $activeLot->id,
            'category_id' => $evCat ? $evCat->id : null,
            'event_type_id' => $evType ? $evType->id : null,
            'title' => 'Solicitud de Cambio Registrada',
            'description' => "Se ha abierto el ticket #{$ticket->id} de solicitud de cambio administrativo de datos de la propiedad.",
            'visibility' => 'public',
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Tu solicitud de cambio ha sido enviada con éxito. Se ha abierto el reclamo #' . $ticket->id . ' para el seguimiento.');
    }
}
