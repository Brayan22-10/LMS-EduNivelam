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
                    <li><a href="{{ route('admin.emails.index') }}">  Buzón IMAP</a></li>
                    <li><a href="{{ route('admin.security') }}" class="active"> Seguridad y Accesos</a></li>
                @endcan

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
                <h2>Monitoreo de Seguridad</h2>
                
                {{-- Indicador visual de estado de seguridad --}}
                @if($securityStatus === 'secure')
                    <span class="badge bg-success text-white p-2">Sistema Seguro (Operación Normal)</span>
                @elseif($securityStatus === 'warning')
                    <span class="badge bg-warning text-dark p-2">Precaución: Actividad Inusual</span>
                @else
                    <span class="badge bg-danger text-white p-2">Alerta Crítica de Seguridad</span>
                @endif
            </div>

            {{-- APARTADO EXCLUSIVO DEL WIDGET DE SEGURIDAD Y ACCESOS --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0"> Widget de Estadísticas de Seguridad y Accesos</h5>
                            <span class="badge bg-secondary">Últimas 24 horas</span>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-4">
                                <div class="col-md-6 border-end">
                                    <h2 class="fw-bold {{ $failedCount > 0 ? 'text-danger' : 'text-success' }}">{{ $failedCount }}</h2>
                                    <p class="text-muted mb-0">Intentos de acceso erróneos detectados</p>
                                </div>
                                <div class="col-md-6">
                                    <h2 class="fw-bold text-dark">{{ $stats['users'] }}</h2>
                                    <p class="text-muted mb-0">Cuentas activas en la plataforma</p>
                                </div>
                            </div>

                            <hr>

                            <h6 class="text-dark fw-bold mb-3">Bitácora de Eventos de Seguridad Recientes</h6>
                            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha y Hora</th>
                                            <th>Correo Involucrado</th>
                                            <th>Dirección IP</th>
                                            <th>Detalle del Evento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentFailedLogins as $log)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                                                <td><code>{{ $log->email ?? 'N/D' }}</code></td>
                                                <td><span class="badge bg-light text-dark border">{{ $log->ip_address }}</span></td>
                                                <td><span class="text-danger fw-semibold">{{ $log->description }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    No se han registrado intentos fallidos de acceso recientes. El sistema opera con total normalidad.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection