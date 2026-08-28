<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryCategory;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryAttachment;
use App\Models\LotFollowUp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class LotHistoryController extends Controller
{
    /**
     * Display general history timeline for all lots.
     */
    public function index(Request $request)
    {
        $query = LotHistoryEvent::with(['lot', 'eventType', 'category', 'owner', 'tenant', 'user']);

        // Filter by Lot
        if ($request->filled('lot_id')) {
            $query->where('lot_id', $request->input('lot_id'));
        }

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by Event Type
        if ($request->filled('event_type_id')) {
            $query->where('event_type_id', $request->input('event_type_id'));
        }

        // Filter by Search Query
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

        // Filter by Visibility
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->input('visibility'));
        }

        // Filter by Priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by Date Range
        if ($request->filled('date_start')) {
            $query->whereDate('event_date', '>=', $request->input('date_start'));
        }
        if ($request->filled('date_end')) {
            $query->whereDate('event_date', '<=', $request->input('date_end'));
        }

        // Order
        $order = $request->input('order', 'desc');
        $events = $query->orderBy('event_date', $order)->paginate(15)->withQueryString();

        $lots = Lot::orderBy(DB::raw('CAST(number AS UNSIGNED)'), 'asc')->get();
        $categories = LotHistoryCategory::all();
        $eventTypes = LotHistoryEventType::all();

        return view('admin.history.index', compact('events', 'lots', 'categories', 'eventTypes'));
    }

    /**
     * Show timeline history for a specific lot.
     */
    public function show(Request $request, Lot $lot)
    {
        $lot->load(['owner', 'tenant', 'functionalUnits']);

        $query = LotHistoryEvent::where('lot_id', $lot->id)
            ->with(['eventType', 'category', 'owner', 'tenant', 'user', 'attachments', 'followUps']);

        // Filters inside specific lot history
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->input('category'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $order = $request->input('order', 'desc');
        $events = $query->orderBy('event_date', $order)->get();

        $categories = LotHistoryCategory::all();
        $operators = User::whereIn('relationship_type', ['admin', 'superadmin', 'operator', 'accounting'])->get();

        // Calculate some statistics for summary header
        $ticketsCount = \App\Models\Ticket::where('lot_id', $lot->id)->count();
        $pendingTickets = \App\Models\Ticket::where('lot_id', $lot->id)->whereNotIn('status', ['resolved', 'closed'])->count();
        $pendingFollowups = LotFollowUp::where('lot_id', $lot->id)->where('status', 'pending')->count();
        
        return view('admin.lots.history', compact(
            'lot', 'events', 'categories', 'operators', 
            'ticketsCount', 'pendingTickets', 'pendingFollowups'
        ));
    }

    /**
     * Store a manual note in the lot history.
     */
    public function storeNote(Request $request, Lot $lot)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:lot_history_categories,id',
            'priority' => 'required|string|in:low,medium,high',
            'visibility' => 'required|string|in:internal,public',
            'is_confidential' => 'nullable|boolean',
            'attachment' => 'nullable|file|max:10240', // 10MB limit
            'create_followup' => 'nullable|boolean',
            'followup_reason' => 'required_if:create_followup,1|string|nullable',
            'followup_assignee_id' => 'required_if:create_followup,1|exists:users,id|nullable',
            'followup_due_date' => 'required_if:create_followup,1|date|nullable',
        ]);

        DB::transaction(function () use ($request, $lot) {
            $noteType = LotHistoryEventType::where('name', 'note_added')->first();

            $event = LotHistoryEvent::create([
                'lot_id' => $lot->id,
                'functional_unit_id' => $lot->functionalUnits->first()?->id,
                'event_type_id' => $noteType ? $noteType->id : 1,
                'category_id' => $request->category_id,
                'owner_id' => $lot->current_owner_id,
                'tenant_id' => $lot->current_tenant_id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'event_date' => now(),
                'visibility' => $request->visibility,
                'is_confidential' => $request->has('is_confidential'),
                'priority' => $request->priority,
                'source_channel' => 'admin',
            ]);

            // Handle file upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('lot_history_attachments', 'public');

                LotHistoryAttachment::create([
                    'lot_history_event_id' => $event->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            // Create associated follow up if requested
            if ($request->boolean('create_followup')) {
                LotFollowUp::create([
                    'lot_history_event_id' => $event->id,
                    'lot_id' => $lot->id,
                    'reason' => $request->followup_reason,
                    'assignee_id' => $request->followup_assignee_id,
                    'due_date' => $request->followup_due_date,
                    'priority' => $request->priority,
                    'status' => 'pending',
                ]);
            }
        });

        return back()->with('success', 'Nota administrativa registrada correctamente en el historial del lote.');
    }

    /**
     * Export lot history.
     */
    public function export(Request $request, Lot $lot)
    {
        $format = $request->input('format', 'csv');

        $query = LotHistoryEvent::where('lot_id', $lot->id)
            ->with(['eventType', 'category', 'user']);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->input('category'));
            });
        }

        $events = $query->orderBy('event_date', 'desc')->get();

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=historial_lote_{$lot->number}.csv",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($events, $lot) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['HISTORIAL INTEGRAL - LOTE ' . $lot->number]);
                fputcsv($file, ['Fecha de Exportacion:', now()->format('d/m/Y H:i')]);
                fputcsv($file, ['Propietario Actual:', $lot->owner ? $lot->owner->full_name : 'Sin asignar']);
                fputcsv($file, ['Inquilino Actual:', $lot->tenant ? $lot->tenant->full_name : 'Ninguno']);
                fputcsv($file, ['Saldo:', $lot->balance]);
                fputcsv($file, []);

                fputcsv($file, ['FECHA', 'TIPO EVENTO', 'CATEGORIA', 'TITULO', 'DESCRIPCION', 'VISIBILIDAD', 'CREADO POR']);

                foreach ($events as $event) {
                    fputcsv($file, [
                        $event->event_date->format('d/m/Y H:i'),
                        $event->eventType->display_name,
                        $event->category->display_name,
                        $event->title,
                        $event->description,
                        $event->visibility,
                        $event->user ? $event->user->full_name : 'Sistema',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // HTML Print format
        $lot->load(['owner', 'tenant']);
        return view('admin.lots.print_history', compact('lot', 'events'));
    }
}
