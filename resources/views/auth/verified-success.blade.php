<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo Verificado - EduNivelam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-5 text-center border-0" style="max-width: 450px; width: 100%;">
        <div class="mb-4">
            <div class="text-success display-1 mb-3">✓</div>
            <h3 class="fw-bold text-dark">¡Correo Verificado con Éxito!</h3>
            <p class="text-muted mt-2">
                Tu cuenta ha sido activada correctamente en EduNivelam. Puedes cerrar esta pestaña y regresar a la plataforma principal.
            </p>
        </div>
        <button onclick="window.close()" class="btn btn-dark w-100 py-2">Cerrar pestaña</button>
    </div>

    <script>
        setTimeout(function() {
            window.close();
        }, 2000);
    </script>
</body>
</html>