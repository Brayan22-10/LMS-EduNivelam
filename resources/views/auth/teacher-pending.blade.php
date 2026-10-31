@extends('layouts.app')

@section('title', 'Solicitud enviada - EduNivelam')

@section('styles')
    @vite(['resources/css/teacher-status.css'])
@endsection

@section('content')
<div class="ts-page">
    <div class="ts-card">
        <span class="ts-icon ts-icon-ok"><i class="bi bi-check2-circle"></i></span>

        <h1 class="ts-title">¡Solicitud enviada!</h1>
        <p class="ts-lead">
            Tu cuenta de docente fue creada con éxito, pero
            <strong>debe esperar la aprobación del administrador</strong>.
        </p>

        <ol class="ts-steps">
            <li class="is-done">
                <span class="ts-dot"><i class="bi bi-check-lg"></i></span>
                <div>
                    <strong>Solicitud recibida</strong>
                    <small>Ya registramos tus datos.</small>
                </div>
            </li>
            <li class="is-current">
                <span class="ts-dot"><i class="bi bi-hourglass-split"></i></span>
                <div>
                    <strong>En cola de revisión</strong>
                    <small>El equipo revisa las solicitudes en orden de llegada.</small>
                </div>
            </li>
            <li>
                <span class="ts-dot"><i class="bi bi-envelope-check"></i></span>
                <div>
                    <strong>Recibirás tus credenciales</strong>
                    <small>Si es aprobada, te las enviamos por correo electrónico.</small>
                </div>
            </li>
        </ol>

        <div class="ts-actions">
            <a href="{{ route('teacher.status') }}" class="ts-btn ts-btn-primary">
                <i class="bi bi-search"></i> Consultar el estado de mi solicitud
            </a>
            <a href="{{ route('home') }}" class="ts-btn ts-btn-ghost">Volver al inicio</a>
        </div>

        <p class="ts-note">
            Guarda tu correo y tu número de documento: los necesitarás para consultar el estado.
        </p>
    </div>
</div>
@endsection