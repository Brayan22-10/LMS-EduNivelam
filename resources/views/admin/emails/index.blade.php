@extends('layouts.app')

@section('title', 'Bandeja de Entrada - CMS')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"> Bandeja de Entrada (IMAP)</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill">Volver al Dashboard</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Estado</th>
                        <th>Remitente</th>
                        <th>Asunto</th>
                        <th>Fecha de Recepción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr class="{{ !$email->is_read ? 'fw-bold bg-light' : '' }}">
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
                            <td>
                                <a href="{{ route('admin.emails.show', $email->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Ver Mensaje</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay correos recibidos en la base de datos. Ejecuta <code class="text-dark">php artisan mail:receive</code> para consultar el buzón.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $emails->links() }}
    </div>
</div>
@endsection