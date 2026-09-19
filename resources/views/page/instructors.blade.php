@extends('layouts.app')

@section('title', 'Instructores Expertos - EduNivelam')

@section('styles')
    @vite(['resources/css/instructors.css'])
@endsection

@section('content')
<div class="instructors-hero py-5 mb-5 text-center text-white rounded-4 shadow-lg">
    <div class="container py-4">
        <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold">👨‍🏫 Cuerpo Docente Calificado</span>
        <h1 class="display-4 fw-bold mb-3">Aprende de los mejores expertos</h1>
        <p class="lead text-light opacity-90 mx-auto" style="max-width: 700px;">
            Nuestros instructores son profesionales apasionados por la educación superior, dedicados a guiarte paso a paso para eliminar cualquier vacío académico.
        </p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        @foreach($instructors as $instructor)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 instructor-card p-3">
                    <div class="card-body text-center">
                        
                        <span class="badge {{ $instructor['badge_color'] }} mb-2 px-3 py-1 rounded-pill small">{{ $instructor['badge'] }}</span>
                        
                        <h4 class="fw-bold text-dark mb-1">{{ $instructor['name'] }}</h4>
                        <p class="text-primary small fw-semibold mb-3">{{ $instructor['role'] }}</p>
                        
                        <p class="text-muted small mb-4">
                            {{ $instructor['bio'] }}
                        </p>
                        
                        <div class="border-top pt-3">
                            <span class="text-muted extra-small d-block mb-2">Especialidad: <strong>{{ $instructor['specialty'] }}</strong></span>
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">Solicitar Asesoría</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="container mb-5">
    <div class="p-5 bg-light border rounded-4 text-center shadow-sm">
        <h3 class="fw-bold text-dark mb-3">¿Eres docente y quieres unirte al equipo?</h3>
        <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">Los instructores de EduNivelam tienen la capacidad de crear, estructurar y publicar módulos de nivelación para toda la comunidad estudiantil una vez habilitados por la administración.</p>
        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">Postularme como Instructor</a>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/instructors.js'])
@endsection