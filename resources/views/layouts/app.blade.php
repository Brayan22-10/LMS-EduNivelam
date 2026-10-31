@php
    $isAdminArea = request()->routeIs('dashboard') || request()->routeIs('admin.*') || request()->routeIs('settings.*');
    $isFullBleed = request()->routeIs('teacher.pending') || request()->routeIs('teacher.status*');
    $hideFooter = request()->routeIs('login*') || request()->routeIs('register*');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $isAdminArea ? 'admin-root' : 'public-root' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduNivelam - LMS Educativo de Cursos')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js', 'resources/js/dashboard.js'])
    @yield('styles')
</head>
<body style="font-size: {{ (Auth::check() && Auth::user()->font_size) ? Auth::user()->font_size : '14px' }};" class="{{ (Auth::check() && Auth::user()->high_contrast) ? 'bg-dark-mode text-light' : 'bg-light text-dark' }} {{ $isAdminArea ? 'admin-fixed-body' : 'public-scroll-body' }} {{ $isFullBleed ? 'page-full-bleed' : '' }}">
    
    @if($isAdminArea)
        <button class="btn btn-dark text-white border shadow" id="sidebarToggleFloat" title="Mostrar Menú" style="position: fixed; top: 22px; left: 15px; z-index: 1020; display: none; width: 40px; height: 40px; border-radius: 50%; align-items: center; justify-content: center; padding: 0;">
            <i class="bi bi-list fs-5"></i>
        </button>

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