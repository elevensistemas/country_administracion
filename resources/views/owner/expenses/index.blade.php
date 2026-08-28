@extends('layouts.owner')

@section('title', 'Mis Expensas')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12 mb-2">
        <h4 class="fw-bold m-0 text-success"><i class="bi bi-receipt-cutoff me-2"></i>Mis Expensas</h4>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Consulta la liquidación del período actual y el historial de expensas.</p>
    </div>

    @php
        $latestExpense = $expenses->first();
    @endphp

    @if($latestExpense)
        <!-- Latest Expense Breakdown Card -->
        <div class="col-12">
            <div class="ios-card bg-body-secondary border-0 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="badge bg-success text-white badge-ios">Última Expensa</span>
                        <h5 class="fw-bold m-0 mt-1">Período {{ $latestExpense->billingPeriod->period }}</h5>
                    </div>
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.85rem;">
                        Vence: {{ $latestExpense->due_date->format('d/m/Y') }}
                    </span>
                </div>

                <!-- Financial Breakdown -->
                <div class="p-3 bg-body-tertiary rounded-4 mb-3 border border-ios">
                    <div class="d-flex justify-content-between py-1 border-bottom border-ios" style="font-size: 0.88rem;">
                        <span class="text-muted">Expensa del Mes:</span>
                        <span class="fw-bold text-dark">${{ number_format($latestExpense->capital_amount, 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom border-ios" style="font-size: 0.88rem;">
                        <span class="text-muted">Saldo Anterior:</span>
                        <span class="fw-bold text-muted">${{ number_format($latestExpense->previous_balance, 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom border-ios" style="font-size: 0.88rem;">
                        <span class="text-muted">Intereses por Mora:</span>
                        <span class="fw-bold text-danger">${{ number_format($latestExpense->interest_amount, 2, ',', '.') }}</span>
                    </div>
                    @if($latestExpense->adjustments_amount != 0)
                        <div class="d-flex justify-content-between py-1 border-bottom border-ios" style="font-size: 0.88rem;">
                            <span class="text-muted">Ajustes / Otros Cargos:</span>
                            <span class="fw-bold text-dark">${{ number_format($latestExpense->adjustments_amount, 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($latestExpense->discount_amount > 0)
                        <div class="d-flex justify-content-between py-1 border-bottom border-ios" style="font-size: 0.88rem;">
                            <span class="text-muted">Bonificaciones / Descuentos:</span>
                            <span class="fw-bold text-success">-${{ number_format($latestExpense->discount_amount, 2, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between pt-2 fw-bold" style="font-size: 1.15rem;">
                        <span class="text-success">TOTAL ADEUDADO:</span>
                        <span class="text-danger">${{ number_format($latestExpense->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('owner.expenses.download', $latestExpense) }}" target="_blank" class="btn btn-ios btn-ios-secondary flex-fill text-success py-2.5">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar PDF
                    </a>
                    <a href="{{ route('owner.payments.report') }}" class="btn btn-ios btn-ios-primary flex-fill py-2.5">
                        <i class="bi bi-credit-card-fill me-1"></i> Informar Pago
                    </a>
                </div>
            </div>
        </div>

        <!-- History Title -->
        <div class="col-12 mt-2">
            <h6 class="fw-bold text-muted mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Historial de Expensas</h6>
        </div>

        <!-- Previous Expenses Cards -->
        @foreach($expenses as $exp)
            @if($loop->first && $expenses->currentPage() === 1)
                @continue
            @endif
            <div class="col-12">
                <div class="ios-card bg-body-tertiary p-3 mb-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">Período {{ $exp->billingPeriod->period }}</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Lote {{ $exp->functionalUnit->lot->number }} | Vence {{ $exp->due_date->format('d/m/Y') }}</small>
                        </div>
                        @if($exp->status === 'paid')
                            <span class="badge bg-success-subtle text-success badge-ios">Pagada</span>
                        @elseif($exp->status === 'published')
                            <span class="badge bg-primary-subtle text-primary badge-ios">Impaga</span>
                        @elseif($exp->status === 'partial')
                            <span class="badge bg-warning-subtle text-warning badge-ios">Parcial</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger badge-ios">Vencida</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-ios">
                        <span class="fw-bold text-danger" style="font-size: 1.05rem;">
                            ${{ number_format($exp->total_amount, 2, ',', '.') }}
                        </span>
                        <a href="{{ route('owner.expenses.download', $exp) }}" target="_blank" class="btn btn-sm btn-ios btn-ios-secondary text-success">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ver PDF
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="col-12 mt-3">
            {{ $expenses->links() }}
        </div>
    @else
        <div class="col-12 text-center py-5">
            <div class="ios-card">
                <i class="bi bi-receipt-cutoff text-muted fs-1 d-block mb-3"></i>
                <span class="text-muted">No se registran expensas publicadas para este lote.</span>
            </div>
        </div>
    @endif
</div>
@endsection
