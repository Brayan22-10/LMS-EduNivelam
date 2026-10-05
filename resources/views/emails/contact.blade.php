<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mensaje del CMS</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
    <h2 style="color: #0d6efd;">Nuevo mensaje recibido desde EduNivelam</h2>
    
    <p><strong>Nombre:</strong> {{ $name }}</p>
    <p><strong>Correo electrónico:</strong> {{ $email }}</p>
    <p><strong>Asunto / Motivo:</strong> {{ $subjectText }}</p>
    
    <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 20px 0;">
    
    <h3 style="font-size: 16px; color: #495057;">Mensaje detallado:</h3>
    <p style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef;">
        {{ $userMessage }}
    </p>
</body>
</html>