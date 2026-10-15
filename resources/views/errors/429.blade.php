<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demasiadas Solicitudes - EduNivelam</title>
    @vite(['resources/css/errors/error-429.css', 'resources/js/app.js'])
</head>
<body class="error-401-body">

    <div class="error-card">
        <div id="lottie-429" style="width: 100%; max-width: 200px; height: 160px; margin: 0 auto 0.5rem auto;"></div>

        <div class="error-code" style="color: #dc3545;">429</div>
        <h2 class="error-title">Demasiadas Solicitudes</h2>
        <p class="error-text">
            Has realizado demasiadas solicitudes en muy poco tiempo. Por seguridad, tu acceso se encuentra limitado temporalmente.
        </p>
        <div>
            <a href="{{ route('home') }}" class="btn-home" style="background-color: #dc3545; color: #fff;">
                Regresar al Inicio
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lottie.loadAnimation({
                container: document.getElementById('lottie-429'),
                renderer: 'svg', loop: true, autoplay: true,
                path: "{{ asset('animations/too-many-requests.json') }}"
            });
        });
    </script>
</body>
</html>