<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ request()->routeIs('dashboard') || request()->routeIs('admin.*') ? 'admin-root' : 'public-root' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduNivelam - LMS Educativo de Cursos')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body style="font-size: {{ (Auth::check() && Auth::user()->font_size) ? Auth::user()->font_size : '14px' }};" class="{{ (Auth::check() && Auth::user()->high_contrast) ? 'bg-dark-mode text-light' : 'bg-light text-dark' }} {{ request()->routeIs('dashboard') || request()->routeIs('admin.*') ? 'admin-fixed-body' : 'public-scroll-body' }}">
    
    @if(!request()->routeIs('admin.*'))
        @include('layouts.navigation')
    @endif

    @if(request()->routeIs('dashboard') || request()->routeIs('admin.*') || request()->routeIs('login*') || request()->routeIs('register*') || request()->routeIs('teacher.pending'))
        <div class="auth-wrapper-content">
            @yield('content')
        </div>
    @else
        <main class="py-4">  
            <div class="container">
                @yield('content')
            </div>
        </main>
        @include('layouts.footer')
    @endif

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

    @yield('scripts')
</body>
</html>