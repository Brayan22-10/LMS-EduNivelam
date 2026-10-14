@extends('layouts.app')

@section('title', 'Gestión de Noticias - Admin')

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

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

            <ul class="nav nav-pills gap-2 mb-4">
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold {{ ($tab ?? 'all') == 'all' ? 'active bg-dark text-white' : 'bg-light text-dark border' }}" 
                        href="{{ route('admin.news.index', ['tab' => 'all']) }}">
                        Todas <span class="badge bg-secondary ms-1">{{ $countAll ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold {{ ($tab ?? '') == 'publicado' ? 'active bg-dark text-white' : 'bg-light text-dark border' }}" 
                        href="{{ route('admin.news.index', ['tab' => 'publicado']) }}">
                        Publicadas <span class="badge bg-success ms-1">{{ $countPublished ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold {{ ($tab ?? '') == 'programado' ? 'active bg-dark text-white' : 'bg-light text-dark border' }}" 
                        href="{{ route('admin.news.index', ['tab' => 'programado']) }}">
                        Programadas <span class="badge bg-warning text-dark ms-1">{{ $countScheduled ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold {{ ($tab ?? '') == 'borrador' ? 'active bg-dark text-white' : 'bg-light text-dark border' }}" 
                        href="{{ route('admin.news.index', ['tab' => 'borrador']) }}">
                        Borradores <span class="badge bg-secondary ms-1">{{ $countDrafts ?? 0 }}</span>
                    </a>
                </li>
            </ul>

            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark text-white">
                            <tr>
                                <th class="py-3 px-3">ESTADO</th>
                                <th class="py-3">TÍTULO DEL AVISO</th>
                                <th class="py-3">AUTOR</th>
                                <th class="py-3">PROGRAMACIÓN / EMISIÓN</th>
                                <th class="py-3 text-end pe-3">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($news as $item)
                                <tr>
                                    <td class="px-3">
                                        @if($item->status == 'publicado')
                                            <span class="badge bg-success text-white px-3 py-1 rounded-pill">Publicado</span>
                                        @elseif($item->status == 'programado')
                                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="bi bi-clock me-1"></i> Programado</span>
                                        @else
                                            <span class="badge bg-secondary text-white px-3 py-1 rounded-pill">Borrador</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $item->title }}</td>
                                    <td class="text-muted">{{ $item->user->name ?? 'Administrador' }}</td>
                                    <td class="text-muted small">
                                        @if($item->scheduled_at)
                                            <i class="bi bi-calendar-event text-primary me-1"></i> {{ \Carbon\Carbon::parse($item->scheduled_at)->format('d/m/Y H:i') }}
                                        @else
                                            {{ $item->created_at->format('d/m/Y H:i') }}
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.news.show', $item->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                <i class="bi bi-eye me-1"></i> Ver
                                            </a>
                                            <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 text-dark">
                                                <i class="bi bi-pencil me-1"></i> Editar
                                            </a>
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
                                        No hay avisos o noticias registrados en esta sección.
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