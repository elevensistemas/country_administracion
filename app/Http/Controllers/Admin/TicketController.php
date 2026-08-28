<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketInternalNote;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Lot;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['lot', 'user', 'category', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->whereNotIn('status', ['resolved', 'closed']); // Default to active ones
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('lot', function ($lq) use ($search) {
                      $lq->where('number', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        $categories = TicketCategory::all();

        return view('admin.tickets.index', compact('tickets', 'categories'));
    }

    /**
     * Display details of a ticket.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['lot.owner', 'user', 'category', 'assignee', 'messages.user', 'internalNotes.user', 'attachments']);

        $operators = User::whereIn('relationship_type', ['admin', 'superadmin', 'operator', 'accounting'])->get();

        return view('admin.tickets.show', compact('ticket', 'operators'));
    }

    /**
     * Update ticket assignment or status.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|string|in:open,in_progress,resolved,closed',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $oldStatus = $ticket->status;
        $oldAssignee = $ticket->assignee_id;

        DB::transaction(function () use ($request, $ticket, $oldStatus, $oldAssignee) {
            $ticket->update([
                'status' => $request->status,
                'assignee_id' => $request->assignee_id,
            ]);

            // If status changed to resolved or closed, log it in Lot History
            if ($oldStatus !== $request->status && ($request->status === 'resolved' || $request->status === 'closed')) {
                $lot = $ticket->lot;
                $evType = \App\Models\LotHistoryEventType::where('name', 'ticket_closed')->first();
                $evCat = \App\Models\LotHistoryCategory::where('name', 'admin')->first();

                \App\Models\LotHistoryEvent::create([
                    'lot_id' => $lot->id,
                    'event_type_id' => $evType ? $evType->id : 1,
                    'category_id' => $evCat ? $evCat->id : 1,
                    'related_model_type' => Ticket::class,
                    'related_model_id' => $ticket->id,
                    'owner_id' => $lot->current_owner_id,
                    'tenant_id' => $lot->current_tenant_id,
                    'user_id' => auth()->id(),
                    'title' => "Reclamo Resuelto / Cerrado",
                    'description' => "Se cerró el ticket de reclamo N° #{$ticket->id}: \"{$ticket->title}\". Estado final: {$request->status}.",
                    'event_date' => now(),
                    'visibility' => 'public',
                ]);
            }
        });

        return back()->with('success', 'Ticket actualizado correctamente.');
    }

    /**
     * Post reply to the user.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        DB::transaction(function () use ($request, $ticket) {
            $msg = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'message' => $request->message,
                'is_internal' => false,
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
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                ]);
            }

            // Update ticket timestamp
            $ticket->touch();
        });

        return back()->with('success', 'Respuesta enviada al propietario.');
    }

    /**
     * Store internal note visible only to operators.
     */
    public function storeInternalNote(Request $request, Ticket $ticket)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        TicketInternalNote::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'note' => $request->note,
        ]);

        $ticket->touch();

        return back()->with('success', 'Nota interna registrada.');
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $categories = TicketCategory::orderBy('display_name')->get();
        $lots = Lot::with('owner')->orderBy('number')->get();
        
        // Fetch all resident users with their associated lots
        $users = User::whereIn('relationship_type', ['owner', 'tenant'])
            ->with('functionalUnits')
            ->orderBy('name')
            ->get();

        return view('admin.tickets.create', compact('categories', 'lots', 'users'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:ticket_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'source_channel' => 'required|string|in:phone,email,in_person,web',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $ticket = DB::transaction(function () use ($request) {
            $lot = Lot::findOrFail($request->lot_id);

            $ticket = Ticket::create([
                'lot_id' => $request->lot_id,
                'user_id' => $request->user_id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'source_channel' => $request->source_channel,
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
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                ]);
            }

            // Log event in Lot History
            $evType = LotHistoryEventType::where('name', 'ticket_created')->first();
            $evCat = LotHistoryCategory::where('name', 'admin')->first();

            LotHistoryEvent::create([
                'lot_id' => $lot->id,
                'functional_unit_id' => $lot->functionalUnits->first()?->id,
                'event_type_id' => $evType ? $evType->id : 1,
                'category_id' => $evCat ? $evCat->id : 1,
                'related_model_type' => Ticket::class,
                'related_model_id' => $ticket->id,
                'owner_id' => $lot->current_owner_id,
                'tenant_id' => $lot->current_tenant_id,
                'user_id' => auth()->id(),
                'title' => "Reclamo Registrado por Administración",
                'description' => "Se registró un nuevo reclamo: \"{$ticket->title}\". Reportado por: " . User::find($request->user_id)->full_name . " vía " . match($request->source_channel) {
                    'phone' => 'Teléfono',
                    'in_person' => 'Presencial / Oficina',
                    'email' => 'Correo Electrónico',
                    'web' => 'Portal Web',
                    default => $request->source_channel
                } . ".",
                'event_date' => now(),
                'visibility' => 'public',
            ]);

            return $ticket;
        });

        return redirect()->route('admin.tickets.index')->with('success', 'El reclamo ha sido registrado correctamente.');
    }
}
