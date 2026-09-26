@extends('layouts.app')

@section('styles')
    @vite(['resources/css/login.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="mb-0">Verifica tu dirección de correo electrónico</h4>
                </div>
                <div class="card-body p-4 text-center">
                    
                    @if (session('message'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        ¡Gracias por registrarte en EduNivelam! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar?
                    </p>

                    <p class="text-muted small mb-4">
                        Si no recibiste el correo electrónico, con gusto te enviaremos otro.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-dark w-100 py-2">
                            Reenviar correo de verificación
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100 py-2">
                            Cerrar sesión
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection