@extends('layouts.auth')

@section('title', 'Recuperar Contraseña')

@section('content')
<h4 class="fw-bold text-center mb-1">¿Olvidaste tu contraseña?</h4>
<p class="text-muted text-center mb-4" style="font-size: 0.85rem;">Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla.</p>

@if (session('status'))
    <div class="alert alert-success border-0 rounded-3 py-2 mb-4" style="font-size: 0.9rem;">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <!-- Email Address -->
    <div class="mb-4">
        <label for="email" class="form-label fw-semibold" style="font-size: 0.85rem;">Correo Electrónico</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control form-control-ios @error('email') is-invalid @enderror" placeholder="ejemplo@laranita.com">
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-ios btn-ios-primary">
            Enviar Enlace de Recuperación
        </button>
        <a href="{{ route('login') }}" class="btn btn-ios btn-outline-secondary btn-sm mt-2">
            Volver al Inicio de Sesión
        </a>
    </div>
</form>
@endsection
