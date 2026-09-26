@extends('layouts.app')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/admin-users.css'])
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
                <li><a href="{{ route('admin.users') }}" class="active"> Gestión de Usuarios</a></li>
                <li><a href="#"> Configuración del Sistema</a></li>
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
                <h2>Gestión de Usuarios</h2>
                <span class="badge bg-success text-white p-2">Total: {{ $users->count() }}</span>
            </div>

            {{-- Alertas de éxito o error (para el bloqueo de autoeliminación) --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 p-4">
                <h4 class="mb-3 text-dark">Usuarios Registrados</h4>
                <p class="text-muted mb-4">Haz clic en cualquier usuario de la lista para ver su información detallada o utiliza las acciones disponibles.</p>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Fecha de Registro</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    {{-- Celdas con clic para abrir el modal de detalles --}}
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        <strong>#{{ $user->id }}</strong>
                                    </td>
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        {{ $user->name }}
                                    </td>
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        {{ $user->email }}
                                    </td>
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        {{ $user->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    
                                    {{-- Columna de Acciones (KAN-187, KAN-188, KAN-190) --}}
                                    <td class="text-center">
                                        @if(Auth::id() !== $user->id)
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>

                                            {{-- Modal de Confirmación de Borrado por Usuario --}}
                                            <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">Confirmar eliminación</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            ¿Estás seguro de que deseas dar de baja al usuario <strong>{{ $user->name }}</strong>? Esta acción eliminará su cuenta de forma permanente.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Tu cuenta</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay usuarios registrados en este momento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Modal para ver detalles del usuario --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="userModalLabel">Detalles del Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <h4 id="modal-username" class="fw-bold text-dark mb-2"></h4>
                <p id="modal-useremail" class="text-muted fs-5 mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/admin-users.js'])
@endsection