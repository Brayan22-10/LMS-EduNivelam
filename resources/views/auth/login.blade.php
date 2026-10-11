@extends('layouts.app')

@section('styles')
    @vite(['resources/css/login.css'])
@endsection

@section('content')
@php
    $throttleKey = \Illuminate\Support\Str::lower(old('email', '')) . '|' . request()->ip();
    $secondsRemaining = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
    $isLocked = \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5);
    
    $initialMinutes = ceil($secondsRemaining / 60);
    $initialMinutesFormatted = ($initialMinutes < 10 ? "0" : "") . $initialMinutes . ":00";

    $savedEmail = \Illuminate\Support\Facades\Cookie::get('saved_email');
    $savedPassword = \Illuminate\Support\Facades\Cookie::get('saved_password');
    $rememberChecked = \Illuminate\Support\Facades\Cookie::get('remember_checked');
@endphp

<div id="lockout-modal" class="lockout-modal-overlay" style="display: {{ $isLocked ? 'flex' : 'none' }};">
    <div class="lockout-modal-card">
        <h3 class="lockout-title">¡Acceso Bloqueado!</h3>
        <p class="lockout-text-muted">Número de intentos acabado por seguridad.</p>
        <p class="lockout-text">Podrás volver a ingresar en:</p>
        <h1 id="countdown-timer" class="lockout-timer">{{ $initialMinutesFormatted }}</h1>
        <p class="lockout-text-small">Puedes cerrar esta ventana para navegar por el sitio, pero el formulario de login seguirá bloqueado hasta que termine el tiempo.</p>
        
        <button type="button" id="close-modal-btn" class="lockout-btn">
            Cerrar ventana y navegar por el sitio
        </button>
    </div>
</div>

<div class="container mt-5" id="login-container" data-is-locked="{{ $isLocked ? 'true' : 'false' }}" data-seconds-remaining="{{ $secondsRemaining }}">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">Login</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login.store') }}" id="login-form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Dirección de correo electrónico</label>
                            <input type="email" name="email" value="{{ old('email', $rememberChecked ? $savedEmail : '') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="off">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="password" value="{{ $rememberChecked ? $savedPassword : '' }}" class="form-control pe-5 @error('password') is-invalid @enderror" required autocomplete="new-password">
                                
                                <button class="btn border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-2 text-muted" type="button" id="togglePassword" style="z-index: 10; box-shadow: none;">
                                    <i class="bi bi-eye-slash fs-5" id="toggleIcon"></i>
                                </button>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 form-check d-flex justify-content-between align-items-center">
                            <div>
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') || $rememberChecked ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Recordar datos</label>
                            </div>
                            
                            <div>
                                <a href="{{ route('password.request') }}" class="text-decoration-none small">¿Olvidaste tu contraseña?</a>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">Sign In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/login.js'])
@endsection