@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('page_title', 'Datos de mi Perfil')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('owner.profile.update') }}">
            @csrf

            <!-- Profile Info -->
            <div class="ios-card mb-3">
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

            @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin() || in_array(Auth::user()->relationship_type, ['admin', 'superadmin', 'operator', 'accounting']))
            <!-- Dual Mode View Switcher -->
            <div class="ios-card mb-3 border border-primary-subtle" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(25, 135, 84, 0.05) 100%);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                            <i class="bi bi-arrow-left-right fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-primary">Modo de Vista de Usuario</h6>
                            <p class="text-muted small mb-0">Estás en <strong>Vista Propietario</strong>. Tu cuenta posee permisos de gestión para ingresar al panel de administración.</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-ios rounded-3 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Cambiar a Vista Administrador</span>
                    </a>
                </div>
            </div>
            @endif

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
