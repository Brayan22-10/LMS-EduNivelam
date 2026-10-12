<aside class="admin-sidebar">
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
        <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Resumen General</a></li>
        
        @if(Auth::user()->role === 'admin' || (method_exists(Auth::user(), 'hasRole') && Auth::user()->hasRole('admin')))
            @can('manage-users')
                <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">Gestión de Usuarios</a></li>
                <li><a href="{{ route('admin.teachers.pending') }}" class="{{ request()->routeIs('admin.teachers*') ? 'active' : '' }}">Aceptar Docentes</a></li>
                <li><a href="{{ route('admin.emails.index') }}" class="{{ request()->routeIs('admin.emails*') ? 'active' : '' }}">Buzón IMAP</a></li>
                <li><a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners*') ? 'active' : '' }}">Gestión de Banners</a></li>
                <li><a href="{{ route('admin.news.index') }}" class="{{ request()->routeIs('admin.news*') ? 'active' : '' }}">Gestión de Noticias</a></li>
                <li><a href="{{ route('admin.security') }}" class="{{ request()->routeIs('admin.security*') ? 'active' : '' }}">Seguridad y Accesos</a></li>
            @endcan
        @elseif(Auth::user()->role === 'teacher')
            <li><a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.*') ? 'active' : '' }}">Inicio Docente</a></li>
        @else
            <li><a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.*') ? 'active' : '' }}">Inicio Estudiante</a></li>
        @endif

        <li><a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">Configuración</a></li>
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
</aside>