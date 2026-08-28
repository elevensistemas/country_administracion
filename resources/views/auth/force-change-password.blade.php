@extends('layouts.auth')

@section('title', 'Configurar tu cuenta')

@section('content')
<h4 class="fw-bold text-center mb-1">Bienvenido a La Ranita</h4>
<p class="text-muted text-center mb-4" style="font-size: 0.85rem;">Para activar tu cuenta, por favor establece tu contraseña de acceso y acepta los términos.</p>

<form method="POST" action="{{ route('password.force_change.post') }}">
    @csrf

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold" style="font-size: 0.85rem;">Nueva Contraseña</label>
        <input id="password" type="password" name="password" required class="form-control form-control-ios @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Debe incluir letras, números y al menos un símbolo especial.</small>
    </div>

    <!-- Confirm Password -->
    <div class="mb-3">
        <label for="password_confirmation" class="form-label fw-semibold" style="font-size: 0.85rem;">Confirmar Contraseña</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control form-control-ios" placeholder="Repite la contraseña">
    </div>

    <!-- Terms and Conditions -->
    <div class="mb-4">
        <div class="form-check">
            <input class="form-check-input text-success @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
            <label class="form-check-label text-muted" for="terms" style="font-size: 0.8rem; line-height: 1.4;">
                Acepto los <a href="#" class="text-success text-decoration-none fw-semibold">Términos y Condiciones de Uso</a> y las políticas de privacidad y procesamiento de datos.
            </label>
            @error('terms')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-ios btn-ios-primary">
            Activar mi Cuenta
        </button>
    </div>
</form>
@endsection
