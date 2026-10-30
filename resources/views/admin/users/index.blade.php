@extends('layouts.app')

@section('title', 'Gestión de Usuarios - EduNivelam')

@section('styles')
    @vite(['resources/css/dashboard.css', 'resources/css/admin-users.css'])
@endsection

@section('content')
@php
    $cards = [
        ['label' => 'Total de usuarios',   'value' => $stats['total'],    'icon' => 'bi-people-fill',      'color' => 'primary'],
        ['label' => 'Nuevos este mes',     'value' => $stats['newMonth'], 'icon' => 'bi-person-plus-fill', 'color' => 'info',
         'extra' => $stats['growth'] === null ? 'Sin datos del mes anterior' : (($stats['growth'] >= 0 ? '+' : '') . $stats['growth'] . '% vs. mes anterior')],
        ['label' => 'Cuentas activas',     'value' => $stats['active'],   'icon' => 'bi-check-circle-fill','color' => 'success'],
        ['label' => 'Cuentas inactivas',   'value' => $stats['inactive'], 'icon' => 'bi-slash-circle-fill','color' => 'danger'],
    ];

    $tabs = [
        'all'     => ['label' => 'Todos',          'count' => $countAll],
        'student' => ['label' => 'Estudiantes',    'count' => $roleCounts->get('student', 0)],
        'teacher' => ['label' => 'Docentes',       'count' => $roleCounts->get('teacher', 0)],
        'admin'   => ['label' => 'Administradores','count' => $roleCounts->get('admin', 0)],
    ];
@endphp

<div class="dashboard-fullscreen-fix">
    <div class="dashboard-wrapper">
        @include('layouts.partials.sidebar')

        <main class="dashboard-main-content">
            <div class="dashboard-topbar mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark-adaptive">Gestión de Usuarios</h2>
                </div>
                <button type="button" class="btn btn-info text-white fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#kanbanRolesModal">
                    <i class="bi bi-kanban me-1"></i> Gestionar Roles
                </button>
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

            <div class="row g-3 mb-4">
                @foreach($cards as $card)
                    <div class="col-6 col-xl-3">
                        <div class="card border-0 shadow-sm bg-card-adaptive h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }}">
                                    <i class="bi {{ $card['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="text-secondary small">{{ $card['label'] }}</div>
                                    <div class="fs-3 fw-bold text-dark-adaptive lh-1">{{ $card['value'] }}</div>
                                    @isset($card['extra'])
                                        <div class="small text-secondary mt-1">{{ $card['extra'] }}</div>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="userCharts" data-charts="{{ json_encode($charts) }}">
                <div class="row g-3 mb-3">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm bg-card-adaptive h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark-adaptive">Registros de cuentas</h6>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Rango">
                                        <button type="button" class="btn btn-outline-secondary active" data-range="daily">30 días</button>
                                        <button type="button" class="btn btn-outline-secondary" data-range="monthly">12 meses</button>
                                    </div>
                                </div>
                                <p class="small text-secondary mb-2">Muestra los picos de registro para relacionarlos con campañas, inicio de ciclo o comunicados.</p>
                                <div class="chart-box"><canvas id="chartRegistrations"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm bg-card-adaptive h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-1 text-dark-adaptive">Distribución por rol</h6>
                                <p class="small text-secondary mb-2">Proporción de estudiantes, docentes y administradores.</p>
                                <div class="chart-box"><canvas id="chartRoles"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm bg-card-adaptive h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-1 text-dark-adaptive">Registros por rol (últimos 12 meses)</h6>
                                <p class="small text-secondary mb-2">Compara si crecen más los estudiantes o los docentes.</p>
                                <div class="chart-box"><canvas id="chartRoleMonthly"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm bg-card-adaptive h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-1 text-dark-adaptive">Estado de cuentas</h6>
                                <p class="small text-secondary mb-2">Activas frente a inactivas.</p>
                                <div class="chart-box"><canvas id="chartStatus"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-pills gap-2 mb-4">
                @foreach($tabs as $key => $t)
                    <li class="nav-item">
                        <a class="nav-link rounded-pill px-4 fw-semibold {{ $tab === $key ? 'active bg-dark text-white' : 'bg-light text-dark border' }}"
                           href="{{ route('admin.users', ['tab' => $key]) }}">
                            {{ $t['label'] }} <span class="badge bg-secondary ms-1">{{ $t['count'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="card shadow-sm border-0 p-4 bg-card-adaptive">
                <h4 class="mb-3 text-dark-adaptive">Usuarios Registrados</h4>
                <p class="text-secondary mb-4">Administra los roles, estados y accesos de los usuarios registrados en la plataforma.</p>

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
                                    <td><strong>#{{ $user->id }}</strong></td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary text-uppercase">{{ $roleLabels[$user->role] ?? $user->role }}</span>
                                    </td>
                                    <td>
                                        @if($user->is_active ?? true)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/D' }}</td>
                                    <td class="text-center">
                                        @if(Auth::id() !== $user->id)
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    @if($user->is_active ?? true)
                                                        <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold" onclick="return confirm('¿Estás seguro de desactivar a este usuario?')">
                                                            Desactivar
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-success btn-sm fw-bold">
                                                            Activar
                                                        </button>
                                                    @endif
                                                </form>

                                                <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¡Atención! Esta acción eliminará permanentemente al usuario. ¿Deseas continuar?')">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Tu cuenta</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No hay usuarios en esta clasificación.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="kanbanRolesModal" tabindex="-1" aria-labelledby="kanbanRolesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-card-adaptive">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="kanbanRolesModalLabel"><i class="bi bi-kanban me-2"></i> Tablero de Gestión de Roles de Usuarios</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary text-center mb-4">Visualiza los usuarios clasificados por su rol actual y reasígnales un nuevo rol de forma inmediata.</p>

                <div class="row g-3">
                    @php
                        $rolesList = [
                            'admin'   => ['title' => 'Administradores',   'color' => 'danger'],
                            'teacher' => ['title' => 'Docentes',          'color' => 'success'],
                            'student' => ['title' => 'Estudiantes',       'color' => 'primary'],
                            'user'    => ['title' => 'Usuarios Generales','color' => 'secondary'],
                        ];
                    @endphp

                    @foreach($rolesList as $roleKey => $roleData)
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100 bg-card-adaptive">
                                <div class="card-header bg-{{ $roleData['color'] }} text-white text-center fw-bold">
                                    {{ $roleData['title'] }}
                                </div>
                                <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                                    @forelse($boardUsers->where('role', $roleKey) as $u)
                                        <div class="card mb-2 p-2 border shadow-sm bg-card-adaptive">
                                            <div class="small fw-bold text-dark-adaptive">{{ $u->name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $u->email }}</div>

                                            <form action="{{ route('admin.users.updateRole', $u->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group input-group-sm">
                                                    <select name="role" class="form-select form-select-sm" style="font-size: 0.75rem;">
                                                        <option value="" disabled selected>Mover a...</option>
                                                        @foreach(\App\Models\User::ROLES as $r)
                                                            <option value="{{ $r }}">{{ $roleLabels[$r] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-outline-secondary btn-sm text-dark-adaptive" type="submit" style="font-size: 0.75rem;">Mover</button>
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