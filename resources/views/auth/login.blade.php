@extends('layouts.app')

@section('title', 'Iniciar sesión - EduNivelam')

@section('styles')
    @vite(['resources/css/login.css'])
@endsection

@section('content')
@php
    $savedEmail = \Illuminate\Support\Facades\Cookie::get('saved_email');
    $rememberChecked = \Illuminate\Support\Facades\Cookie::get('remember_checked');

    $throttleEmail = \Illuminate\Support\Str::lower(trim((string) old('email', $rememberChecked ? $savedEmail : '')));
    $throttleKey = $throttleEmail . '|' . request()->ip();
    $secondsRemaining = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
    $isLocked = \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5);

    $initialMinutes = ceil($secondsRemaining / 60);
    $initialMinutesFormatted = ($initialMinutes < 10 ? '0' : '') . $initialMinutes . ':00';

    $googleEnabled = filled(config('services.google.client_id'))
        && \Illuminate\Support\Facades\Route::has('auth.google.redirect');
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

<div class="auth-main-container" id="login-container">
    <div class="auth-card">
        <aside class="auth-hero" style="--hero-img: url('{{ asset('images/login-illustration.jpg') }}');">
            <div class="auth-hero-copy">
                <h2>Fortalece tus habilidades blandas</h2>
                <p>Comunicación, liderazgo y trabajo en equipo, a tu ritmo y desde cualquier dispositivo.</p>
                <div class="auth-chips">
                    <span>Comunicación</span>
                    <span>Liderazgo</span>
                    <span>Trabajo en equipo</span>
                </div>
            </div>
        </aside>

        <section class="auth-panel">
            <div class="auth-panel-inner">
                <span class="auth-logo"><i class="bi bi-mortarboard-fill"></i></span>
                <h1 class="auth-title">Inicio de sesión</h1>
                <p class="auth-subtitle">Inicia sesión para continuar tu aprendizaje.</p>

                @if(session('success'))
                    <div class="alert alert-success py-2 small auth-alert" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info py-2 small auth-alert" role="alert">
                        <i class="bi bi-info-circle-fill me-1"></i> {{ session('info') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger py-2 small auth-alert" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" id="login-form">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label auth-label">Correo electrónico</label>
                        <div class="auth-field">
                            <i class="bi bi-envelope auth-field-icon"></i>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email', $rememberChecked ? $savedEmail : '') }}"
                                   class="form-control auth-input @error('email') is-invalid @enderror"
                                   placeholder="tucorreo@ejemplo.com" required autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label auth-label">Contraseña</label>
                        <div class="auth-field">
                            <i class="bi bi-lock auth-field-icon"></i>
                            <input type="password" name="password" id="password"
                                   class="form-control auth-input pe-5 @error('password') is-invalid @enderror"
                                   placeholder="Tu contraseña" required autocomplete="current-password">

                            <button class="auth-eye" type="button" id="togglePassword" aria-label="Mostrar u ocultar contraseña">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </button>

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') || $rememberChecked ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember">Recordar mi correo</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="auth-link small">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="auth-submit">
                        <span>Iniciar sesión</span>
                        <span class="auth-submit-arrow"><i class="bi bi-arrow-right"></i></span>
                    </button>
                </form>

                @if($googleEnabled)
                    <div class="auth-divider"><span>o continúa con</span></div>

                    <a href="{{ route('auth.google.redirect') }}" class="auth-google">
                        <svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        <span>Continuar con Google</span>
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('register'))
                    <p class="auth-register">¿Aún no tienes cuenta? <a href="{{ route('register') }}" class="auth-link">Regístrate</a></p>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('teacher.status'))
                    <p class="auth-register auth-register-sub">
                        ¿Eres docente y ya te postulaste?
                        <a href="{{ route('teacher.status') }}" class="auth-link">Consulta tu solicitud</a>
                    </p>
                @endif
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/login.js'])
@endsection