@extends('layouts.app')

@section('title', 'Acerca de Nosotros - EduNivelam')

@section('styles')
    @vite(['resources/css/about.css'])
@endsection

@section('content')
<div class="about-hero py-5 mb-5 text-center text-white rounded-4 shadow-lg">
    <div class="container py-4">
        <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold"> Nuestra Historia y Propósito</span>
        <h1 class="display-4 fw-bold mb-3">Transformando la transición a la educación superior</h1>
        <p class="lead text-light opacity-90 mx-auto" style="max-width: 700px;">
            En EduNivelam creemos que el éxito universitario comienza con bases sólidas. Democratizamos el acceso a cursos de nivelación de alta calidad en matemáticas, lectura crítica y habilidades blandas.
        </p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4 align-items-stretch">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 mission-card">
                <div class="card-body">
                    <h3 class="fw-bold text-dark mb-3">Nuestra Misión</h3>
                    <p class="text-muted">
                        Brindar herramientas de nivelación académica interactivas y accesibles que preparen a los estudiantes de primer ingreso para superar los retos cognitivos y metodológicos de la educación superior, reduciendo la deserción estudiantil.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 vision-card">
                <div class="card-body">
                    <h3 class="fw-bold text-dark mb-3">Nuestra Visión</h3>
                    <p class="text-muted">
                        Convertirnos en la plataforma LMS de referencia en la región para la nivelación universitaria temprana, integrando tecnología de vanguardia y una comunidad docente altamente comprometida con el desarrollo estudiantil.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5 py-4">
    <div class="text-center mb-5">
        <span class="text-uppercase text-primary fw-bold tracking-wider small">Nuestros Pilares</span>
        <h2 class="fw-bold text-dark">Valores que nos Guían</h2>
        <p class="text-muted">Los principios éticos y académicos detrás de cada módulo de nuestro LMS.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-3 col-6">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm border h-100 value-box">
                <h5 class="fw-bold text-dark">Excelencia</h5>
                <p class="text-muted small mb-0">Contenidos rigurosos diseñados por expertos académicos.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm border h-100 value-box">
                <h5 class="fw-bold text-dark">Empatía</h5>
                <p class="text-muted small mb-0">Comprendemos los desafíos del cambio de etapa escolar a universitaria.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm border h-100 value-box">
                <h5 class="fw-bold text-dark">Innovación</h5>
                <p class="text-muted small mb-0">Plataforma dinámica, interactiva y centrada en el estudiante.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm border h-100 value-box">
                <h5 class="fw-bold text-dark">Inclusión</h5>
                <p class="text-muted small mb-0">Acceso abierto y equitativo al conocimiento base para todos.</p>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="p-5 bg-light border rounded-4 text-center shadow-sm">
        <h3 class="fw-bold text-dark mb-3">¿Quieres formar parte de nuestra red de instructores?</h3>
        <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">Si eres docente y deseas habilitar tu capacidad de publicación de cursos en nuestro LMS, ponte en contacto con nuestro equipo administrativo.</p>
        <a href="{{ route('contact') }}" class="btn btn-dark btn-lg rounded-pill px-5 fw-bold shadow-sm">Contactar con Administración</a>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/about.js'])
@endsection