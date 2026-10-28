@extends('layouts.app')

@section('title', 'Solicitud en proceso - EduNivelam')

@section('styles')
    @vite(['resources/css/login.css', 'resources/css/auth-pages.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="ap-stage">
    <div class="ap-card ap-card--wide text-center">

        <span class="ap-icon ap-icon--warm"><i class="bi bi-hourglass-split"></i></span>
        <h1 class="ap-title">Solicitud en proceso</h1>
        <p class="ap-sub">
            ¡Gracias por registrarte! Tu cuenta de docente fue creada con éxito, pero
            <strong>debe esperar la aprobación del administrador</strong>.
        </p>

        <ol class="ap-steps">
            <li class="is-done">
                <span class="ap-step-dot"><i class="bi bi-check-lg"></i></span>
                <div>
                    <p class="ap-step-title">Solicitud enviada</p>
                    <p class="ap-step-text">Tu registro como docente quedó guardado.</p>
                </div>
            </li>
            <li class="is-current">
                <span class="ap-step-dot"><i class="bi bi-person-check"></i></span>
                <div>
                    <p class="ap-step-title">Revisión del administrador</p>
                    <p class="ap-step-text">Estamos validando tu información.</p>
                </div>
            </li>
            <li>
                <span class="ap-step-dot"><i class="bi bi-envelope-paper"></i></span>
                <div>
                    <p class="ap-step-title">Recibirás tus credenciales</p>
                    <p class="ap-step-text">Cuando se apruebe tu cuenta, te enviaremos tus credenciales oficiales de acceso por correo electrónico.</p>
                </div>
            </li>
        </ol>

        <a href="{{ route('home') }}" class="auth-submit text-decoration-none">
            <span>Regresar al inicio</span>
            <span class="auth-submit-arrow"><i class="bi bi-house-door"></i></span>
        </a>
    </div>
</div>
@endsection