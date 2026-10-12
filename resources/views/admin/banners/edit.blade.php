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
                    <h2 class="fw-bold mb-1 text-dark fs-4">Editar Configuración de Sección</h2>
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

            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 w-100 mb-4">
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3 pb-3 border-bottom">
                        <label for="page_identifier" class="form-label fw-bold text-dark small mb-1">Selecciona la Página Destino <span class="text-danger">*</span></label>
                        <select class="form-select border py-1.5" id="page_identifier" name="page_identifier" required>
                            <option value="home" {{ old('page_identifier', $banner->page_identifier) == 'home' ? 'selected' : '' }}>Home</option>
                            <option value="about" {{ old('page_identifier', $banner->page_identifier) == 'about' ? 'selected' : '' }}>About</option>
                            <option value="news" {{ old('page_identifier', $banner->page_identifier) == 'news' ? 'selected' : '' }}>News</option>
                            <option value="courses" {{ old('page_identifier', $banner->page_identifier) == 'courses' ? 'selected' : '' }}>Courses</option>
                            <option value="instructors" {{ old('page_identifier', $banner->page_identifier) == 'instructors' ? 'selected' : '' }}>Instructors</option>
                            <option value="contact" {{ old('page_identifier', $banner->page_identifier) == 'contact' ? 'selected' : '' }}>Contact</option>
                        </select>
                        <div class="form-text" style="font-size: 0.75rem;">Los campos se adaptarán ordenadamente según la sección elegida.</div>
                    </div>

                    <div id="dynamicFieldsContainer">
                        <div class="py-2 px-3 small rounded-3 mb-3 border shadow-sm d-flex align-items-center" style="background-color: #f8f9fa !important; border-color: #dee2e6 !important;">
                            <i class="bi bi-info-circle-fill me-2 text-primary fs-6"></i> 
                            <span style="color: #212529 !important; font-weight: 600 !important;">Modificando los parámetros de la sección seleccionada.</span>
                        </div>

                        <div class="mb-3" id="fieldBadgeWrapper">
                            <label for="badge_text" class="form-label small fw-semibold text-dark mb-1">Texto de la Etiqueta (Badge)</label>
                            <input type="text" class="form-control border py-1.5" id="badge_text" name="badge_text" value="{{ old('badge_text', $banner->badge_text) }}" placeholder="Ej. Oferta Académica U">
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label small fw-semibold text-dark mb-1">Título Principal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border py-1.5" id="title" name="title" value="{{ old('title', $banner->title) }}" placeholder="Título principal..." required>
                        </div>

                        <div class="mb-3">
                            <label for="subtitle" class="form-label small fw-semibold text-dark mb-1">Subtítulo / Descripción Corta</label>
                            <textarea class="form-control border py-1.5" id="subtitle" name="subtitle" rows="2" placeholder="Párrafo descriptivo de la cabecera...">{{ old('subtitle', $banner->subtitle) }}</textarea>
                        </div>

                        <div class="row g-3 mb-2 align-items-center">
                            <div class="col-md-7">
                                <label for="image_path" class="form-label small fw-semibold text-dark mb-1">Imagen Ilustrativa / Fondo <span class="text-muted">(Opcional)</span></label>
                                <input type="file" class="form-control border py-1.5" id="image_path" name="image_path" accept="image/png, image/jpeg, image/jpg, image/webp">
                                @if($banner->image_path)
                                    <div class="form-text text-success" style="font-size: 0.75rem;">Imagen actual cargada correctamente.</div>
                                @endif
                            </div>
                            <div class="col-md-5 pt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-dark ms-2" for="is_active">Sección Activa en Plataforma</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
                            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm px-4 fw-semibold rounded-pill">Cancelar</a>
                            <button type="submit" class="btn btn-dark btn-sm px-4 fw-semibold rounded-pill shadow-sm text-white">Actualizar Configuración</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection