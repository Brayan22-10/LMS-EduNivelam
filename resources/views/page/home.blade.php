@extends('layouts.app')

@section('styles')
    @vite(['resources/css/home.css'])
@endsection

@section('content')
<div class="home-hero-section py-5 rounded-4 text-white mb-5 shadow-lg position-relative overflow-hidden">
    <div class="container px-4 py-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold shadow-sm"> Tu primer paso hacia la U</span>
                <h1 class="display-4 fw-bold mb-3">Fortalece tus bases para la vida universitaria</h1>
                <p class="lead mb-4 text-light opacity-90">
                    Plataforma LMS especializada en cursos de nivelación académica. Desarrolla tus habilidades en matemáticas, lectura crítica y competencias blandas esenciales para tu éxito profesional.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-bold text-primary shadow-sm px-4">Regístrate Gratis</a>
                    <a href="{{ route('courses') }}" class="btn btn-outline-light btn-lg px-4">Explorar Cursos</a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-5 mt-lg-0">
                <div class="hero-card-floating p-4 bg-white text-dark rounded-4 shadow-lg border-0">
                    <div class="badge bg-primary text-white mb-2 px-2 py-1 rounded">Destacado U</div>
                    <h3 class="fw-bold text-dark mb-2"> Campus Interactivo</h3>
                    <p class="text-muted small mb-3">Módulos guiados por docentes expertos con evaluaciones y recursos descargables.</p>
                    <div id="welcome-dynamic-msg" class="alert alert-info py-2 small mb-0 fw-bold text-primary border-0 bg-info bg-opacity-10">¡Prepárate para el éxito académico!</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row text-center py-4 bg-white rounded-4 shadow-sm border align-items-center">
        <div class="col-md-4 mb-3 mb-md-0">
            <h3 class="fw-bold text-primary mb-0">+500</h3>
            <p class="text-muted small mb-0">Estudiantes Nivelados</p>
        </div>
        <div class="col-md-4 mb-3 mb-md-0 border-start border-end">
            <h3 class="fw-bold text-success mb-0">100%</h3>
            <p class="text-muted small mb-0">Enfoque Práctico U</p>
        </div>
        <div class="col-md-4">
            <h3 class="fw-bold text-warning text-dark mb-0">3 Áreas</h3>
            <p class="text-muted small mb-0">Clave para tu Ingreso</p>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="text-center mb-5">
        <span class="text-uppercase text-primary fw-bold tracking-wider small">Ruta de Aprendizaje</span>
        <h2 class="fw-bold text-dark">Nuestros Pilares de Formación</h2>
        <p class="text-muted">Áreas clave diseñadas para potenciar tu perfil profesional desde el primer día de clases.</p>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm course-card p-3 rounded-4">
                <div class="card-body">
                    <div class="icon-box bg-primary text-white rounded-circle mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px; font-size: 1.5rem;"></div>
                    <div class="badge bg-light text-primary mb-2">Fundamentos</div>
                    <h4 class="fw-bold text-dark">Matemáticas Básicas</h4>
                    <p class="text-muted mt-2 small">Refuerza el pensamiento lógico, álgebra y resolución de problemas fundamentales para cálculo universitario.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm course-card p-3 rounded-4">
                <div class="card-body">
                    <div class="icon-box bg-success text-white rounded-circle mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px; font-size: 1.5rem;"></div>
                    <div class="badge bg-light text-success mb-2">Análisis</div>
                    <h4 class="fw-bold text-dark">Lectura Crítica</h4>
                    <p class="text-muted mt-2 small">Mejora la comprensión de textos académicos complejos, argumentación sólida y pensamiento analítico.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm course-card p-3 rounded-4">
                <div class="card-body">
                    <div class="icon-box bg-warning text-dark rounded-circle mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px; font-size: 1.5rem;"></div>
                    <div class="badge bg-light text-warning text-dark mb-2">Desarrollo Personal</div>
                    <h4 class="fw-bold text-dark">Habilidades Blandas</h4>
                    <p class="text-muted mt-2 small">Potencia el liderazgo, la gestión eficiente del tiempo, el trabajo en equipo y la comunicación efectiva.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5 py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Lo que dicen nuestros estudiantes</h2>
        <p class="text-muted">Experiencias reales de quienes superaron con éxito su transición a la universidad.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="p-4 bg-white rounded-4 shadow-sm border h-100 position-relative">
                <div class="text-warning mb-2">⭐⭐⭐⭐⭐</div>
                <p class="text-muted fst-italic">"Gracias a los cursos de matemáticas básicas logré entender los temas de cálculo sin frustrarme en mi primer semestre. La plataforma es súper intuitiva."</p>
                <h6 class="fw-bold text-dark mb-0">— Carlos Mendoza</h6>
                <small class="text-muted">Estudiante de Ingeniería</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 bg-white rounded-4 shadow-sm border h-100 position-relative">
                <div class="text-warning mb-2">⭐⭐⭐⭐⭐</div>
                <p class="text-muted fst-italic">"El módulo de lectura crítica cambió por completo mi forma de analizar textos científicos y ensayos. Totalmente recomendado para los de primer ingreso."</p>
                <h6 class="fw-bold text-dark mb-0">— Andrea Torres</h6>
                <small class="text-muted">Estudiante de Administración</small>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="p-5 bg-gradient bg-primary text-white rounded-4 text-center shadow-lg">
        <h2 class="fw-bold mb-3">¿Listo para asegurar tu éxito universitario?</h2>
        <p class="lead mb-4 text-light opacity-90 mx-auto" style="max-width: 600px;">Únete hoy a EduNivelam, accede a los cursos creados por docentes expertos y comienza tu camino sin vacíos académicos.</p>
        <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold text-dark px-5 py-3 shadow">Crear Cuenta Gratis</a>
    </div>
</div>
@endsection