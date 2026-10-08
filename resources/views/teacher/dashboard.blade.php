@extends('layouts.app')

@section('title', 'Panel de Docente - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/login.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        {{-- Menú Lateral del Docente --}}
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h4>Panel Docente</h4>
                <span class="sidebar-role">Cuerpo Académico</span>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="{{ route('teacher.dashboard') }}" class="active"> Inicio Docente</a></li>
                <li><a href="#"> Mis Cursos</a></li>
                <li><a href="#"> Gestión de Cursos</a></li>
                <li><a href="{{ route('settings.index') }}"> Configuración de Cuenta</a></li>
            </ul>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">Cerrar Sesión</button>
                </form>
            </div>
        </aside>

        {{-- Contenido Principal del Docente --}}
        <main class="dashboard-main-content">
            <div class="dashboard-topbar">
                <h2>Bienvenido, Docente {{ Auth::user()->name }}</h2>
                <span class="badge bg-success px-3 py-2 rounded-pill">Portal Docente</span>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                        <h3 class="fw-bold text-dark mb-3">Gestión de tutorías y nivelación</h3>
                        <p class="text-muted lead">Este es tu panel de control como instructor. Aquí podrás administrar los contenidos de tus materias a cargo, subir guías metodológicas y evaluar el progreso de los estudiantes inscritos.</p>
                        <hr class="my-4">
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#" class="btn btn-success rounded-pill px-4 fw-bold py-2 text-white">Administrar mis cursos</a>
                            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold py-2">Configurar perfil</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection