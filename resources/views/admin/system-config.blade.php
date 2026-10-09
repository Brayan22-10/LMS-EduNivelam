@extends('layouts.app')

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h4>Panel Admin</h4>
                <span class="sidebar-role">Configuración Global</span>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="{{ route('dashboard') }}"> Resumen General</a></li>
                @can('manage-users')
                    <li><a href="{{ route('admin.users') }}"> Gestión de Usuarios</a></li>
                    <li><a href="{{ route('admin.teachers.pending') }}"> Aceptar Docentes</a></li>
                    <li><a href="{{ route('admin.emails.index') }}"> Buzón IMAP</a></li>
                    <li><a href="{{ route('admin.banners.index') }}"> Gestión de Banners</a></li>
                    <li><a href="{{ route('admin.news.index') }}"> Gestión de Noticias</a></li>
                    <li><a href="{{ route('admin.security') }}"> Seguridad y Accesos</a></li>
                    <li><a href="{{ route('admin.system.config') }}" class="active"> Configuración del Sistema</a></li>
                @endcan
            </ul>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">Cerrar Sesión</button>
                </form>
            </div>
        </aside>

        <main class="dashboard-main-content">
            <div class="dashboard-topbar">
                <h2>Configuración y Mantenimiento del Sistema</h2>
                <span class="badge bg-secondary p-2">Herramientas de Servidor</span>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 p-4 mb-4">
                <h4 class="mb-3 text-dark">Rendimiento y Limpieza del Servidor</h4>
                <p class="text-muted small">
                    Si realizas cambios en el código, rutas o configuraciones y no se reflejan de inmediato, puedes vaciar la caché compilada y optimizar el framework con un solo clic.
                </p>
                <hr>
                <form action="{{ route('admin.system.clearcache') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark" onclick="return confirm('¿Estás seguro de limpiar la caché general y optimizar el sistema?');">
                        Limpiar Caché y Optimizar Sistema
                    </button>
                </form>
            </div>

        </main>
    </div>
</div>
@endsection