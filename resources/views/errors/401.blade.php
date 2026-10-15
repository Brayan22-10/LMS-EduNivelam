<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Autorizado - EduNivelam</title>
    @vite(['resources/css/errors/error-401.css', 'resources/js/app.js'])
</head>
<body class="error-401-body">

    <div class="error-card">
        <div id="lottie-401" style="width: 100%; max-width: 200px; height: 160px; margin: 0 auto 0.5rem auto;"></div>

        <div class="error-code">401</div>
        <h2 class="error-title">Acceso No Autorizado</h2>
        <p class="error-text">
            Debes iniciar sesión con una cuenta válida para poder acceder a este espacio académico en EduNivelam.
        </p>
        <div>
            <a href="{{ route('login') }}" class="btn-home">
                Iniciar Sesión
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lottie.loadAnimation({
                container: document.getElementById('lottie-401'),
                renderer: 'svg', loop: true, autoplay: true,
                path: "{{ asset('animations/student-401.json') }}"
            });
        });
    </script>
</body>
</html>