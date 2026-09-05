@extends('layouts.app')

@section('styles')
    @vite(['resources/css/register.css'])
@endsection

@section('content')
{{-- Ventana emergente (Modal) genérica de error para el registro --}}
@if(session('registration_error'))
<div id="error-modal" class="register-modal-overlay" style="display: flex;">
    <div class="register-modal-card">
        <h3 class="register-modal-title">¡Atención!</h3>
        <p class="register-modal-text">{{ session('registration_error') }}</p>
        <button type="button" id="close-error-modal" class="register-modal-btn">
            Aceptar
        </button>
    </div>
</div>
@endif

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">Registro de Usuario</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register.store') }}" id="register-form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            
                            <div class="mt-2 p-2 bg-light rounded border password-strength-container">
                                <div class="mb-1 fw-bold">
                                    Nivel de seguridad: <span id="strength-label" class="text-danger">Débil</span>
                                </div>
                                <ul class="list-unstyled mb-0" id="password-criteria">
                                    <li id="crit-length" class="text-danger">✗ Mínimo 8 caracteres</li>
                                    <li id="crit-uppercase" class="text-danger">✗ Al menos una mayúscula</li>
                                    <li id="crit-symbol" class="text-danger">✗ Al menos un símbolo (ej: @, #, $, !)</li>
                                    <li id="crit-number" class="text-danger">✗ Al menos un número</li>
                                    <li id="crit-name" class="text-danger">✗ No puede contener el nombre de la cuenta</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" id="submit-btn" class="btn btn-dark w-100" disabled>Registrarse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/register.js'])
@endsection