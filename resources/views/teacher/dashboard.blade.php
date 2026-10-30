@extends('layouts.app')

@section('title', 'Panel Docente - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/login.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content">
            <div class="dashboard-topbar">
                <h2>Bienvenido, Profesor(a) {{ Auth::user()->name }}</h2>
                <span class="badge bg-success px-3 py-2 rounded-pill">Portal Docente Activo</span>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                        <h3 class="fw-bold text-dark mb-3">Gestión de tutorías y nivelación académica</h3>
                        <p class="text-muted lead">Este es tu panel de control como instructor. Aquí podrás administrar los contenidos de tus materias a cargo, subir guías metodológicas y evaluar el progreso de los estudiantes inscritos.</p>
                        <hr class="my-4">
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('admin.profile') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold py-2"><i class="bi bi-person-badge me-1"></i> Mi Perfil Docente</a>
                            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold py-2"><i class="bi bi-gear me-1"></i> Configuración de Cuenta</a>
                            <a href="{{ route('courses') }}" target="_blank" class="btn btn-primary rounded-pill px-4 fw-bold py-2"><i class="bi bi-box-arrow-up-right me-1"></i> Ver Vitrina / Cursos</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection