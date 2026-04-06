<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud - Armonihz</title>
    <style>
        body { margin:0; padding:0; background:#f4f7fc; font-family:'Inter',Arial,sans-serif; }
        .wrapper { max-width:520px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
        .header { background:linear-gradient(135deg,#1a0b38,#6c3fc5); padding:36px 32px; text-align:center; }
        .header h1 { margin:0; color:#fff; font-size:24px; font-weight:700; letter-spacing: 0.5px; }
        .body { padding:36px 32px; }
        .greeting { font-size:16px; color:#1f2937; margin-bottom:24px; line-height:1.6; }
        .btn-wrap { text-align:center; margin:32px 0; }
        .btn {
            display:inline-block; background:linear-gradient(135deg,#6c3fc5,#2f93f5);
            color:#fff; font-size:16px; font-weight:700; padding:16px 40px;
            border-radius:12px; text-decoration:none; letter-spacing:.3px;
            box-shadow:0 4px 16px rgba(108,63,197,.35);
        }
        .info-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px;
                   padding:12px 16px; font-size:14px; color:#15803d; margin:24px 0;
                   display:flex; align-items:center; gap:8px; }
        .footer { background:#f9fafb; padding:20px 32px; text-align:center; font-size:12px; color:#9ca3af; border-top:1px solid #f0f0f0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🎵 Armonihz</h1>
    </div>
    <div class="body">
        <p class="greeting">
            Hola <strong>{{ $musicianName }}</strong>,<br><br>
            ¡Excelentes noticias! El cliente <strong>{{ $clientName }}</strong> te ha enviado una solicitud de contratación para un evento el día <strong>{{ \Carbon\Carbon::parse($eventDate)->format('d/m/Y') }}</strong>.
        </p>

        <div class="info-box">
            ⭐ &nbsp; Responde rápido para no perder esta oportunidad.
        </div>

        <div class="btn-wrap">
            <a href="https://armonihz-web-armonihz.lugsb1.easypanel.host/" class="btn">
                Ver detalles y responder
            </a>
        </div>

        <p style="font-size:13px; color:#6b7280; line-height:1.6; margin-top:20px;">
            Inicia sesión en tu portal de músico de Armonihz para aceptar, rechazar o hacerle una contraoferta a este cliente.
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} Armonihz · Todos los derechos reservados
    </div>
</div>
</body>
</html>