<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <div>
            @if(Auth::user()->role === 'admin' || (method_exists(Auth::user(), 'hasRole') && Auth::user()->hasRole('admin')))
                <h4>Panel Admin</h4>
                <span class="sidebar-role">Gestión General</span>
            @elseif(Auth::user()->role === 'teacher')
                <h4>Panel Docente</h4>
                <span class="sidebar-role">Cuerpo Académico</span>
            @else
                <h4>Panel Estudiante</h4>
                <span class="sidebar-role">Nivelación Académica</span>
            @endif
        </div>
        <button class="btn btn-sm btn-dark text-white border-0 shadow-none" id="sidebarToggle" title="Ocultar / Mostrar Menú" style="background-color: rgba(255,255,255,0.08);">
            <i class="bi bi-list fs-5"></i>
        </button>
    </div>
    
    <ul class="sidebar-menu">
        @if(Auth::user()->role === 'admin' || (method_exists(Auth::user(), 'hasRole') && Auth::user()->hasRole('admin')))
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i> Resumen General</a></li>
            @can('manage-users')
                <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="bi bi-people me-2"></i> Gestión de Usuarios</a></li>
                <li><a href="{{ route('admin.teachers.pending') }}" class="{{ request()->routeIs('admin.teachers*') ? 'active' : '' }}"><i class="bi bi-person-check me-2"></i> Aceptar Docentes</a></li>
                <li><a href="{{ route('admin.emails.index') }}" class="{{ request()->routeIs('admin.emails*') ? 'active' : '' }}"><i class="bi bi-inbox me-2"></i> Buzón IMAP</a></li>
                <li><a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners*') ? 'active' : '' }}"><i class="bi bi-image me-2"></i> Gestión de Banners</a></li>
                <li><a href="{{ route('admin.news.index') }}" class="{{ request()->routeIs('admin.news*') ? 'active' : '' }}"><i class="bi bi-newspaper me-2"></i> Gestión de Noticias</a></li>
                <li><a href="{{ route('admin.security') }}" class="{{ request()->routeIs('admin.security*') ? 'active' : '' }}"><i class="bi bi-shield-lock me-2"></i> Seguridad y Accesos</a></li>
            @endcan
        @elseif(Auth::user()->role === 'teacher')
            <li><a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.*') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i> Inicio Docente</a></li>
            <li><a href="#" class="text-muted"><i class="bi bi-journal-bookmark me-2"></i> Mis Cursos Asignados</a></li>
            <li><a href="#" class="text-muted"><i class="bi bi-file-earmark-text me-2"></i> Subir Guías y Material</a></li>
        @else
            <li><a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.*') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i> Inicio Estudiante</a></li>
        @endif

        <li><a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}"><i class="bi bi-gear me-2"></i> Configuración</a></li>
    </ul>

    <div class="sidebar-footer d-flex flex-column gap-2">
        <a href="{{ route('admin.profile') }}" class="btn w-100 text-start py-2 px-3 rounded-6 fw-bold text-decoration-none d-flex align-items-center gap-2 {{ request()->routeIs('admin.profile') ? 'active' : '' }}" style="background-color: #343a40; color: #fff; font-size: 0.88rem; border: none;">
            <i class="bi bi-person-circle fs-6"></i> Mi Perfil
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout-sidebar">Cerrar Sesión</button>
        </form>
    </div>

    @include('layouts.partials.logout-animation')
</aside>