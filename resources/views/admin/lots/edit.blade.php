@extends('layouts.app')

@section('title', 'Editar Lote')
@section('page_title', 'Modificar Lote')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.lots.update', $lot) }}">
            @csrf
            @method('PUT')

            <!-- General Lot Data -->
            <div class="ios-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0"><i class="bi bi-house-door-fill text-success me-2"></i>Datos del Lote</h5>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios">ID: {{ $lot->id }}</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="number" class="form-label fw-semibold" style="font-size: 0.85rem;">Número de Lote</label>
                        <input type="text" name="number" id="number" class="form-control form-control-ios @error('number') is-invalid @enderror" value="{{ old('number', $lot->number) }}" required>
                        @error('number')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="code" class="form-label fw-semibold" style="font-size: 0.85rem;">Código de Lote</label>
                        <input type="text" name="code" id="code" class="form-control form-control-ios @error('code') is-invalid @enderror" value="{{ old('code', $lot->code) }}" required>
                        @error('code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre Identificador (Opcional)</label>
                        <input type="text" name="name" id="name" class="form-control form-control-ios @error('name') is-invalid @enderror" value="{{ old('name', $lot->name) }}">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="internal_address" class="form-label fw-semibold" style="font-size: 0.85rem;">Dirección Interna (Calle)</label>
                        <input type="text" name="internal_address" id="internal_address" class="form-control form-control-ios @error('internal_address') is-invalid @enderror" value="{{ old('internal_address', $lot->internal_address) }}">
                        @error('internal_address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold" style="font-size: 0.85rem;">Estado Físico</label>
                        <select name="status" id="status" class="form-select form-control-ios @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $lot->status) === 'active' ? 'selected' : '' }}>Activo (Construido / Habitado)</option>
                            <option value="under_construction" {{ old('status', $lot->status) === 'under_construction' ? 'selected' : '' }}>En Obra / Construcción</option>
                            <option value="vacant" {{ old('status', $lot->status) === 'vacant' ? 'selected' : '' }}>Baldío / Vacante</option>
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="balance" class="form-label fw-semibold" style="font-size: 0.85rem;">Saldo Actual ($)</label>
                        <input type="number" step="0.01" name="balance" id="balance" class="form-control form-control-ios @error('balance') is-invalid @enderror" value="{{ old('balance', $lot->balance) }}" required>
                        @error('balance')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Usa valores positivos para deudas y negativos para saldos a favor.</small>
                    </div>
                </div>
            </div>

            <!-- Associations -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-people-fill text-success me-2"></i>Asignación de Titulares</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="current_owner_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Propietario Actual</label>
                        <select name="current_owner_id" id="current_owner_id" class="form-select form-control-ios @error('current_owner_id') is-invalid @enderror">
                            <option value="">Sin propietario asignado</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('current_owner_id', $lot->current_owner_id) == $owner->id ? 'selected' : '' }}>
                                    {{ $owner->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('current_owner_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="current_tenant_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Inquilino Ocupante (Opcional)</label>
                        <select name="current_tenant_id" id="current_tenant_id" class="form-select form-control-ios @error('current_tenant_id') is-invalid @enderror">
                            <option value="">Sin inquilino</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ old('current_tenant_id', $lot->current_tenant_id) == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('current_tenant_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text-fill text-success me-2"></i>Observaciones</h5>
                <textarea name="notes" id="notes" rows="4" class="form-control form-control-ios" placeholder="Notas sobre el lote...">{{ old('notes', $lot->notes) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.lots.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
