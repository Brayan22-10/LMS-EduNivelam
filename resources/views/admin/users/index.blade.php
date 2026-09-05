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
                <li><a href="{{ route('dashboard') }}">📊 Resumen General</a></li>
                <li><a href="{{ route('admin.users') }}" class="active">👥 Gestión de Usuarios</a></li>
                <li><a href="#">⚙️ Configuración del Sistema</a></li>
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

            <div class="card shadow-sm border-0 p-4">
                <h4 class="mb-3 text-dark">Usuarios Registrados</h4>
                <p class="text-muted mb-4">Haz clic en cualquier usuario de la lista para ver su información detallada en una ventana emergente.</p>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="user-row-clickable" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#userModal" 
                                    data-name="{{ $user->name }}" 
                                    data-email="{{ $user->email }}">
                                    <td><strong>#{{ $user->id }}</strong></td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay usuarios registrados en este momento.</td>
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
                <div class="mb-3">
                    <span class="user-modal-icon">👤</span>
                </div>
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