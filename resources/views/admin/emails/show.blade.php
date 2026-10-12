@extends('layouts.app')

@section('title', 'Detalle de Mensaje - CMS')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/login.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content">
            <div class="container-fluid px-0">
                <div class="row justify-content-center">
                    <div class="col-lg-11">

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-dark p-3 p-lg-4 rounded-4 shadow-sm border border-secondary mb-4">
                            <a href="{{ route('admin.emails.index') }}" class="btn btn-outline-light btn-sm fw-bold px-3 d-inline-flex align-items-center w-fit">
                                <i class="bi bi-arrow-left me-2"></i> Volver a la Bandeja
                            </a>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-primary btn-sm fw-bold px-3 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#replyModal{{ $email->id ?? 1 }}">
                                    <i class="bi bi-reply-fill me-2"></i> Responder
                                </button>

                                <form action="{{ route('admin.emails.destroy', $email->id ?? 1) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este mensaje de la bandeja?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm fw-bold px-3 d-inline-flex align-items-center">
                                        <i class="bi bi-trash-fill me-2"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card bg-dark text-white border border-secondary shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-black text-white p-4 border-bottom border-secondary">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2">
                                    <h4 class="fw-bold mb-0 text-white fs-5">{{ $email->subject ?? 'Nuevo Comentario / Sugerencia de Usuario - EduNivelam' }}</h4>
                                    <span class="badge bg-secondary text-light px-3 py-2 rounded-pill fs-6 w-fit">
                                        {{ $email->received_at ? $email->received_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="small text-light opacity-75">
                                    <span class="fw-semibold">De:</span> 
                                    <span class="font-monospace text-warning">{{ $email->from_name ?? $email->sender_name ?? 'Usuario' }} &lt;{{ $email->from_email ?? $email->sender_email ?? 'soporte@edunivelam.com' }}&gt;</span>
                                </div>
                            </div>

                            <div class="card-body p-4 p-lg-5 bg-dark">
                                <div class="bg-black p-4 rounded-4 border border-secondary shadow-sm text-light">
                                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-chat-heart me-2"></i> EduNivelam - Reporte de Comentarios</h6>
                                    
                                    <p class="text-white mb-2">Hola, <strong>Administrador</strong>,</p>
                                    <p class="text-muted small mb-4">Un usuario ha enviado un nuevo comentario o reporte de sugerencia desde el panel de configuración de la plataforma:</p>

                                    <div class="border-top border-secondary pt-3 mb-4 small text-secondary">
                                        <p class="mb-1 text-light"><strong>Remitente:</strong> {{ $email->from_name ?? 'Usuario' }} ({{ $email->from_email ?? 'admin@admin.com' }})</p>
                                        <p class="mb-0 text-light"><strong>Rol en plataforma:</strong> <span class="badge bg-secondary">Admin / Usuario</span></p>
                                    </div>

                                    <div class="p-3 bg-dark border-start border-4 border-primary rounded-end text-white small leading-relaxed">
                                        {{ $email->clean_message ?? 'No hay contenido disponible.' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade text-start" id="replyModal{{ $email->id ?? 1 }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" data-bs-theme="dark">
                                <div class="modal-content bg-dark text-white border border-secondary shadow-lg rounded-4">
                                    <form action="{{ route('admin.emails.reply', $email->id ?? 1) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-bottom border-secondary px-4 py-3">
                                            <h5 class="modal-title fs-6 text-white fw-bold">
                                                Responder a: <span class="text-warning font-monospace">{{ $email->from_email ?? 'soporte@edunivelam.com' }}</span>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-light opacity-75">Asunto Original:</label>
                                                <input type="text" class="form-control form-control-sm" value="{{ $email->subject ?? 'Soporte EduNivelam' }}" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label for="reply_message{{ $email->id ?? 1 }}" class="form-label small fw-bold text-light">Mensaje de Respuesta:</label>
                                                <textarea class="form-control @error('reply_message') is-invalid @enderror" id="reply_message{{ $email->id ?? 1 }}" name="reply_message" rows="5" placeholder="Escribe tu respuesta aquí..." required></textarea>
                                                @error('reply_message')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top border-secondary px-4 py-3">
                                            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Enviar Respuesta</button>
                                        </div>
                                    </form>
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