@extends('layouts.app')

@section('title', 'Registro de docentes - EduNivelam')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
    @vite(['resources/css/login.css', 'resources/css/auth-pages.css', 'resources/css/register.css'])
@endsection

@section('content')
<div class="ap-stage">
    <div class="ap-card ap-card--form">

        <div class="rg-head">
            <span class="ap-icon"><i class="bi bi-person-workspace"></i></span>
            <h1 class="ap-title">Registro de docentes</h1>
            <p class="ap-sub">Completa tus datos profesionales para postularte a la plataforma.</p>
        </div>

        <div class="rg-note">
            <i class="bi bi-info-circle-fill"></i>
            <span>Debes ser mayor de 18 años. Tu solicitud queda sujeta a aprobación del administrador.</span>
        </div>

        <form method="POST" action="{{ route('register.teacher.store') }}" id="teacher-register-form">
            @csrf

            <div class="rg-row">
                <div class="mb-3">
                    <label for="name" class="form-label auth-label">Nombre de usuario</label>
                    <div class="auth-field">
                        <i class="bi bi-person auth-field-icon"></i>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="form-control auth-input @error('name') is-invalid @enderror"
                               placeholder="Tu nombre de usuario" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label auth-label">Correo electrónico</label>
                    <div class="auth-field">
                        <i class="bi bi-envelope auth-field-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control auth-input @error('email') is-invalid @enderror"
                               placeholder="tucorreo@ejemplo.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="rg-row">
                <div class="mb-3">
                    <label for="dni" class="form-label auth-label">Cédula / Documento (solo números)</label>
                    <div class="auth-field">
                        <i class="bi bi-card-text auth-field-icon"></i>
                        <input type="text" id="dni" name="dni" value="{{ old('dni') }}"
                               class="form-control auth-input @error('dni') is-invalid @enderror"
                               data-digits-only inputmode="numeric" placeholder="Ej: 1023456789" required>
                        @error('dni')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label auth-label">Teléfono (solo números)</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                           class="form-control auth-input @error('phone') is-invalid @enderror"
                           data-digits-only placeholder="Ej: 3001234567" required>
                    <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', '+57') }}">

                    @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('country_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="rg-row">
                <div class="mb-3">
                    <label for="birthdate" class="form-label auth-label">Fecha de nacimiento</label>
                    <div class="auth-field">
                        <i class="bi bi-calendar-event auth-field-icon"></i>
                        <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}"
                               max="{{ now()->subYears(18)->format('Y-m-d') }}"
                               class="form-control auth-input @error('birthdate') is-invalid @enderror" required>
                        @error('birthdate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="teaching_type" class="form-label auth-label">Categoría de postulación</label>
                    <div class="auth-field">
                        <i class="bi bi-briefcase auth-field-icon"></i>
                        <select name="teaching_type" id="teaching_type"
                                class="form-select auth-input @error('teaching_type') is-invalid @enderror" required>
                            <option value="" disabled {{ old('teaching_type') ? '' : 'selected' }}>Seleccione una opción...</option>
                            <option value="courses" {{ old('teaching_type') == 'courses' ? 'selected' : '' }}>Creación de cursos</option>
                            <option value="advisories" {{ old('teaching_type') == 'advisories' ? 'selected' : '' }}>Asesorías académicas</option>
                        </select>
                        @error('teaching_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="auth-submit">
                <span>Enviar solicitud de docente</span>
                <span class="auth-submit-arrow"><i class="bi bi-send"></i></span>
            </button>

            <div class="rg-switch">
                <a href="{{ route('register') }}" class="auth-link">
                    <i class="bi bi-arrow-left me-1"></i>Volver al registro de estudiantes
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    @vite(['resources/js/register.js'])
@endsection