@extends('layouts.app')

@section('title', 'Noticias y Avisos - EduNivelam')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        {{-- Cambiado text-white por text-dark para que se vea perfecto con fondo claro --}}
        <h2 class="fw-bold text-dark mb-1">Avisos y Novedades Institucionales</h2>
        <p class="text-muted small">Mantente al día con las últimas actualizaciones y comunicados de EduNivelam.</p>
    </div>

    {{-- Grid de 4 columnas para tarjetas más compactas y organizadas --}}
    <div class="row g-3">
        @forelse($news as $item)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 news-card position-relative bg-dark text-white overflow-hidden" 
                     style="transition: transform 0.25s ease, box-shadow 0.25s ease; cursor: pointer;"
                     data-bs-toggle="modal" 
                     data-bs-target="#newsModal"
                     data-title="{{ $item->title }}"
                     data-category="{{ $item->category ?? 'General' }}"
                     data-date="{{ $item->created_at->format('d/m/Y H:i') }}"
                     data-author="{{ $item->user->name ?? 'Administrador' }}"
                     data-content="{{ $item->content }}"
                     data-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}">
                    
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->title }}" style="height: 120px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-muted" style="height: 100px;">
                            <i class="bi bi-newspaper fs-2"></i>
                        </div>
                    @endif

                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-info bg-opacity-25 text-info px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                {{ $item->category ?? 'General' }}
                            </span>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('d/m/Y') }}</span>
                        </div>

                        <h6 class="fw-bold text-white mb-2 text-truncate">{{ $item->title }}</h6>
                        
                        <p class="text-muted mb-3 flex-grow-1" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $item->excerpt ?? strip_tags($item->content) }}
                        </p>

                        <div class="pt-2 border-top border-secondary d-flex align-items-center justify-content-between text-info fw-semibold" style="font-size: 0.75rem;">
                            <span>Ver detalles</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h5>No hay noticias publicadas por el momento.</h5>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $news->links() }}
    </div>
</div>

{{-- Ventana Emergente (Modal) para ver el detalle sin cambiar de página --}}
<div class="modal fade" id="newsModal" tabindex="-1" aria-labelledby="newsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-secondary px-4 py-3">
                <div>
                    <span id="modalCategory" class="badge bg-info bg-opacity-25 text-info px-2 py-0.5 rounded-pill mb-1" style="font-size: 0.75rem;">General</span>
                    <h5 class="modal-title fw-bold text-white mb-0" id="newsModalLabel">Título de la Noticia</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalImageContainer" class="mb-3 d-none">
                    <img id="modalImage" src="" class="img-fluid rounded-3 w-100 shadow-sm" style="max-height: 300px; object-fit: cover;" alt="Imagen Noticia">
                </div>
                <div class="mb-3 text-muted small">
                    Emitido por: <strong id="modalAuthor" class="text-light">Administrador</strong> el <span id="modalDate">07/10/2026</span>
                </div>
                <hr class="border-secondary my-3">
                <div id="modalContent" class="text-light" style="white-space: pre-wrap; line-height: 1.7; font-size: 0.95rem;">
                    Contenido detallado de la noticia...
                </div>
            </div>
            <div class="modal-footer border-secondary px-4 py-3">
                <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .news-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,.3) !important;
    }
</style>

@section('scripts')
    @vite(['resources/js/news-modal.js'])
@endsection
@endsection