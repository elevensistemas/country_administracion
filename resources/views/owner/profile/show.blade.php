@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('page_title', 'Datos de mi Perfil')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('owner.profile.update') }}">
            @csrf

            <!-- Profile Info -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-person-fill text-success me-2"></i>Mis Datos Registrados</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Nombre Completo</span>
                        <span class="fw-bold fs-6">{{ $user->full_name }}</span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Relación</span>
                        <span class="badge bg-secondary-subtle text-secondary badge-ios text-uppercase">{{ $user->relationship_type }}</span>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted d-block" style="font-size: 0.8rem;">Correo Electrónico (Principal)</span>
                        <span class="fw-semibold fs-6">{{ $user->email }}</span>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold" style="font-size: 0.85rem;">Teléfono Móvil (WhatsApp)</label>
                        <input type="text" name="phone" id="phone" class="form-control form-control-ios" value="{{ old('phone', $user->phone) }}" placeholder="Ej: +54911...">
                    </div>
                </div>
            </div>

            <!-- Password Change -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock-fill text-success me-2"></i>Cambiar Contraseña</h5>
                <p class="text-muted mb-4" style="font-size: 0.85rem;">Deja estos campos vacíos si no deseas modificar tu clave de acceso.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold" style="font-size: 0.85rem;">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control form-control-ios @error('password') is-invalid @enderror">
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold" style="font-size: 0.85rem;">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-ios">
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('owner.dashboard') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
