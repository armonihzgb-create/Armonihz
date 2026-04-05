<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupera tu contraseña - Armonihz</title>
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
        .note { font-size:13px; color:#6b7280; line-height:1.6; margin-top:20px; }
        .url-box { background:#f5f3ff; border:1px solid #ede9fe; border-radius:8px;
                   padding:12px 14px; font-size:11px; color:#6c3fc5; word-break:break-all;
                   margin-top:12px; }
        .warning-box { background:#fffbeb; border:1px solid #fef08a; border-radius:8px;
                   padding:12px 16px; font-size:14px; color:#b45309; margin:24px 0;
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
            Hola,<br><br>
            Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en Armonihz. Puedes hacerlo haciendo clic en el siguiente botón:
        </p>

        <div class="btn-wrap">
            <a href="{{ $resetLink }}" class="btn">
                Restablecer mi contraseña
            </a>
        </div>

        <div class="warning-box">
            ⚠️ &nbsp; Si no solicitaste este cambio, simplemente ignora este correo. Tu contraseña seguirá siendo la misma.
        </div>

        <p class="note">
            Si el botón superior no funciona, copia y pega el siguiente enlace en tu navegador web:
        </p>
        <div class="url-box">{{ $resetLink }}</div>
    </div>
    <div class="footer">
        © {{ date('Y') }} Armonihz · Todos los derechos reservados
    </div>
</div>
</body>
</html>