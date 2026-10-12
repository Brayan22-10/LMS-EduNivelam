@php
    $isPanelRoute = request()->is('admin*') || request()->is('dashboard*') || request()->is('teacher*') || request()->is('student*') || request()->is('settings*');
    $isInAnyPanel = $isPanelRoute || request()->routeIs('dashboard*') || request()->routeIs('admin.*') || request()->routeIs('settings.*') || request()->routeIs('teacher.*') || request()->routeIs('student.*');
@endphp

@if($isInAnyPanel)
    <div class="panel-nav-container px-0 pt-2 mb-3">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-pill px-4 shadow-lg floating-navbar d-flex justify-content-between align-items-center">
            <div class="container-fluid px-0 d-flex justify-content-between align-items-center w-100">
                
                <a class="navbar-brand fw-bold text-info" href="{{ route('home') }}">EduNivelam</a>

                <button class="navbar-toggler border-0 ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavPanel" aria-controls="navbarNavPanel" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavPanel">
                    <ul class="navbar-nav align-items-center gap-2 mb-0">
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill {{ request()->routeIs('home') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill {{ request()->routeIs('about') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('about') }}">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill {{ request()->routeIs('news.*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('news.index') }}">News</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill {{ request()->routeIs('courses*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('courses') }}">Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill {{ request()->routeIs('instructors*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('instructors') }}">Instructors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 rounded-pill {{ request()->routeIs('contact*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('contact') }}">Contact</a>
                        </li>

                        @if(Auth::check())
                            @if(Auth::user()->role === 'admin')
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill fw-bold text-warning" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                            @elseif(Auth::user()->role === 'teacher')
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill fw-bold text-warning" href="{{ route('teacher.dashboard') }}">Mi Campus</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill fw-bold text-info" href="{{ route('student.dashboard') }}">Mi Campus</a>
                                </li>
                            @endif
                        @endif

                        <li class="nav-item ms-lg-3 text-light small">
                            <span>Hola, {{ Auth::user()->name ?? '' }}</span>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Salir</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
@else
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
                            <li class="nav-item">
                                <a class="nav-link px-3 rounded-pill {{ request()->routeIs('home') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 rounded-pill {{ request()->routeIs('about') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('about') }}">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 rounded-pill {{ request()->routeIs('news.*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('news.index') }}">News</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 rounded-pill {{ request()->routeIs('courses*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('courses') }}">Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 rounded-pill {{ request()->routeIs('instructors*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('instructors') }}">Instructors</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-3 rounded-pill {{ request()->routeIs('contact*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('contact') }}">Contact</a>
                            </li>
                            <li class="nav-item ms-lg-2">
                                <a class="nav-link btn btn-outline-light px-3 py-1 rounded-pill" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn btn-info text-dark fw-bold px-3 py-1 rounded-pill" href="{{ route('register') }}">Sign Up</a>
                            </li>
                        @else
                            @if(Auth::user()->hasVerifiedEmail())
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill {{ request()->routeIs('home') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('home') }}">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill {{ request()->routeIs('about') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('about') }}">About</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill {{ request()->routeIs('news.*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('news.index') }}">News</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill {{ request()->routeIs('courses*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('courses') }}">Courses</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill {{ request()->routeIs('instructors*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('instructors') }}">Instructors</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 rounded-pill {{ request()->routeIs('contact*') ? 'bg-primary text-white fw-semibold shadow-sm' : '' }}" href="{{ route('contact') }}">Contact</a>
                                </li>

                                @if(Auth::user()->role === 'admin')
                                    <li class="nav-item">
                                        <a class="nav-link text-warning fw-bold px-3" href="{{ route('dashboard') }}">Dashboard</a>
                                    </li>
                                @elseif(Auth::user()->role === 'teacher')
                                    <li class="nav-item ms-lg-2">
                                        <a class="btn btn-warning btn-sm text-dark fw-bold px-3 py-1 rounded-pill shadow-sm" href="{{ route('teacher.dashboard') }}">Mi Campus</a>
                                    </li>
                                @else
                                    <li class="nav-item ms-lg-2">
                                        <a class="btn btn-info btn-sm text-dark fw-bold px-3 py-1 rounded-pill shadow-sm" href="{{ route('student.dashboard') }}">Mi Campus</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item">
                                    <span class="nav-link text-warning small px-3">Verificación de correo pendiente</span>
                                </li>
                            @endif

                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
@endif