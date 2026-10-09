@extends('layouts.app')

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h4>Panel Admin</h4>
                <span class="sidebar-role">Gestión General</span>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="{{ route('dashboard') }}"> Resumen General</a></li>
                
                @can('manage-users')
                    <li><a href="{{ route('admin.users') }}"> Gestión de Usuarios</a></li>
                    <li><a href="{{ route('admin.teachers.pending') }}" class="active"> Aceptar Docentes</a></li>
                    <li><a href="{{ route('admin.emails.index') }}"> Buzón IMAP</a></li>
                    <li><a href="{{ route('admin.banners.index') }}"> Gestión de Banners</a></li>
                    <li><a href="{{ route('admin.news.index') }}"> Gestión de Noticias</a></li>
                    <li><a href="{{ route('admin.security') }}"> Seguridad y Accesos</a></li>
                @endcan

                <li><a href="{{ route('settings.index') }}"> Configuración del Sistema</a></li>
            </ul>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">Cerrar Sesión</button>
                </form>
            </div>
        </aside>

        <main class="dashboard-main-content">
            <div class="dashboard-topbar">
                <h2>Gestión de Solicitudes de Docentes</h2>
                <span class="badge bg-warning text-dark p-2">Pendientes: {{ $pendingTeachers->count() }}</span>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 p-4">
                <h4 class="mb-3 text-dark">Profesores en espera de aprobación</h4>
                <p class="text-muted small">
                    Aprueba las solicitudes para generarles automáticamente sus credenciales de acceso o deniégalas para eliminar sus registros por completo.
                </p>

                @if($pendingTeachers->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4 d-block mb-2"></i>
                        <p class="mb-0">No hay solicitudes de docentes pendientes de aprobación en este momento.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Cédula (DNI)</th>
                                    <th>Teléfono</th>
                                    <th>F. Nacimiento</th>
                                    <th>Postulación</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingTeachers as $teacher)
                                <tr>
                                    <td class="fw-semibold">{{ $teacher->name }}</td>
                                    <td>{{ $teacher->email }}</td>
                                    <td>{{ $teacher->dni }}</td>
                                    <td>{{ $teacher->country_code }} {{ $teacher->phone }}</td>
                                    <td>{{ \Carbon\Carbon::parse($teacher->birthdate)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($teacher->teaching_type === 'courses')
                                            <span class="badge bg-primary">Creación de Cursos</span>
                                        @else
                                            <span class="badge bg-info text-dark">Asesorías</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.teachers.approve', $teacher->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success me-1" title="Aprobar y enviar credenciales">
                                                Aprobar
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.teachers.deny', $teacher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de denegar y eliminar por completo este registro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar solicitud">
                                                Denegar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection