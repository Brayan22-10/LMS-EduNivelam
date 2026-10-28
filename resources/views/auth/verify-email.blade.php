@extends('layouts.app')

@section('title', 'Verifica tu correo - EduNivelam')

@section('styles')
    @vite(['resources/css/login.css', 'resources/css/auth-pages.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="ap-stage" data-verify-email
     data-status-url="{{ url('/check-session-status') }}"
     data-home-url="{{ route('home') }}">
    <div class="ap-card text-center">

        <span class="ap-icon"><i class="bi bi-envelope-check"></i></span>
        <h1 class="ap-title">Verifica tu correo</h1>
        <p class="ap-sub">
            ¡Gracias por registrarte en EduNivelam! Antes de comenzar, haz clic en el enlace que te enviamos para confirmar tu dirección.
        </p>

        @auth
            <div class="ap-email"><i class="bi bi-envelope"></i> {{ Auth::user()->email }}</div>
        @endauth

        @if (session('message') || session('status') === 'verification-link-sent')
            <div class="alert alert-success py-2 small auth-alert text-start" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i>
                {{ session('message') ?? 'Te enviamos un nuevo enlace de verificación.' }}
            </div>
        @endif

        <div class="ap-wait d-flex justify-content-center">
            <span class="ap-dot"></span>
            <span>Esperando la verificación… esta página se actualizará sola.</span>
        </div>

        <form method="POST" action="{{ route('verification.send') }}" class="mb-3 js-once">
            @csrf
            <button type="submit" class="auth-submit">
                <span>Reenviar correo de verificación</span>
                <span class="auth-submit-arrow"><i class="bi bi-send"></i></span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="ap-btn-ghost">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/auth-pages.js'])
@endsection