@extends('layouts.app')

@section('title', 'Expensas')
@section('page_title', 'Facturación de Expensas')

@section('content')
<div class="row">
    <!-- Billing Periods Panel (Left) -->
    <div class="col-lg-4 mb-4">
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0"><i class="bi bi-calendar3 text-success me-2"></i>Períodos Facturados</h6>
                <a href="{{ route('admin.expenses.create-period') }}" class="btn btn-sm btn-ios btn-ios-secondary text-success"><i class="bi bi-plus-circle"></i> Nuevo</a>
            </div>

            <div class="list-group list-group-flush">
                @forelse($periods as $period)
                    <div class="list-group-item bg-transparent border-0 px-0 py-3 border-bottom border-ios">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold m-0" style="font-size: 1.05rem;">Período {{ $period->period }}</h6>
                                <small class="text-muted">{{ $period->start_date->format('d/m/Y') }} al {{ $period->end_date->format('d/m/Y') }}</small>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary badge-ios text-uppercase" style="font-size: 0.7rem;">
                                {{ $period->status }}
                            </span>
                        </div>

                        <!-- Generation triggers -->
                        @if($period->status === 'draft')
                            <div class="d-flex flex-column gap-2 mt-2">
                                <form action="{{ route('admin.expenses.generate') }}" method="POST" class="d-grid w-100 m-0">
                                    @csrf
                                    <input type="hidden" name="billing_period_id" value="{{ $period->id }}">
                                    <button type="submit" class="btn btn-sm btn-ios btn-ios-primary w-100">
                                        <i class="bi bi-gear-fill me-1"></i> Generar Expensas Masivas
                                    </button>
                                </form>
                                <a href="{{ route('admin.imports.index') }}" class="btn btn-sm btn-ios btn-ios-secondary text-success text-decoration-none d-flex align-items-center justify-content-center py-2" style="font-size: 0.8rem;">
                                    <i class="bi bi-file-earmark-spreadsheet-fill me-1.5 text-success"></i> Importar Expensas desde Excel
                                </a>
                            </div>
                        @else
                            <div class="d-grid mt-2">
                                <span class="btn btn-sm btn-ios btn-ios-secondary disabled text-muted"><i class="bi bi-check-circle-fill me-1"></i> Expensas Generadas</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted fs-1 d-block mb-2"></i>
                        <span class="text-muted">No hay períodos registrados</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Generated Expenses List (Right) -->
    <div class="col-lg-8">
        <!-- Filters -->
        <div class="ios-card mb-4">
            <form method="GET" action="{{ route('admin.expenses.index') }}" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <select name="billing_period_id" class="form-select form-control-ios" onchange="this.form.submit()">
                        <option value="">Todos los Períodos</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ request('billing_period_id') == $p->id ? 'selected' : '' }}>
                                Período {{ $p->period }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <select name="status" class="form-select form-control-ios" onchange="this.form.submit()">
                        <option value="">Todos los Estados</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Borrador</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Pago Parcial</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagado</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencido</option>
                    </select>
                </div>

                <div class="col-md-3 d-grid">
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar Filtros</a>
                </div>
            </form>
        </div>

        <!-- Expenses Table -->
        <div class="ios-card">
            <h5 class="fw-bold mb-4">Liquidaciones Emitidas</h5>

            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">UF / LOTE</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">PERÍODO</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">PROPIETARIO</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">IMPORTE</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 20%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr class="border-bottom border-ios">
                                <td class="fw-bold">Lote {{ $exp->functionalUnit->lot->number }}</td>
                                <td>{{ $exp->billingPeriod->period }}</td>
                                <td>{{ $exp->functionalUnit->lot->owner ? $exp->functionalUnit->lot->owner->full_name : 'Sin asignar' }}</td>
                                <td class="text-end fw-semibold text-danger">${{ number_format($exp->total_amount, 2, ',', '.') }}</td>
                                <td>
                                    @if($exp->status === 'draft')
                                        <span class="badge bg-secondary-subtle text-secondary badge-ios">Borrador</span>
                                    @elseif($exp->status === 'published')
                                        <span class="badge bg-primary-subtle text-primary badge-ios">Publicado</span>
                                    @elseif($exp->status === 'paid')
                                        <span class="badge bg-success-subtle text-success badge-ios">Pagado</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger badge-ios">{{ $exp->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Publish -->
                                        @if($exp->status === 'draft')
                                            <form action="{{ route('admin.expenses.publish', $exp) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Publicar Expensa">
                                                    <i class="bi bi-cloud-arrow-up-fill"></i> Publicar
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Download simulated PDF -->
                                        <a href="{{ route('admin.expenses.pdf', $exp) }}" target="_blank" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Descargar Liquidación PDF">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Ver PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-receipt-cutoff text-muted fs-1 d-block mb-3"></i>
                                    <span class="text-muted">No se registran expensas liquidadas en los filtros actuales.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile view: Stacked Cards -->
            <div class="d-block d-md-none">
                @forelse($expenses as $exp)
                    <div class="p-3 border-bottom border-ios mb-3 rounded-4 bg-body-tertiary">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold m-0" style="font-size: 1.05rem;">Lote {{ $exp->functionalUnit->lot->number }}</h6>
                                <small class="text-muted">Período: {{ $exp->billingPeriod->period }}</small>
                            </div>
                            @if($exp->status === 'draft')
                                <span class="badge bg-secondary-subtle text-secondary badge-ios">Borrador</span>
                            @elseif($exp->status === 'published')
                                <span class="badge bg-primary-subtle text-primary badge-ios">Publicado</span>
                            @elseif($exp->status === 'paid')
                                <span class="badge bg-success-subtle text-success badge-ios">Pagado</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger badge-ios">{{ $exp->status }}</span>
                            @endif
                        </div>

                        <div class="my-2" style="font-size: 0.85rem; line-height: 1.5;">
                            <div class="mb-1"><strong>Propietario:</strong> {{ $exp->functionalUnit->lot->owner ? $exp->functionalUnit->lot->owner->full_name : 'Sin asignar' }}</div>
                            <div>
                                <strong>Importe Liquidado:</strong>
                                <span class="fw-bold text-danger">
                                    ${{ number_format($exp->total_amount, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top border-ios">
                            @if($exp->status === 'draft')
                                <form action="{{ route('admin.expenses.publish', $exp) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-primary px-3 py-2">
                                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Publicar
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.expenses.pdf', $exp) }}" target="_blank" class="btn btn-sm btn-ios btn-ios-secondary text-success px-3 py-2">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ver PDF
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        No se registran expensas liquidadas en los filtros actuales.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
