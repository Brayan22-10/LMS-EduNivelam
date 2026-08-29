@extends('layouts.app')

@section('content')
@php
    $throttleKey = Str::lower(old('email', '')) . '|' . request()->ip();
    $secondsRemaining = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
    $isLocked = \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5);
    
    // Calculamos los minutos iniciales para mostrar en el cronómetro por defecto si está bloqueado
    $initialMinutes = ceil($secondsRemaining / 60);
    $initialMinutesFormatted = ($initialMinutes < 10 ? "0" : "") . $initialMinutes . ":00";
@endphp

{{-- Ventana emergente (Modal) --}}
<div id="lockout-modal" style="display: {{ $isLocked ? 'flex' : 'none' }}; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.7); z-index: 99999; justify-content: center; align-items: center;">
    <div style="background: #ffffff; padding: 30px; border-radius: 8px; max-width: 420px; width: 90%; text-align: center; box-shadow: 0px 5px 15px rgba(0,0,0,0.3);">
        <h3 style="color: #dc3545; font-weight: bold; margin-bottom: 15px;">¡Acceso Bloqueado!</h3>
        <p style="color: #6c757d; margin-bottom: 5px;">Número de intentos acabado por seguridad.</p>
        <p style="margin-bottom: 10px;">Podrás volver a ingresar en:</p>
        <h1 id="countdown-timer" style="font-size: 2.5rem; font-weight: bold; color: #212529; margin-bottom: 15px;">{{ $initialMinutesFormatted }}</h1>
        <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 20px;">Puedes cerrar esta ventana para navegar por el sitio, pero el formulario de login seguirá bloqueado hasta que termine el tiempo.</p>
        
        <button type="button" id="close-modal-btn" style="background-color: #6c757d; color: white; border: none; padding: 10px 15px; width: 100%; border-radius: 4px; font-weight: bold; cursor: pointer;">
            Cerrar ventana y navegar por el sitio
        </button>
    </div>
</div>

<div class="container mt-5">
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById('lockout-modal');
        const closeBtn = document.getElementById('close-modal-btn');
        const isCurrentlyLocked = @json($isLocked);

        if (sessionStorage.getItem('closed_lockout_modal') === 'true') {
            if (modal) modal.style.display = 'none';
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (modal) {
                    modal.style.display = 'none';
                }
                sessionStorage.setItem('closed_lockout_modal', 'true');
            });
        }

        @if($isLocked)
            let timeLeft = {{ $secondsRemaining }};
            const timerElement = document.getElementById('countdown-timer');

            const countdown = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    sessionStorage.removeItem('closed_lockout_modal');
                    window.location.reload();
                    return;
                }
                
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                
                if(timerElement) {
                    timerElement.textContent = 
                        (minutes < 10 ? "0" : "") + minutes + ":" + 
                        (seconds < 10 ? "0" : "") + seconds;
                }
                
                timeLeft--;
            }, 1000);
        @endif

        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', function(event) {
                if (isCurrentlyLocked) {
                    if (modal) {
                        modal.style.display = 'flex';
                        sessionStorage.removeItem('closed_lockout_modal');
                    }
                    event.preventDefault();
                }
            });
        }
    });
</script>
@endsection