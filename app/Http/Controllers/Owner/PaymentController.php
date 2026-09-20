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
                ->with(['lot', 'functionalUnit', 'receipts'])
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
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->filter()->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId) ?: $lots->first();
        if ($activeLot && $activeLotId !== $activeLot->id) {
            session(['active_lot_id' => $activeLot->id]);
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
        $lots = $user->functionalUnits->map(fn($u) => $u->lot)->filter()->unique('id');
        $activeLotId = session('active_lot_id');
        $activeLot = $lots->firstWhere('id', $activeLotId) ?: $lots->first();

        if (!$activeLot) {
            return redirect()->back()->with('error', 'No posees un lote seleccionado para informar el pago.');
        }

        if ($request->filled('functional_unit_id')) {
            $unit = $activeLot->functionalUnits()->where('id', $request->functional_unit_id)->first();
            if (!$unit) {
                return redirect()->back()->with('error', 'La unidad funcional seleccionada no pertenece al lote activo.');
            }
        } else {
            $unit = $activeLot->functionalUnits()->first();
        }

        if (!$unit) {
            return redirect()->back()->with('error', 'La unidad funcional no está configurada.');
        }
        
        // Find owner profile associated to this user email, DNI or unit owner
        $owner = Owner::where('email', $user->email)->first()
            ?: ($user->dni ? Owner::where('dni', $user->dni)->first() : null)
            ?: $activeLot->owner
            ?: $unit->owners()->first()
            ?: $activeLot->owners()->first();

        $ownerId = $owner ? $owner->id : ($activeLot->current_owner_id ?: null);

        DB::transaction(function () use ($request, $user, $unit, $activeLot, $ownerId, $owner) {
            $payment = Payment::create([
                'functional_unit_id' => $unit->id,
                'lot_id' => $activeLot->id,
                'owner_id' => $ownerId,
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
                'lot_id' => $activeLot->id,
                'functional_unit_id' => $unit->id,
                'event_type_id' => $evType ? $evType->id : 1,
                'category_id' => $evCat ? $evCat->id : 1,
                'related_model_type' => Payment::class,
                'related_model_id' => $payment->id,
                'owner_id' => $ownerId,
                'tenant_id' => $activeLot->current_tenant_id,
                'user_id' => $user->id,
                'title' => "Pago Informado por Vecino",
                'description' => "Se informó pago de $ " . number_format($request->amount, 2, ',', '.') . " mediante {$request->payment_method}. Op N°: {$request->operation_number}.",
                'event_date' => now(),
                'visibility' => 'public',
            ]);

            // Notify all admin, superadmin, accounting, and operator staff
            $staffUsers = \App\Models\User::whereIn('relationship_type', ['admin', 'superadmin', 'accounting', 'operator'])->get();
            $amountFmt = number_format($request->amount, 2, ',', '.');

            foreach ($staffUsers as $staff) {
                \App\Models\Notification::create([
                    'user_id' => $staff->id,
                    'title' => "Nuevo Pago - Lote {$activeLot->number}",
                    'message' => "{$user->full_name} informó un pago de $ {$amountFmt} (Op: {$request->operation_number}).",
                    'type' => 'payment',
                    'link' => route('admin.payments.show', $payment->id),
                ]);
            }

            // Also create confirmation notification for the reporting resident / owner
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => "Comprobante de Pago Recibido",
                'message' => "Tu pago de $ {$amountFmt} (Lote {$activeLot->number}) fue registrado y está pendiente de validación contable.",
                'type' => 'payment',
                'link' => route('owner.payments.history'),
            ]);
        });

        return redirect()->route('owner.payments.history')->with('success', 'Pago informado correctamente. Se encuentra pendiente de validación contable.');
    }
}
