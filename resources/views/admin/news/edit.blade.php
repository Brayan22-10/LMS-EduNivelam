@extends('layouts.app')

@section('title', 'Editar Noticia - Admin')

@section('styles')
    @vite(['resources/css/dashboard.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content py-3 px-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="fw-bold mb-1 text-dark fs-4">Editar Noticia o Aviso</h2>
                    <p class="text-muted small mb-0">Modifica los campos y ajusta la imagen ilustrativa del aviso institucional.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
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
                <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-2">
                        <div class="col-lg-8">
                            <label for="title" class="form-label small fw-semibold text-dark mb-1">Título del Aviso <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm border py-1.5 @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $news->title) }}" required>
                        </div>
                        <div class="col-lg-4">
                            <label for="category" class="form-label small fw-semibold text-dark mb-1">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm border py-1.5" id="category" name="category" required>
                                <option value="General" {{ old('category', $news->category) == 'General' ? 'selected' : '' }}>General</option>
                                <option value="Académico" {{ old('category', $news->category) == 'Académico' ? 'selected' : '' }}>Académico</option>
                                <option value="Mantenimiento" {{ old('category', $news->category) == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="Eventos" {{ old('category', $news->category) == 'Eventos' ? 'selected' : '' }}>Eventos</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="excerpt" class="form-label small fw-semibold text-dark mb-1">Resumen / Extracto Corto <span class="text-muted">(Opcional)</span></label>
                        <textarea class="form-control form-control-sm border py-1" id="excerpt" name="excerpt" rows="1">{{ old('excerpt', $news->excerpt) }}</textarea>
                    </div>

                    <div class="mb-2">
                        <label for="content" class="form-label small fw-semibold text-dark mb-1">Contenido Detallado <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm border py-1" id="content" name="content" rows="3" required>{{ old('content', $news->content) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-lg-12">
                            <label class="form-label small fw-semibold text-dark mb-1">Imagen Ilustrativa <span class="text-muted">(Opcional)</span></label>
                            
                            @if($news->image)
                                <div class="mb-2 p-2 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $news->image) }}" id="existingImageSrc" class="rounded-2 border" style="height: 50px; width: 80px; object-fit: cover;" alt="Actual">
                                        <div>
                                            <span class="text-muted small d-block">Imagen actual cargada en el sistema.</span>
                                            <button type="button" id="reajustBtn" class="btn btn-link btn-sm p-0 text-decoration-underline fw-semibold" style="font-size: 0.8rem;">
                                                <i class="bi bi-crop me-1"></i> Reajustar encuadre / zoom de esta foto
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                                        <label class="form-check-label text-danger small fw-bold" for="remove_image">
                                            Eliminar foto
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <input type="file" class="form-control form-control-sm border py-1" id="imageInput" accept="image/png, image/jpeg, image/jpg, image/webp">
                            <input type="hidden" name="cropped_image" id="croppedImageOutput">
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Sube una nueva foto o reajusta la actual.</small>

                            <div id="cropperWrapper" class="mt-3 p-3 bg-dark rounded-4 d-none">
                                <p class="text-white small fw-bold mb-2"><i class="bi bi-crop me-1"></i> Ajusta el zoom y encuadre de la imagen:</p>
                                <div style="width: 100%; height: 320px; background-color: #000; overflow: hidden; display: flex; align-items: center; justify-content: center;" class="rounded-3 mb-3 border border-secondary">
                                    <img id="imageToCrop" src="" alt="Previsualización" style="max-width: 100%; display: block;">
                                </div>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" id="cancelCropButton" class="btn btn-outline-light btn-sm rounded-pill px-3">Cancelar</button>
                                    <button type="button" id="cropButton" class="btn btn-success btn-sm rounded-pill px-4 fw-bold">Aplicar Recorte</button>
                                </div>
                            </div>

                            <div id="previewContainer" class="mt-2 d-none">
                                <span class="text-success small fw-bold d-block mb-1"><i class="bi bi-check-circle-fill me-1"></i> Imagen ajustada lista para guardar:</span>
                                <img id="croppedPreview" class="rounded-3 border shadow-sm" style="height: 90px; object-fit: cover;" alt="Preview">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <label for="scheduled_at" class="form-label small fw-semibold text-dark mb-1">Programar Fecha y Hora de Publicación <span class="text-muted">(Opcional)</span></label>
                            <input type="datetime-local" class="form-control form-control-sm border py-1.5" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $news->scheduled_at ? \Carbon\Carbon::parse($news->scheduled_at)->format('Y-m-d\TH:i') : '') }}">
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Si seleccionas una fecha futura, la noticia permanecerá oculta hasta que llegue el día y la hora programada.</small>
                        </div>

                        <div class="col-lg-12">
                            <label for="status" class="form-label small fw-semibold text-dark mb-1">Estado de Publicación <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm border py-1.5" id="status" name="status" required>
                                <option value="publicado" {{ old('status', $news->status) == 'publicado' ? 'selected' : '' }}>Publicado (Visible)</option>
                                <option value="borrador" {{ old('status', $news->status) == 'borrador' ? 'selected' : '' }}>Borrador (Oculto)</option>
                                <option value="programado" {{ old('status', $news->status) == 'programado' ? 'selected' : '' }}>Próximamente a publicar (Programado)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm px-4 fw-semibold rounded-pill">Cancelar</a>
                        <button type="submit" class="btn btn-dark btn-sm px-4 fw-semibold rounded-pill shadow-sm text-white">Actualizar Noticia</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    @vite(['resources/js/news-cropper.js'])
@endsection