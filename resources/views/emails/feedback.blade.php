<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eaeaea; border-radius: 8px; background: #f9f9f9; }
        .header { background: #212529; color: #fff; padding: 15px; text-align: center; border-radius: 6px 6px 0 0; font-weight: bold; }
        .content { padding: 20px; background: #fff; border-radius: 0 0 6px 6px; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">EduNivelam - Reporte de Comentarios</div>
        <div class="content">
            <p>Hola, <strong>Administrador</strong>,</p>
            <p>Un usuario ha enviado un nuevo comentario o reporte de sugerencia desde el panel de configuración de la plataforma:</p>
            
            <hr style="border:0; border-top:1px solid #eee; margin: 15px 0;">
            
            <p><strong>Remitente:</strong> {{ $user->name }} ({{ $user->email }})</p>
            <p><strong>Rol en plataforma:</strong> {{ ucfirst($user->role ?? 'Usuario') }}</p>
            <p><strong>Mensaje del usuario:</strong></p>
            <blockquote style="background: #f1f3f5; padding: 12px; border-left: 4px solid #212529; margin: 0; border-radius: 4px;">
                {{ $feedbackMessage }}
            </blockquote>
        </div>
        <div class="footer">
            Este es un correo automatizado generado desde el sistema EduNivelam. Por favor no respondas directamente a este mensaje sin contactar al usuario por su correo institucional.
        </div>
    </div>
</body>
</html>