@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<h4 class="fw-bold text-center mb-4">Iniciar Sesión</h4>

@if(session('status'))
    <div class="alert alert-success border-0 rounded-3 py-2 mb-3" style="font-size: 0.9rem;">
        {{ session('status') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success border-0 rounded-3 py-2 mb-3" style="font-size: 0.9rem;">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold" style="font-size: 0.85rem;">Correo Electrónico</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control form-control-ios @error('email') is-invalid @enderror" placeholder="ejemplo@laranita.com">
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-semibold m-0" style="font-size: 0.85rem;">Contraseña</label>
            <a class="text-success text-decoration-none" style="font-size: 0.8rem;" href="{{ route('password.request') }}">
                ¿La olvidaste?
            </a>
        </div>
        <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control form-control-ios @error('password') is-invalid @enderror" placeholder="••••••••">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="form-check mb-4">
        <input class="form-check-input text-success" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label text-muted" for="remember" style="font-size: 0.85rem;">
            Recordar mi sesión
        </label>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-ios btn-ios-primary">
            Ingresar
        </button>
    </div>
</form>

<div class="mt-4 text-center">
    <small class="text-muted">Acceso administrativo predeterminado:</small>
    <div class="bg-body-secondary p-2 rounded-3 mt-1" style="font-size: 0.75rem;">
        <code>superadmin@laranita.com</code> / <code>password</code><br>
        <code>admin1@laranita.com</code> / <code>password</code><br>
        <code>contabilidad@laranita.com</code> / <code>password</code>
    </div>
</div>
@endsection
