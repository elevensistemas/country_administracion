@extends('layouts.app')

@section('title', 'Editar Unidad Funcional')
@section('page_title', 'Modificar Unidad Funcional')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.functional-units.update', $functionalUnit) }}">
            @csrf
            @method('PUT')

            <!-- General Functional Unit Data -->
            <div class="ios-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0"><i class="bi bi-building-fill text-success me-2"></i>Datos de la Unidad Funcional</h5>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios">ID: {{ $functionalUnit->id }}</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="lot_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Lote Físico Relacionado</label>
                        <select name="lot_id" id="lot_id" class="form-select form-control-ios @error('lot_id') is-invalid @enderror" required>
                            @foreach($lots as $lot)
                                <option value="{{ $lot->id }}" {{ old('lot_id', $functionalUnit->lot_id) == $lot->id ? 'selected' : '' }}>
                                    Lote {{ $lot->number }} ({{ $lot->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('lot_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="code" class="form-label fw-semibold" style="font-size: 0.85rem;">Código Identificador</label>
                        <input type="text" name="code" id="code" class="form-control form-control-ios @error('code') is-invalid @enderror" value="{{ old('code', $functionalUnit->code) }}" required>
                        @error('code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control form-control-ios @error('name') is-invalid @enderror" value="{{ old('name', $functionalUnit->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="balance" class="form-label fw-semibold" style="font-size: 0.85rem;">Saldo Actual ($)</label>
                        <input type="number" step="0.01" name="balance" id="balance" class="form-control form-control-ios @error('balance') is-invalid @enderror" value="{{ old('balance', $functionalUnit->balance) }}" required>
                        @error('balance')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold" style="font-size: 0.85rem;">Descripción (Opcional)</label>
                        <input type="text" name="description" id="description" class="form-control form-control-ios @error('description') is-invalid @enderror" value="{{ old('description', $functionalUnit->description) }}">
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Associations with Owners -->
            <div class="ios-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-success me-2"></i>Copropietarios / Titulares de Pago</h5>
                <p class="text-muted mb-4" style="font-size: 0.85rem;">Asigna los propietarios responsables de esta unidad funcional.</p>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="row border-ios p-3 rounded-4 bg-body-secondary" style="max-height: 200px; overflow-y: auto;">
                            @foreach($owners as $owner)
                                <div class="col-sm-6 col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input text-success" type="checkbox" name="owner_ids[]" value="{{ $owner->id }}" id="owner_{{ $owner->id }}" {{ in_array($owner->id, old('owner_ids', $associatedOwners)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="owner_{{ $owner->id }}">
                                            {{ $owner->full_name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.functional-units.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
