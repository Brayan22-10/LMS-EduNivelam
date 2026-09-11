<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Environmental CMS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('site.reports') }}">Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('site.directory') }}">Directory</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact') }}">Contact</a>
                </li>

                {{-- Opciones para usuarios no autenticados (Invitados) --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Sign Up</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @else
                    {{-- Opciones exclusivas para Administradores con permisos de gestión --}}
                    @can('manage-users')
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @endcan

                    {{-- Saludo y control de sesión para cualquier usuario autenticado --}}
                    <li class="nav-item ms-lg-3 text-light">
                        <span>Hola, {{ Auth::user()->name }}</span>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">Cerrar sesión</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>