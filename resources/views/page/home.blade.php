@extends('layouts.app')

@section('title', 'Inicio - EduNivelam')

@section('styles')
    @vite(['resources/css/home.css'])
@endsection

@section('content')
<section class="hero-section position-relative py-5 overflow-hidden mb-6">
    <div class="container px-4 py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-4 fw-semibold small">
                    <i class="bi bi-mortarboard-fill me-2"></i> Tu primer paso hacia la educación superior
                </div>

                <h1 class="display-4 fw-bold mb-4 tracking-tight text-dark lh-tight">
                    @if(isset($banner) && $banner)
                        {!! str_replace('universitaria', '<span class="text-primary">universitaria</span>', e($banner->title)) !!}
                    @else
                        Fortalece tus bases para la <span class="text-primary">vida universitaria</span>
                    @endif
                </h1>

                <p class="lead mb-4 text-secondary fs-6 lh-relaxed">
                    @if(isset($banner) && $banner && $banner->subtitle)
                        {{ $banner->subtitle }}
                    @else
                        Plataforma LMS especializada en cursos de nivelación académica. Desarrolla tus habilidades en matemáticas, lectura crítica y competencias blandas esenciales para tu éxito profesional.
                    @endif
                </p>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg fw-bold text-white px-4 py-3 rounded-pill shadow-sm hover-scale">
                        Regístrate Gratis <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    <a href="{{ route('courses') }}" class="btn btn-outline-dark btn-lg px-4 py-3 rounded-pill">
                        Explorar Cursos
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                @if(isset($banner) && $banner && $banner->image_path)
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white p-2">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" class="img-fluid rounded-4 w-100 shadow-sm" style="max-height: 380px; object-fit: cover;" alt="{{ $banner->title }}">
                    </div>
                @else
                    <div class="card border-0 shadow-lg rounded-4 p-4 bg-white position-relative overflow-hidden border-start border-primary border-4">
                        <div class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-1 rounded-pill fw-semibold d-inline-block w-auto">Destacado U</div>
                        <h3 class="fw-bold text-dark mb-2 fs-4">Campus Interactivo</h3>
                        <p class="text-muted small mb-4 lh-relaxed">Módulos guiados por docentes expertos con evaluaciones interactivas y recursos descargables diseñados para eliminar vacíos académicos.</p>
                        
                        <div class="p-3 rounded-3 bg-light border-0 d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold text-dark small" id="welcome-dynamic-msg">¡Prepárate para el éxito académico!</span>
                                <small class="text-muted" style="font-size: 0.75rem;">Acceso disponible 24/7</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="container mb-6">
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100">
                <h3 class="fw-bold text-primary display-5 mb-1">+500</h3>
                <p class="text-muted small mb-0 fw-semibold text-uppercase tracking-wider">Estudiantes Nivelados</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100">
                <h3 class="fw-bold text-success display-5 mb-1">100%</h3>
                <p class="text-muted small mb-0 fw-semibold text-uppercase tracking-wider">Enfoque Práctico U</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100">
                <h3 class="fw-bold text-warning display-5 mb-1">3 Áreas</h3>
                <p class="text-muted small mb-0 fw-semibold text-uppercase tracking-wider">Clave para tu Ingreso</p>
            </div>
        </div>
    </div>
</section>

