@extends('layouts.app')

@section('title', 'Cursos de Nivelación - EduNivelam')

@section('styles')
    @vite(['resources/css/courses.css'])
@endsection

@section('content')
<div class="courses-hero py-5 mb-5 text-center text-white rounded-4 shadow-lg">
    <div class="container py-4">
        <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold">Oferta Académica U</span>
        <h1 class="display-4 fw-bold mb-3">Catálogo de Cursos de Nivelación</h1>
        <p class="lead text-light opacity-90 mx-auto" style="max-width: 700px;">
            Selecciona el módulo de tu interés y fortalece tus competencias antes de iniciar tu vida universitaria. Contenidos diseñados por expertos.
        </p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        @foreach($courses as $course)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 course-item-card p-3 d-flex flex-column justify-content-between">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge {{ $course['badge_color'] }} px-3 py-1 rounded-pill small">{{ $course['category'] }}</span>
                            <span class="text-muted small fw-semibold"> {{ $course['duration'] }}</span>
                        </div>
                        
                        <h4 class="fw-bold text-dark mb-2">{{ $course['title'] }}</h4>
                        <p class="text-muted small mb-4">
                            {{ $course['description'] }}
                        </p>
                    </div>
                    
                    <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <span class="text-secondary extra-small">Nivel: <strong>{{ $course['level'] }}</strong></span>
                            <a href="{{ route('register') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Inscribirme</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="container mb-5">
    <div class="p-5 bg-light border rounded-4 text-center shadow-sm">
        <h3 class="fw-bold text-dark mb-3">¿Eres estudiante registrado?</h3>
        <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">Una vez que la administración habilite tu cuenta y pases a los módulos interactivos, podrás desarrollar los cursos y evaluaciones desde tu acceso exclusivo.</p>
        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg rounded-pill px-5 fw-bold">Iniciar Sesión en el Campus</a>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/courses.js'])
@endsection