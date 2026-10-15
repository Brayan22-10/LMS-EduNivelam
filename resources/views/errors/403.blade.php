<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - EduNivelam</title>
    @vite(['resources/css/errors/error-403.css', 'resources/js/app.js'])
</head>
<body class="error-403-body">

    <div class="error-card">
        <div style="max-width: 200px; margin: 0 auto 1rem auto;">
            <img src="{{ asset('animations/error-403.svg') }}" alt="Acceso Restringido" style="width: 100%; height: auto;">
        </div>

        <div class="error-code">403</div>
        <h2 class="error-title">Acceso Restringido</h2>
        <p class="error-text">
            Lo sentimos, no cuentas con los permisos o privilegios de rol necesarios para acceder a esta sección o panel administrativo.
        </p>
        <div>
            @php
                $dashboardRoute = 'home';
                if (auth()->check()) {
                    $userRole = auth()->user()->role ?? '';
                    if ($userRole === 'admin') {
                        $dashboardRoute = 'dashboard';
                    } elseif ($userRole === 'teacher' || $userRole === 'docente') {
                        $dashboardRoute = 'teacher.dashboard';
                    } elseif ($userRole === 'student') {
                        $dashboardRoute = 'student.dashboard';
                    }
                }
            @endphp
            <a href="{{ route($dashboardRoute) }}" class="btn-home">
                Regresar a mi Panel
            </a>
        </div>
    </div>

</body>
</html>