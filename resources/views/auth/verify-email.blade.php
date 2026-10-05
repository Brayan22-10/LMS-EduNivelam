@extends('layouts.app')

@section('styles')
    @vite(['resources/css/login.css'])
    <style>
        footer, .footer { display: none !important; }
    </style>
@endsection

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm border-0 p-5 text-center" style="max-width: 600px; width: 100%;">
        
        <div class="mb-4">
            <h3 class="fw-bold text-dark">Verifica tu dirección de correo electrónico</h3>
        </div>

        @if (session('message'))
            <div class="alert alert-success" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <p class="text-muted mb-4">
            ¡Gracias por registrarte en EduNivelam! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar?
        </p>

        <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-dark w-100 py-2">Reenviar correo de verificación</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100 py-2">Cerrar sesión</button>
        </form>
    </div>
</div>

{{-- Script optimizado para revisar cada 1 segundo (1000 ms) --}}
<script>
    setInterval(function() {
        fetch('/check-session-status', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Pendiente');
        })
        .then(data => {
            if (data.status === 'verified') {
                window.location.href = "{{ route('home') }}";
            }
        })
        .catch(error => {
        });
    }, 1000); 
</script>
@endsection