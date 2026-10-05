@extends('layouts.app')

@section('title', 'Soporte y Contacto - EduNivelam')

@section('styles')
    @vite(['resources/css/contact.css'])
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center text-center mb-5">
        <div class="col-lg-8">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill fw-bold">Centro de Ayuda y Soporte</span>
            <h1 class="fw-bold text-dark display-5">¿Cómo podemos ayudarte hoy?</h1>
            <p class="text-muted lead">¿Tienes dudas sobre los cursos de nivelación, acceso a la plataforma o necesitas asistencia técnica? Escríbenos o consulta nuestras respuestas rápidas.</p>
        </div>
    </div>

    {{-- Alerta de Éxito --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

<<<<<<< HEAD
=======
    {{-- Alerta de Error (Falla SMTP / Conexión) --}}
>>>>>>> 1e97314e18ff1f44cece9b29feae78790298efab
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 align-items-stretch">
        {{-- Sección Lateral: Preguntas Frecuentes Dinámicas (Banco de 50 preguntas) --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="fw-bold text-dark m-0">Preguntas Frecuentes</h3>
                    <button id="resetFaqBtn" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 0.75rem; display: none;">Ver principales</button>
                </div>
                <p class="text-muted small mb-3">Haz clic en una pregunta para ver su solución y filtrar temas relacionados.</p>
                
                <div class="faq-scroll-container pe-1 flex-grow-1" style="max-height: 520px; overflow-y: auto;">
                    <div class="accordion accordion-flush" id="faqAccordion">
                        {{-- Las preguntas se inyectan dinámicamente mediante el archivo contact.js --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario de Contacto Principal --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <h3 class="fw-bold text-dark mb-4">Envíanos un mensaje</h3>
                
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label fw-bold small text-muted">Nombre completo</label>
                            <input type="text" class="form-control rounded-pill px-3 py-2 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ej. Brayan Castillo" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold small text-muted">Correo electrónico</label>
                            <input type="email" class="form-control rounded-pill px-3 py-2 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="subject" class="form-label fw-bold small text-muted">Asunto o Motivo</label>
                            <select class="form-select rounded-pill px-3 py-2 @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                                <option value="" selected disabled>Selecciona...</option>
                                <option value="Soporte Técnico / Plataforma" {{ old('subject') == 'Soporte Técnico / Plataforma' ? 'selected' : '' }}>Soporte Técnico</option>
                                <option value="Inscripción a Cursos" {{ old('subject') == 'Inscripción a Cursos' ? 'selected' : '' }}>Inscripción a Cursos</option>
                                <option value="Asesoría con Instructores" {{ old('subject') == 'Asesoría con Instructores' ? 'selected' : '' }}>Asesoría Instructores</option>
                            </select>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="message" class="form-label fw-bold small text-muted">Mensaje detallado</label>
                            <textarea class="form-control rounded-4 p-3 @error('message') is-invalid @enderror" id="message" name="message" rows="4" placeholder="Describe tu solicitud..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 my-3">
                            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
                        </div>

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm py-3">Enviar Mensaje de Soporte</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/contact.js'])
@endsection