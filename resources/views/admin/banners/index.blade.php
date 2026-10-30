@extends('layouts.app')

@section('title', 'Gestión de Banners - Admin')

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
                    <h2 class="fw-bold mb-1 text-dark-adaptive">Gestión de Banners y Encabezados</h2>
                    <p class="text-muted small mb-0">Administra los banners dinámicos de las páginas principales del sitio.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.banners.create') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Crear Nuevo Banner
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
                       href="{{ route('admin.banners.index', ['tab' => 'all']) }}">
                        Todas <span class="badge bg-secondary ms-1">{{ $countAll ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold {{ ($tab ?? '') == 'active' ? 'active bg-dark text-white' : 'bg-light text-dark border' }}"
                       href="{{ route('admin.banners.index', ['tab' => 'active']) }}">
                        Activas <span class="badge bg-success ms-1">{{ $countActive ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4 fw-semibold {{ ($tab ?? '') == 'hidden' ? 'active bg-dark text-white' : 'bg-light text-dark border' }}"
                       href="{{ route('admin.banners.index', ['tab' => 'hidden']) }}">
                        Ocultas <span class="badge bg-secondary ms-1">{{ $countHidden ?? 0 }}</span>
                    </a>
                </li>
            </ul>

            <div class="card border-0 shadow-sm rounded-4 bg-card-adaptive p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark text-white">
                            <tr>
                                <th class="py-3 px-3">PÁGINA</th>
                                <th class="py-3">TÍTULO PRINCIPAL</th>
                                <th class="py-3">ESTADO</th>
                                <th class="py-3 text-end pe-3">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $banner)
                                <tr>
                                    <td class="px-3 fw-semibold text-uppercase">
                                        <code>{{ $banner->page_identifier }}</code>
                                    </td>
                                    <td class="fw-semibold">{{ $banner->title }}</td>
                                    <td>
                                        <span class="badge {{ $banner->is_active ? 'bg-success' : 'bg-secondary' }} text-white px-3 py-1 rounded-pill">
                                            {{ $banner->is_active ? 'Activo' : 'Oculto' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                                <i class="bi bi-pencil me-1"></i> Editar
                                            </a>
                                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar este banner?');" class="d-inline">
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
                                    <td colspan="4" class="text-center text-muted py-5">
                                        @if(($tab ?? 'all') === 'active')
                                            No hay banners activos.
                                        @elseif(($tab ?? 'all') === 'hidden')
                                            No hay banners ocultos.
                                        @else
                                            No hay banners registrados actualmente.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($banners->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $banners->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection