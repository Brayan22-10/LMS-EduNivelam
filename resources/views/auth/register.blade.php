@extends('layouts.app')

@section('title', 'Registro de estudiante - EduNivelam')

@section('styles')
    @vite(['resources/css/login.css', 'resources/css/auth-pages.css', 'resources/css/register.css'])
@endsection

@section('content')
@if(session('registration_error'))
<div id="error-modal" class="register-modal-overlay" style="display: flex;">
    <div class="register-modal-card">
        <span class="register-modal-icon"><i class="bi bi-exclamation-triangle"></i></span>
        <h3 class="register-modal-title">¡Atención!</h3>
        <p class="register-modal-text">{{ session('registration_error') }}</p>
        <button type="button" id="close-error-modal" class="register-modal-btn">
            Aceptar
        </button>
    </div>
</div>
@endif

<div class="ap-stage">
    <div class="ap-card ap-card--form">

        <div class="rg-head">
            <span class="ap-icon"><i class="bi bi-person-plus"></i></span>
            <h1 class="ap-title">Crea tu cuenta</h1>
            <p class="ap-sub">Registro de estudiante en EduNivelam.</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" id="register-form">
            @csrf

            <div class="rg-row">
                <div class="mb-3">
                    <label for="name" class="form-label auth-label">Nombre</label>
                    <div class="auth-field">
                        <i class="bi bi-person auth-field-icon"></i>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="form-control auth-input @error('name') is-invalid @enderror"
                               placeholder="Tu nombre" required autofocus>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="dni" class="form-label auth-label">Cédula / Documento</label>
                    <div class="auth-field">
                        <i class="bi bi-card-text auth-field-icon"></i>
                        <input type="text" name="dni" id="dni" value="{{ old('dni') }}"
                               class="form-control auth-input @error('dni') is-invalid @enderror"
                               data-digits-only inputmode="numeric" placeholder="Solo números" required>
                        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="rg-row">
                <div class="mb-3">
                    <label for="phone" class="form-label auth-label">
                        Teléfono <span class="rg-optional">(opcional)</span>
                    </label>
                    <div class="auth-field">
                        <i class="bi bi-telephone auth-field-icon"></i>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                               class="form-control auth-input @error('phone') is-invalid @enderror"
                               data-digits-only inputmode="numeric" placeholder="Ej: 3001234567">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label auth-label">Correo electrónico</label>
                    <div class="auth-field">
                        <i class="bi bi-envelope auth-field-icon"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="form-control auth-input @error('email') is-invalid @enderror"
                               placeholder="tucorreo@ejemplo.com" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <p class="rg-hint mb-3">
                <i class="bi bi-info-circle"></i>
                <span>Puedes registrar el teléfono ahora o agregarlo después desde tu perfil con validación SMS.</span>
            </p>

            <div class="mb-3">
                <label for="password" class="form-label auth-label">Contraseña</label>
                <div class="auth-field">
                    <i class="bi bi-lock auth-field-icon"></i>
                    <input type="password" name="password" id="password"
                           class="form-control auth-input pe-5 @error('password') is-invalid @enderror"
                           placeholder="Crea una contraseña segura" required autocomplete="new-password">
                    <button class="auth-eye" type="button" data-toggle-pass="password" aria-label="Mostrar u ocultar contraseña">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="pw-meter" id="pw-meter" data-level="weak">
                    <div class="pw-meter-head">
                        <span>Nivel de seguridad</span>
                        <span id="strength-label" class="pw-label">Débil</span>
                    </div>
                    <div class="pw-bars" aria-hidden="true">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <ul class="pw-criteria" id="password-criteria">
                        <li data-crit="length"><i class="bi bi-x-circle"></i> Mínimo 8 caracteres</li>
                        <li data-crit="uppercase"><i class="bi bi-x-circle"></i> Al menos una mayúscula</li>
                        <li data-crit="symbol"><i class="bi bi-x-circle"></i> Al menos un símbolo (@, #, $, !)</li>
                        <li data-crit="number"><i class="bi bi-x-circle"></i> Al menos un número</li>
                        <li data-crit="name"><i class="bi bi-x-circle"></i> No contiene el nombre de la cuenta</li>
                    </ul>
                </div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label auth-label">Confirmar contraseña</label>
                <div class="auth-field">
                    <i class="bi bi-lock-fill auth-field-icon"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control auth-input pe-5"
                           placeholder="Repite la contraseña" required autocomplete="new-password">
                    <button class="auth-eye" type="button" data-toggle-pass="password_confirmation" aria-label="Mostrar u ocultar contraseña">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <div class="pw-match" id="pw-match">
                    <i class="bi bi-x-circle-fill"></i>
                    <span>Las contraseñas no coinciden</span>
                </div>
            </div>

            <button type="submit" id="submit-btn" class="auth-submit" disabled>
                <span>Registrarse</span>
                <span class="auth-submit-arrow"><i class="bi bi-arrow-right"></i></span>
            </button>

            <div class="rg-switch">
                <p>¿Ya tienes cuenta? <a href="{{ route('login') }}" class="auth-link">Inicia sesión</a></p>
                <p class="mb-0">¿Eres docente y deseas formar parte de la plataforma?</p>
                <a href="{{ route('register.teacher') }}" class="auth-link">Regístrate como docente aquí →</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/register.js'])
@endsection