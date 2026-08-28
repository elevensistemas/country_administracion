@extends('layouts.app')

@section('title', 'Planificador de Pagos')
@section('page_title', 'Control de Pagos Semanales')

@section('content')
<!-- KPI Row: Cash Needed for Planning -->
<div class="row">
    <!-- Total Overdue Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card d-flex align-items-center bg-danger-subtle border-danger-subtle">
            <div class="bg-danger text-white rounded-4 p-3 me-3">
                <i class="bi bi-calendar-x-fill" style="font-size: 1.8rem;"></i>
            </div>
            <div>
                <span class="text-danger d-block fw-bold" style="font-size: 0.8rem;">Vencido Impago</span>
                <h4 class="fw-bold m-0 text-danger">${{ number_format($totalOverdue, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- This Week Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card d-flex align-items-center bg-warning-subtle border-warning-subtle">
            <div class="bg-warning text-dark rounded-4 p-3 me-3">
                <i class="bi bi-calendar-event-fill" style="font-size: 1.8rem;"></i>
            </div>
            <div>
                <span class="text-warning-dark d-block fw-bold" style="font-size: 0.8rem; color: #856404;">Esta Semana</span>
                <h4 class="fw-bold m-0" style="color: #856404;">${{ number_format($totalThisWeek, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- Next Week Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card d-flex align-items-center bg-info-subtle border-info-subtle">
            <div class="bg-info text-dark rounded-4 p-3 me-3">
                <i class="bi bi-calendar-week-fill" style="font-size: 1.8rem;"></i>
            </div>
            <div>
                <span class="text-info-dark d-block fw-bold" style="font-size: 0.8rem; color: #0c5460;">Próxima Semana</span>
                <h4 class="fw-bold m-0" style="color: #0c5460;">${{ number_format($totalNextWeek, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- Future Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card d-flex align-items-center bg-light border-light">
            <div class="bg-secondary text-white rounded-4 p-3 me-3">
                <i class="bi bi-calendar2-check-fill" style="font-size: 1.8rem;"></i>
            </div>
            <div>
                <span class="text-secondary d-block fw-bold" style="font-size: 0.8rem;">Previsión Futura</span>
                <h4 class="fw-bold m-0 text-secondary">${{ number_format($totalFuture, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.supplier-invoices.index') }}" class="row g-3 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-ios" placeholder="Buscar por concepto o factura..." value="{{ request('search') }}">
        </div>

        <div class="col-md-3">
            <select name="supplier_id" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todos los Proveedores</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->business_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Excluir Anuladas</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Solo Pendientes</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Solo Programadas</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Solo Pagadas</option>
                <option value="voided" {{ request('status') === 'voided' ? 'selected' : '' }}>Solo Anuladas</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <a href="{{ route('admin.supplier-invoices.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Main Planning Lists -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold m-0">Agenda Semanal de Pagos</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.supplier-invoices.print', request()->query()) }}" target="_blank" class="btn btn-ios btn-ios-secondary"><i class="bi bi-printer-fill me-2"></i>Imprimir Agenda</a>
        <a href="{{ route('admin.supplier-invoices.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-plus-circle me-2"></i>Nueva Factura</a>
    </div>
</div>

@php
    $sections = [
        ['title' => 'Facturas Vencidas Impagas', 'icon' => 'bi-exclamation-triangle-fill', 'textClass' => 'text-danger', 'bgClass' => 'bg-danger-subtle', 'list' => $overdueInvoices, 'amount' => $totalOverdue],
        ['title' => 'Vencen Esta Semana', 'icon' => 'bi-calendar-event-fill', 'textClass' => 'text-warning-dark', 'bgClass' => 'bg-warning-subtle', 'list' => $thisWeekInvoices, 'amount' => $totalThisWeek],
        ['title' => 'Vencen Próxima Semana', 'icon' => 'bi-calendar-week-fill', 'textClass' => 'text-info-dark', 'bgClass' => 'bg-info-subtle', 'list' => $nextWeekInvoices, 'amount' => $totalNextWeek],
        ['title' => 'Vencimientos Posteriores (Previsión)', 'icon' => 'bi-calendar2-check-fill', 'textClass' => 'text-secondary', 'bgClass' => 'bg-light', 'list' => $futureInvoices, 'amount' => $totalFuture],
    ];
@endphp

@foreach($sections as $sec)
    <div class="ios-card mb-4 border-0 shadow-sm">
        <div class="p-3 {{ $sec['bgClass'] }} rounded-top-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0 {{ $sec['textClass'] }}">
                <i class="bi {{ $sec['icon'] }} me-2"></i>{{ $sec['title'] }} ({{ $sec['list']->count() }})
            </h6>
            <span class="badge rounded-pill bg-white {{ $sec['textClass'] }} border px-3 py-1.5 fw-bold" style="font-size: 0.85rem;">
                Total: ${{ number_format($sec['amount'], 2, ',', '.') }}
            </span>
        </div>

        <div class="p-0">
            <!-- Table View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle m-0">
                    <tbody>
                        @forelse($sec['list'] as $invoice)
                            <tr class="border-bottom border-ios">
                                <td style="width: 25%;" class="ps-3">
                                    <h6 class="fw-bold m-0" style="font-size: 0.95rem;">
                                        <a href="{{ route('admin.supplier-invoices.show', $invoice) }}" class="text-decoration-none text-reset">
                                            {{ $invoice->supplier->business_name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">CUIT: {{ $invoice->supplier->cuit }}</small>
                                </td>
                                <td style="width: 35%;">
                                    <span class="d-block" style="font-size: 0.9rem;">{{ $invoice->concept }}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">Factura: {{ $invoice->invoice_number }}</small>
                                </td>
                                <td style="width: 15%;">
                                    <span class="d-block" style="font-size: 0.85rem; font-weight: 500;">
                                        Vencimiento: {{ $invoice->due_date->format('d/m/Y') }}
                                    </span>
                                    @php
                                        $days = Carbon::today()->diffInDays($invoice->due_date, false);
                                    @endphp
                                    @if($days < 0)
                                        <small class="text-danger fw-bold" style="font-size: 0.75rem;">Vencida hace {{ abs($days) }} días</small>
                                    @elseif($days === 0)
                                        <small class="text-warning fw-bold" style="font-size: 0.75rem;">Vence hoy</small>
                                    @else
                                        <small class="text-muted" style="font-size: 0.75rem;">En {{ $days }} días</small>
                                    @endif
                                </td>
                                <td style="width: 10%;">
                                    @if($invoice->status === 'scheduled')
                                        <span class="badge bg-info-subtle text-info badge-ios">Programado</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning badge-ios">Pendiente</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark text-end" style="width: 15%; font-size: 1rem;">
                                    ${{ number_format($invoice->amount, 2, ',', '.') }}
                                </td>
                                <td class="text-end pe-3" style="width: 10%;">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.supplier-invoices.show', $invoice) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Detalle y CBU">
                                            <i class="bi bi-file-earmark-text-fill"></i>
                                        </a>
                                        <a href="{{ route('admin.supplier-invoices.edit', $invoice) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('admin.supplier-invoices.destroy', $invoice) }}" method="POST" class="d-inline form-delete" data-invoice-info="{{ $invoice->supplier->business_name }} - {{ $invoice->invoice_number }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger" title="Eliminar">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                                    No hay facturas registradas en este período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-block d-md-none p-2">
                @forelse($sec['list'] as $invoice)
                    <div class="p-3 border-bottom border-ios rounded-3 bg-body-tertiary mb-2">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold m-0" style="font-size: 0.95rem;">
                                    <a href="{{ route('admin.supplier-invoices.show', $invoice) }}" class="text-decoration-none">
                                        {{ $invoice->supplier->business_name }}
                                    </a>
                                </h6>
                                <small class="text-muted d-block">Factura: {{ $invoice->invoice_number }}</small>
                            </div>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                                ${{ number_format($invoice->amount, 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="my-2" style="font-size: 0.8rem;">
                            <div><strong>Concepto:</strong> {{ $invoice->concept }}</div>
                            <div>
                                <strong>Vencimiento:</strong> {{ $invoice->due_date->format('d/m/Y') }}
                                @php
                                    $days = Carbon::today()->diffInDays($invoice->due_date, false);
                                @endphp
                                @if($days < 0)
                                    <span class="text-danger fw-bold ms-1">(Hace {{ abs($days) }} días)</span>
                                @elseif($days === 0)
                                    <span class="text-warning fw-bold ms-1">(Vence hoy)</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-ios">
                            @if($invoice->status === 'scheduled')
                                <span class="badge bg-info-subtle text-info badge-ios">Programado</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning badge-ios">Pendiente</span>
                            @endif
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.supplier-invoices.show', $invoice) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success px-2 py-1">Detalle</a>
                                <a href="{{ route('admin.supplier-invoices.edit', $invoice) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary px-2 py-1">Editar</a>
                                <form action="{{ route('admin.supplier-invoices.destroy', $invoice) }}" method="POST" class="d-inline form-delete" data-invoice-info="{{ $invoice->supplier->business_name }} - {{ $invoice->invoice_number }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger px-2 py-1">Borrar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted" style="font-size: 0.8rem;">
                        No hay facturas registradas en este período.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endforeach

<!-- PAID INVOICES SECTION (COLLAPSED BY DEFAULT) -->
<div class="ios-card">
    <div class="accordion accordion-flush" id="accordionPaid">
        <div class="accordion-item bg-transparent">
            <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button collapsed bg-transparent fw-bold text-success px-0" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    <i class="bi bi-check-circle-fill me-2 text-success"></i> Historial de Facturas Pagadas (Últimas 50)
                </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionPaid">
                <div class="accordion-body px-0 pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.8rem;">PROVEEDOR</th>
                                    <th class="text-muted" style="font-size: 0.8rem;">CONCEPTO</th>
                                    <th class="text-muted" style="font-size: 0.8rem;">EMISION</th>
                                    <th class="text-muted" style="font-size: 0.8rem;">PAGO</th>
                                    <th class="text-muted text-end" style="font-size: 0.8rem;">IMPORTE</th>
                                    <th class="text-muted text-end" style="font-size: 0.8rem; width: 10%;">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paidInvoices->take(50) as $invoice)
                                    <tr class="border-bottom border-ios">
                                        <td>
                                            <h6 class="fw-bold m-0" style="font-size: 0.9rem;">{{ $invoice->supplier->business_name }}</h6>
                                        </td>
                                        <td style="font-size: 0.85rem;">
                                            <span>{{ $invoice->concept }}</span>
                                            <small class="text-muted d-block">Factura: {{ $invoice->invoice_number }}</small>
                                        </td>
                                        <td style="font-size: 0.85rem;">{{ $invoice->issue_date->format('d/m/Y') }}</td>
                                        <td style="font-size: 0.85rem;">
                                            <span class="badge bg-success text-white badge-ios">Pagado</span>
                                        </td>
                                        <td class="fw-bold text-success text-end" style="font-size: 0.9rem;">
                                            ${{ number_format($invoice->amount, 2, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('admin.supplier-invoices.show', $invoice) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Detalle"><i class="bi bi-file-earmark-text-fill"></i></a>
                                                <a href="{{ route('admin.supplier-invoices.edit', $invoice) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No hay registros de facturas pagadas recientemente.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const info = this.getAttribute('data-invoice-info');
                if (confirm(`¿Estás seguro de que deseas eliminar la factura de "${info}"? Esta acción no se puede deshacer y borrará el comprobante.`)) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
