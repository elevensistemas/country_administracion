<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\FunctionalUnit;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * List payment report history.
     */
    public function index()
    {
        $user = auth()->user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);
        if (!$activeLot) {
            $activeLot = $lots->first();
            $activeLotId = $activeLot?->id;
            session(['active_lot_id' => $activeLotId]);
        }

        $payments = [];
        if ($activeLot) {
            $payments = Payment::where('lot_id', $activeLot->id)
                ->with(['lot', 'functionalUnit'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('owner.payments.index', compact('payments', 'activeLot'));
    }

    /**
     * Show payment report form.
     */
    public function create()
    {
        $user = auth()->user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);
        if (!$activeLot) {
            $activeLot = $lots->first();
            $activeLotId = $activeLot?->id;
            session(['active_lot_id' => $activeLotId]);
        }

        return view('owner.payments.report', compact('user', 'activeLot'));
    }

    /**
     * Store reported payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|string|in:transfer,deposit,other',
            'bank' => 'required|string',
            'operation_number' => 'required|string',
            'notes' => 'nullable|string',
            'receipt' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240', // Support PDF, camera, gallery
        ]);

        $user = auth()->user();
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId);

        if (!$activeLot) {
            return redirect()->back()->with('error', 'Lote no seleccionado.');
        }

        $unit = $activeLot->functionalUnits()->first();
        if (!$unit) {
            return redirect()->back()->with('error', 'La unidad funcional no está configurada.');
        }
        
        // Find owner profile associated to this user email/phone
        $owner = Owner::where('email', $user->email)->first();

        DB::transaction(function () use ($request, $user, $unit, $owner) {
            $payment = Payment::create([
                'functional_unit_id' => $unit->id,
                'lot_id' => $unit->lot_id,
                'owner_id' => $owner ? $owner->id : 1,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'bank' => $request->bank,
                'operation_number' => $request->operation_number,
                'status' => 'pending',
                'notes' => $request->notes,
                'source_channel' => 'portal',
            ]);

            // Save receipt image
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $path = $file->store('payment_receipts', 'public');

                PaymentReceipt::create([
                    'payment_id' => $payment->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ]);
            }

            // Log event in Lot History (Payment reported)
            $evType = \App\Models\LotHistoryEventType::where('name', 'payment_received')->first();
            $evCat = \App\Models\LotHistoryCategory::where('name', 'finance')->first();

            \App\Models\LotHistoryEvent::create([
                'lot_id' => $unit->lot_id,
                'functional_unit_id' => $unit->id,
                'event_type_id' => $evType ? $evType->id : 1,
                'category_id' => $evCat ? $evCat->id : 1,
                'related_model_type' => Payment::class,
                'related_model_id' => $payment->id,
                'owner_id' => $owner ? $owner->id : null,
                'tenant_id' => $unit->lot->current_tenant_id,
                'user_id' => $user->id,
                'title' => "Pago Informado por Vecino",
                'description' => "Se informó pago de $ " . number_format($request->amount, 2, ',', '.') . " mediante {$request->payment_method}. Op N°: {$request->operation_number}.",
                'event_date' => now(),
                'visibility' => 'public',
            ]);
        });

        return redirect()->route('owner.payments.history')->with('success', 'Pago informado correctamente. Se encuentra pendiente de validación contable.');
    }
}
