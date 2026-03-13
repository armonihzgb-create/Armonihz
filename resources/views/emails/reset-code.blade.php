<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de recuperación - Armonihz</title>
    <style>
        body { margin:0; padding:0; background:#f4f7fc; font-family:'Inter',Arial,sans-serif; }
        .wrapper { max-width:520px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
        .header { background:linear-gradient(135deg,#1a0b38,#6c3fc5); padding:36px 32px; text-align:center; }
        .header img { height:40px; }
        .header h1 { margin:16px 0 0; color:#fff; font-size:22px; font-weight:700; }
        .body { padding:36px 32px; }
        .greeting { font-size:15px; color:#374151; margin-bottom:20px; line-height:1.6; }
        .code-box {
            background:#f5f3ff; border:2px dashed #6c3fc5; border-radius:12px;
            text-align:center; padding:20px; margin:24px 0;
        }
        .code-box .label { font-size:12px; font-weight:700; color:#6c3fc5; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px; }
        .code-box .code { font-size:42px; font-weight:800; color:#1a0b38; letter-spacing:10px; line-height:1; }
        .note { font-size:13px; color:#6b7280; line-height:1.6; margin-top:20px; }
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
            Recibimos una solicitud para restablecer la contraseña de tu cuenta.<br>
            Usa el siguiente código para continuar. <strong>Expira en 15 minutos.</strong>
        </p>

        <div class="code-box">
            <div class="label">Tu código de verificación</div>
            <div class="code">{{ $code }}</div>
        </div>

        <p class="note">
            Ingresa este código en la página de recuperación de contraseña que tienes abierta en tu navegador.<br><br>
            Si no solicitaste este código, puedes ignorar este correo. Tu cuenta está segura.
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} Armonihz · Todos los derechos reservados
    </div>
</div>
</body>
</html>
