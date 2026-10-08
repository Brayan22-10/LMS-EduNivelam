@extends('layouts.app')

@section('title', 'Panel de Estudiante - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/login.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        {{-- Menú Lateral del Estudiante (Sin accesos de administrador) --}}
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h4>Panel Estudiante</h4>
                <span class="sidebar-role">Nivelación Académica</span>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="{{ route('student.dashboard') }}" class="active"> Inicio Estudiante</a></li>
                <li><a href="#" onclick="return false;" class="text-muted text-decoration-line-through" title="Próximamente"> Mis Cursos Inscritos</a></li>
                <li><a href="#" onclick="return false;" class="text-muted text-decoration-line-through" title="Próximamente"> Calificaciones</a></li>
                <li><a href="{{ route('settings.index') }}"> Configuración de Cuenta</a></li>
            </ul>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">Cerrar Sesión</button>
                </form>
            </div>
        </aside>

        {{-- Contenido Principal del Estudiante --}}
        <main class="dashboard-main-content">
            <div class="dashboard-topbar">
                <h2>Bienvenido, Estudiante {{ Auth::user()->name }}</h2>
                <span class="badge bg-primary px-3 py-2 rounded-pill">Portal Académico</span>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                        <h3 class="fw-bold text-dark mb-3">Tu espacio de nivelación activa</h3>
                        <p class="text-muted lead">Desde aquí podrás consultar los cursos de nivelación a los que te has inscrito, revisar tus guías de estudio y hacer seguimiento a tus calificaciones de forma sencilla.</p>
                        <hr class="my-4">
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold py-2">Configurar mi perfil</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection