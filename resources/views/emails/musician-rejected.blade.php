<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Verificación - Armonihz</title>
    <style>
        body { margin:0; padding:0; background:#f4f7fc; font-family:'Inter',Arial,sans-serif; }
        .wrapper { max-width:520px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
        .header { background:linear-gradient(135deg,#1a0b38,#6c3fc5); padding:36px 32px; text-align:center; }
        .header h1 { margin:0; color:#fff; font-size:22px; font-weight:700; }
        .body { padding:36px 32px; }
        .greeting { font-size:15px; color:#374151; margin-bottom:24px; line-height:1.6; }
        .btn-wrap { text-align:center; margin:28px 0; }
        .btn {
            display:inline-block; background:linear-gradient(135deg,#6c3fc5,#2f93f5);
            color:#fff; font-size:15px; font-weight:700; padding:14px 36px;
            border-radius:12px; text-decoration:none; letter-spacing:.3px;
            box-shadow:0 4px 16px rgba(108,63,197,.35);
        }
        .note { font-size:13px; color:#6b7280; line-height:1.6; margin-top:20px; }
        .note a { color:#6c3fc5; }
        .url-box { background:#f5f3ff; border:1px solid #ede9fe; border-radius:8px;
                   padding:10px 14px; font-size:11px; color:#6c3fc5; word-break:break-all;
                   margin-top:12px; }
        .reject-box { background:#fff1f2; border:1px solid #fecaca; border-radius:8px;
                   padding:12px 16px; font-size:13px; color:#991b1b; margin:20px 0; }
        .reject-label { font-size:11px; font-weight:800; color:#be123c; text-transform:uppercase; display:block; margin-bottom:4px; }
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
            Hola <strong>{{ $stageName }}</strong>,<br><br>
            Hemos revisado el documento de identidad que enviaste y, lamentablemente, no ha podido ser verificado en esta ocasión.
            Tu perfil sigue activo, pero necesitamos que vuelvas a intentar el proceso de validación.
        </p>

        <div class="reject-box">
            <span class="reject-label">Motivo de revisión requerida:</span>
            "{{ $rejectionReason }}"
        </div>

        <div class="btn-wrap">
            <a href="{{ $retryUrl }}" class="btn">
                📤 &nbsp; Subir documento de nuevo
            </a>
        </div>

        <p class="note">
            Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:
        </p>
        <div class="url-box">{{ $retryUrl }}</div>

        <p class="note" style="margin-top:24px;">
            Si crees que esto es un error y tu documento es válido, por favor contáctanos respondiendo a este correo.
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} Armonihz · Todos los derechos reservados
    </div>
</div>
</body>
</html>
