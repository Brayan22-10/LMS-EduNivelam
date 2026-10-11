@extends('layouts.app')

@section('title', 'Instructores Expertos - EduNivelam')

@section('styles')
    @vite(['resources/css/instructors.css'])
@endsection

@section('content')
<section class="hero-section position-relative py-5 overflow-hidden mb-6 bg-white text-dark rounded-5 shadow-sm border border-secondary border-opacity-10">
    <div class="container px-4 py-4 text-center">
        <div class="d-inline-flex align-items-center bg-warning bg-opacity-10 text-dark px-3 py-1 rounded-pill mb-4 fw-semibold small border border-warning border-opacity-25">
            <i class="bi bi-people-fill me-2"></i> Cuerpo Docente Calificado
        </div>
        <h1 class="display-4 fw-bold mb-4 tracking-tight text-dark lh-tight mx-auto" style="max-width: 800px;">
            Aprende de los <span class="text-primary">mejores expertos</span>
        </h1>
        <p class="lead text-secondary fs-6 mx-auto lh-relaxed" style="max-width: 700px;">
            Nuestros instructores son profesionales apasionados por la educación superior, dedicados a guiarte paso a paso para eliminar cualquier vacío académico.
        </p>
    </div>
</section>

<section class="container mb-6 py-3">
    <div class="row g-4">
        @foreach($instructors as $instructor)
            <div class="col-md-4">
                <div class="card border border-secondary border-opacity-10 shadow-sm rounded-4 h-100 instructor-card p-4 bg-white hover-translate-y d-flex flex-column justify-content-between">
                    <div class="card-body p-0 text-center">
                        
                        <div class="mb-4 d-flex justify-content-center">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 70px; height: 70px; font-size: 1.5rem;">
                                {{ strtoupper(substr($instructor['name'], 0, 2)) }}
                            </div>
                        </div>

                        <span class="badge {{ $instructor['badge_color'] }} mb-3 px-3 py-1 rounded-pill small fw-semibold">{{ $instructor['badge'] }}</span>
                        
                        <h4 class="fw-bold text-dark mb-1 fs-4">{{ $instructor['name'] }}</h4>
                        <p class="text-primary small fw-semibold mb-3">{{ $instructor['role'] }}</p>
                        
                        <p class="text-secondary small mb-4 lh-relaxed">
                            {{ $instructor['bio'] }}
                        </p>
                    </div>
                    
                    <div class="border-top border-light pt-3 mt-auto text-center">
                        <span class="text-muted small d-block mb-3">Especialidad: <strong class="text-dark">{{ $instructor['specialty'] }}</strong></span>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold w-100">Solicitar Asesoría</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="container mb-5">
    <div class="p-5 bg-dark text-white rounded-5 shadow-lg text-center position-relative overflow-hidden border border-secondary border-opacity-25 py-6">
        <div class="position-relative" style="z-index: 2; max-width: 650px; margin: 0 auto;">
            <h3 class="fw-bold text-white mb-3 fs-3">¿Eres docente y quieres unirte al equipo?</h3>
            <p class="text-light opacity-75 small mb-4 lh-relaxed">Los instructores de EduNivelam tienen la capacidad de crear, estructurar y publicar módulos de nivelación para toda la comunidad estudiantil una vez habilitados por la administración.</p>
            <a href="{{ route('contact') }}" class="btn btn-warning btn-md fw-bold text-dark px-5 py-2.5 shadow-sm rounded-pill hover-scale">Postularme como Instructor</a>
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
    @vite(['resources/js/instructors.js'])
@endsection