@extends('layouts.app')

@section('title', 'Detalle de Factura')
@section('page_title', 'Factura ' . $supplierInvoice->invoice_number)

@section('content')
<div class="row">
    <!-- Invoice details & bank info -->
    <div class="col-lg-6 mb-4">
        <!-- Main details -->
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h5 class="fw-bold m-0 text-success">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>{{ $supplierInvoice->supplier->business_name }}
                    </h5>
                    <small class="text-muted">CUIT: {{ $supplierInvoice->supplier->cuit }}</small>
                </div>
                <div>
                    @if($supplierInvoice->status === 'paid')
                        <span class="badge bg-success text-white badge-ios fs-7 px-3 py-2">Pagado</span>
                    @elseif($supplierInvoice->status === 'scheduled')
                        <span class="badge bg-info text-white badge-ios fs-7 px-3 py-2">Programado</span>
                    @elseif($supplierInvoice->status === 'voided')
                        <span class="badge bg-danger text-white badge-ios fs-7 px-3 py-2">Anulada</span>
                    @else
                        <span class="badge bg-warning text-dark badge-ios fs-7 px-3 py-2">Pendiente</span>
                    @endif
                </div>
            </div>

            <div class="row g-3" style="font-size: 0.9rem;">
                <div class="col-sm-6">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Número de Factura</span>
                    <strong class="text-dark">{{ $supplierInvoice->invoice_number }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Monto Total</span>
                    <strong class="text-dark fs-5">${{ number_format($supplierInvoice->amount, 2, ',', '.') }}</strong>
                </div>
                <div class="col-sm-12">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Concepto del Gasto</span>
                    <strong class="text-dark">{{ $supplierInvoice->concept }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Fecha de Emisión</span>
                    <strong class="text-dark">{{ $supplierInvoice->issue_date->format('d/m/Y') }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Fecha de Vencimiento</span>
                    <strong class="text-dark">{{ $supplierInvoice->due_date->format('d/m/Y') }}</strong>
                </div>
                @if($supplierInvoice->notes)
                    <div class="col-sm-12">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Notas</span>
                        <div class="p-2.5 bg-light rounded-3 mt-1" style="font-size: 0.85rem; border: 1px solid var(--ios-border);">
                            {{ $supplierInvoice->notes }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-top border-ios d-flex gap-2">
                <a href="{{ route('admin.supplier-invoices.edit', $supplierInvoice) }}" class="btn btn-ios btn-ios-secondary text-primary"><i class="bi bi-pencil-fill me-2"></i>Editar Factura</a>
                <form action="{{ route('admin.supplier-invoices.destroy', $supplierInvoice) }}" method="POST" class="d-inline form-delete" data-invoice-info="{{ $supplierInvoice->invoice_number }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ios btn-ios-secondary text-danger"><i class="bi bi-trash-fill me-2"></i>Eliminar</button>
                </form>
            </div>
        </div>

        <!-- Bank deposit copy clipboard box -->
        <div class="ios-card mt-4 border-success-subtle bg-success-subtle bg-opacity-25">
            <h6 class="fw-bold mb-3 text-success"><i class="bi bi-bank me-2 text-success"></i>Datos para Pago y Transferencia</h6>
            
            @if($supplierInvoice->supplier->bank_name || $supplierInvoice->supplier->cbu_alias)
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted m-0" style="font-size: 0.75rem;">Proveedor / Razón Social</label>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-dark flex-grow-1" id="val-business">{{ $supplierInvoice->supplier->business_name }}</span>
                            <button class="btn btn-sm btn-light border p-1 rounded-3 ms-2" onclick="copyToClipboard('val-business')"><i class="bi bi-clipboard2"></i> Copiar</button>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted m-0" style="font-size: 0.75rem;">CUIT del Proveedor</label>
                        <div class="d-flex align-items-center">
                            <span class="fw-bold text-dark flex-grow-1" id="val-cuit">{{ $supplierInvoice->supplier->cuit }}</span>
                            <button class="btn btn-sm btn-light border p-1 rounded-3 ms-2" onclick="copyToClipboard('val-cuit')"><i class="bi bi-clipboard2"></i> Copiar</button>
                        </div>
                    </div>

                    @if($supplierInvoice->supplier->bank_name)
                        <div class="col-12">
                            <label class="form-label text-muted m-0" style="font-size: 0.75rem;">Banco Destinatario</label>
                            <span class="fw-bold text-dark d-block">{{ $supplierInvoice->supplier->bank_name }}</span>
                        </div>
                    @endif

                    @if($supplierInvoice->supplier->cbu_alias)
                        <div class="col-12">
                            <label class="form-label text-muted m-0" style="font-size: 0.75rem;">CBU / Alias CBU</label>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold text-dark flex-grow-1" id="val-cbu">{{ $supplierInvoice->supplier->cbu_alias }}</span>
                                <button class="btn btn-sm btn-light border p-1 rounded-3 ms-2" onclick="copyToClipboard('val-cbu')"><i class="bi bi-clipboard2"></i> Copiar</button>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-exclamation-circle text-muted fs-3 mb-2 d-block"></i>
                    <span>Este proveedor no posee datos bancarios configurados.</span>
                    <div class="mt-2">
                        <a href="{{ route('admin.suppliers.edit', $supplierInvoice->supplier) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary">Agregar datos bancarios</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Visor de archivo adjunto -->
    <div class="col-lg-6 mb-4">
        <div class="ios-card h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-check me-2"></i>Archivo de Factura / Comprobante</h6>
            
            @if($supplierInvoice->file_path)
                @php
                    $extension = pathinfo($supplierInvoice->file_path, PATHINFO_EXTENSION);
                    $assetPath = asset('storage/' . $supplierInvoice->file_path);
                @endphp

                @if(strtolower($extension) === 'pdf')
                    <!-- PDF Viewer -->
                    <div style="height: 500px; border: 1px solid var(--ios-border);" class="rounded-3 overflow-hidden">
                        <iframe src="{{ $assetPath }}" width="100%" height="100%" style="border: none;"></iframe>
                    </div>
                @else
                    <!-- Image Viewer -->
                    <div class="text-center border rounded-3 p-3 bg-light d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <img src="{{ $assetPath }}" class="img-fluid rounded shadow-sm" style="max-height: 480px; object-fit: contain;" alt="Factura">
                    </div>
                @endif
                <div class="mt-3 text-center">
                    <a href="{{ $assetPath }}" target="_blank" class="btn btn-ios btn-ios-secondary"><i class="bi bi-box-arrow-up-right me-2"></i>Abrir en nueva pestaña</a>
                </div>
            @else
                <div class="d-flex flex-column align-items-center justify-content-center h-75 text-muted py-5">
                    <i class="bi bi-file-earmark-excel fs-1 mb-2 opacity-50"></i>
                    <span>No se ha adjuntado ningún comprobante digital para esta factura.</span>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="mb-5">
    <a href="{{ route('admin.supplier-invoices.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver al Planificador</a>
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

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const info = this.getAttribute('data-invoice-info');
                if (confirm(`¿Estás seguro de que deseas eliminar la factura "${info}"?`)) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
