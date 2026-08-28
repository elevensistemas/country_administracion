<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LotFollowUp;
use App\Models\Lot;
use App\Models\User;
use App\Models\LotHistoryEvent;
use App\Models\LotHistoryEventType;
use App\Models\LotHistoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowUpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LotFollowUp::with(['lot', 'assignee', 'event']);

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('lot', function ($lq) use ($search) {
                      $lq->where('number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->whereIn('status', ['pending', 'in_progress', 'waiting_response']);
        }

        // Filter by Assignee
        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->input('assignee_id'));
        }

        $followUps = $query->orderBy('due_date', 'asc')->paginate(10)->withQueryString();

        $operators = User::whereIn('relationship_type', ['admin', 'superadmin', 'operator', 'accounting'])->get();

        return view('admin.follow-ups.index', compact('followUps', 'operators'));
    }

    /**
     * Store a new follow-up from lot history interface.
     */
    public function store(Request $request, Lot $lot)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'assignee_id' => 'required|exists:users,id',
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
        ]);

        LotFollowUp::create([
            'lot_id' => $lot->id,
            'reason' => $request->reason,
            'assignee_id' => $request->assignee_id,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Seguimiento creado correctamente.');
    }

    /**
     * Update status of the follow up and log history event.
     */
    public function updateStatus(Request $request, LotFollowUp $followUp)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_progress,waiting_response,completed,cancelled',
        ]);

        $oldStatus = $followUp->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back();
        }

        DB::transaction(function () use ($followUp, $newStatus) {
            $followUp->update(['status' => $newStatus]);

            // If completed or cancelled, write it to Lot History Timeline!
            if ($newStatus === 'completed' || $newStatus === 'cancelled') {
                $statusText = $newStatus === 'completed' ? 'Completado' : 'Cancelado';
                
                $evType = LotHistoryEventType::where('name', 'note_added')->first();
                $evCat = LotHistoryCategory::where('name', 'admin')->first();

                LotHistoryEvent::create([
                    'lot_id' => $followUp->lot_id,
                    'event_type_id' => $evType ? $evType->id : 1,
                    'category_id' => $evCat ? $evCat->id : 1,
                    'user_id' => auth()->id(),
                    'owner_id' => $followUp->lot->current_owner_id,
                    'tenant_id' => $followUp->lot->current_tenant_id,
                    'title' => "Seguimiento {$statusText}",
                    'description' => "Se marcó como {$newStatus} el seguimiento: \"{$followUp->reason}\". Notas: " . ($followUp->notes ?? 'Ninguna.'),
                    'event_date' => now(),
                    'visibility' => 'internal', // Interno
                    'priority' => $followUp->priority,
                ]);
            }
        });

        return back()->with('success', 'Estado del seguimiento actualizado.');
    }
}
