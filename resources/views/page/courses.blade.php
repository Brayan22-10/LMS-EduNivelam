@extends('layouts.app')

@section('title', 'Cursos - EduNivelam')

@section('styles')
    @vite(['resources/css/courses.css'])
@endsection

@section('content')
<section class="hero-section position-relative py-5 overflow-hidden mb-6 bg-white text-dark rounded-5 shadow-sm border border-secondary border-opacity-10">
    <div class="container px-4 py-4 text-center">
        <div class="d-inline-flex align-items-center bg-warning bg-opacity-10 text-dark px-3 py-1 rounded-pill mb-4 fw-semibold small border border-warning border-opacity-25">
            <i class="bi bi-journal-bookmark-fill me-2"></i> Oferta Académica U
        </div>
        <h1 class="display-4 fw-bold mb-4 tracking-tight text-dark lh-tight mx-auto" style="max-width: 800px;">
            Catálogo de <span class="text-primary">Cursos</span>
        </h1>
        <p class="lead text-secondary fs-6 mx-auto lh-relaxed" style="max-width: 700px;">
            Selecciona el módulo de tu interés y fortalece tus competencias antes de iniciar tu vida universitaria. Contenidos diseñados por expertos.
        </p>
    </div>
</section>

<section class="container mb-6 py-3">
    <div class="row g-4">
        @foreach($courses as $course)
            <div class="col-md-4">
                <div class="card border border-secondary border-opacity-10 shadow-sm rounded-4 h-100 course-item-card p-4 bg-white hover-translate-y d-flex flex-column justify-content-between">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge {{ $course['badge_color'] }} px-3 py-1 rounded-pill small fw-semibold">{{ $course['category'] }}</span>
                            <span class="text-muted small fw-semibold"><i class="bi bi-clock me-1"></i>{{ $course['duration'] }}</span>
                        </div>
                        
                        <h4 class="fw-bold text-dark mb-2 fs-4">{{ $course['title'] }}</h4>
                        <p class="text-secondary small mb-4 lh-relaxed">
                            {{ $course['description'] }}
                        </p>
                    </div>
                    
                    <div class="card-footer bg-transparent border-0 pt-3 pb-0 px-0 mt-auto">
                        <div class="d-flex justify-content-between align-items-center border-top border-light pt-3">
                            <span class="text-muted small">Nivel: <strong class="text-dark">{{ $course['level'] }}</strong></span>
                            <a href="{{ route('register') }}" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-sm">Inscribirme</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="container mb-5">
    <div class="p-5 bg-dark text-white rounded-5 shadow-lg text-center position-relative overflow-hidden border border-secondary border-opacity-25 py-6">
        <div class="position-relative" style="z-index: 2; max-width: 650px; margin: 0 auto;">
            <h3 class="fw-bold text-white mb-3 fs-3">¿Eres estudiante registrado?</h3>
            <p class="text-light opacity-75 small mb-4 lh-relaxed">Una vez que la administración habilite tu cuenta y pases a los módulos interactivos, podrás desarrollar los cursos y evaluaciones desde tu acceso exclusivo.</p>
            <a href="{{ route('login') }}" class="btn btn-warning btn-md fw-bold text-dark px-5 py-2.5 shadow-sm rounded-pill hover-scale">Iniciar Sesión en el Campus</a>
        </div>
    </div>
</section>

<style>
    .hover-translate-y {
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .hover-translate-y:hover {
        transform: translateY(-6px);
        box-shadow: 0 1rem 2.5rem rgba(0,0,0,.08) !important;
    }
    .hover-scale {
        transition: transform 0.2s ease;
    }
    .hover-scale:hover {
        transform: scale(1.03);
    }
</style>
@endsection

@section('scripts')
    @vite(['resources/js/courses.js'])
@endsection