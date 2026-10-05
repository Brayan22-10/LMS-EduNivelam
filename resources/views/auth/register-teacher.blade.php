@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
    <style>
        .iti { width: 100%; display: block; }
        .iti__flag { background-image: url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/img/flags.png"); }
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .iti__flag { background-image: url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/img/flags@2x.png"); }
        }
    </style>
@endsection

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
    <div class="card shadow-sm border-0 p-4 p-md-5" style="max-width: 600px; width: 100%;">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark">Registro de Docentes</h3>
            <p class="text-muted small">Completa tus datos profesionales. Debes ser mayor de 18 años. Sujeto a aprobación.</p>
        </div>

        <form method="POST" action="{{ route('register.teacher.store') }}" id="teacher-register-form">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nombre de usuario</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="dni" class="form-label fw-semibold">Cédula / Documento de Identidad (Solo números)</label>
                <input type="text" class="form-control @error('dni') is-invalid @enderror" id="dni" name="dni" value="{{ old('dni') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Ej: 1023456789" required>
                @error('dni')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label fw-semibold">Número de Teléfono (Solo números)</label>
                <div>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Ej: 3001234567" required>
                    <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', '+57') }}">
                    
                    @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('country_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="birthdate" class="form-label fw-semibold">Fecha de Nacimiento (Debes ser mayor de 18 años)</label>
                <input type="date" class="form-control @error('birthdate') is-invalid @enderror" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                @error('birthdate')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="teaching_type" class="form-label fw-semibold">¿A qué categoría deseas postularte?</label>
                <select name="teaching_type" id="teaching_type" class="form-select @error('teaching_type') is-invalid @enderror" required>
                    <option value="" disabled selected>Seleccione una opción...</option>
                    <option value="courses" {{ old('teaching_type') == 'courses' ? 'selected' : '' }}>Profesor para Creación de Cursos</option>
                    <option value="advisories" {{ old('teaching_type') == 'advisories' ? 'selected' : '' }}>Profesor de Asesorías Académicas</option>
                </select>
                @error('teaching_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-dark w-100 py-2 mb-3">Enviar Solicitud de Docente</button>
            
            <div class="text-center">
                <a href="{{ route('register') }}" class="text-decoration-none small text-muted">← Volver al registro de estudiantes</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const input = document.querySelector("#phone");
            const countryCodeInput = document.querySelector("#country_code");
            const form = document.querySelector("#teacher-register-form");

            if (input) {
                const iti = window.intlTelInput(input, {
                    initialCountry: "co",
                    separateDialCode: true,
                    nationalMode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
                });

                form.addEventListener("submit", function () {
                    const countryData = iti.getSelectedCountryData();
                    if (countryData && countryData.dialCode) {
                        countryCodeInput.value = "+" + countryData.dialCode;
                    }
                });
            }
        });
    </script>
@endsection