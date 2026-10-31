@extends('layouts.app')

@section('title', 'Estado de mi solicitud - EduNivelam')

@section('styles')
    @vite(['resources/css/teacher-status.css'])
@endsection

@section('content')
@php $result = session('result'); @endphp

<div class="ts-page">
    <div class="ts-card ts-card-wide">

        @if($result)
            @if($result['status'] === 'approved')
                <span class="ts-icon ts-icon-ok"><i class="bi bi-patch-check-fill"></i></span>
                <h1 class="ts-title">¡Solicitud aprobada, {{ $result['name'] }}!</h1>
                <p class="ts-lead">
                    Te enviamos tus credenciales de acceso al correo con el que te postulaste.
                    Revisa también la carpeta de spam.
                </p>
            @else
                <span class="ts-icon ts-icon-wait"><i class="bi bi-hourglass-split"></i></span>
                <h1 class="ts-title">Hola, {{ $result['name'] }}</h1>
                <p class="ts-lead">Tu solicitud está <strong>en cola de revisión</strong>.</p>

                <div class="ts-queue">
                    <div>
                        <span class="ts-queue-num">#{{ $result['position'] }}</span>
                        <small>Tu lugar en la cola</small>
                    </div>
                    <div>
                        <span class="ts-queue-num">{{ $result['total'] }}</span>
                        <small>{{ $result['total'] === 1 ? 'Solicitud pendiente' : 'Solicitudes pendientes' }}</small>
                    </div>
                </div>
            @endif

            <ol class="ts-steps">
                <li class="is-done">
                    <span class="ts-dot"><i class="bi bi-check-lg"></i></span>
                    <div>
                        <strong>Solicitud recibida</strong>
                        <small>{{ $result['sent_at'] }}</small>
                    </div>
                </li>
                <li class="{{ $result['status'] === 'approved' ? 'is-done' : 'is-current' }}">
                    <span class="ts-dot">
                        <i class="bi {{ $result['status'] === 'approved' ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i>
                    </span>
                    <div>
                        <strong>Revisión del administrador</strong>
                        <small>{{ $result['status'] === 'approved' ? 'Completada.' : 'En orden de llegada.' }}</small>
                    </div>
                </li>
                <li class="{{ $result['status'] === 'approved' ? 'is-done' : '' }}">
                    <span class="ts-dot"><i class="bi {{ $result['status'] === 'approved' ? 'bi-check-lg' : 'bi-envelope-check' }}"></i></span>
                    <div>
                        <strong>Credenciales enviadas</strong>
                        <small>{{ $result['status'] === 'approved' ? 'Revisa tu correo.' : 'Te llegarán por correo cuando se apruebe.' }}</small>
                    </div>
                </li>
            </ol>

            <p class="ts-meta"><i class="bi bi-person-workspace"></i> Tipo de postulación: <strong>{{ $result['type'] }}</strong></p>

            <div class="ts-actions">
                @if($result['status'] === 'approved')
                    <a href="{{ route('login') }}" class="ts-btn ts-btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                    </a>
                @else
                    <a href="{{ route('teacher.status') }}" class="ts-btn ts-btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Actualizar estado
                    </a>
                @endif
                <a href="{{ route('home') }}" class="ts-btn ts-btn-ghost">Volver al inicio</a>
            </div>

        @else
            <span class="ts-icon"><i class="bi bi-search"></i></span>
            <h1 class="ts-title">Estado de mi solicitud</h1>
            <p class="ts-lead">Ingresa los datos con los que te postulaste como docente.</p>

            <form method="POST" action="{{ route('teacher.status.check') }}" id="statusForm" class="ts-form" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="ts-label">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control ts-input @error('email') is-invalid @enderror"
                           placeholder="tucorreo@ejemplo.com" required autocomplete="email">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="dni" class="ts-label">Número de documento</label>
                    <input type="text" id="dni" name="dni" value="{{ old('dni') }}" inputmode="numeric"
                           class="form-control ts-input @error('dni') is-invalid @enderror"
                           placeholder="Solo números" required autocomplete="off">
                    @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="ts-btn ts-btn-primary ts-btn-block" id="statusSubmit">
                    <span>Consultar</span>
                </button>
            </form>

            <p class="ts-note">
                ¿Aún no te has postulado?
                <a href="{{ route('register.teacher') }}">Regístrate como docente</a>
            </p>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/teacher-status.js'])
@endsection