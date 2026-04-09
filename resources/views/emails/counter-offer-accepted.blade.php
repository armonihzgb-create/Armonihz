<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contraoferta Aceptada - Armonihz</title>
    <style>
        body { margin:0; padding:0; background:#f4f7fc; font-family:'Inter',Arial,sans-serif; }
        .wrapper { max-width:520px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
        .header { background:linear-gradient(135deg,#1a0b38,#6c3fc5); padding:36px 32px; text-align:center; }
        .header h1 { margin:0; color:#fff; font-size:24px; font-weight:700; letter-spacing: 0.5px; }
        .body { padding:36px 32px; }
        .greeting { font-size:16px; color:#1f2937; margin-bottom:24px; line-height:1.6; }
        .btn-wrap { text-align:center; margin:32px 0; }
        .btn {
            display:inline-block; background:linear-gradient(135deg,#16a34a,#22c55e);
            color:#fff; font-size:16px; font-weight:700; padding:16px 40px;
            border-radius:12px; text-decoration:none; letter-spacing:.3px;
            box-shadow:0 4px 16px rgba(22,163,74,.35);
        }
        .info-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px;
                    padding:16px; font-size:15px; color:#15803d; margin:24px 0;
                    text-align: center; font-weight: 600; }
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
            ¡Excelentes noticias! El cliente <strong>{{ $clientName }}</strong> ha <strong>aceptado la contraoferta</strong> que propusiste para el evento del día <strong>{{ \Carbon\Carbon::parse($eventDate)->format('d/m/Y') }}</strong>.
        </p>

        <div class="info-box">
            ✅ Evento confirmado por: ${{ number_format($agreedPrice, 2) }} MXN
        </div>

        <p style="font-size:15px; color:#374151; line-height:1.6;">
            Este evento ya está agendado oficialmente en tu calendario. Asegúrate de prepararte para dar el mejor espectáculo.
        </p>

        <div class="btn-wrap">
            <a href="https://armonihz-web-armonihz.lugsb1.easypanel.host/requests" class="btn">
                Ver detalles del evento
            </a>
        </div>
    </div>
    <div class="footer">
        © {{ date('Y') }} Armonihz · Todos los derechos reservados
    </div>
</div>
</body>
</html>