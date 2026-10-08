@extends('layouts.app')

@section('title', 'Detalle del Aviso - Admin')

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
                <li><a href="{{ route('dashboard') }}"> Resumen General</a></li>
                @can('manage-users')
                    <li><a href="{{ route('admin.users') }}"> Gestión de Usuarios</a></li>
                    <li><a href="{{ route('admin.teachers.pending') }}"> Aceptar Docentes</a></li>
                    <li><a href="{{ route('admin.emails.index') }}"> Buzón IMAP</a></li>
                    <li><a href="{{ route('admin.news.index') }}" class="active"> Gestión de Noticias</a></li>
                    <li><a href="{{ route('admin.security') }}"> Seguridad y Accesos</a></li>
                @endcan
                <li><a href="{{ route('settings.index') }}"> Configuración del Sistema</a></li>
            </ul>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">Cerrar Sesión</button>
                </form>
            </div>
        </aside>

        <main class="dashboard-main-content py-4 px-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1 text-dark">Detalle del Aviso</h2>
                    <p class="text-muted small mb-0">Vista previa de la publicación tal como la visualizarán los usuarios.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-lg-5">
                <div class="mb-3">
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill mb-2">
                        {{ ucfirst($news->status) }}
                    </span>
                    <h3 class="fw-bold text-dark mb-2">{{ $news->title }}</h3>
                    <small class="text-muted">Emitido por: <strong>{{ $news->user->name ?? 'Administrador' }}</strong> el {{ $news->created_at->format('d/m/Y H:i') }}</small>
                </div>

                <hr class="my-4">

                <div class="text-secondary" style="white-space: pre-wrap; line-height: 1.8; font-size: 1rem;">
                    {{ $news->content }}
                </div>
            </div>
        </main>
    </div>
</div>
@endsection