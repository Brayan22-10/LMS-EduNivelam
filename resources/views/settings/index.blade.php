@extends('layouts.app')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/settings/settings.css'])
@endsection

@section('hide_footer')
@endsection

@section('content')
@include('layouts.navigation')

<div class="dashboard-fullscreen-fix" data-server-active="{{ (session('success') || session('cache_modal') || session('db_report_modal') || session('log_modal') || session('diagnostic_modal')) ? 'true' : 'false' }}">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content py-3 px-lg-4">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Centro de Configuración y Herramientas</h2>
                    <p class="text-muted mb-0">Gestiona tu identidad digital, credenciales de acceso y preferencias avanzadas del sistema.</p>
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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Por favor corrige los siguientes errores:</div>
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-4 col-xl-3">
                    <div class="card card-settings p-3 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center px-2 py-2 mb-3 border-bottom">
                            <div class="avatar-upload-box me-3" style="width: 40px; height: 40px; min-width: 40px;">
                                @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="avatar-preview">
                                @else
                                    <div class="avatar-preview bg-secondary text-white d-flex align-items-center justify-content-center fw-bold fs-6">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-truncate">
                                <h6 class="fw-bold mb-0 text-dark text-truncate fs-6">{{ $user->name }}</h6>
                                <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">{{ $user->email }}</small>
                            </div>
                        </div>

                        <div class="nav flex-column settings-sidebar" id="settingsTab" role="tablist">
                            <button class="nav-link active text-start d-flex align-items-center" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button">
                                <i class="bi bi-shield-lock fs-6 me-2"></i> Seguridad
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="appearance-tab" data-bs-toggle="pill" data-bs-target="#appearance" type="button">
                                <i class="bi bi-eye fs-6 me-2"></i> Apariencia y Accesibilidad
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="sessions-tab" data-bs-toggle="pill" data-bs-target="#sessions" type="button">
                                <i class="bi bi-laptop fs-6 me-2"></i> Sesiones y Dispositivos
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="activity-tab" data-bs-toggle="pill" data-bs-target="#activity" type="button">
                                <i class="bi bi-clock-history fs-6 me-2"></i> Historial de Actividad
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="privacy-tab" data-bs-toggle="pill" data-bs-target="#privacy" type="button">
                                <i class="bi bi-shield-check fs-6 me-2"></i> Privacidad y Datos
                            </button>
                            <button class="nav-link text-start d-flex align-items-center" id="help-tab" data-bs-toggle="pill" data-bs-target="#help" type="button">
                                <i class="bi bi-question-circle fs-6 me-2"></i> Ayuda y Comentarios
                            </button>

                            @if(isset($isAdmin) && $isAdmin)
                                <button class="nav-link text-start d-flex align-items-center" id="server-tab" data-bs-toggle="pill" data-bs-target="#server" type="button">
                                    <i class="bi bi-hdd-rack fs-6 me-2"></i> Configuración del Servidor
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-xl-9">
                    <div class="tab-content pb-5" id="settingsTabContent">
                        
                        <div class="tab-pane fade show active" id="security" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold text-dark mb-1">Seguridad y Credenciales</h4>
                                <p class="text-muted small mb-4">Protege tu cuenta actualizando tu contraseña bajo los estándares avanzados de seguridad.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <form action="{{ route('settings.password.update') }}" method="POST" id="passwordUpdateForm" data-username="{{ $user->name }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label for="current_password" class="form-label fw-semibold text-dark">Contraseña Actual</label>
                                        <input type="password" class="form-control py-2" id="current_password" name="current_password" placeholder="••••••••••••" required>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label fw-semibold text-dark">Nueva Contraseña</label>
                                            <input type="password" class="form-control py-2" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                                            
                                            <div class="progress mt-2" style="height: 6px;">
                                                <div id="password-strength-bar" class="progress-bar bg-danger" role="progressbar" style="width: 0%;"></div>
                                            </div>
                                            <small id="password-strength-text" class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Seguridad: Muy débil</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="password_confirmation" class="form-label fw-semibold text-dark">Confirmar Nueva Contraseña</label>
                                            <input type="password" class="form-control py-2" id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña" required>
                                            <div id="match-status" class="form-text mt-1" style="font-size: 0.75rem;"></div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded-3 mb-4">
                                        <small class="fw-bold text-dark d-block mb-2">Requisitos de seguridad obligatorios:</small>
                                        <ul class="list-unstyled mb-0 small" style="font-size: 0.8rem;">
                                            <li id="req-length" class="text-danger mb-1"><i class="bi bi-x-circle-fill me-1"></i> Mínimo 8 caracteres</li>
                                            <li id="req-space" class="text-danger mb-1"><i class="bi bi-x-circle-fill me-1"></i> Sin espacios en blanco</li>
                                            <li id="req-letter" class="text-danger mb-1"><i class="bi bi-x-circle-fill me-1"></i> Al menos una letra (mayúscula o minúscula)</li>
                                            <li id="req-number" class="text-danger mb-1"><i class="bi bi-x-circle-fill me-1"></i> Al menos un número</li>
                                            <li id="req-symbol" class="text-danger mb-1"><i class="bi bi-x-circle-fill me-1"></i> Al menos un símbolo especial (@$!%*?&)</li>
                                            <li id="req-name" class="text-success mb-0"><i class="bi bi-check-circle-fill me-1"></i> No debe contener tu nombre de usuario ({{ $user->name }})</li>
                                        </ul>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm" id="submitPasswordBtn">Actualizar Contraseña</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="appearance" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold text-dark mb-1">Accesibilidad y Apariencia Visual</h4>
                                <p class="text-muted small mb-4">Personaliza el tamaño de la tipografía y el contraste de la plataforma según tus necesidades.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <form action="{{ route('settings.appearance.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-4">
                                        <label for="font_size" class="form-label fw-semibold text-dark">Tamaño de la Tipografía</label>
                                        <select id="font_size" name="font_size" class="form-select py-2">
                                            <option value="14px" {{ ($user->font_size ?? '14px') == '14px' ? 'selected' : '' }}>Normal (Predeterminado - 14px)</option>
                                            <option value="16px" {{ ($user->font_size ?? '') == '16px' ? 'selected' : '' }}>Grande (16px)</option>
                                            <option value="18px" {{ ($user->font_size ?? '') == '18px' ? 'selected' : '' }}>Extra Grande / Alta Legibilidad (18px)</option>
                                        </select>
                                        <div class="form-text text-muted mt-1">Ajusta el tamaño base de la letra en toda tu sesión.</div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold text-dark d-block">Modo de Contraste y Tema</label>
                                        <div class="form-check form-switch fs-6">
                                            <input class="form-check-input" type="checkbox" role="switch" id="high_contrast" name="high_contrast" value="1" {{ ($user->high_contrast ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark fw-medium" for="high_contrast">Activar modo de alto contraste / tema oscuro adaptativo</label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-dark px-5 py-2 fw-semibold shadow-sm">Guardar Preferencias Visuales</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="sessions" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold text-dark mb-1">Sesiones Activas</h4>
                                <p class="text-muted small mb-4">Dispositivos desde donde se ha accedido recientemente a tu cuenta.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Dirección IP</th>
                                                <th>Navegador / Dispositivo</th>
                                                <th>Última Actividad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeDevices as $device)
                                                <tr>
                                                    <td><code>{{ $device->ip_address }}</code></td>
                                                    <td class="text-truncate" style="max-width: 250px;" title="{{ $device->user_agent }}">{{ $device->user_agent }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($device->last_active_at)->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">Sesión actual registrada en este dispositivo.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold text-dark mb-1">Historial de Actividad</h4>
                                <p class="text-muted small mb-4">Bitácora privada con los últimos cambios y acciones realizadas en tu cuenta.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha y Hora</th>
                                                <th>Acción Registrada</th>
                                                <th>Dirección IP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activityLogs as $log)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                                                    <td class="fw-semibold text-dark">{{ $log->action }}</td>
                                                    <td><code>{{ $log->ip_address }}</code></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">No hay registros de actividad recientes.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="privacy" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold text-dark mb-1">Privacidad y Control de Datos</h4>
                                <p class="text-muted small mb-4">Administra la transparencia de tu información personal en la plataforma.</p>
                                <hr class="text-muted opacity-25 mb-4">

                                <div class="bg-light p-4 rounded-4 mb-4">
                                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-download me-2"></i> Exportar Información Personal</h6>
                                    <p class="text-muted small mb-3">Puedes descargar una copia estructurada en formato JSON con todos los datos personales asociados a tu cuenta en EduNivelam.</p>
                                    <a href="{{ route('settings.data.download') }}" class="btn btn-dark px-4 py-2 fw-semibold">
                                        Descargar Mis Datos
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="help" role="tabpanel">
                            <div class="card card-settings p-4 p-lg-5">
                                <h4 class="fw-bold text-dark mb-1">Ayuda, Soporte y Comentarios</h4>
                                <p class="text-muted small mb-4">Encuentra recursos de asistencia, reporta incidencias o consulta la información legal de EduNivelam.</p>
                                <hr class="text-muted opacity-25 mb-4">
                                <div class="bg-light p-4 rounded-4 mb-4 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-journal-richtext me-2"></i> Centro de Ayuda y Documentación</h6>
                                        <p class="text-muted small mb-0">Consulta guías interactivas, preguntas frecuentes y manuales de usuario para docentes y estudiantes.</p>
                                    </div>
                                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold px-4 text-nowrap" data-bs-toggle="modal" data-bs-target="#helpCenterModal">
                                        Abrir Centro de Ayuda
                                    </button>
                                </div>
                                <div class="bg-light p-4 rounded-4 mb-4 border">
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-chat-heart me-2"></i> Envíanos tus Comentarios o Sugerencias</h6>
                                    <p class="text-muted small mb-3">Tu opinión es vital para mejorar la plataforma. ¿Tienes alguna idea o reportas un error de interfaz?</p>
                                    <form action="{{ route('settings.feedback.send') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <textarea class="form-control form-control-sm" name="feedback_message" rows="3" placeholder="Escribe aquí tus comentarios, sugerencias o reporte de error..." required></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-dark btn-sm fw-bold px-4">Enviar Comentarios</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="bg-light p-4 rounded-4 mb-4 border">
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-shield-shaded me-2"></i> Condiciones de Uso y Políticas de Privacidad</h6>
                                    <p class="text-muted small mb-3">Revisa nuestros lineamientos de protección de datos, propiedad intelectual y normativas de la comunidad académica.</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#termsModal">Ver Términos y Condiciones</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#privacyModal">Política de Privacidad</button>
                                    </div>
                                </div>
                                <div class="bg-white p-4 rounded-4 border text-center">
                                    <div class="avatar-upload-box mx-auto mb-2 bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-4 rounded-circle shadow-sm" style="width: 50px; height: 50px;">
                                        E
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">EduNivelam - Plataforma de Gestión de Aprendizaje</h6>
                                    <p class="text-muted small mb-2">Versión estable <strong>2.4.0-prod</strong> (Build 2026)</p>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Desarrollado bajo estándares avanzados de ingeniería de software institucional.</p>
                                </div>
                            </div>
                        </div>

                        @if(isset($isAdmin) && $isAdmin)
                            <div class="tab-pane fade" id="server" role="tabpanel">
                                <div class="card card-settings p-4 p-lg-5 border-dark">
                                    <h4 class="fw-bold text-dark mb-1">Centro de Comando y Mantenimiento Avanzado</h4>
                                    <p class="text-muted small mb-4">Herramientas críticas exclusivas para la administración de infraestructura de EduNivelam.</p>
                                    <hr class="text-muted opacity-25 mb-4">
                                    
                                    <div class="bg-light p-4 rounded-4 mb-4 border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-shield-exclamation me-2"></i> Estado de Mantenimiento Global</h6>
                                                <p class="text-muted small mb-0">Bloquea el acceso a usuarios comunes mostrando una pantalla de mantenimiento programado.</p>
                                            </div>
                                            <div>
                                                @if(isset($isMaintenanceActive) && $isMaintenanceActive)
                                                    <form action="{{ route('admin.system.maintenance') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="up">
                                                        <button type="submit" class="btn btn-success btn-sm fw-bold px-3">Desactivar Mantenimiento</button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.system.maintenance') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="down">
                                                        <button type="submit" class="btn btn-danger btn-sm fw-bold px-3" onclick="return confirm('¿Activar modo mantenimiento para estudiantes y docentes?');">Activar Modo Mantenimiento</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-4 rounded-4 mb-4 border">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-cpu me-2"></i> Diagnóstico de Red y Base de Datos</h6>
                                                <p class="text-muted small mb-0">Verifica la respuesta en tiempo real o simula un escenario de fallo operativo.</p>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.system.testemail') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-dark btn-sm fw-bold px-3">Probar Estado Estable</button>
                                                </form>
                                                <form action="{{ route('admin.system.testemail') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="simular_falla" value="1">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-3" onclick="return confirm('¿Simular un fallo crítico de red/base de datos?');">Simular Operación Anormal</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-4 rounded-4 mb-4 border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-database-check me-2"></i> Copias de Seguridad (Backups)</h6>
                                                <p class="text-muted small mb-0">Genera y descarga un respaldo seguro en formato SQL de toda la base de datos.</p>
                                            </div>
                                            <form action="{{ route('admin.system.backup') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-dark btn-sm fw-bold px-3">Generar Respaldo Manual</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="bg-light p-4 rounded-4 mb-4 border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-text me-2"></i> Control de Errores y Logs</h6>
                                                <p class="text-muted small mb-0">Visualiza los registros recientes de excepciones del sistema o límpialos.</p>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.system.logs.view') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-dark btn-sm fw-bold px-3">Ver Logs</button>
                                                </form>
                                                <form action="{{ route('admin.system.logs.clear') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-3" onclick="return confirm('¿Vaciar el archivo de registros?');">Vaciar Logs</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-4 rounded-4 mb-4 border">
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-envelope-check me-2"></i> Administrador de Correo de Prueba (SMTP)</h6>
                                        <p class="text-muted small mb-3">Envía un mensaje de prueba para verificar la pasarela de correo configurada.</p>
                                        <form action="{{ route('admin.system.smtp.test') }}" method="POST" class="row g-2">
                                            @csrf
                                            <div class="col-md-8">
                                                <input type="email" class="form-control form-control-sm" name="test_email" placeholder="Correo destinatario (ej. admin@edunivelam.com)" required>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-dark btn-sm fw-bold w-100">Enviar Correo Prueba</button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="bg-light p-4 rounded-4 mb-4 border">
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-database-gear me-2"></i> Optimización y Caché Granular</h6>
                                        <p class="text-muted small mb-3">Mejora el rendimiento purgando fragmentación de tablas y cachés específicas.</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <form action="{{ route('admin.system.db.optimize') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-dark btn-sm fw-bold">Optimizar Tablas SQL</button>
                                            </form>
                                            <form action="{{ route('admin.system.cache.inspect') }}" method="POST">
                                                @csrf <input type="hidden" name="type" value="route">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold">Gestionar Caché Rutas</button>
                                            </form>
                                            <form action="{{ route('admin.system.cache.inspect') }}" method="POST">
                                                @csrf <input type="hidden" name="type" value="view">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold">Gestionar Caché Vistas</button>
                                            </form>
                                            <form action="{{ route('admin.system.cache.inspect') }}" method="POST">
                                                @csrf <input type="hidden" name="type" value="config">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold">Gestionar Caché Config</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="bg-light p-4 rounded-4 mb-4 border">
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-sliders me-2"></i> Variables de Entorno del Servidor (.env)</h6>
                                        <p class="text-muted small mb-3">Modifica parámetros globales en caliente sin tocar la consola.</p>
                                        <form action="{{ route('admin.system.env.update') }}" method="POST">
                                            @csrf
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Nombre de App</label>
                                                    <input type="text" class="form-control form-control-sm" name="app_name" value="{{ config('app.name') }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Modo Debug</label>
                                                    <select class="form-select form-select-sm" name="app_debug">
                                                        <option value="true" {{ config('app.debug') ? 'selected' : '' }}>Activado (True)</option>
                                                        <option value="false" {{ !config('app.debug') ? 'selected' : '' }}>Desactivado (False)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold">Driver de Correo</label>
                                                    <select class="form-select form-select-sm" name="mail_mailer">
                                                        <option value="smtp" {{ config('mail.default') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                        <option value="log" {{ config('mail.default') === 'log' ? 'selected' : '' }}>Log (Pruebas local)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-dark btn-sm fw-bold px-4" onclick="return confirm('¿Guardar cambios en el archivo .env?');">Actualizar Entorno</button>
                                        </form>
                                    </div>

                                    <div class="bg-light p-4 rounded-4 border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-hdd-fill me-2"></i> Almacenamiento en Disco (Avatares y Archivos)</h6>
                                                <p class="text-muted small mb-0">Espacio total utilizado en el servidor: <strong>{{ $storageUsage ?? '0.00' }} MB</strong></p>
                                            </div>
                                            <form action="{{ route('admin.system.clearcache') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold px-3" onclick="return confirm('¿Limpiar caché general y archivos temporales?');">Limpiar Caché del Sistema</button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

@if(session('diagnostic_modal'))
<div class="modal fade show" id="diagnosticModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.6);" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-{{ session('diagnostic_status') === 'success' ? 'success' : 'danger' }} text-white px-4 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-{{ session('diagnostic_status') === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' }} me-2"></i>
                    {{ session('diagnostic_title') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('diagnosticModal').remove();"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <p class="text-muted small mb-3">Datos detallados en tiempo real del motor del sistema:</p>
                <div class="bg-white p-3 rounded-3 border">
                    <table class="table table-sm table-borderless mb-0">
                        @foreach(session('diagnostic_data') as $key => $value)
                        <tr>
                            <td class="fw-bold text-secondary text-uppercase" style="width: 40%;">{{ str_replace('_', ' ', $key) }}:</td>
                            <td><code class="text-dark">{{ is_array($value) ? json_encode($value) : $value }}</code></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-white px-4 py-3 border-top">
                <button type="button" class="btn btn-dark px-4 fw-semibold btn-sm" onclick="document.getElementById('diagnosticModal').remove();">Cerrar Informe</button>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('log_modal'))
<div class="modal fade show" id="logModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.6);" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-journal-code me-2"></i> Registro de Errores del Servidor (Laravel Log)</h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('logModal').remove();"></button>
            </div>
            <div class="modal-body p-4 bg-dark font-monospace rounded-bottom" style="max-height: 480px; overflow-y: auto; font-size: 0.8rem;">
                <pre class="mb-0 text-white" style="white-space: pre-wrap; word-break: break-all;"><code>{{ session('log_content') }}</code></pre>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top">
                <button type="button" class="btn btn-dark px-4 fw-semibold btn-sm" onclick="document.getElementById('logModal').remove();">Cerrar Visor</button>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('cache_modal'))
<div class="modal fade show" id="cacheInspectModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.6);" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i> Inspección de Caché</h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('cacheInspectModal').remove();"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <h6 class="fw-bold text-dark">{{ session('cache_title') }}</h6>
                <p class="text-muted small mb-3">{{ session('cache_desc') }}</p>
                
                <div class="p-3 bg-white rounded-3 border mb-3 text-center">
                    <span class="text-secondary small d-block uppercase fw-semibold">Espacio Ocupado Actualmente:</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ session('cache_size') }}</h3>
                </div>
                
                <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> ¿Deseas purgar y eliminar permanentemente este contenido en caché?</p>
            </div>
            <div class="modal-footer bg-white px-4 py-3 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 fw-semibold btn-sm" onclick="document.getElementById('cacheInspectModal').remove();">Cancelar</button>
                <form action="{{ route('admin.system.cache.granular') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ session('cache_type') }}">
                    <button type="submit" class="btn btn-danger px-4 fw-semibold btn-sm">Sí, Borrar y Purgar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('db_report_modal'))
<div class="modal fade show" id="dbReportModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.6);" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white px-4 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Reporte de Optimización SQL</h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('dbReportModal').remove();"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <p class="text-muted small mb-3">Respaldo técnico del rendimiento obtenido tras desfragmentar la base de datos:</p>
                <div class="bg-white p-3 rounded-3 border">
                    <table class="table table-sm table-borderless mb-0">
                        @foreach(session('db_report_data') as $key => $value)
                        <tr>
                            <td class="fw-bold text-secondary text-uppercase" style="width: 50%;">{{ str_replace('_', ' ', $key) }}:</td>
                            <td><code class="text-dark fw-bold">{{ $value }}</code></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-white px-4 py-3 border-top">
                <button type="button" class="btn btn-dark px-4 fw-semibold btn-sm" onclick="document.getElementById('dbReportModal').remove();">Entendido</button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="helpCenterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-journal-richtext me-2"></i> Centro de Ayuda Rápida</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="accordion" id="helpAccordion">
                    <div class="accordion-item mb-2 rounded border">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                ¿Cómo puedo matricularme en un curso activo?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#helpAccordion">
                            <div class="accordion-body small text-dark fw-medium" style="color: #212529 !important;">
                                Dirígete al apartado de "Cursos" en el menú superior, explora el catálogo disponible y haz clic en el botón de inscripción directa o solicitud de aprobación docente.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item mb-2 rounded border">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                ¿Qué hago si olvidé mi contraseña de acceso?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body small text-dark fw-medium" style="color: #212529 !important;">
                                En la pantalla de inicio de sesión, selecciona la opción "¿Olvidaste tu contraseña?" e ingresa tu correo registrado para recibir un enlace seguro de restablecimiento.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white px-4 py-3">
                <button type="button" class="btn btn-dark btn-sm fw-bold px-4" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-shaded me-2"></i> Condiciones de Uso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light small text-muted" style="max-height: 400px; overflow-y: auto;">
                <p>Bienvenido a EduNivelam. Al utilizar nuestra plataforma, aceptas cumplir con los estándares de conducta académica, uso ético de los recursos y respeto a la propiedad intelectual de los contenidos institucionales.</p>
                <p>Queda estrictamente prohibida la divulgación no autorizada de credenciales administrativas y el uso de scripts automatizados que comprometan la estabilidad del servidor.</p>
            </div>
            <div class="modal-footer bg-white px-4 py-3">
                <button type="button" class="btn btn-dark btn-sm fw-bold px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-check me-2"></i> Política de Privacidad</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light small text-muted" style="max-height: 400px; overflow-y: auto;">
                <p>En EduNivelam protegemos tus datos personales conforme a las normativas vigentes de privacidad. La información recopilada (como registros de actividad e identidad de perfil) es utilizada estrictamente para fines de gestión educativa y seguridad informática.</p>
            </div>
            <div class="modal-footer bg-white px-4 py-3">
                <button type="button" class="btn btn-dark btn-sm fw-bold px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite([
        'resources/js/settings/password-validation.js',
        'resources/js/settings/appearance-settings.js',
        'resources/js/settings/server-actions.js'
    ])
@endsection