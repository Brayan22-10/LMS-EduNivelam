@extends('layouts.app')

@section('title', 'Recuperar contraseña - EduNivelam')

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
            <span class="ap-icon"><i class="bi bi-key"></i></span>
            <h1 class="ap-title">Recuperar contraseña</h1>
            <p class="ap-sub">Escribe tu correo y te enviaremos un código de 6 dígitos para restablecerla.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success py-2 small auth-alert" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label auth-label">Correo electrónico</label>
                <div class="auth-field">
                    <i class="bi bi-envelope auth-field-icon"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control auth-input @error('email') is-invalid @enderror"
                           placeholder="tucorreo@ejemplo.com" required autofocus autocomplete="off">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="auth-submit">
                <span>Enviar código de 6 dígitos</span>
                <span class="auth-submit-arrow"><i class="bi bi-send"></i></span>
            </button>
        </form>

        <div class="ap-links">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>
@endsection