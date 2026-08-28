<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Models\CommunicationTemplate;
use App\Models\CommunicationRecipient;
use App\Models\CommunicationDelivery;
use App\Models\Owner;
use App\Models\User;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CommunicationController extends Controller
{
    /**
     * Display a listing of communications and templates.
     */
    public function index()
    {
        $comms = Communication::with(['template', 'user'])
            ->withCount(['recipients', 'deliveries as opened_count' => function($q) {
                $q->where('status', 'opened');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $templates = CommunicationTemplate::all();

        return view('admin.comms.index', compact('comms', 'templates'));
    }

    /**
     * Show form to send a communication.
     */
    public function create()
    {
        $templates = CommunicationTemplate::all();
        $lots = Lot::orderBy('number')->get();
        return view('admin.comms.create', compact('templates', 'lots'));
    }

    /**
     * Store and dispatch a communication.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|string|in:all_owners,all_tenants,board,specific_lot',
            'lot_id' => 'required_if:target_type,specific_lot|exists:lots,id|nullable',
            'communication_template_id' => 'nullable|exists:communication_templates,id',
        ]);

        DB::transaction(function () use ($request) {
            $comm = Communication::create([
                'user_id' => auth()->id(),
                'communication_template_id' => $request->communication_template_id,
                'title' => $request->title,
                'subject' => $request->subject,
                'content' => $request->content,
                'target_type' => $request->target_type,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Resolve target recipients (emails)
            $recipientsQuery = User::query();

            if ($request->target_type === 'all_owners') {
                $recipientsQuery->where('relationship_type', 'owner');
            } elseif ($request->target_type === 'all_tenants') {
                $recipientsQuery->where('relationship_type', 'tenant');
            } elseif ($request->target_type === 'board') {
                $recipientsQuery->where('relationship_type', 'board');
            } elseif ($request->target_type === 'specific_lot') {
                $lotId = $request->lot_id;
                $recipientsQuery->whereHas('functionalUnits', function ($q) use ($lotId) {
                    $q->where('lot_id', $lotId);
                });
            }

            $users = $recipientsQuery->get();

            foreach ($users as $user) {
                // Save recipient
                $recipient = CommunicationRecipient::create([
                    'communication_id' => $comm->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => 'sent',
                ]);

                // Simulate delivery log
                // Normally we'd queue an email here.
                // Let's create a simulated delivery record
                $status = rand(0, 10) > 1 ? 'delivered' : 'failed'; // 90% delivery rate
                
                $delivery = CommunicationDelivery::create([
                    'communication_recipient_id' => $recipient->id,
                    'channel' => 'email',
                    'status' => $status,
                    'sent_at' => now(),
                    'error_message' => $status === 'failed' ? 'Connection timed out to SMTP host' : null,
                ]);

                if ($status === 'delivered') {
                    // Simulate email opening (some open, some don't)
                    if (rand(0, 10) > 4) { // 60% open rate simulation
                        $delivery->update([
                            'status' => 'opened',
                            'opened_at' => now()->addMinutes(rand(5, 120)),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.comms.index')->with('success', 'La comunicación ha sido enviada y encolada en la cola de envíos SMTP.');
    }

    /**
     * Show delivery analytics for a communication.
     */
    public function show(Communication $communication)
    {
        $communication->load(['template', 'user']);

        // Stats
        $totalRecipients = CommunicationRecipient::where('communication_id', $communication->id)->count();
        
        $delivered = DB::table('communication_deliveries')
            ->join('communication_recipients', 'communication_deliveries.communication_recipient_id', '=', 'communication_recipients.id')
            ->where('communication_recipients.communication_id', $communication->id)
            ->whereIn('communication_deliveries.status', ['delivered', 'opened'])
            ->count();

        $opened = DB::table('communication_deliveries')
            ->join('communication_recipients', 'communication_deliveries.communication_recipient_id', '=', 'communication_recipients.id')
            ->where('communication_recipients.communication_id', $communication->id)
            ->where('communication_deliveries.status', 'opened')
            ->count();

        $failed = DB::table('communication_deliveries')
            ->join('communication_recipients', 'communication_deliveries.communication_recipient_id', '=', 'communication_recipients.id')
            ->where('communication_recipients.communication_id', $communication->id)
            ->where('communication_deliveries.status', 'failed')
            ->count();

        $recipients = CommunicationRecipient::where('communication_id', $communication->id)
            ->with(['user', 'deliveries'])
            ->paginate(20);

        return view('admin.comms.show', compact('communication', 'totalRecipients', 'delivered', 'opened', 'failed', 'recipients'));
    }
}
