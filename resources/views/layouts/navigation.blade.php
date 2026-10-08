<div class="container py-3">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-pill px-4 shadow-lg floating-navbar">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-info" href="{{ route('home') }}">EduNivelam</a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2">
                    
                    @guest
                        {{-- Menú público para visitantes --}}
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('about') }}">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('courses') }}">Cursos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('instructors') }}">Instructores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('contact') }}">Contact</a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link btn btn-outline-light px-3 py-1 rounded-pill" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-info text-dark fw-bold px-3 py-1 rounded-pill" href="{{ route('register') }}">Sign Up</a>
                        </li>
                    @else
                        {{-- Usuario Autenticado: Verificamos si ya validó su correo --}}
                        @if(Auth::user()->hasVerifiedEmail())
                            <li class="nav-item">
                                <a class="nav-link px-3" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3" href="{{ route('about') }}">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3" href="{{ route('courses') }}">Cursos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3" href="{{ route('instructors') }}">Instructores</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3" href="{{ route('contact') }}">Contact</a>
                            </li>

                            {{-- Botón rápido dinámico según el Rol del usuario --}}
                            @if(Auth::user()->role === 'admin' || (method_exists(Auth::user(), 'hasRole') && Auth::user()->hasRole('admin')))
                                <li class="nav-item">
                                    <a class="nav-link text-warning fw-bold px-3" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                            @elseif(Auth::user()->role === 'teacher' || (method_exists(Auth::user(), 'hasRole') && Auth::user()->hasRole('teacher')))
                                <li class="nav-item ms-lg-2">
                                    <a class="btn btn-warning btn-sm text-dark fw-bold px-3 py-1 rounded-pill shadow-sm" href="{{ route('teacher.dashboard') }}">
                                        Mi Campus
                                    </a>
                                </li>
                            @else
                                <li class="nav-item ms-lg-2">
                                    <a class="btn btn-info btn-sm text-dark fw-bold px-3 py-1 rounded-pill shadow-sm" href="{{ route('student.dashboard') }}">
                                        Mi Campus
                                    </a>
                                </li>
                            @endif
                        @else
                            {{-- Si NO ha verificado su correo, ocultamos la navegación para evitar que escape --}}
                            <li class="nav-item">
                                <span class="nav-link text-warning small px-3">Verificación de correo pendiente</span>
                            </li>
                        @endif

                        <li class="nav-item ms-lg-3 text-light small">
                            <span>Hola, {{ Auth::user()->name }}</span>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Salir</button>
                            </form>
                        </li>
                    @endguest

                </ul>
            </div>
        </div>
    </nav>
</div>