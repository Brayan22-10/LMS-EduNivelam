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
            <div class="card shadow">
                <div class="card-header bg-dark text-white">Verificar Código</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Hemos enviado un código de 6 dígitos a tu correo electrónico.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.verify.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Código de 6 dígitos</label>
                            <input type="text" name="code" class="form-control text-center fs-4 @error('code') is-invalid @enderror" maxlength="6" required autofocus autocomplete="off" style="letter-spacing: 5px;">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-dark w-100 mb-3">Verificar Código</button>

                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-decoration-none small">Reintentar / Reenviar código</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection