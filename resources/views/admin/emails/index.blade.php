@extends('layouts.app')

@section('title', 'Bandeja de Entrada - CMS')

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
            <div class="dashboard-topbar mb-4">
                <h2 class="text-dark-adaptive fw-bold">Bandeja de Entrada (IMAP)</h2>
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

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-card-adaptive">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-dark-adaptive">
                        <thead class="table-dark">
                            <tr>
                                <th>Estado</th>
                                <th>Remitente</th>
                                <th>Asunto</th>
                                <th>Fecha de Recepción</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($emails as $email)
                                <tr class="{{ !$email->is_read ? 'fw-bold' : '' }}">
                                    <td>
                                        @if(!$email->is_read)
                                            <span class="badge bg-primary">No leído</span>
                                        @else
                                            <span class="badge bg-secondary">Leído</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $email->from_name ? $email->from_name . ' (' . $email->from_email . ')' : $email->from_email }}
                                    </td>
                                    <td>{{ $email->subject }}</td>
                                    <td>{{ $email->received_at ? $email->received_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('admin.emails.show', $email->id) }}" 
                                               class="btn btn-sm btn-outline-primary px-2 py-1 rounded-pill" title="Ver Mensaje">
                                                <i class="bi bi-eye-fill"></i> Ver
                                            </a>

                                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1 rounded-pill" 
                                                    data-bs-toggle="modal" data-bs-target="#replyModal{{ $email->id }}" title="Responder">
                                                <i class="bi bi-reply-fill"></i> Responder
                                            </button>

                                            <form action="{{ route('admin.emails.destroy', $email->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este correo de la bandeja?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-pill" title="Eliminar Correo">
                                                    <i class="bi bi-trash-fill"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>

                                        <div class="modal fade text-start" id="replyModal{{ $email->id }}" tabindex="-1" aria-labelledby="replyModalLabel{{ $email->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" data-bs-theme="dark">
                                                <div class="modal-content bg-dark text-white border border-secondary shadow-lg rounded-4">
                                                    <form action="{{ route('admin.emails.reply', $email->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header border-bottom border-secondary px-4 py-3">
                                                            <h5 class="modal-title fs-6 text-white fw-bold" id="replyModalLabel{{ $email->id }}">
                                                                Responder a: <span class="text-warning font-monospace">{{ $email->from_email }}</span>
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-light opacity-75">Asunto Original:</label>
                                                                <input type="text" class="form-control form-control-sm" value="{{ $email->subject }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="reply_message{{ $email->id }}" class="form-label small fw-bold text-light">Mensaje de Respuesta:</label>
                                                                <textarea class="form-control @error('reply_message') is-invalid @enderror" id="reply_message{{ $email->id }}" name="reply_message" rows="5" placeholder="Escribe tu respuesta aquí..." required></textarea>
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No hay correos recibidos en la base de datos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $emails->links() }}
            </div>
        </main>
    </div>
</div>
@endsection