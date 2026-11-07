@extends('layouts.app')

@section('title', 'Panel de Estudiante - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        {{-- Contenido Principal del Estudiante --}}
        <main class="dashboard-main-content py-4 px-3 px-sm-4">
            <div class="dashboard-topbar d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
                <div>
                    <h2 class="fw-bold mb-1 text-dark-adaptive">Bienvenido, Estudiante {{ Auth::user()->name }}</h2>
                    <p class="text-muted small mb-0">Portal y espacio de nivelación académica.</p>
                </div>
                <span class="badge bg-primary px-3 py-2 rounded-pill">Portal Académico</span>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-card-adaptive">
                        <h3 class="fw-bold text-dark-adaptive mb-3">Tu espacio de nivelación activa</h3>
                        <p class="text-secondary lead fs-6 lh-relaxed">Desde aquí podrás consultar los cursos de nivelación a los que te has inscrito, revisar tus guías de estudio y hacer seguimiento a tus calificaciones de forma sencilla.</p>
                        <hr class="my-4 opacity-25">
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold py-2">Configurar mi perfil</a>
                            <a href="{{ route('courses') }}" class="btn btn-primary rounded-pill px-4 fw-bold py-2 text-white">Explorar catálogo de cursos</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/dashboard.js'])
@endsection
