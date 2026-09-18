@extends('layouts.app')

@section('title', 'Reportes y Estadísticas')
@section('page_title', 'Tablero Ejecutivo e Informes')

@section('content')
<!-- Header Toolbar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <p class="text-muted m-0" style="font-size: 0.9rem;">Métricas consolidadas, cobranzas, ranking de morosidad y auditoría general.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <!-- Year filter -->
        <form method="GET" action="{{ route('admin.reports.index') }}" class="d-inline-flex align-items-center gap-1">
            <select name="year" class="form-select form-select-sm form-control-ios" onchange="this.form.submit()" style="width: 110px;">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Año {{ $y }}</option>
                @endfor
            </select>
        </form>

        <!-- Export dropdown -->
        <div class="dropdown">
            <button class="btn btn-ios btn-outline-success btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar Datos
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2">
                <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.reports.export', ['type' => 'debtors']) }}"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Ranking de Deudores (CSV)</a></li>
                <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.reports.export', ['type' => 'lots']) }}"><i class="bi bi-house-door-fill text-primary me-2"></i>Padrón de Lotes Completo (CSV)</a></li>
                <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.reports.export', ['type' => 'payments']) }}"><i class="bi bi-cash-stack text-success me-2"></i>Historial de Cobranzas (CSV)</a></li>
                <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.reports.export', ['type' => 'suppliers']) }}"><i class="bi bi-truck text-warning me-2"></i>Gastos de Proveedores (CSV)</a></li>
                <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.reports.export', ['type' => 'tickets']) }}"><i class="bi bi-chat-dots-fill text-info me-2"></i>Historial de Reclamos (CSV)</a></li>
            </ul>
        </div>

        <!-- Print button -->
        <button onclick="window.print()" class="btn btn-ios btn-ios-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-printer-fill"></i> Imprimir Informe
        </button>
    </div>
</div>

