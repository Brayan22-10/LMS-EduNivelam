@extends('layouts.app')

@section('title', 'Editar Banner / Sección - Admin')

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
                    <h2 class="fw-bold mb-1 text-dark-adaptive fs-4">Editar Configuración de Sección</h2>
                    <p class="text-muted small mb-0">Modifica los parámetros de la página seleccionada.</p>
                </div>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Listado
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3 py-2 px-3" role="alert">
                    <div class="fw-bold small mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> Corrige los errores del formulario.</div>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 bg-card-adaptive p-4 w-100 mb-4">
                @include('admin.banners._form')
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/banner-form.js'])
@endsection