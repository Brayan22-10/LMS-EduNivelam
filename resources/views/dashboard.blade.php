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
                <li><a href="{{ route('dashboard') }}" class="active">📊 Resumen General</a></li>
                <li><a href="{{ route('admin.users') }}">👥 Gestión de Usuarios</a></li>
                <li><a href="#">⚙️ Configuración del Sistema</a></li>
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
                        <div class="stat-icon bg-primary-light text-primary">👥</div>
                        <div>
                            <h6 class="text-muted mb-1">Usuarios Registrados</h6>
                            <h3 class="fw-bold mb-0">{{ \App\Models\User::count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card shadow-sm border-0 p-3 bg-white rounded">
                        <div class="stat-icon bg-success-light text-success">🛡️</div>
                        <div>
                            <h6 class="text-muted mb-1">Nivel de Seguridad</h6>
                            <h3 class="fw-bold mb-0 text-success">Óptimo</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <h4 class="mb-3 text-dark">Panel de Control General</h4>
                <p class="text-muted">
                    Este panel se encuentra protegido por un middleware de autenticación del servidor a nivel estricto. Desde aquí puedes supervisar las métricas de usuarios y administrar la configuración general del sistema mediante la barra lateral izquierda.
                </p>
                <hr>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.users') }}" class="btn btn-dark">Administrar Usuarios</a>
                    <a href="#" class="btn btn-outline-secondary">Ajustes del Sistema</a>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/dashboard.js'])
@endsection