<!-- 4 KPI Executive Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card p-3 h-100 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Recaudado ({{ $selectedYear }})</span>
                <span class="badge bg-success-subtle text-success rounded-pill p-2"><i class="bi bi-cash-coin fs-6"></i></span>
            </div>
            <h4 class="fw-bold text-success m-0">${{ number_format($totalCollectedYear, 2, ',', '.') }}</h4>
            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Cobranzas aprobadas del período</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="ios-card p-3 h-100 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Facturado Expensas ({{ $selectedYear }})</span>
                <span class="badge bg-primary-subtle text-primary rounded-pill p-2"><i class="bi bi-receipt fs-6"></i></span>
            </div>
            <h4 class="fw-bold text-primary m-0">${{ number_format($totalInvoicedYear, 2, ',', '.') }}</h4>
            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Total emitido en expensas</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="ios-card p-3 h-100 border-start border-4 border-danger">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Deuda Activa Total</span>
                <span class="badge bg-danger-subtle text-danger rounded-pill p-2"><i class="bi bi-exclamation-octagon-fill fs-6"></i></span>
            </div>
            <h4 class="fw-bold text-danger m-0">${{ number_format($totalDebt, 2, ',', '.') }}</h4>
            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">{{ $debtors->count() }} lotes con saldo pendiente</small>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="ios-card p-3 h-100 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Gastos Proveedores</span>
                <span class="badge bg-warning-subtle text-warning rounded-pill p-2"><i class="bi bi-truck fs-6"></i></span>
            </div>
            <h4 class="fw-bold text-warning m-0">${{ number_format($totalSupplierExpensesYear, 2, ',', '.') }}</h4>
            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Facturas cargadas de proveedores</small>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-pills mb-4 gap-2 border-bottom border-ios pb-3" id="reportsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill px-3 py-2 fw-semibold" id="finances-tab" data-bs-toggle="tab" data-bs-target="#finances" type="button" role="tab">
            <i class="bi bi-graph-up-arrow me-1"></i> Finanzas y Cobranzas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-3 py-2 fw-semibold position-relative" id="debtors-tab" data-bs-toggle="tab" data-bs-target="#debtors" type="button" role="tab">
            <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Ranking de Morosidad
            @if($debtors->count() > 0)
                <span class="badge bg-danger rounded-pill ms-1">{{ $debtors->count() }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="suppliers-tab" data-bs-toggle="tab" data-bs-target="#suppliers" type="button" role="tab">
            <i class="bi bi-truck me-1"></i> Proveedores y Gastos
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets" type="button" role="tab">
            <i class="bi bi-chat-left-text-fill me-1"></i> Reclamos e Incidencias
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="lots-tab" data-bs-toggle="tab" data-bs-target="#lots" type="button" role="tab">
            <i class="bi bi-house-door-fill me-1"></i> Padrón de Lotes ({{ $totalLots }})
        </button>
    </li>
</ul>

<!-- Tab Contents -->
<div class="tab-content" id="reportsTabContent">

    <!-- TAB 1: FINANZAS Y COBRANZAS -->
    <div class="tab-pane fade show active" id="finances" role="tabpanel">
        <div class="row g-4 mb-4">
            <!-- Monthly collection table -->
            <div class="col-lg-6">
                <div class="ios-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0 text-success"><i class="bi bi-cash-stack me-2"></i>Historial de Cobranzas Mensuales</h6>
                        <span class="badge bg-success-subtle text-success">Últimos 12 meses</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.8rem;">PERÍODO</th>
                                    <th class="text-muted text-center" style="font-size: 0.8rem;">PAGOS APROBADOS</th>
                                    <th class="text-muted text-end" style="font-size: 0.8rem;">TOTAL COBRADO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyCollection as $col)
                                    <tr class="border-bottom border-ios">
                                        <td class="fw-semibold">{{ \Carbon\Carbon::createFromFormat('Y-m', $col->month)->isoFormat('MMMM Y') }}</td>
                                        <td class="text-center"><span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">{{ $col->count }} transacciones</span></td>
                                        <td class="text-end fw-bold text-success">${{ number_format($col->total_collected, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No se registran pagos aprobados en los períodos analizados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Monthly expenses billed table -->
            <div class="col-lg-6">
                <div class="ios-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0 text-primary"><i class="bi bi-receipt me-2"></i>Facturación de Expensas</h6>
                        <span class="badge bg-primary-subtle text-primary">Liquidaciones Emitidas</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.8rem;">PERÍODO</th>
                                    <th class="text-muted text-center" style="font-size: 0.8rem;">UNIDADES</th>
                                    <th class="text-muted text-end" style="font-size: 0.8rem;">TOTAL EMITIDO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyExpenses as $exp)
                                    <tr class="border-bottom border-ios">
                                        <td class="fw-semibold">{{ \Carbon\Carbon::createFromFormat('Y-m', $exp->month)->isoFormat('MMMM Y') }}</td>
                                        <td class="text-center"><span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">{{ $exp->count }} lotes</span></td>
                                        <td class="text-end fw-bold text-primary">${{ number_format($exp->total_billed, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No se registran expensas liquidadas en los períodos analizados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consolidated Account Balances -->
        <div class="ios-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-wallet2 text-success me-2"></i>Resumen de Balances de Cuentas Corrientes</h6>
            <div class="row text-center g-3">
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-body-secondary">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">SALDO TOTAL DEUDOR</span>
                        <h4 class="fw-bold text-danger mt-1 m-0">${{ number_format($totalDebt, 2, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 0.75rem;">Mora acumulada del consorcio</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-body-secondary">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">SALDO TOTAL A FAVOR</span>
                        <h4 class="fw-bold text-success mt-1 m-0">${{ number_format($totalSurplus, 2, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 0.75rem;">Créditos y pagos adelantados</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-3 bg-body-secondary">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">POSICIÓN NETA CON RESIDENTES</span>
                        @php $netPosition = $totalDebt - $totalSurplus; @endphp
                        <h4 class="fw-bold {{ $netPosition > 0 ? 'text-danger' : 'text-success' }} mt-1 m-0">
                            ${{ number_format($netPosition, 2, ',', '.') }}
                        </h4>
                        <small class="text-muted" style="font-size: 0.75rem;">Balance global consorcio</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: RANKING DE MOROSIDAD -->
    <div class="tab-pane fade" id="debtors" role="tabpanel">
        <div class="ios-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h5 class="fw-bold m-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Ranking de Lotes Deudores</h5>
                    <small class="text-muted">Listado ordenado de mayor a menor saldo pendiente de pago.</small>
                </div>
                <a href="{{ route('admin.reports.export', ['type' => 'debtors']) }}" class="btn btn-ios btn-outline-danger btn-sm">
                    <i class="bi bi-download me-1"></i> Descargar Listado de Deudores (CSV)
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.8rem; width: 80px;">POS</th>
                            <th class="text-muted" style="font-size: 0.8rem;">LOTE</th>
                            <th class="text-muted" style="font-size: 0.8rem;">PROPIETARIO / RESPONSABLE</th>
                            <th class="text-muted" style="font-size: 0.8rem;">CONTACTO</th>
                            <th class="text-muted" style="font-size: 0.8rem;">ESTADO LOTE</th>
                            <th class="text-muted text-end" style="font-size: 0.8rem;">SALDO DEUDOR</th>
                            <th class="text-muted text-end" style="font-size: 0.8rem;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debtors as $idx => $d)
                            <tr class="border-bottom border-ios">
                                <td class="fw-bold text-muted">#{{ $idx + 1 }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 fs-6">
                                        Lote {{ $d->number }}
                                    </span>
                                    <small class="d-block text-muted" style="font-size: 0.75rem;">{{ $d->code }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $d->owner?->full_name ?? 'Sin propietario asignado' }}</span>
                                    @if($d->tenant)
                                        <small class="d-block text-muted" style="font-size: 0.75rem;">Inquilino: {{ $d->tenant->full_name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div><i class="bi bi-telephone text-muted me-1"></i>{{ $d->owner?->phone ?? 'Sin teléfono' }}</div>
                                    <small class="text-muted"><i class="bi bi-envelope text-muted me-1"></i>{{ $d->owner?->email ?? 'Sin email' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary text-capitalize">
                                        {{ str_replace('_', ' ', $d->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <h6 class="fw-bold text-danger m-0">${{ number_format($d->balance, 2, ',', '.') }}</h6>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.lots.show', $d->id) }}" class="btn btn-sm btn-ios btn-outline-secondary py-1 px-2" title="Ver Lote">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-success fs-1 mb-2"><i class="bi bi-check-circle-fill"></i></div>
                                    <h6 class="fw-bold text-success">¡Excelente! No hay lotes con saldo deudor en el consorcio.</h6>
                                    <p class="text-muted m-0" style="font-size: 0.85rem;">Todos los propietarios se encuentran al día con sus expensas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: PROVEEDORES Y GASTOS -->
    <div class="tab-pane fade" id="suppliers" role="tabpanel">
        <div class="row g-4 mb-4">
            <!-- Expenses by supplier -->
            <div class="col-lg-6">
                <div class="ios-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0 text-warning"><i class="bi bi-truck me-2"></i>Gastos por Proveedor</h6>
                        <span class="badge bg-warning-subtle text-warning">{{ $supplierTotals->count() }} Proveedores</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.8rem;">PROVEEDOR</th>
                                    <th class="text-muted text-center" style="font-size: 0.8rem;">FACTURAS</th>
                                    <th class="text-muted text-end" style="font-size: 0.8rem;">TOTAL ($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplierTotals as $st)
                                    <tr class="border-bottom border-ios">
                                        <td>
                                            <span class="fw-bold">{{ $st->supplier?->name ?? 'Proveedor General' }}</span>
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">CUIT: {{ $st->supplier?->tax_id ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">{{ $st->count }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">${{ number_format($st->total_amount, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No hay facturas de proveedores registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent supplier invoices -->
            <div class="col-lg-6">
                <div class="ios-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0 text-dark"><i class="bi bi-clock-history me-2"></i>Últimos Comprobantes de Gastos</h6>
                        <a href="{{ route('admin.supplier-invoices.index') }}" class="btn btn-sm btn-link text-success text-decoration-none p-0">Ver todos</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.8rem;">FECHA</th>
                                    <th class="text-muted" style="font-size: 0.8rem;">PROVEEDOR / CONCEPTO</th>
                                    <th class="text-muted text-end" style="font-size: 0.8rem;">MONTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSupplierInvoices as $inv)
                                    <tr class="border-bottom border-ios">
                                        <td class="fw-semibold text-muted" style="font-size: 0.8rem;">
                                            {{ $inv->issue_date?->format('d/m/Y') ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $inv->supplier?->name ?? 'Proveedor' }}</span>
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">{{ $inv->concept ?: ($inv->invoice_number ? 'Factura #' . $inv->invoice_number : 'Gasto operativo') }}</small>
                                        </td>
                                        <td class="text-end fw-bold text-dark">${{ number_format($inv->amount, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No hay comprobantes recientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 4: RECLAMOS E INCIDENCIAS -->
    <div class="tab-pane fade" id="tickets" role="tabpanel">
        <div class="row g-4">
            <!-- Tickets by category -->
            <div class="col-lg-6">
                <div class="ios-card h-100">
                    <h6 class="fw-bold mb-3 text-info"><i class="bi bi-pie-chart-fill me-2"></i>Reclamos por Categoría</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.8rem;">CATEGORÍA</th>
                                    <th class="text-muted text-end" style="font-size: 0.8rem;">CANTIDAD DE TICKETS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketsByCategory as $tc)
                                    <tr class="border-bottom border-ios">
                                        <td class="fw-semibold">{{ $tc->category?->display_name ?? 'General / Sin categoría' }}</td>
                                        <td class="text-end fw-bold text-dark">{{ $tc->count }} reclamos</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted">No hay reclamos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tickets by status -->
            <div class="col-lg-6">
                <div class="ios-card h-100">
                    <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-activity me-2"></i>Estado de Resolución de Reclamos</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.8rem;">ESTADO</th>
                                    <th class="text-muted text-end" style="font-size: 0.8rem;">CANTIDAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketsByStatus as $ts)
                                    <tr class="border-bottom border-ios">
                                        <td>
                                            <span class="badge text-capitalize px-2 py-1
                                                @if($ts->status === 'closed' || $ts->status === 'resolved') bg-success-subtle text-success
                                                @elseif($ts->status === 'open') bg-danger-subtle text-danger
                                                @else bg-warning-subtle text-warning @endif">
                                                {{ str_replace('_', ' ', $ts->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">{{ $ts->count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted">No hay estadísticas de reclamos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: PADRÓN DE LOTES Y OCUPACIÓN -->
    <div class="tab-pane fade" id="lots" role="tabpanel">
        <div class="ios-card mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-success me-2"></i>Distribución por Estado de Ocupación y Construcción</h6>
            <div class="row g-3">
                @foreach($lotsByStatus as $lbs)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="p-3 bg-body-secondary rounded-3 text-center">
                            <span class="badge bg-secondary text-capitalize mb-2">{{ str_replace('_', ' ', $lbs->status) }}</span>
                            <h4 class="fw-bold m-0">{{ $lbs->count }} Lotes</h4>
                            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">
                                Deuda: ${{ number_format($lbs->total_debt, 2, ',', '.') }}
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-list-ul me-2"></i>Padrón Completo de Unidades Funcionales</h6>
                <a href="{{ route('admin.reports.export', ['type' => 'lots']) }}" class="btn btn-ios btn-outline-success btn-sm">
                    <i class="bi bi-download me-1"></i> Descargar Padrón (CSV)
                </a>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios sticky-top bg-body">
                            <th class="text-muted" style="font-size: 0.8rem;">LOTE</th>
                            <th class="text-muted" style="font-size: 0.8rem;">CÓDIGO</th>
                            <th class="text-muted" style="font-size: 0.8rem;">PROPIETARIO</th>
                            <th class="text-muted" style="font-size: 0.8rem;">INQUILINO</th>
                            <th class="text-muted" style="font-size: 0.8rem;">ESTADO</th>
                            <th class="text-muted text-end" style="font-size: 0.8rem;">SALDO ACTUAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lotsSummary as $l)
                            <tr class="border-bottom border-ios">
                                <td class="fw-bold text-success">Lote {{ $l->number }}</td>
                                <td class="text-muted" style="font-size: 0.85rem;">{{ $l->code }}</td>
                                <td>{{ $l->owner?->full_name ?? 'Sin asignar' }}</td>
                                <td>{{ $l->tenant?->full_name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary text-capitalize" style="font-size: 0.75rem;">
                                        {{ str_replace('_', ' ', $l->status) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold {{ $l->balance > 0 ? 'text-danger' : ($l->balance < 0 ? 'text-success' : 'text-muted') }}">
                                    ${{ number_format($l->balance, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
