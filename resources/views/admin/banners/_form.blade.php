@php
    $isEdit    = isset($banner) && $banner->exists;
    $usedPages = $usedPages ?? [];
    $hasOld    = session()->hasOldInput();
    $current   = old('page_identifier', $isEdit ? $banner->page_identifier : '');
    $isActive  = $hasOld ? (bool) old('is_active') : ($isEdit ? $banner->is_active : true);

    $config = [
        'mode'            => $isEdit ? 'edit' : 'create',
        'current'         => $current,
        'placeholderHelp' => 'Selecciona una página arriba para habilitar los campos.',
        'pages'           => collect(\App\Models\Banner::PAGES)->map(fn ($p) => [
            'fields' => $p['fields'],
            'help'   => $p['help'],
        ])->all(),
        'messages'        => [
            'change' => '¿Cambiar de página? Los datos escritos en el formulario se perderán.',
        ],
    ];
@endphp

<style>
    #dynamicFieldsContainer { transition: opacity .25s ease; }
    #dynamicFieldsContainer.is-disabled { opacity: .4; pointer-events: none; }

    .banner-helper {
        background-color: #f8f9fa;
        border-color: #dee2e6 !important;
        color: #212529;
    }
    body.bg-dark-mode .banner-helper {
        background-color: #1a222d;
        border-color: #26303d !important;
        color: #f8f9fa;
    }
</style>

<form action="{{ $isEdit ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
      method="POST" enctype="multipart/form-data" id="bannerForm"
      data-config="{{ json_encode($config) }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="mb-3 pb-3 border-bottom">
        <label for="page_identifier" class="form-label fw-bold text-dark-adaptive small mb-1">
            Página Destino @unless($isEdit)<span class="text-danger">*</span>@endunless
        </label>

        @if($isEdit)
            <select class="form-select border py-1.5" id="page_identifier" disabled>
                @foreach(\App\Models\Banner::PAGES as $key => $page)
                    <option value="{{ $key }}" @selected($current === $key)>{{ $page['label'] }}</option>
                @endforeach
            </select>
            <div class="form-text" style="font-size: .75rem;">
                <i class="bi bi-lock-fill me-1"></i>
                La página no se puede cambiar. Para usar otra, elimina este banner y crea uno nuevo.
            </div>
        @else
            <select class="form-select border py-1.5 @error('page_identifier') is-invalid @enderror"
                    id="page_identifier" name="page_identifier" required>
                <option value="" disabled @selected($current === '')>-- Elige una sección de la plataforma --</option>
                @foreach(\App\Models\Banner::PAGES as $key => $page)
                    <option value="{{ $key }}"
                            @selected($current === $key)
                            @disabled(in_array($key, $usedPages, true) && $current !== $key)>
                        {{ $page['label'] }}{{ in_array($key, $usedPages, true) ? ' (ya configurada)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('page_identifier')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text" style="font-size: .75rem;">
                Los campos se adaptarán según la sección elegida. Si cambias de sección con datos escritos, se te pedirá confirmación.
            </div>
        @endif
    </div>

    <div id="dynamicFieldsContainer" class="{{ $current ? '' : 'is-disabled' }}">
        <div class="banner-helper py-2 px-3 small rounded-3 mb-3 border shadow-sm d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-2 text-primary fs-6"></i>
            <span id="helperText" class="fw-semibold"></span>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6" data-field="badge_text">
                <label for="badge_text" class="form-label small fw-semibold text-dark-adaptive mb-1">Texto de la Etiqueta (Badge)</label>
                <input type="text" class="form-control border py-1.5 @error('badge_text') is-invalid @enderror"
                       id="badge_text" name="badge_text"
                       value="{{ old('badge_text', $isEdit ? $banner->badge_text : '') }}"
                       placeholder="Ej. Oferta Académica U">
                @error('badge_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6" data-field="title">
                <label for="title" class="form-label small fw-semibold text-dark-adaptive mb-1">Título Principal <span class="text-danger">*</span></label>
                <input type="text" class="form-control border py-1.5 @error('title') is-invalid @enderror"
                       id="title" name="title" required
                       value="{{ old('title', $isEdit ? $banner->title : '') }}"
                       placeholder="Título principal...">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3" data-field="subtitle">
            <label for="subtitle" class="form-label small fw-semibold text-dark-adaptive mb-1">Subtítulo / Descripción Corta</label>
            <textarea class="form-control border py-1.5 @error('subtitle') is-invalid @enderror"
                      id="subtitle" name="subtitle" rows="2"
                      placeholder="Párrafo descriptivo de la cabecera...">{{ old('subtitle', $isEdit ? $banner->subtitle : '') }}</textarea>
            @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3 mb-2 align-items-center">
            <div class="col-md-7" data-field="image_path">
                <label for="image_path" class="form-label small fw-semibold text-dark-adaptive mb-1">
                    Imagen Ilustrativa / Fondo <span class="text-muted">(Opcional, máx. 2 MB)</span>
                </label>
                <input type="file" class="form-control border py-1.5 @error('image_path') is-invalid @enderror"
                       id="image_path" name="image_path" accept="image/png, image/jpeg, image/webp">
                @error('image_path')<div class="invalid-feedback">{{ $message }}</div>@enderror

                @if($isEdit && $banner->image_url)
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <img src="{{ $banner->image_url }}" alt="Imagen actual" class="rounded border"
                             style="height: 56px; width: auto; object-fit: cover;">
                        <span class="form-text text-success mb-0" style="font-size: .75rem;">
                            Imagen actual. Sube otra solo si quieres reemplazarla.
                        </span>
                    </div>
                @endif
            </div>

            <div class="col-md-5 pt-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active"
                           name="is_active" value="1" @checked($isActive)>
                    <label class="form-check-label small fw-semibold text-dark-adaptive ms-2" for="is_active">
                        Sección Activa en Plataforma
                    </label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3 {{ $current ? '' : 'd-none' }}" id="formActions">
            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary btn-sm px-4 fw-semibold rounded-pill">Cancelar</a>
            <button type="submit" class="btn btn-dark btn-sm px-4 fw-semibold rounded-pill shadow-sm text-white" id="submitBtn">
                {{ $isEdit ? 'Actualizar Configuración' : 'Guardar Configuración' }}
            </button>
        </div>
    </div>
</form>