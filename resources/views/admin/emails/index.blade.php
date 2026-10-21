@extends('layouts.app')

@section('title', 'Bandeja de Entrada - CMS')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/login.css', 'resources/css/email-admin.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content">
            <div class="dashboard-topbar mb-4">
                <h2 class="fw-bold text-dark-adaptive">Bandeja de Entrada (IMAP)</h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="inbox-list">
                @forelse($emails as $email)
                    <div class="inbox-card {{ !$email->is_read ? 'is-unread' : '' }}">

                        <div class="inbox-info">
                            @if(!$email->is_read)
                                <span class="badge bg-primary px-2 py-1">No leído</span>
                            @else
                                <span class="badge bg-secondary px-2 py-1">Leído</span>
                            @endif

                            <div class="inbox-text">
                                <div class="inbox-subject">{{ $email->subject ?? 'Sin asunto' }}</div>
                                <div class="inbox-meta">
                                    <span class="sender">{{ $email->from_name ?: ($email->from_email ?? 'Desconocido') }}</span>
                                    &bull;
                                    <span class="date">{{ $email->received_at ? \Carbon\Carbon::parse($email->received_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="inbox-actions">
                            <a href="{{ route('admin.emails.show', $email->id) }}"
                               class="btn btn-sm btn-outline-primary px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1" title="Ver Mensaje">
                                <i class="bi bi-eye-fill"></i> Ver
                            </a>

                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#replyModal{{ $email->id }}" title="Responder">
                                <i class="bi bi-reply-fill"></i> Responder
                            </button>

                            <form action="{{ route('admin.emails.destroy', $email->id) }}" method="POST"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este correo de la bandeja?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1" title="Eliminar Correo">
                                    <i class="bi bi-trash-fill"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted bg-card-adaptive rounded-4">
                        No hay correos recibidos en la base de datos.
                    </div>
                @endforelse
            </div>

            @foreach($emails as $email)
                <div class="modal fade text-start" id="replyModal{{ $email->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" data-bs-theme="dark">
                        <div class="modal-content bg-dark text-white border border-secondary shadow-lg rounded-4">
                            <form action="{{ route('admin.emails.reply', $email->id) }}" method="POST">
                                @csrf
                                <div class="modal-header border-bottom border-secondary px-4 py-3">
                                    <h5 class="modal-title fs-6 text-white fw-bold">
                                        Responder a: <span class="text-warning font-monospace">{{ $email->from_email ?? '' }}</span>
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-light opacity-75">Asunto Original:</label>
                                        <input type="text" class="form-control form-control-sm bg-secondary text-white border-0" value="{{ $email->subject ?? '' }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reply_message{{ $email->id }}" class="form-label small fw-bold text-light">Mensaje de Respuesta:</label>
                                        <textarea class="form-control bg-dark text-white border-secondary" id="reply_message{{ $email->id }}" name="reply_message" rows="5" placeholder="Escribe tu respuesta aquí..." required></textarea>
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
            @endforeach

            @if($emails->hasPages())
                <div class="inbox-pagination">
                    {{ $emails->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </main>
    </div>
</div>
@endsection