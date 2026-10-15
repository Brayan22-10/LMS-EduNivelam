<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - EduNivelam</title>
    @vite(['resources/css/errors/error-404.css', 'resources/js/app.js'])
</head>
<body class="error-404-body">

    <div class="error-card">
        <div id="lottie-error-animation" style="width: 100%; max-width: 220px; height: 180px; margin: 0 auto 0.5rem auto;"></div>

        <div class="error-code">404</div>
        <h2 class="error-title">Página no encontrada</h2>
        <p class="error-text">
            Lo sentimos, la página que buscas no existe o ha sido movida. Vuelve al inicio para continuar navegando.
        </p>
        <div>
            <a href="{{ route('home') }}" class="btn-home">
                Regresar al Inicio
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lottie.loadAnimation({
                container: document.getElementById('lottie-error-animation'),
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: "{{ asset('animations/student-404.json') }}" 
            });
        });
    </script>
</body>
</html>