<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitio en Mantenimiento - EduNivelam</title>
    @vite(['resources/css/errors/maintenance.css', 'resources/js/app.js'])
</head>
<body class="maintenance-body">

    <div class="maintenance-card">
        <div class="maintenance-icon">🛠️</div>
        <h2 class="maintenance-title">Sistema en Mantenimiento</h2>
        <p class="maintenance-text">
            Estamos realizando mejoras y actualizaciones en la plataforma EduNivelam. Estaremos de regreso muy pronto. Gracias por tu paciencia.
        </p>
        <div>
            <a href="{{ route('home') }}" class="btn-maintenance-home">
                Regresar al Inicio
            </a>
        </div>
    </div>

</body>
</html>