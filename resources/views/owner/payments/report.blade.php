@extends('layouts.owner')

@section('title', 'Informar Pago')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <h4 class="fw-bold m-0 text-success"><i class="bi bi-wallet2 me-2"></i>Informar Pago</h4>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Completa los datos de la transferencia o depósito para conciliar tu saldo.</p>
        </div>

        <form method="POST" action="{{ route('owner.payments.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Property Display context -->
            <div class="ios-card bg-body-tertiary">
                <span class="text-muted d-block" style="font-size: 0.8rem;">Imputando pago al lote:</span>
                <strong class="text-dark fs-5">Lote {{ $activeLot->number }}</strong>
                <small class="text-muted d-block" style="font-size: 0.75rem;">UF Asociada: {{ $activeLot->functionalUnits()->first()?->code ?? 'N/C' }}</small>
            </div>

            <!-- Payment Details -->
            <div class="ios-card">
                <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Detalles de la Transacción</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label fw-semibold" style="font-size: 0.85rem;">Importe Transferido ($)</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control form-control-ios @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required placeholder="0.00">
                        @error('amount')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="payment_date" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha del Pago</label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control form-control-ios @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
                        @error('payment_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="payment_method" class="form-label fw-semibold" style="font-size: 0.85rem;">Medio de Pago</label>
                        <select name="payment_method" id="payment_method" class="form-select form-control-ios @error('payment_method') is-invalid @enderror" required>
                            <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transferencia Bancaria</option>
                            <option value="deposit" {{ old('payment_method') === 'deposit' ? 'selected' : '' }}>Depósito Bancario</option>
                            <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Otro Medio</option>
                        </select>
                        @error('payment_method')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="bank" class="form-label fw-semibold" style="font-size: 0.85rem;">Banco de Destino</label>
                        <select name="bank" id="bank" class="form-select form-control-ios @error('bank') is-invalid @enderror" required>
                            <option value="Banco Galicia" {{ old('bank') === 'Banco Galicia' ? 'selected' : '' }}>Banco Galicia (Cuenta Consorcio)</option>
                            <option value="Banco Nación" {{ old('bank') === 'Banco Nación' ? 'selected' : '' }}>Banco Nación (Cuenta Reserva)</option>
                        </select>
                        @error('bank')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="operation_number" class="form-label fw-semibold" style="font-size: 0.85rem;">Nro. Transacción / Operación</label>
                        <input type="text" name="operation_number" id="operation_number" class="form-control form-control-ios @error('operation_number') is-invalid @enderror" value="{{ old('operation_number') }}" required placeholder="Ej: 9812471">
                        @error('operation_number')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Receipt upload -->
            <div class="ios-card">
                <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Adjuntar Comprobante</h6>
                <p class="text-muted mb-3" style="font-size: 0.8rem; line-height: 1.4;">Sube una captura de pantalla, foto o archivo PDF del comprobante bancario. Límite: 10MB.</p>
                
                <div class="col-12">
                    <input type="file" name="receipt" id="receipt" class="form-control form-control-ios @error('receipt') is-invalid @enderror" required accept="image/*,application/pdf">
                    @error('receipt')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="ios-card">
                <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Notas Opcionales</h6>
                <textarea name="notes" id="notes" rows="2" class="form-control form-control-ios" placeholder="Aclaraciones adicionales sobre tu transferencia..." style="border-radius: 12px;">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('owner.dashboard') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-1"></i> Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4">Informar Pago</button>
            </div>
        </form>
    </div>
</div>
@endsection
