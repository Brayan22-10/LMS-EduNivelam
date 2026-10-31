<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta no registrada - EduNivelam</title>
    @vite(['resources/css/errors/google-error.css', 'resources/js/app.js'])
</head>
<body class="error-google-body">

    <div class="error-card">
        <div id="lottie-google-animation" style="width: 100%; max-width: 240px; height: 180px; margin: 0 auto 1rem auto;"></div>

        <div class="error-code">¡Atención!</div>
        <h2 class="error-title">Cuenta no vinculada</h2>
        <p class="error-text">
            {{ $message ?? 'El correo electrónico de Google no se encuentra registrado en EduNivelam. Regístrate para comenzar a usar la plataforma.' }}
        </p>
        
        <div class="error-actions" style="display: flex; justify-content: center; gap: 10px;">
            <a href="{{ route('register') }}" class="btn-home">
                Registrarse Ahora
            </a>
            <a href="{{ route('login') }}" class="btn-secondary-action">
                Volver al Login
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lottie.loadAnimation({
                container: document.getElementById('lottie-google-animation'),
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: "{{ asset('animations/google-error.json') }}" 
            });
        });
    </script>
</body>
</html>