@extends('layouts.owner')

@section('title', 'Mis Pagos')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold m-0 text-success"><i class="bi bi-wallet2 me-2"></i>Mis Pagos</h4>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Historial de transferencias y depósitos reportados a la administración.</p>
        </div>
        <a href="{{ route('owner.payments.report') }}" class="btn btn-sm btn-ios btn-ios-primary"><i class="bi bi-plus-circle me-1"></i>Reportar Pago</a>
    </div>

    <!-- Payments Stacked Cards List -->
    @forelse($payments as $pay)
        <div class="col-12">
            <div class="ios-card bg-body-tertiary p-3 mb-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted d-block" style="font-size: 0.72rem;">Fecha Informado: {{ $pay->created_at->format('d/m/Y H:i') }} hs</span>
                        <strong class="text-dark" style="font-size: 0.95rem;">Lote {{ $pay->lot->number }}</strong>
                    </div>
                    @if($pay->status === 'pending')
                        <span class="badge bg-warning-subtle text-warning badge-ios">Pendiente</span>
                    @elseif($pay->status === 'approved')
                        <span class="badge bg-success-subtle text-success badge-ios">Aprobado</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger badge-ios">Rechazado</span>
                    @endif
                </div>

                <div class="my-2 border-top border-ios pt-2" style="font-size: 0.85rem; line-height: 1.5;">
                    <div class="mb-1"><span class="text-muted">Medio de Pago:</span> <span class="text-capitalize text-dark fw-semibold">{{ $pay->payment_method }}</span></div>
                    <div class="mb-1"><span class="text-muted">Banco Destino:</span> <span class="text-dark fw-semibold">{{ $pay->bank }}</span></div>
                    <div class="mb-1"><span class="text-muted">N° Transacción:</span> <span class="text-dark fw-semibold font-monospace">{{ $pay->operation_number }}</span></div>
                    @if($pay->notes)
                        <div class="bg-body-secondary p-2 rounded-3 mt-1 text-muted" style="font-size: 0.8rem;">
                            <i class="bi bi-chat-left-text me-1"></i> {{ $pay->notes }}
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-ios">
                    <span class="text-muted" style="font-size: 0.8rem;">Monto Reportado:</span>
                    <strong class="text-success" style="font-size: 1.1rem;">
                        ${{ number_format($pay->amount, 2, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="ios-card">
                <i class="bi bi-wallet2 text-muted fs-1 d-block mb-3"></i>
                <span class="text-muted">Aún no has informado ningún pago en el portal para el lote seleccionado.</span>
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="col-12 mt-3">
        {{ $payments->links() }}
    </div>
</div>
@endsection
