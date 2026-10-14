@extends('layouts.app')

@section('title', 'Detalle del Aviso - Admin')

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content py-3 px-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="fw-bold mb-1 text-dark fs-4">Vista Previa del Aviso</h2>
                    <p class="text-muted small mb-0">Así es exactamente como visualizan la noticia los usuarios en la plataforma.</p>
                </div>
                <div>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">
                    <div class="card border-0 shadow-lg rounded-4 text-white overflow-hidden bg-dark">
                        
                        <div class="border-bottom border-secondary border-opacity-25 px-4 py-3">
                            <span class="badge bg-info bg-opacity-25 text-info px-2 py-0.5 rounded-pill mb-1" style="font-size: 0.75rem;">
                                {{ $news->category ?? 'General' }}
                            </span>
                            <h5 class="fw-bold text-white mb-0">{{ $news->title }}</h5>
                        </div>

                        @if($news->image)
                            <div>
                                <img src="{{ asset('storage/' . $news->image) }}" class="w-100" style="height: 280px; object-fit: cover;" alt="{{ $news->title }}">
                            </div>
                        @endif

                        <div class="px-4 py-3 text-muted small border-bottom border-secondary border-opacity-25">
                            Emitido por: <strong class="text-light">{{ $news->user->name ?? 'Administrador' }}</strong> el {{ $news->created_at->format('d/m/Y H:i') }}
                        </div>

                        <div class="px-4 py-3 text-light" style="font-size: 0.95rem; line-height: 1.7;">
                            {!! $news->content !!}
                        </div>

                        <div class="border-top border-secondary border-opacity-25 px-4 py-3 d-flex justify-content-end">
                            <a href="{{ route('admin.news.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-4">
                                Volver
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection