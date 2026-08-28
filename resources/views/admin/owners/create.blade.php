@extends('layouts.app')

@section('title', 'Nuevo Propietario')
@section('page_title', 'Crear Propietario')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.owners.store') }}">
            @csrf

            <!-- Personal & Legal Data -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-person-badge text-success me-2"></i>Datos del Propietario</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control form-control-ios @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Ej. Juan">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label fw-semibold" style="font-size: 0.85rem;">Apellido</label>
                        <input type="text" name="last_name" id="last_name" class="form-control form-control-ios @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required placeholder="Ej. Pérez">
                        @error('last_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="business_name" class="form-label fw-semibold" style="font-size: 0.85rem;">Razón Social (Opcional)</label>
                        <input type="text" name="business_name" id="business_name" class="form-control form-control-ios @error('business_name') is-invalid @enderror" value="{{ old('business_name') }}" placeholder="Para personas jurídicas o fideicomisos">
                        @error('business_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="dni" class="form-label fw-semibold" style="font-size: 0.85rem;">DNI (Opcional)</label>
                        <input type="text" name="dni" id="dni" class="form-control form-control-ios @error('dni') is-invalid @enderror" value="{{ old('dni') }}" placeholder="DNI del titular">
                        @error('dni')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="cuit" class="form-label fw-semibold" style="font-size: 0.85rem;">CUIT (Opcional)</label>
                        <input type="text" name="cuit" id="cuit" class="form-control form-control-ios @error('cuit') is-invalid @enderror" value="{{ old('cuit') }}" placeholder="CUIT de facturación">
                        @error('cuit')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Settings -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-telephone-fill text-success me-2"></i>Contacto y Canales</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold" style="font-size: 0.85rem;">Email Principal</label>
                        <input type="email" name="email" id="email" class="form-control form-control-ios @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="ejemplo@correo.com">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email_alternate" class="form-label fw-semibold" style="font-size: 0.85rem;">Email Alternativo</label>
                        <input type="email" name="email_alternate" id="email_alternate" class="form-control form-control-ios @error('email_alternate') is-invalid @enderror" value="{{ old('email_alternate') }}" placeholder="ejemplo.secundario@correo.com">
                        @error('email_alternate')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold" style="font-size: 0.85rem;">Teléfono Móvil (WhatsApp)</label>
                        <input type="text" name="phone" id="phone" class="form-control form-control-ios @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Ej: +54911...">
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone_alternate" class="form-label fw-semibold" style="font-size: 0.85rem;">Teléfono Alternativo</label>
                        <input type="text" name="phone_alternate" id="phone_alternate" class="form-control form-control-ios @error('phone_alternate') is-invalid @enderror" value="{{ old('phone_alternate') }}" placeholder="Ej: +54911...">
                        @error('phone_alternate')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="preferred_channel" class="form-label fw-semibold" style="font-size: 0.85rem;">Canal de Notificación Preferido</label>
                        <select name="preferred_channel" id="preferred_channel" class="form-select form-control-ios @error('preferred_channel') is-invalid @enderror" required>
                            <option value="email" {{ old('preferred_channel') === 'email' ? 'selected' : '' }}>Solo Email</option>
                            <option value="whatsapp" {{ old('preferred_channel') === 'whatsapp' ? 'selected' : '' }}>Solo WhatsApp</option>
                            <option value="both" {{ old('preferred_channel') === 'both' ? 'selected' : '' }}>Ambos Canales (Email y WhatsApp)</option>
                            <option value="portal" {{ old('preferred_channel') === 'portal' ? 'selected' : '' }}>Solo Portal del Barrio</option>
                        </select>
                        @error('preferred_channel')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label fw-semibold" style="font-size: 0.85rem;">Domicilio Particular (Fuera del Barrio)</label>
                        <input type="text" name="address" id="address" class="form-control form-control-ios @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Ej. Av. del Libertador 1500, CABA">
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text-fill text-success me-2"></i>Observaciones</h5>
                <textarea name="notes" id="notes" rows="4" class="form-control form-control-ios" placeholder="Comentarios o notas adicionales sobre el propietario...">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.owners.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Crear Propietario</button>
            </div>
        </form>
    </div>
</div>
@endsection
