<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Caducada - EduNivelam</title>
    @vite(['resources/css/errors/error-419.css', 'resources/js/app.js'])
</head>
<body class="error-419-body">

    <div class="error-card">
        <div id="lottie-419" style="width: 100%; max-width: 200px; height: 160px; margin: 0 auto 0.5rem auto;"></div>

        <div class="error-code">419</div>
        <h2 class="error-title">La Sesión ha Caducado</h2>
        <p class="error-text">
            Has estado inactivo demasiado tiempo o el token de seguridad expiró. Por favor, recarga la página e intenta de nuevo.
        </p>
        <div>
            <a href="{{ url()->previous() }}" class="btn-home">
                Reintentar / Recargar
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lottie.loadAnimation({
                container: document.getElementById('lottie-419'),
                renderer: 'svg', 
                loop: true, 
                autoplay: true,
                path: "{{ asset('animations/session-expired.json') }}"
            });
        });
    </script>
</body>
</html>