@extends('layouts.app')

@section('title', 'Editar Banner - Admin')

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
                    <li><a href="{{ route('admin.news.index') }}"> Gestión de Noticias</a></li>
                    <li><a href="{{ route('admin.banners.index') }}" class="active"> Gestión de Banners</a></li>
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
                    <h2 class="fw-bold mb-1 text-dark fs-4">Editar Banner</h2>
                    <p class="text-muted small mb-0">Modifica los parámetros del encabezado institucional.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-3 py-2" role="alert">
                    <div class="fw-bold small mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Corrige los siguientes campos:</div>
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-lg-4 w-100 mb-4">
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-2">
                        <div class="col-lg-6">
                            <label for="page_identifier" class="form-label small fw-semibold text-dark mb-1">Página Destino <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm border py-1.5 @error('page_identifier') is-invalid @enderror" id="page_identifier" name="page_identifier" required>
                                <option value="home" {{ old('page_identifier', $banner->page_identifier) == 'home' ? 'selected' : '' }}>Home (Inicio)</option>
                                <option value="about" {{ old('page_identifier', $banner->page_identifier) == 'about' ? 'selected' : '' }}>About (Acerca de)</option>
                                <option value="news" {{ old('page_identifier', $banner->page_identifier) == 'news' ? 'selected' : '' }}>News (Noticias)</option>
                                <option value="courses" {{ old('page_identifier', $banner->page_identifier) == 'courses' ? 'selected' : '' }}>Cursos</option>
                                <option value="instructors" {{ old('page_identifier', $banner->page_identifier) == 'instructors' ? 'selected' : '' }}>Instructores</option>
                                <option value="contact" {{ old('page_identifier', $banner->page_identifier) == 'contact' ? 'selected' : '' }}>Contact (Contacto)</option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="title" class="form-label small fw-semibold text-dark mb-1">Título del Banner <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm border py-1.5 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="subtitle" class="form-label small fw-semibold text-dark mb-1">Subtítulo / Descripción <span class="text-muted">(Opcional)</span></label>
                        <textarea class="form-control form-control-sm border py-1" id="subtitle" name="subtitle" rows="2">{{ old('subtitle', $banner->subtitle) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-lg-6">
                            <label for="image_path" class="form-label small fw-semibold text-dark mb-1">Imagen de Fondo <span class="text-muted">(Opcional)</span></label>
                            <input type="file" class="form-control form-control-sm border py-1" id="image_path" name="image_path" accept="image/png, image/jpeg, image/jpg, image/webp">
                            @if($banner->image_path)
                                <div class="form-text text-success" style="font-size: 0.75rem;">Imagen actual cargada correctamente.</div>
                            @endif
                        </div>
                        <div class="col-lg-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold text-dark ms-2" for="is_active">Banner Activo (Visible en la plataforma)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm px-4 fw-semibold rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-dark btn-sm px-4 fw-semibold rounded-pill shadow-sm text-white">Actualizar Banner</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection