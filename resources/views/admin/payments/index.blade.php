@extends('layouts.app')

@section('title', 'Conciliación de Pagos')
@section('page_title', 'Conciliar Pagos Recibidos')

@section('content')
<!-- Indicators Panel -->
<div class="row g-3 mb-4">
    <div class="col-md-2-4 col-sm-6">
        <div class="ios-card bg-body-secondary border-0 text-center py-3">
            <span class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">PENDIENTES</span>
            <h3 class="fw-bold m-0 text-warning">{{ $stats['pending_count'] }}</h3>
            <small class="text-muted" style="font-size: 0.7rem;">En revisión o espera</small>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="ios-card bg-success-subtle text-success text-center py-3">
            <span class="text-success d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">CONCILIADOS HOY</span>
            <h3 class="fw-bold m-0">{{ $stats['reconciled_today_count'] }}</h3>
            <small class="text-muted" style="font-size: 0.7rem;">Cerrados hoy</small>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="ios-card bg-warning-subtle text-warning text-center py-3">
            <span class="text-warning d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">EN REVISIÓN</span>
            <h3 class="fw-bold m-0">{{ $stats['review_count'] }}</h3>
            <small class="text-muted" style="font-size: 0.7rem;">Requieren atención</small>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-6">
        <div class="ios-card bg-danger-subtle text-danger text-center py-3">
            <span class="text-danger d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">SIN IDENTIFICAR</span>
            <h3 class="fw-bold m-0">{{ $stats['unidentified_count'] }}</h3>
            <small class="text-muted" style="font-size: 0.7rem;">Sin lote asociado</small>
        </div>
    </div>
    <div class="col-md-2-4 col-sm-12">
        <div class="ios-card bg-primary-subtle text-primary text-center py-3">
            <span class="text-primary d-block mb-1" style="font-size: 0.75rem; font-weight: 600;">CONCILIADO HOY</span>
            <h3 class="fw-bold m-0">${{ number_format($stats['reconciled_today_amount'], 2, ',', '.') }}</h3>
            <small class="text-muted" style="font-size: 0.7rem;">Importe total de hoy</small>
        </div>
    </div>
</div>

<!-- CSS helper for 5 columns layout in Bootstrap -->
<style>
@media (min-width: 768px) {
    .col-md-2-4 {
        flex: 0 0 20%;
        max-width: 20%;
    }
}
</style>

<!-- Filter and Action Panel -->
<div class="ios-card mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 align-items-center flex-grow-1 m-0">
            <div class="col-md-6 p-0 pe-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por prop, lote, CUIT, DNI, operac, import..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-4 p-0 pe-md-2">
                <select name="status" class="form-select form-control-ios" onchange="this.form.submit()">
                    <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pendientes de Conciliación</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Conciliados / Aprobados</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazados</option>
                    <option value="review" {{ request('status') === 'review' ? 'selected' : '' }}>Requieren Revisión</option>
                    <option value="unmatched" {{ request('status') === 'unmatched' ? 'selected' : '' }}>Sin Coincidencia (Sin Lote)</option>
                    <option value="excess" {{ request('status') === 'excess' ? 'selected' : '' }}>Con Excedentes / Saldo a Favor</option>
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Todos los Registros</option>
                </select>
            </div>

            <div class="col-md-2 p-0 d-grid">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar</a>
            </div>
        </form>

        @if(request('status', 'pending') === 'pending' || request('status') === 'review')
            <div class="d-grid ps-md-2">
                <a href="{{ route('admin.payments.auto-reconcile.simulate') }}" class="btn btn-ios btn-ios-primary text-nowrap">
                    <i class="bi bi-magic me-1"></i> Conciliar Automáticamente
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Payments List -->
<div class="ios-card">
    <h5 class="fw-bold mb-4">Pagos Registrados en el Sistema</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA INFORME</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">LOTE / UF</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">PROPIETARIO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DETALLE DE PAGO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">IMPORTE</th>
                    <th class="text-muted text-center" style="font-size: 0.85rem; font-weight: 600;">COINCIDENCIA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 12%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pay)
                    <tr class="border-bottom border-ios">
                        <td style="font-size: 0.9rem;">
                            {{ $pay->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="fw-bold">
                            @if($pay->lot)
                                Lote {{ $pay->lot->number }}
                            @else
                                <span class="text-danger"><i class="bi bi-question-circle me-1"></i>Sin Lote</span>
                            @endif
                        </td>
                        <td>
                            @if($pay->owner)
                                {{ $pay->owner->full_name }}
                            @else
                                <span class="text-muted">No Identificado</span>
                            @endif
                        </td>
                        <td style="font-size: 0.85rem;">
                            <span class="d-block text-capitalize fw-semibold">{{ $pay->payment_method }} • {{ $pay->bank ?? 'S/B' }}</span>
                            <span class="text-muted">Op: {{ $pay->operation_number ?? 'S/N' }} • Dep: {{ $pay->payment_date->format('d/m/Y') }}</span>
                        </td>
                        <td class="text-end fw-bold text-success" style="font-size: 1rem;">
                            ${{ number_format($pay->amount, 2, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($pay->status === 'pending' || $pay->status === 'review')
                                @if(isset($pay->match_score))
                                    @if($pay->match_score >= 95)
                                        <span class="badge bg-success-subtle text-success badge-ios d-inline-flex align-items-center">
                                            <i class="bi bi-check-circle-fill me-1"></i> {{ $pay->match_score }}% (Seguro)
                                        </span>
                                    @elseif($pay->match_score >= 50)
                                        <span class="badge bg-warning-subtle text-warning badge-ios d-inline-flex align-items-center">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $pay->match_score }}% (Medio)
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger badge-ios d-inline-flex align-items-center">
                                            <i class="bi bi-x-circle-fill me-1"></i> {{ $pay->match_score }}% (Bajo)
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            @else
                                <span class="text-muted" style="font-size: 0.85rem;">Conciliado ({{ $pay->reconciliation_method ?? 'N/A' }})</span>
                            @endif
                        </td>
                        <td>
                            @if($pay->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning badge-ios">Pendiente</span>
                            @elseif($pay->status === 'review')
                                <span class="badge bg-info-subtle text-info badge-ios">Revisión</span>
                            @elseif($pay->status === 'approved')
                                <span class="badge bg-success-subtle text-success badge-ios">Conciliado</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger badge-ios">Rechazado</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.payments.show', $pay) }}" class="btn btn-sm btn-ios {{ $pay->status === 'approved' ? 'btn-ios-secondary' : 'btn-ios-primary' }}">
                                @if($pay->status === 'approved')
                                    <i class="bi bi-eye-fill"></i> Ver
                                @else
                                    <i class="bi bi-shield-check"></i> Revisar
                                @endif
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-wallet2 text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No se encontraron pagos informados en esta sección.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection
