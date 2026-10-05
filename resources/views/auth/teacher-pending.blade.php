@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm border-0 p-5 text-center" style="max-width: 550px; width: 100%;">
        <div class="mb-4">
            <h3 class="fw-bold text-dark">Solicitud en Proceso</h3>
            <p class="text-muted mt-3">
                ¡Gracias por registrarte! Tu cuenta de docente ha sido creada con éxito pero <strong>debe esperar la aprobación del administrador</strong>.
            </p>
            <p class="text-muted small">
                Una vez que el administrador apruebe tu cuenta, se te enviarán tus credenciales oficiales de acceso a tu correo electrónico.
            </p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-dark w-100 py-2">Regresar al Inicio</a>
    </div>
</div>
@endsection