@extends('layouts.app')

@section('title', 'Mi Perfil - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content py-4 px-4">
            <div class="dashboard-topbar mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1 text-dark-adaptive">Configuración de Perfil</h2>
                    <p class="text-muted small mb-0">Gestiona tu identidad digital y credenciales de acceso personal.</p>
                </div>
                <span class="badge bg-secondary px-3 py-2 rounded-pill">Rol: {{ ucfirst(Auth::user()->role ?? 'Admin') }}</span>
            </div>

            <div class="card shadow-sm border-0 p-4 rounded-4 bg-card-adaptive">
                <h4 class="mb-3 fw-bold text-dark-adaptive">Información de Perfil</h4>
                <p class="text-secondary small mb-4">Actualiza tu foto de avatar, nombre de usuario y descripción profesional.</p>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 70px; height: 70px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <label class="form-label fw-bold text-dark-adaptive">Fotografía de Perfil</label>
                            <input type="file" name="avatar" class="form-control form-control-sm">
                            <div class="form-text text-muted small">Formatos: JPEG, PNG, WEBP. Máx: 2MB.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark-adaptive">Nombre de usuario</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark-adaptive">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark-adaptive">Biografía Profesional o Descripción</label>
                        <textarea name="bio" rows="3" class="form-control">{{ old('bio', Auth::user()->bio ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-dark fw-bold px-4 rounded-pill">Guardar Cambios</button>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/dashboard.js'])
@endsection