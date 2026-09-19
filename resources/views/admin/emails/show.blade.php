@extends('layouts.app')

@section('title', 'Detalle de Correo - CMS')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('admin.emails.index') }}" class="btn btn-outline-secondary rounded-pill">&larr; Volver a la Bandeja</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">{{ $email->subject ?? 'Sin Asunto' }}</h3>
                <p class="text-muted small mb-0">De: <strong>{{ $email->from_name ? $email->from_name . ' <' . $email->from_email . '>' : $email->from_email }}</strong></p>
            </div>
            <span class="text-muted small">{{ $email->received_at ? $email->received_at->format('d/m/Y H:i') : '' }}</span>
        </div>

        <div class="email-body text-dark" style="line-height: 1.7;">
            {!! $email->body !!}
        </div>
    </div>
</div>
@endsection