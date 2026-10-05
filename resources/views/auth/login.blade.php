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
@endphp

{{-- Ventana emergente (Modal) --}}
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
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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