<section class="container mb-6 py-3">
    <div class="text-center mb-5">
        <span class="text-uppercase text-primary fw-bold tracking-widest small d-block mb-2">Ruta de Aprendizaje</span>
        <h2 class="fw-bold text-dark display-5 mb-2">Nuestros Pilares de Formación</h2>
        <p class="text-muted mx-auto" style="max-width: 650px;">Áreas clave diseñadas para potenciar tu perfil profesional desde el primer día de clases universitarias.</p>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm course-card p-4 rounded-4 bg-white transition-all hover-translate-y border-top border-primary border-4">
                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; font-size: 1.3rem;">
                    <i class="bi bi-calculator"></i>
                </div>
                <span class="badge bg-light text-primary mb-2 fw-semibold">Fundamentos</span>
                <h4 class="fw-bold text-dark fs-4 mb-2">Matemáticas Básicas</h4>
                <p class="text-secondary small mb-0 lh-relaxed">Refuerza el pensamiento lógico, álgebra y resolución de problemas fundamentales para cálculo universitario.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm course-card p-4 rounded-4 bg-white transition-all hover-translate-y border-top border-success border-4">
                <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; font-size: 1.3rem;">
                    <i class="bi bi-book"></i>
                </div>
                <span class="badge bg-light text-success mb-2 fw-semibold">Análisis</span>
                <h4 class="fw-bold text-dark fs-4 mb-2">Lectura Crítica</h4>
                <p class="text-secondary small mb-0 lh-relaxed">Mejora la comprensión de textos académicos complejos, argumentación sólida y pensamiento analítico.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm course-card p-4 rounded-4 bg-white transition-all hover-translate-y border-top border-warning border-4">
                <div class="icon-box bg-warning bg-opacity-10 text-dark rounded-3 mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; font-size: 1.3rem;">
                    <i class="bi bi-people"></i>
                </div>
                <span class="badge bg-light text-dark mb-2 fw-semibold">Desarrollo Personal</span>
                <h4 class="fw-bold text-dark fs-4 mb-2">Habilidades Blandas</h4>
                <p class="text-secondary small mb-0 lh-relaxed">Potencia el liderazgo, la gestión eficiente del tiempo, el trabajo en equipo y la comunicación efectiva.</p>
            </div>
        </div>
    </div>
</section>

<section class="container mb-6 py-3">
    <div class="text-center mb-5">
        <span class="text-uppercase text-success fw-bold tracking-widest small d-block mb-2">Comunidad</span>
        <h2 class="fw-bold text-dark display-5 mb-2">Lo que dicen nuestros estudiantes</h2>
        <p class="text-muted">Experiencias reales de quienes superaron con éxito su transición a la universidad.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="text-warning mb-3 small">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary fst-italic mb-4 lh-relaxed">"Gracias a los cursos de matemáticas básicas logré entender los temas de cálculo sin frustrarme en mi primer semestre. La plataforma es súper intuitiva."</p>
                </div>
                <div class="d-flex align-items-center pt-3 border-top border-light">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 42px; height: 42px; font-size: 0.9rem;">CM</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Carlos Mendoza</h6>
                        <small class="text-muted">Estudiante de Ingeniería</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 bg-white rounded-4 shadow-sm border border-secondary border-opacity-10 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="text-warning mb-3 small">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-secondary fst-italic mb-4 lh-relaxed">"El módulo de lectura crítica cambió por completo mi forma de analizar textos científicos y ensayos. Totalmente recomendado para los de primer ingreso."</p>
                </div>
                <div class="d-flex align-items-center pt-3 border-top border-light">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 42px; height: 42px; font-size: 0.9rem;">AT</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Andrea Torres</h6>
                        <small class="text-muted">Estudiante de Administración</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container mb-5">
    <div class="p-5 bg-dark text-white rounded-5 shadow-lg text-center position-relative overflow-hidden border border-secondary border-opacity-25 py-6">
        <div class="position-relative" style="z-index: 2; max-width: 650px; margin: 0 auto;">
            <h2 class="fw-bold mb-3 display-5 text-white">¿Listo para asegurar tu éxito universitario?</h2>
            <p class="lead mb-4 text-light opacity-75 fs-6 lh-relaxed">Únete hoy a EduNivelam, accede a los cursos creados por docentes expertos y comienza tu camino sin vacíos académicos.</p>
            <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold text-dark px-5 py-3 shadow-sm rounded-pill hover-scale">Crear Cuenta Gratis</a>
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