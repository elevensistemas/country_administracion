@extends('layouts.app')

@section('title', 'Perfil de Proveedor')
@section('page_title', $supplier->business_name)

@section('content')
<div class="row">
    <!-- Supplier Profile Card & Bank Info -->
    <div class="col-lg-4 mb-4">
        <!-- Profile Card -->
        <div class="ios-card">
            <div class="d-flex align-items-center mb-4">
                <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 55px; height: 55px; font-weight: 600; font-size: 1.25rem;">
                    {{ substr($supplier->business_name, 0, 1) }}
                </div>
                <div>
                    <h5 class="fw-bold m-0 text-success">{{ $supplier->business_name }}</h5>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios mt-1">{{ $supplier->category }}</span>
                </div>
            </div>

            <div class="row g-3" style="font-size: 0.9rem;">
                <div class="col-12">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">CUIT</span>
                    <strong class="text-dark">{{ $supplier->cuit }}</strong>
                </div>

                @if($supplier->email)
                    <div class="col-12">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Email</span>
                        <a href="mailto:{{ $supplier->email }}" class="text-success text-decoration-none fw-semibold">{{ $supplier->email }}</a>
                    </div>
                @endif

                @if($supplier->phone)
                    <div class="col-12">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Teléfono</span>
                        <strong class="text-dark">{{ $supplier->phone }}</strong>
                    </div>
                @endif

                @if($supplier->address)
                    <div class="col-12">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Dirección</span>
                        <strong class="text-dark">{{ $supplier->address }}</strong>
                    </div>
                @endif

                <div class="col-12">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Estado en el Sistema</span>
                    @if($supplier->status === 'active')
                        <span class="badge bg-success text-white badge-ios">Activo</span>
                    @else
                        <span class="badge bg-danger text-white badge-ios">Inactivo</span>
                    @endif
                </div>
            </div>

            <div class="mt-4 pt-3 border-top border-ios d-grid">
                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-ios btn-ios-secondary text-primary"><i class="bi bi-pencil-fill me-2"></i>Editar Proveedor</a>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="ios-card mt-4 bg-success-subtle bg-opacity-25 border-success-subtle">
            <h6 class="fw-bold mb-3 text-success"><i class="bi bi-bank me-2 text-success"></i>Datos de Cuenta / Depósito</h6>
            
            @if($supplier->bank_name || $supplier->cbu_alias)
                <div class="row g-3" style="font-size: 0.9rem;">
                    @if($supplier->bank_name)
                        <div class="col-12">
                            <span class="text-muted d-block" style="font-size: 0.75rem;">Banco</span>
                            <strong class="text-dark">{{ $supplier->bank_name }}</strong>
                        </div>
                    @endif

                    @if($supplier->cbu_alias)
                        <div class="col-12">
                            <span class="text-muted d-block" style="font-size: 0.75rem;">CBU / Alias</span>
                            <div class="d-flex align-items-center">
                                <strong class="text-dark flex-grow-1" id="val-cbu">{{ $supplier->cbu_alias }}</strong>
                                <button class="btn btn-sm btn-light border p-1 rounded-3 ms-2" onclick="copyToClipboard('val-cbu')"><i class="bi bi-clipboard2"></i> Copiar</button>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-muted m-0" style="font-size: 0.85rem;">No se han registrado datos de cuenta bancaria.</p>
            @endif
        </div>
    </div>

    <!-- Stats & Invoices History List -->
    <div class="col-lg-8 mb-4">
        <!-- Stats Widgets -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="ios-card bg-success-subtle border-success-subtle d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                    <div>
                        <span class="text-success d-block fw-semibold" style="font-size: 0.8rem;">Total Abonado</span>
                        <h4 class="fw-bold m-0 text-success">${{ number_format($totalPaid, 2, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="ios-card bg-warning-subtle border-warning-subtle d-flex align-items-center">
                    <div class="bg-warning text-dark rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <span class="text-warning-dark d-block fw-semibold" style="font-size: 0.8rem; color: #856404;">Pendiente de Pago</span>
                        <h4 class="fw-bold m-0" style="color: #856404;">${{ number_format($totalPending, 2, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Historial de Facturas</h5>
                <a href="{{ route('admin.supplier-invoices.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-ios btn-ios-primary btn-sm"><i class="bi bi-plus-circle me-2"></i>Nueva Factura</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.8rem;">FACTURA</th>
                            <th class="text-muted" style="font-size: 0.8rem;">CONCEPTO</th>
                            <th class="text-muted" style="font-size: 0.8rem;">EMISIÓN / VTO</th>
                            <th class="text-muted" style="font-size: 0.8rem;">ESTADO</th>
                            <th class="text-muted text-end" style="font-size: 0.8rem;">IMPORTE</th>
                            <th class="text-muted text-end" style="font-size: 0.8rem; width: 15%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr class="border-bottom border-ios">
                                <td class="fw-semibold">{{ $invoice->invoice_number }}</td>
                                <td style="font-size: 0.85rem;">{{ $invoice->concept }}</td>
                                <td style="font-size: 0.85rem;">
                                    <span class="d-block">Emisión: {{ $invoice->issue_date->format('d/m/Y') }}</span>
                                    <small class="text-muted">Vto: {{ $invoice->due_date->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($invoice->status === 'paid')
                                        <span class="badge bg-success text-white badge-ios">Pagado</span>
                                    @elseif($invoice->status === 'scheduled')
                                        <span class="badge bg-info text-white badge-ios">Programado</span>
                                    @elseif($invoice->status === 'voided')
                                        <span class="badge bg-danger text-white badge-ios">Anulada</span>
                                    @else
                                        <span class="badge bg-warning text-dark badge-ios">Pendiente</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-end" style="font-size: 0.95rem;">
                                    ${{ number_format($invoice->amount, 2, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.supplier-invoices.show', $invoice) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Ver Detalle"><i class="bi bi-file-earmark-text-fill"></i></a>
                                        <a href="{{ route('admin.supplier-invoices.edit', $invoice) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-x text-muted fs-1 mb-2 d-block"></i>
                                    <span>No hay facturas cargadas para este proveedor.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver a Proveedores</a>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Copiado al portapapeles: ' + text);
        }).catch(err => {
            console.error('Error al copiar: ', err);
        });
    }
</script>
@endsection
