@extends('layouts.app')

@section('title', 'Nueva Factura')
@section('page_title', 'Registrar Factura de Proveedor')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.supplier-invoices.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Invoice Details -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-arrow-up-fill text-success me-2"></i>Detalle del Comprobante</h5>
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="supplier_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Proveedor</label>
                        <select name="supplier_id" id="supplier_id" class="form-select form-control-ios @error('supplier_id') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Proveedor --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->business_name }} (CUIT: {{ $supplier->cuit }})
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="invoice_number" class="form-label fw-semibold" style="font-size: 0.85rem;">Número de Factura</label>
                        <input type="text" name="invoice_number" id="invoice_number" class="form-control form-control-ios @error('invoice_number') is-invalid @enderror" value="{{ old('invoice_number') }}" required placeholder="Ej. 0002-00045612">
                        @error('invoice_number')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="amount" class="form-label fw-semibold" style="font-size: 0.85rem;">Monto Total ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);">$</span>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control form-control-ios border-start-0 @error('amount') is-invalid @enderror" style="border-radius: 0 12px 12px 0;" value="{{ old('amount') }}" required placeholder="0.00">
                        </div>
                        @error('amount')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="concept" class="form-label fw-semibold" style="font-size: 0.85rem;">Concepto / Detalle del Gasto</label>
                        <input type="text" name="concept" id="concept" class="form-control form-control-ios @error('concept') is-invalid @enderror" value="{{ old('concept') }}" required placeholder="Ej. Abono limpieza quincena julio">
                        @error('concept')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dates & Status -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-calendar-event text-success me-2"></i>Fechas y Programación</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="issue_date" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha de Emisión</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control form-control-ios @error('issue_date') is-invalid @enderror" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                        @error('issue_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="due_date" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha de Vencimiento</label>
                        <input type="date" name="due_date" id="due_date" class="form-control form-control-ios @error('due_date') is-invalid @enderror" value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required>
                        @error('due_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="status" class="form-label fw-semibold" style="font-size: 0.85rem;">Estado de la Factura</label>
                        <select name="status" id="status" class="form-select form-control-ios @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pendiente de Pago</option>
                            <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Pago Programado</option>
                            <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Pagado</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Invoice attachment (PDF or image) -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-image text-success me-2"></i>Adjuntar Comprobante (Opcional)</h5>
                
                <div class="row g-3">
                    <div class="col-12">
                        <label for="file" class="form-label fw-semibold" style="font-size: 0.85rem;">Archivo de la Factura (PDF, PNG, JPG, JPEG)</label>
                        <input type="file" name="file" id="file" class="form-control form-control-ios @error('file') is-invalid @enderror">
                        <small class="text-muted d-block mt-1">Tamaño máximo permitido: 5MB</small>
                        @error('file')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text-fill text-success me-2"></i>Notas / Observaciones</h5>
                <textarea name="notes" id="notes" rows="3" class="form-control form-control-ios" placeholder="Comentarios adicionales sobre esta factura...">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.supplier-invoices.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Cargar Factura</button>
            </div>
        </form>
    </div>
</div>
@endsection
