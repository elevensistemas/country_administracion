<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $tickets = Ticket::where('user_id', $user->id)
            ->with(['lot', 'category', 'assignee'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('owner.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $user->load('functionalUnits.lot');

        $lots = $user->functionalUnits->map(function ($unit) {
            return $unit->lot;
        })->unique('id');

        $categories = TicketCategory::all();

        return view('owner.tickets.create', compact('lots', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string|in:low,medium,high',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $user = auth()->user();

        // Check if user owns or lives in that lot
        $associatedLots = $user->functionalUnits->pluck('lot_id')->toArray();
        if (!in_array($request->lot_id, $associatedLots)) {
            abort(403, 'No tienes permiso para reportar incidentes en este lote.');
        }

        DB::transaction(function () use ($request, $user) {
            $ticket = Ticket::create([
                'lot_id' => $request->lot_id,
                'user_id' => $user->id,
                'category_id' => $request->ticket_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => 'open',
            ]);

            // Save attachment if uploaded
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('ticket_attachments', 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            // Write Lot history event
            $lot = \App\Models\Lot::find($request->lot_id);
            $evType = \App\Models\LotHistoryEventType::where('name', 'ticket_created')->first();
            $evCat = \App\Models\LotHistoryCategory::where('name', 'admin')->first();

            \App\Models\LotHistoryEvent::create([
                'lot_id' => $lot->id,
                'functional_unit_id' => $lot->functionalUnits->first()?->id,
                'event_type_id' => $evType ? $evType->id : 1,
                'category_id' => $evCat ? $evCat->id : 1,
                'related_model_type' => Ticket::class,
                'related_model_id' => $ticket->id,
                'owner_id' => $lot->current_owner_id,
                'tenant_id' => $lot->current_tenant_id,
                'user_id' => $user->id,
                'title' => "Reclamo Registrado por Propietario",
                'description' => "Se abrió el ticket de reclamo N° #{$ticket->id}: \"{$ticket->title}\". Categoría: " . $ticket->category->display_name,
                'event_date' => now(),
                'visibility' => 'public',
            ]);

            // Notify all admin and operator staff via bell notification
            $staffUsers = \App\Models\User::whereIn('relationship_type', ['admin', 'superadmin', 'operator', 'accounting'])->get();
            $lotNumber = $lot ? $lot->number : $ticket->lot_id;
            $catName = $ticket->category ? $ticket->category->display_name : 'Reclamo';

            foreach ($staffUsers as $staff) {
                \App\Models\Notification::create([
                    'user_id' => $staff->id,
                    'title' => "Nuevo Reclamo - Lote {$lotNumber}",
                    'message' => "{$user->full_name} ({$catName}): \"" . \Illuminate\Support\Str::limit($ticket->title, 45) . "\"",
                    'type' => 'ticket',
                    'link' => route('admin.tickets.show', $ticket->id),
                ]);
            }
        });

        return redirect()->route('owner.tickets.index')->with('success', 'Tu ticket ha sido creado correctamente y un operador se pondrá en contacto a la brevedad.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $user = auth()->user();
        if ($ticket->user_id !== $user->id) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }

        $ticket->load(['lot', 'category', 'assignee', 'messages.user', 'attachments']);

        return view('owner.tickets.show', compact('ticket'));
    }

    /**
     * Post message response to the ticket.
     */
    public function storeMessage(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        if ($ticket->user_id !== $user->id) {
            abort(403, 'No tienes permiso para responder a este ticket.');
        }

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        DB::transaction(function () use ($request, $ticket, $user) {
            $msg = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $request->message,
                'is_admin' => false,
            ]);

            // Save attachment if exists
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('ticket_attachments', 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'ticket_message_id' => $msg->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            $ticket->touch();

            // Notify staff members of the new response
            $staffToNotify = $ticket->assigned_to 
                ? \App\Models\User::where('id', $ticket->assigned_to)->get() 
                : \App\Models\User::whereIn('relationship_type', ['admin', 'superadmin', 'operator'])->get();

            $lot = $ticket->lot;
            $lotNumber = $lot ? $lot->number : $ticket->lot_id;

            foreach ($staffToNotify as $staff) {
                \App\Models\Notification::create([
                    'user_id' => $staff->id,
                    'title' => "Mensaje en Reclamo #{$ticket->id} (Lote {$lotNumber})",
                    'message' => "{$user->full_name}: \"" . \Illuminate\Support\Str::limit($request->message, 45) . "\"",
                    'type' => 'ticket',
                    'link' => route('admin.tickets.show', $ticket->id),
                ]);
            }
        });

        return back()->with('success', 'Mensaje enviado correctamente.');
    }
}
