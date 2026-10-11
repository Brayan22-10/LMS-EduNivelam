@extends('layouts.app')

@section('title', 'Acerca de Nosotros - EduNivelam')

@section('styles')
    @vite(['resources/css/about.css'])
@endsection

@section('content')
<section class="hero-section position-relative py-5 overflow-hidden mb-5 bg-white text-dark rounded-5 shadow-sm border border-secondary border-opacity-10">
    <div class="container px-4 py-4 text-center">
        <div class="d-inline-flex align-items-center bg-warning bg-opacity-10 text-dark px-3 py-1 rounded-pill mb-4 fw-semibold small border border-warning border-opacity-25">
            <i class="bi bi-compass me-2"></i> Nuestra Historia y Propósito
        </div>
        <h1 class="display-4 fw-bold mb-4 tracking-tight text-dark lh-tight mx-auto" style="max-width: 800px;">
            Transformando la transición a la <span class="text-primary">educación superior</span>
        </h1>
        <p class="lead text-secondary fs-6 mx-auto lh-relaxed" style="max-width: 700px;">
            En EduNivelam creemos que el éxito universitario comienza con bases sólidas. Democratizamos el acceso a cursos de nivelación de alta calidad en matemáticas, lectura crítica y habilidades blandas.
        </p>
    </div>
</section>

<section class="container mb-6 mt-4">
    <div class="row g-4 align-items-stretch">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-primary border-4 hover-translate-y">
                <div class="card-body p-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-3 fs-4">Nuestra Misión</h3>
                    <p class="text-secondary small lh-relaxed mb-0">
                        Brindar herramientas de nivelación académica interactivas y accesibles que preparen a los estudiantes de primer ingreso para superar los retos cognitivos y metodológicos de la educación superior, reduciendo la deserción estudiantil.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-top border-success border-4 hover-translate-y">
                <div class="card-body p-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-3 fs-4">Nuestra Visión</h3>
                    <p class="text-secondary small lh-relaxed mb-0">
                        Convertirnos en la plataforma LMS de referencia en la región para la nivelación universitaria temprana, integrando tecnología de vanguardia y una comunidad docente altamente comprometida con el desarrollo estudiantil.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container mb-6 py-3">
    <div class="text-center mb-5">
        <span class="text-uppercase text-primary fw-bold tracking-widest small d-block mb-2">Nuestros Pilares</span>
        <h2 class="fw-bold text-dark display-5 mb-2">Valores que nos Guían</h2>
        <p class="text-muted">Los principios éticos y académicos detrás de cada módulo de nuestro LMS.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-3 col-6">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100 value-box hover-translate-y">
                <div class="text-primary fs-3 mb-2"><i class="bi bi-award"></i></div>
                <h5 class="fw-bold text-dark fs-5">Excelencia</h5>
                <p class="text-muted small mb-0 lh-relaxed">Contenidos rigurosos diseñados por expertos académicos.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100 value-box hover-translate-y">
                <div class="text-success fs-3 mb-2"><i class="bi bi-heart"></i></div>
                <h5 class="fw-bold text-dark fs-5">Empatía</h5>
                <p class="text-muted small mb-0 lh-relaxed">Comprendemos los desafíos del cambio de etapa escolar a universitaria.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100 value-box hover-translate-y">
                <div class="text-warning fs-3 mb-2"><i class="bi bi-lightbulb"></i></div>
                <h5 class="fw-bold text-dark fs-5">Innovación</h5>
                <p class="text-muted small mb-0 lh-relaxed">Plataforma dinámica, interactiva y centrada en el estudiante.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100 value-box hover-translate-y">
                <div class="text-info fs-3 mb-2"><i class="bi bi-people"></i></div>
                <h5 class="fw-bold text-dark fs-5">Inclusión</h5>
                <p class="text-muted small mb-0 lh-relaxed">Acceso abierto y equitativo al conocimiento base para todos.</p>
            </div>
        </div>
    </div>
</section>

<section class="container mb-5">
    <div class="p-5 bg-dark text-white rounded-5 shadow-lg text-center position-relative overflow-hidden border border-secondary border-opacity-25 py-6">
        <div class="position-relative" style="z-index: 2; max-width: 650px; margin: 0 auto;">
            <h3 class="fw-bold text-white mb-3 fs-3">¿Quieres formar parte de nuestra red de instructores?</h3>
            <p class="text-light opacity-75 small mb-4 lh-relaxed">Si eres docente y deseas habilitar tu capacidad de publicación de cursos en nuestro LMS, ponte en contacto con nuestro equipo administrativo.</p>
            <a href="{{ route('contact') }}" class="btn btn-warning btn-md fw-bold text-dark px-5 py-2.5 shadow-sm rounded-pill hover-scale">Contactar con Administración</a>
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
    @vite(['resources/js/about.js'])
@endsection