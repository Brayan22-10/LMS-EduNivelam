@extends('layouts.app')

@section('title', 'Panel General - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content py-4 px-4">
            <div class="dashboard-topbar mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1 text-dark-adaptive">Bienvenido, {{ Auth::user()->name }}</h2>
                    <p class="text-muted small mb-0">Panel de control y supervisión general del sistema.</p>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="theme-switch-container">
                        <button type="button" id="btn-light-mode" class="theme-switch-btn {{ !(Auth::check() && Auth::user()->high_contrast) ? 'active' : '' }}" title="Modo Claro">
                            <i class="bi bi-sun-fill"></i>
                        </button>
                        <button type="button" id="btn-dark-mode" class="theme-switch-btn {{ (Auth::check() && Auth::user()->high_contrast) ? 'active' : '' }}" title="Modo Oscuro">
                            <i class="bi bi-moon-stars-fill"></i>
                        </button>
                    </div>

                    <span class="badge bg-success text-white px-3 py-2 rounded-pill">Sistema Activo</span>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="stat-card shadow-sm border-0 p-4 rounded-4 h-100 bg-card-adaptive">
                        <h6 class="text-muted mb-2 text-uppercase small fw-bold">Usuarios Registrados</h6>
                        <h3 class="fw-bold mb-0 display-6 text-dark-adaptive">{{ $stats['users'] }}</h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card shadow-sm border-0 p-4 rounded-4 h-100 bg-card-adaptive">
                        <h6 class="text-muted mb-2 text-uppercase small fw-bold">Publicaciones Activas</h6>
                        <h3 class="fw-bold mb-0 text-success display-6">{{ $stats['posts'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-4 rounded-4 bg-card-adaptive">
                <h4 class="mb-3 fw-bold text-dark-adaptive">Panel de Control General</h4>
                <p class="text-secondary small lh-relaxed">
                    Este panel se encuentra protegido por un middleware de autenticación del servidor a nivel estricto. Desde aquí puedes supervisar las métricas de usuarios, revisar el buzón de soporte y administrar la seguridad general del sistema mediante la barra lateral izquierda.
                </p>
                <hr class="text-muted opacity-25">
                <div class="d-flex gap-2 flex-wrap pt-2">
                    @can('manage-users')
                        <a href="{{ route('admin.users') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">Administrar Usuarios</a>
                        <a href="{{ route('admin.teachers.pending') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-bold text-white">Aceptar Docentes</a>
                        <a href="{{ route('admin.emails.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">Ver Buzón IMAP</a>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">Gestión de Banners</a>
                        <a href="{{ route('admin.news.index') }}" class="btn btn-info text-white btn-sm rounded-pill px-3 fw-bold">Gestión de Noticias</a>
                        <a href="{{ route('admin.security') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Auditoría de Seguridad</a>
                    @endcan
                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">Ajustes del Sistema</a>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/dashboard.js'])
@endsection