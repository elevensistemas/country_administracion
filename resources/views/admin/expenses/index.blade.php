@extends('layouts.app')

@section('title', 'Expensas')
@section('page_title', 'Facturación de Expensas')

@section('content')
<div class="row">
    <!-- Billing Periods Panel (Left) -->
    <div class="col-lg-4 mb-4">
        <!-- Action Card for PDF Import -->
        <div class="ios-card mb-4 bg-primary text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-file-earmark-pdf-fill fs-3 text-white"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0 text-white">Importar Liquidación</h6>
                    <small class="text-white-50">Carga automática del Boletín mensual</small>
                </div>
            </div>
            <p class="text-white-50 mb-3" style="font-size: 0.82rem; line-height: 1.4;">
                Sube el PDF oficial emitido por la administración para procesar automáticamente los 131 lotes, saldos, mora y adjuntar el boletín.
            </p>
            <button type="button" class="btn btn-light w-100 fw-bold py-2 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#importPdfModal">
                <i class="bi bi-cloud-arrow-up-fill text-primary"></i> Subir PDF del Mes
            </button>
        </div>

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
            <form method="GET" action="{{ route('admin.expenses.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar lote (ej: 14) o vecino..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="billing_period_id" class="form-select form-control-ios" onchange="this.form.submit()">
                        <option value="">Todos los Períodos</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->id }}" {{ request('billing_period_id') == $p->id ? 'selected' : '' }}>
                                Período {{ $p->period }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-control-ios" onchange="this.form.submit()">
                        <option value="">Todos los Estados</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Borrador</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Pago Parcial</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagado</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencido</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-ios btn-ios-primary flex-fill" title="Filtrar">
                        <i class="bi bi-funnel-fill"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'billing_period_id', 'status', 'lot_id']))
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-ios btn-ios-secondary" title="Limpiar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
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

<!-- Modal Importar PDF Liquidación -->
<div class="modal fade" id="importPdfModal" tabindex="-1" aria-labelledby="importPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom border-ios p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0" id="importPdfModalLabel">Importar Liquidación (PDF)</h5>
                        <small class="text-muted">Procesamiento automático de los 131 lotes y boletín</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.expenses.import-pdf') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Período de Liquidación</label>
                        <input type="month" name="period" class="form-control form-control-ios" required value="{{ date('Y-m') }}">
                        <small class="text-muted">Indique el mes y año liquidado (Ej: 2026-09).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Archivo PDF Oficial</label>
                        <input type="file" name="pdf_file" class="form-control form-control-ios" accept="application/pdf" required>
                        <small class="text-muted">Seleccione el PDF emitido por la administración que contiene el boletín y las planillas de liquidación.</small>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 mb-0" style="font-size: 0.85rem;">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Procesamiento Inteligente:</strong> El sistema extrae automáticamente el saldo anterior, intereses por mora, expensas ordinarias, canon de obra, pronto pago y total a pagar de cada lote (UF 1 al 131), vinculando además el boletín en PDF para descarga de los vecinos.
                    </div>
                </div>
                <div class="modal-footer border-top border-ios p-3 bg-body-tertiary">
                    <button type="button" class="btn btn-ios btn-ios-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-ios btn-ios-primary text-white">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Iniciar Importación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
