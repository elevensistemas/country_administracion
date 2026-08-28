@extends('layouts.app')

@section('title', 'Nuevo Proveedor')
@section('page_title', 'Crear Proveedor')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf

            <!-- General Data -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-person-badge text-success me-2"></i>Datos del Proveedor</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="business_name" class="form-label fw-semibold" style="font-size: 0.85rem;">Razón Social / Nombre</label>
                        <input type="text" name="business_name" id="business_name" class="form-control form-control-ios @error('business_name') is-invalid @enderror" value="{{ old('business_name') }}" required placeholder="Ej. Seguridad SA">
                        @error('business_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="cuit" class="form-label fw-semibold" style="font-size: 0.85rem;">CUIT</label>
                        <input type="text" name="cuit" id="cuit" class="form-control form-control-ios @error('cuit') is-invalid @enderror" value="{{ old('cuit') }}" required placeholder="Ej. 20-33444555-9 o 20334445559">
                        @error('cuit')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="category" class="form-label fw-semibold" style="font-size: 0.85rem;">Rubro / Categoría</label>
                        <input type="text" name="category" id="category" class="form-control form-control-ios @error('category') is-invalid @enderror" value="{{ old('category') }}" required placeholder="Ej. Seguridad, Mantenimiento, Jardinería">
                        @error('category')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Data -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-telephone-fill text-success me-2"></i>Contacto y Localización</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold" style="font-size: 0.85rem;">Correo Electrónico (Opcional)</label>
                        <input type="email" name="email" id="email" class="form-control form-control-ios @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="ejemplo@proveedor.com">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold" style="font-size: 0.85rem;">Teléfono de Contacto (Opcional)</label>
                        <input type="text" name="phone" id="phone" class="form-control form-control-ios @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Ej: 1122334455">
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-semibold" style="font-size: 0.85rem;">Dirección Física (Opcional)</label>
                        <input type="text" name="address" id="address" class="form-control form-control-ios @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Ej. Av. de Mayo 800, CABA">
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Bank Data -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-bank text-success me-2"></i>Datos de Depósito / Cuenta Bancaria</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="bank_name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre del Banco (Opcional)</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control form-control-ios @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}" placeholder="Ej. Banco Galicia">
                        @error('bank_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="cbu_alias" class="form-label fw-semibold" style="font-size: 0.85rem;">CBU o Alias de Cuenta (Opcional)</label>
                        <input type="text" name="cbu_alias" id="cbu_alias" class="form-control form-control-ios @error('cbu_alias') is-invalid @enderror" value="{{ old('cbu_alias') }}" placeholder="CBU de 22 dígitos o alias de texto">
                        @error('cbu_alias')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text-fill text-success me-2"></i>Observaciones</h5>
                <textarea name="notes" id="notes" rows="4" class="form-control form-control-ios" placeholder="Notas internas adicionales sobre este proveedor...">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Crear Proveedor</button>
            </div>
        </form>
    </div>
</div>
@endsection
