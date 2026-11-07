@php
    $isAdminArea = request()->routeIs('dashboard*') 
        || request()->routeIs('admin.*') 
        || request()->routeIs('settings.*')
        || request()->routeIs('student.*')
        || request()->routeIs('teacher.dashboard');
    $isFullBleed = request()->routeIs('teacher.pending') || request()->routeIs('teacher.status*');
    $hideFooter = request()->routeIs('login*') || request()->routeIs('register*');
    $isAuth = request()->routeIs('login*') || request()->routeIs('register*') || request()->routeIs('password.*');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $isAdminArea ? 'admin-root' : 'public-root' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduNivelam - LMS Educativo de Cursos')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-light.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js', 'resources/js/dashboard.js'])
    @yield('styles')
</head>
<body style="font-size: {{ (Auth::check() && Auth::user()->font_size) ? Auth::user()->font_size : '14px' }};" class="{{ (Auth::check() && Auth::user()->high_contrast) ? 'bg-dark-mode text-light' : 'bg-light text-dark' }} {{ $isAdminArea ? 'admin-fixed-body' : 'public-scroll-body' }} {{ $isFullBleed ? 'page-full-bleed' : '' }}">

    @if($isAdminArea)
        <button class="btn btn-dark text-white border shadow" id="sidebarToggleFloat" title="Mostrar Menú" aria-label="Abrir menú de navegación">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

        <div class="dashboard-nav-container">
            @include('layouts.navigation')
        </div>
    @else
        @include('layouts.navigation')
    @endif

    @if($isAdminArea)
        <div class="auth-wrapper-content">
            @yield('content')
        </div>
    @elseif($isFullBleed)
        <main class="public-full">
            @yield('content')
        </main>
    @elseif($isAuth)
        <div class="auth-page-wrapper">
            @yield('content')
        </div>
    @else
        <main class="py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>
        
        @unless($hideFooter)
            @include('layouts.footer')
        @endunless
    @endif

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

    @yield('scripts')
</body>
</html>
