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
        <div id="lottie-maintenance-animation" style="width: 100%; max-width: 220px; height: 180px; margin: 0 auto 0.5rem auto;"></div>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lottie.loadAnimation({
                container: document.getElementById('lottie-maintenance-animation'),
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: "{{ asset('animations/maintenance.json') }}"
            });
        });
    </script>

</body>
</html>