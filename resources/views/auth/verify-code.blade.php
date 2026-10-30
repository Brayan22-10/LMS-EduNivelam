@extends('layouts.app')

@section('title', 'Verificar código - EduNivelam')

@section('styles')
    @vite(['resources/css/login.css', 'resources/css/auth-pages.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="ap-stage">
    <div class="ap-card text-center">

        <span class="ap-icon"><i class="bi bi-shield-check"></i></span>
        <h1 class="ap-title">Verificar código</h1>
        <p class="ap-sub">
            Te enviamos un código de 6 dígitos a tu correo electrónico. El código vence en 15 minutos.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small auth-alert text-start" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.verify.submit') }}" class="text-start">
            @csrf

            <div class="mb-4">
                <label for="code" class="form-label auth-label">Código de 6 dígitos</label>
                <input type="text" id="code" name="code"
                       class="form-control auth-input ap-code @error('code') is-invalid @enderror"
                       maxlength="6" data-digits-only inputmode="numeric" pattern="[0-9]{6}"
                       placeholder="••••••" required autofocus autocomplete="one-time-code">
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="auth-submit">
                <span>Verificar código</span>
                <span class="auth-submit-arrow"><i class="bi bi-arrow-right"></i></span>
            </button>
        </form>

        <div class="ap-links">
            <a href="{{ route('password.request') }}" class="auth-link">
                <i class="bi bi-arrow-repeat me-1"></i>Reintentar / Reenviar código
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/auth-pages.js'])
@endsection