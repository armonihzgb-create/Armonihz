<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo - Armonihz</title>
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
        .expiry { background:#fef3c7; border:1px solid #fde68a; border-radius:8px;
                  padding:10px 16px; font-size:13px; color:#92400e; margin:20px 0;
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
            Hola <strong>{{ $user->name }}</strong>,<br><br>
            ¡Gracias por registrarte en Armonihz! Para activar tu cuenta y comenzar a conectar con clientes, necesitas verificar tu dirección de correo electrónico.
        </p>

        <div class="btn-wrap">
            <a href="{{ $verificationUrl }}" class="btn">
                ✅ &nbsp; Verificar mi correo electrónico
            </a>
        </div>

        <div class="expiry">
            ⏱️ &nbsp; Este enlace expira en <strong>60 minutos</strong>.
        </div>

        <p class="note">
            Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:
        </p>
        <div class="url-box">{{ $verificationUrl }}</div>

        <p class="note" style="margin-top:24px;">
            Si no creaste esta cuenta, puedes ignorar este correo con seguridad.
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} Armonihz · Todos los derechos reservados
    </div>
</div>
</body>
</html>
