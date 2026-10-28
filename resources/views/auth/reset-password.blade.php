@extends('layouts.app')

@section('title', 'Restablecer contraseña - EduNivelam')

@section('styles')
    @vite(['resources/css/login.css', 'resources/css/auth-pages.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="ap-stage">
    <div class="ap-card">

        <div class="text-center">
            <span class="ap-icon"><i class="bi bi-shield-lock"></i></span>
            <h1 class="ap-title">Restablecer contraseña</h1>
            <p class="ap-sub">Elige una contraseña nueva y segura para tu cuenta.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <div class="mb-3">
                <label for="password" class="form-label auth-label">Nueva contraseña</label>
                <div class="auth-field">
                    <i class="bi bi-lock auth-field-icon"></i>
                    <input type="password" id="password" name="password"
                           class="form-control auth-input pe-5 @error('password') is-invalid @enderror"
                           placeholder="Mínimo 8 caracteres" required autofocus autocomplete="new-password">
                    <button class="auth-eye" type="button" data-toggle-pass="password" aria-label="Mostrar u ocultar contraseña">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label auth-label">Confirmar nueva contraseña</label>
                <div class="auth-field">
                    <i class="bi bi-lock-fill auth-field-icon"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control auth-input pe-5"
                           placeholder="Repite la contraseña" required autocomplete="new-password">
                    <button class="auth-eye" type="button" data-toggle-pass="password_confirmation" aria-label="Mostrar u ocultar contraseña">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <ul class="ap-rules">
                <li id="rule-len"><i class="bi bi-circle"></i> Mínimo 8 caracteres</li>
                <li id="rule-space"><i class="bi bi-circle"></i> Sin espacios en blanco</li>
                <li id="rule-match"><i class="bi bi-circle"></i> Las dos contraseñas coinciden</li>
            </ul>

            <button type="submit" class="auth-submit">
                <span>Actualizar contraseña</span>
                <span class="auth-submit-arrow"><i class="bi bi-check-lg"></i></span>
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/auth-pages.js'])
@endsection