@extends('layouts.app')

@section('title', 'Gestión de Noticias - Admin')

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        {{-- Menú Lateral del Administrador --}}
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

        {{-- Contenido Principal --}}
        <main class="dashboard-main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1 text-dark">Bandeja de Noticias y Avisos</h2>
                    <p class="text-muted small mb-0">Administra las comunicaciones informativas dirigidas a la comunidad académica.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.news.create') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Crear Nueva Noticia
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark text-white">
                            <tr>
                                <th class="py-3 px-3">ESTADO</th>
                                <th class="py-3">TÍTULO DEL AVISO</th>
                                <th class="py-3">AUTOR</th>
                                <th class="py-3">FECHA DE EMISIÓN</th>
                                <th class="py-3 text-end pe-3">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($news as $item)
                                <tr>
                                    <td class="px-3">
                                        <span class="badge bg-success text-white px-3 py-1 rounded-pill">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $item->title }}</td>
                                    <td class="text-muted">{{ $item->user->name ?? 'Administrador' }}</td>
                                    <td class="text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            {{-- Botón Ver minimalista estilo outline con icono --}}
                                            <a href="{{ route('admin.news.show', $item->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                <i class="bi bi-eye me-1"></i> Ver
                                            </a>
                                            {{-- Botón Editar minimalista estilo outline con icono --}}
                                            <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 text-dark">
                                                <i class="bi bi-pencil me-1"></i> Editar
                                            </a>
                                            {{-- Botón Eliminar minimalista estilo outline con icono --}}
                                            <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta noticia?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                    <i class="bi bi-trash me-1"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        No hay avisos o noticias registrados actualmente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $news->links() }}
                </div>
            </div>
        </main>
    </div>
</div>
@endsection