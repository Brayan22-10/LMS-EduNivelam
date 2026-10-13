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
            <div class="dashboard-topbar mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2>Gestión de Usuarios</h2>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-info text-white fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#kanbanRolesModal">
                        <i class="bi bi-kanban me-1"></i> Gestionar Roles
                    </button>
                    <span class="badge bg-success text-white p-2">Total: {{ $users->count() }}</span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <th>Rol Actual</th>
                                <th>Estado</th>
                                <th>Fecha de Registro</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="user-row-clickable" data-bs-toggle="modal" data-bs-target="#userModal">
                                        <strong>#{{ $user->id }}</strong>
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary text-uppercase">{{ $user->role ?? 'usuario' }}</span>
                                    </td>
                                    <td>
                                        @if($user->is_active ?? true)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
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
                                        @else
                                            <span class="badge bg-secondary">Tu cuenta</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No hay usuarios registrados en este momento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="kanbanRolesModal" tabindex="-1" aria-labelledby="kanbanRolesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-light">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="kanbanRolesModalLabel"><i class="bi bi-kanban me-2"></i> Tablero de Gestión de Roles de Usuarios</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted text-center mb-4">Visualiza los usuarios clasificados por su rol actual y reasignales un nuevo rol de forma inmediata.</p>
                
                <div class="row g-3">
                    @php
                        $rolesList = [
                            'admin' => ['title' => 'Administradores', 'color' => 'danger'],
                            'teacher' => ['title' => 'Docentes', 'color' => 'success'],
                            'student' => ['title' => 'Estudiantes', 'color' => 'primary'],
                            'user' => ['title' => 'Usuarios Generales', 'color' => 'secondary']
                        ];
                    @endphp

                    @foreach($rolesList as $roleKey => $roleData)
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-{{ $roleData['color'] }} text-white text-center fw-bold">
                                    {{ $roleData['title'] }}
                                </div>
                                <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                                    @php
                                        $filteredUsers = $users->where('role', $roleKey);
                                    @endphp

                                    @forelse($filteredUsers as $u)
                                        <div class="card mb-2 p-2 border bg-white shadow-sm">
                                            <div class="small fw-bold text-dark">{{ $u->name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $u->email }}</div>
                                            
                                            <form action="{{ route('admin.users.updateRole', $u->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group input-group-sm">
                                                    <select name="role" class="form-select form-select-sm" style="font-size: 0.75rem;">
                                                        <option value="" disabled selected>Mover a...</option>
                                                        <option value="admin">Administrador</option>
                                                        <option value="docente">Docente</option>
                                                        <option value="student">Estudiante</option>
                                                        <option value="user">Usuario</option>
                                                    </select>
                                                    <button class="btn btn-outline-dark btn-sm" type="submit" style="font-size: 0.75rem;">Mover</button>
                                                </div>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted small py-4">No hay usuarios en este rol.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/admin-users.js'])
@endsection