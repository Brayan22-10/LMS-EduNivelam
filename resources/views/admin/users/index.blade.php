@extends('layouts.app')

@section('title', 'Gestión de Usuarios - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/admin-users.css'])
@endsection

@section('content')
<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content">
            {{-- Barra de título superior recuperada --}}
            <div class="dashboard-topbar mb-4">
                <h2>Gestión de Usuarios</h2>
                <span class="badge bg-success text-white p-2">Total: {{ $users->count() }}</span>
            </div>

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
                <p class="text-muted mb-4">Haz clic en cualquier usuario de la lista para ver su información detallada o gestiona su estado y acceso.</p>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Estado</th>
                                <th>Fecha de Registro</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        <strong>#{{ $user->id }}</strong>
                                    </td>
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        {{ $user->name }}
                                    </td>
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        @if($user->is_active ?? true)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        {{ $user->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    
                                    <td class="text-center">
                                        @if(Auth::id() !== $user->id)
                                            <div class="d-flex justify-content-center gap-2">
                                                @if($user->is_active ?? true)
                                                    <button type="button" class="btn btn-warning btn-sm text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#statusModal{{ $user->id }}">
                                                        Desactivar
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#statusModal{{ $user->id }}">
                                                        Activar
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                                    Eliminar
                                                </button>
                                            </div>

                                            <div class="modal fade" id="statusModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header {{ ($user->is_active ?? true) ? 'bg-warning text-dark' : 'bg-success text-white' }}">
                                                            <h5 class="modal-title">Confirmar cambio de estado</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            @if($user->is_active ?? true)
                                                                ¿Estás seguro de desactivar a <strong>{{ $user->name }}</strong>? No podrá iniciar sesión hasta que lo vuelvas a activar.
                                                            @else
                                                                ¿Estás seguro de activar nuevamente a <strong>{{ $user->name }}</strong>? Recuperará el acceso a la plataforma.
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn {{ ($user->is_active ?? true) ? 'btn-warning' : 'btn-success' }}">
                                                                    {{ ($user->is_active ?? true) ? 'Sí, desactivar' : 'Sí, activar' }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">Confirmar eliminación permanente</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            ¿Estás seguro de eliminar permanentemente al usuario <strong>{{ $user->name }}</strong> de la base de datos? Esta acción no se puede deshacer.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST">
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
                                    <td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados en este momento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

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