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
                <span class="sidebar-role">Gestión General</span>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="{{ route('dashboard') }}" class="active"> Resumen General</a></li>
                
                @can('manage-users')
                    <li><a href="{{ route('admin.users') }}"> Gestión de Usuarios</a></li>
                    <li><a href="{{ route('admin.teachers.pending') }}"> Aceptar Docentes</a></li>
                    <li><a href="{{ route('admin.emails.index') }}"> Buzón IMAP</a></li>
                    <li><a href="{{ route('admin.security') }}"> Seguridad y Accesos</a></li>
                @endcan

                {{-- Enlace unificado al Centro de Configuración y Herramientas --}}
                <li><a href="{{ route('settings.index') }}"> Configuración del Sistema</a></li>
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
                <h2>Bienvenido, {{ Auth::user()->name }}</h2>
                <span class="badge bg-success text-white p-2">Sistema Activo</span>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="stat-card shadow-sm border-0 p-3 bg-white rounded">
                        <div>
                            <h6 class="text-muted mb-1">Usuarios Registrados</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['users'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card shadow-sm border-0 p-3 bg-white rounded">
                        <div>
                            <h6 class="text-muted mb-1">Publicaciones Activas</h6>
                            <h3 class="fw-bold mb-0 text-success">{{ $stats['posts'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <h4 class="mb-3 text-dark">Panel de Control General</h4>
                <p class="text-muted">
                    Este panel se encuentra protegido por un middleware de autenticación del servidor a nivel estricto. Desde aquí puedes supervisar las métricas de usuarios, revisar el buzón de soporte y administrar la seguridad general del sistema mediante la barra lateral izquierda.
                </p>
                <hr>
                <div class="d-flex gap-3 flex-wrap">
                    @can('manage-users')
                        <a href="{{ route('admin.users') }}" class="btn btn-dark">Administrar Usuarios</a>
                        <a href="{{ route('admin.teachers.pending') }}" class="btn btn-success">Aceptar Docentes</a>
                        <a href="{{ route('admin.emails.index') }}" class="btn btn-primary"> Ver Buzón IMAP</a>
                        <a href="{{ route('admin.security') }}" class="btn btn-outline-danger"> Auditoría de Seguridad</a>
                    @endcan
                    {{-- Botón rápido en el panel hacia configuración --}}
                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">Ajustes del Sistema</a>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/dashboard.js'])
@endsection