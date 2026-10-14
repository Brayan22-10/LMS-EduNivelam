@extends('layouts.app')

@section('title', 'Mi Perfil - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/settings/settings.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content py-3 px-lg-4">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="fw-bold mb-1">Gestión de Perfil</h2>
                    <p class="text-muted mb-0">Administra tu identidad digital, enlaces profesionales, logros y seguridad de tu cuenta.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <span class="badge bg-dark text-white px-3 py-2 rounded-pill fs-6">
                        Rol: {{ ucfirst(Auth::user()->role ?? 'Estudiante') }}
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-4 col-xl-3">
                    <div class="card card-settings p-3 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center px-2 py-2 mb-3 border-bottom">
                            <div class="avatar-upload-box me-3" style="width: 40px; height: 40px; min-width: 40px;">
                                @if(Auth::user()->avatar && Storage::disk('public')->exists(Auth::user()->avatar))
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="avatar-preview">
                                @else
                                    <div class="avatar-preview bg-secondary text-white d-flex align-items-center justify-content-center fw-bold fs-6">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-truncate">
                                <h6 class="fw-bold mb-0 text-truncate fs-6">{{ Auth::user()->name }}</h6>
                                <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">{{ Auth::user()->email }}</small>
                            </div>
                        </div>

                        <div class="nav flex-column settings-sidebar" id="profileTab" role="tablist">
                            <button class="nav-link active text-start d-flex align-items-center" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                                <i class="bi bi-person-gear fs-6 me-2"></i> Información General
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="preview-tab" data-bs-toggle="pill" data-bs-target="#preview" type="button" role="tab">
                                <i class="bi bi-eye fs-6 me-2"></i> Vista Previa del Perfil
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="social-tab" data-bs-toggle="pill" data-bs-target="#social" type="button" role="tab">
                                <i class="bi bi-globe fs-6 me-2"></i> Redes y Enlaces
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="achievements-tab" data-bs-toggle="pill" data-bs-target="#achievements" type="button" role="tab">
                                <i class="bi bi-award fs-6 me-2"></i> Logros e Insignias
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="sessions-tab" data-bs-toggle="pill" data-bs-target="#sessions" type="button" role="tab">
                                <i class="bi bi-laptop fs-6 me-2"></i> Sesiones y Actividad
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-xl-9">
                    <div class="tab-content pb-5" id="profileTabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold mb-1">Información de Perfil</h4>
                                <p class="text-muted small mb-4">Actualiza tu foto de avatar, nombre de usuario y descripción profesional.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <form action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-4 d-flex align-items-center gap-3">
                                        <div class="avatar-upload-box" style="width: 60px; height: 60px; min-width: 60px;">
                                            @if(Auth::user()->avatar && Storage::disk('public')->exists(Auth::user()->avatar))
                                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" id="existingAvatarSrc" alt="Avatar" class="avatar-preview">
                                            @else
                                                <div class="avatar-preview bg-secondary text-white d-flex align-items-center justify-content-center fw-bold fs-4">
                                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="form-label fw-semibold">Fotografía de Perfil</label>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <input type="file" id="imageInput" class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg, image/webp" style="max-width: 280px;">
                                                <input type="hidden" name="cropped_avatar" id="croppedImageOutput">
                                                
                                                @if(Auth::user()->avatar)
                                                    <div class="form-check m-0">
                                                        <input class="form-check-input border-danger" type="checkbox" name="remove_avatar" value="1" id="removeAvatarCheck">
                                                        <label class="form-check-label text-danger small fw-semibold" for="removeAvatarCheck">
                                                            Quitar foto actual
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>

                                            @if(Auth::user()->avatar && Storage::disk('public')->exists(Auth::user()->avatar))
                                                <div class="mt-1">
                                                    <button type="button" 
                                                            id="reajustAvatarBtn" 
                                                            data-original="{{ Auth::user()->original_avatar && Storage::disk('public')->exists(Auth::user()->original_avatar) ? asset('storage/' . Auth::user()->original_avatar) : asset('storage/' . Auth::user()->avatar) }}" 
                                                            class="btn btn-link btn-sm p-0 text-decoration-underline fw-semibold text-primary" 
                                                            style="font-size: 0.8rem;">
                                                        <i class="bi bi-crop me-1"></i> Reajustar encuadre / zoom desde la imagen original
                                                    </button>
                                                </div>
                                            @endif

                                            <div class="form-text text-muted small">Formatos: JPEG, PNG, WEBP. Máx: 2MB.</div>

                                            <div id="cropperWrapper" class="mt-3 p-3 bg-dark rounded-4 d-none">
                                                <p class="text-white small fw-bold mb-2"><i class="bi bi-crop me-1"></i> Ajusta el encuadre cuadrado de tu avatar:</p>
                                                <div style="width: 100%; height: 300px; background-color: #000; overflow: hidden; display: flex; align-items: center; justify-content: center;" class="rounded-3 mb-3 border border-secondary">
                                                    <img id="imageToCrop" src="" alt="Previsualización" style="max-width: 100%; display: block;">
                                                </div>
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <button type="button" id="cancelCropButton" class="btn btn-outline-light btn-sm rounded-pill px-3">Cancelar</button>
                                                    <button type="button" id="cropButton" class="btn btn-success btn-sm rounded-pill px-4 fw-bold">Aplicar Recorte</button>
                                                </div>
                                            </div>

                                            <div id="previewContainer" class="mt-2 d-none">
                                                <span class="text-success small fw-bold d-block mb-1"><i class="bi bi-check-circle-fill me-1"></i> Avatar ajustado listo para guardar:</span>
                                                <img id="croppedPreview" class="rounded-circle border shadow-sm" style="width: 70px; height: 70px; object-fit: cover;" alt="Preview">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nombre de usuario</label>
                                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-control py-2" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Correo Electrónico</label>
                                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="form-control py-2" required>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Biografía o Descripción</label>
                                        <textarea name="bio" rows="3" class="form-control" placeholder="Cuéntanos sobre tus intereses de aprendizaje autónomo...">{{ old('bio', Auth::user()->bio ?? '') }}</textarea>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="preview" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold mb-1">Vista Previa de tu Perfil Público</h4>
                                <p class="text-muted small mb-4">Así es como los demás miembros de la plataforma visualizan tu tarjeta de presentación.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <div class="card border shadow-sm rounded-4 p-4 text-center mx-auto bg-white" style="max-width: 450px;">
                                    <div class="mx-auto mb-3" style="width: 90px; height: 90px;">
                                        @if(Auth::user()->avatar && Storage::disk('public')->exists(Auth::user()->avatar))
                                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm">
                                        @else
                                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-2 w-100 h-100 shadow-sm">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <h4 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h4>
                                    <span class="badge bg-dark text-white px-3 py-1 rounded-pill mb-2 fs-6">
                                        {{ ucfirst(Auth::user()->role ?? 'Estudiante') }}
                                    </span>
                                    <p class="text-muted small mb-3 px-2">
                                        {{ Auth::user()->bio ?? 'Aún no has agregado una biografía en tu perfil. ¡Cuéntanos algo sobre ti!' }}
                                    </p>
                                    <div class="d-flex justify-content-center gap-2 mb-4">
                                        @if(!empty(Auth::user()->github_url))
                                            <a href="{{ Auth::user()->github_url }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-github"></i></a>
                                        @endif
                                        @if(!empty(Auth::user()->linkedin_url))
                                            <a href="{{ Auth::user()->linkedin_url }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-linkedin"></i></a>
                                        @endif
                                        @if(!empty(Auth::user()->portfolio_url))
                                            <a href="{{ Auth::user()->portfolio_url }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-globe"></i></a>
                                        @endif
                                    </div>
                                    <hr class="text-muted opacity-25 my-2">
                                    <div class="text-start mt-3">
                                        <small class="fw-bold text-secondary text-uppercase d-block mb-2" style="font-size: 0.75rem;">Insignias Destacadas</small>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-star-fill text-warning me-1"></i> Desarrollador Junior</span>
                                            <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-shield-check text-success me-1"></i> Seguridad Web</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="social" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold mb-1">Redes Sociales y Enlaces Académicos</h4>
                                <p class="text-muted small mb-4">Conecta tus perfiles profesionales y repositorios de código para la comunidad.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <form action="{{ route('settings.profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold"><i class="bi bi-github me-1"></i> Perfil de GitHub</label>
                                        <input type="url" name="github_url" class="form-control py-2" placeholder="https://github.com/tu-usuario" value="{{ old('github_url', Auth::user()->github_url ?? '') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold"><i class="bi bi-linkedin me-1"></i> Perfil de LinkedIn</label>
                                        <input type="url" name="linkedin_url" class="form-control py-2" placeholder="https://linkedin.com/in/tu-usuario" value="{{ old('linkedin_url', Auth::user()->linkedin_url ?? '') }}">
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold"><i class="bi bi-globe me-1"></i> Sitio Web o Portafolio Personal</label>
                                        <input type="url" name="portfolio_url" class="form-control py-2" placeholder="https://tuportafolio.com" value="{{ old('portfolio_url', Auth::user()->portfolio_url ?? '') }}">
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm">Actualizar Enlaces</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="achievements" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold mb-1">Logros e Insignias de Aprendizaje</h4>
                                <p class="text-muted small mb-4">Resumen de tu progreso autónomo y reconocimientos obtenidos en los cursos.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 text-center border">
                                            <i class="bi bi-journal-check fs-3 text-dark mb-1 d-block"></i>
                                            <span class="text-muted small d-block">Cursos Completados</span>
                                            <h3 class="fw-bold text-dark mb-0">4</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 text-center border">
                                            <i class="bi bi-award-fill fs-3 text-warning mb-1 d-block"></i>
                                            <span class="text-muted small d-block">Insignias Obtenidas</span>
                                            <h3 class="fw-bold text-dark mb-0">7</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3 text-center border">
                                            <i class="bi bi-clock-history fs-3 text-primary mb-1 d-block"></i>
                                            <span class="text-muted small d-block">Horas de Estudio</span>
                                            <h3 class="fw-bold text-dark mb-0">32h</h3>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-3">Tus Insignias Recientes</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="badge bg-dark text-white p-2 px-3 rounded-pill d-flex align-items-center gap-2">
                                        <i class="bi bi-star-fill text-warning"></i> Desarrollador Backend Junior
                                    </div>
                                    <div class="badge bg-dark text-white p-2 px-3 rounded-pill d-flex align-items-center gap-2">
                                        <i class="bi bi-shield-check text-success"></i> Fundamentos de Seguridad Web
                                    </div>
                                    <div class="badge bg-dark text-white p-2 px-3 rounded-pill d-flex align-items-center gap-2">
                                        <i class="bi bi-code-slash text-info"></i> Maestro de Git y Control de Versiones
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="sessions" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold mb-1">Historial de Actividad y Sesiones</h4>
                                <p class="text-muted small mb-4">Dispositivos conectados recientemente y bitácora de actividad de tu cuenta.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <h6 class="fw-bold mb-3">Dispositivos Activos</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-hover align-middle small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Dirección IP</th>
                                                <th>Navegador / Dispositivo</th>
                                                <th>Última Actividad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeDevices ?? [] as $device)
                                                <tr>
                                                    <td><code>{{ $device->ip_address }}</code></td>
                                                    <td class="text-truncate" style="max-width: 250px;">{{ $device->user_agent }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($device->last_active_at)->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">Sesión actual registrada en este navegador.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <h6 class="fw-bold mb-3">Últimas Acciones Registradas</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha y Hora</th>
                                                <th>Acción</th>
                                                <th>IP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activityLogs ?? [] as $log)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                                                    <td class="fw-semibold">{{ $log->action }}</td>
                                                    <td><code>{{ $log->ip_address }}</code></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">Sin registros de actividad recientes.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    @vite(['resources/js/dashboard.js', 'resources/js/profile-cropper.js'])
@endsection