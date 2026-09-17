@extends('layouts.auth')

@section('title', 'Restablecer Contraseña')

@section('content')
<h4 class="fw-bold text-center mb-1">Restablecer Contraseña</h4>
<p class="text-muted text-center mb-4" style="font-size: 0.85rem;">Ingresa tu nueva contraseña para acceder al sistema.</p>

@if ($errors->any())
    <div class="alert alert-danger border-0 rounded-3 py-2 mb-3" style="font-size: 0.85rem;">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold" style="font-size: 0.85rem;">Correo Electrónico</label>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus class="form-control form-control-ios @error('email') is-invalid @enderror" placeholder="ejemplo@laranita.com">
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold" style="font-size: 0.85rem;">Nueva Contraseña</label>
        <input id="password" type="password" name="password" required autocomplete="new-password" class="form-control form-control-ios @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-semibold" style="font-size: 0.85rem;">Confirmar Nueva Contraseña</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-control form-control-ios" placeholder="Repite la contraseña">
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-ios btn-ios-primary">
            Restablecer Contraseña
        </button>
        <a href="{{ route('login') }}" class="btn btn-ios btn-outline-secondary btn-sm mt-2">
            Volver al Inicio de Sesión
        </a>
    </div>
</form>
@endsection