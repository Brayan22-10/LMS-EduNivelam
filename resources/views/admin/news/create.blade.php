@extends('layouts.app')

@section('title', 'Crear Noticia - Admin')

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

        <main class="dashboard-main-content py-3 px-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="fw-bold mb-1 text-dark fs-4">Creación de un Nuevo Aviso o Noticia</h2>
                    <p class="text-muted small mb-0">Estructura optimizada de publicación institucional.</p>
                </div>
                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Listado
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-3 py-2" role="alert">
                    <div class="fw-bold small mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Corrige los siguientes errores:</div>
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Tarjeta de ancho completo pero con padding vertical reducido --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-lg-4 w-100 mb-4">
                <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-2">
                        <div class="col-lg-8">
                            <label for="title" class="form-label small fw-semibold text-dark mb-1">Título del Aviso <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm border py-1.5 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Ej. Actualización del sistema y mantenimiento programado" required>
                        </div>
                        <div class="col-lg-4">
                            <label for="category" class="form-label small fw-semibold text-dark mb-1">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm border py-1.5" id="category" name="category" required>
                                <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>General</option>
                                <option value="Académico" {{ old('category') == 'Académico' ? 'selected' : '' }}>Académico</option>
                                <option value="Mantenimiento" {{ old('category') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="Eventos" {{ old('category') == 'Eventos' ? 'selected' : '' }}>Eventos</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="excerpt" class="form-label small fw-semibold text-dark mb-1">Resumen / Extracto Corto <span class="text-muted">(Opcional)</span></label>
                        <textarea class="form-control form-control-sm border py-1" id="excerpt" name="excerpt" rows="1" placeholder="Breve descripción para vistas previas...">{{ old('excerpt') }}</textarea>
                    </div>

                    <div class="mb-2">
                        <label for="content" class="form-label small fw-semibold text-dark mb-1">Contenido Detallado <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm border py-1" id="content" name="content" rows="3" placeholder="Escribe aquí el cuerpo completo del mensaje informativo..." required>{{ old('content') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-lg-6">
                            <label for="image" class="form-label small fw-semibold text-dark mb-1">Imagen Ilustrativa <span class="text-muted">(Opcional)</span></label>
                            <input type="file" class="form-control form-control-sm border py-1" id="image" name="image" accept="image/png, image/jpeg, image/jpg, image/webp">
                        </div>
                        <div class="col-lg-6">
                            <label for="status" class="form-label small fw-semibold text-dark mb-1">Estado de Publicación <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm border py-1.5" id="status" name="status" required>
                                <option value="publicado" {{ old('status') == 'publicado' ? 'selected' : '' }}>Publicado (Visible para todos)</option>
                                <option value="borrador" {{ old('status') == 'borrador' ? 'selected' : '' }}>Borrador (Oculto / En revisión)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm px-4 fw-semibold rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-dark btn-sm px-4 fw-semibold rounded-pill shadow-sm text-white">Guardar Noticia</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection