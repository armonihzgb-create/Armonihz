<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Verificación - Armonihz</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f4ff; padding: 40px 16px; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 0 auto; }
        .card { background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.06); }
        .header { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); padding: 48px 40px; text-align: center; }
        .header img { height: 48px; filter: brightness(0) invert(1); margin-bottom: 24px; }
        .badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 8px 16px; border-radius: 50px; font-size: 13px; font-weight: 700; border: 1px solid rgba(239, 68, 68, 0.3); }
        .body { padding: 48px 40px; }
        .icon-circle { width: 80px; height: 80px; background: #fff1f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 32px; font-size: 40px; }
        h1 { font-size: 28px; font-weight: 800; color: #1e1b4b; text-align: center; margin-bottom: 16px; letter-spacing: -0.5px; }
        p { font-size: 16px; line-height: 1.7; color: #475569; margin-bottom: 20px; }
        .reason-box { background: #fff1f2; border: 1px solid #fecaca; border-radius: 16px; padding: 24px; margin: 32px 0; }
        .reason-box .label { font-size: 11px; font-weight: 800; color: #be123c; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: block; }
        .reason-box .reason-text { font-size: 15px; color: #991b1b; font-weight: 600; line-height: 1.6; font-style: italic; }
        .steps-box { background: #f8faff; border: 1px solid #e0e7ff; border-radius: 16px; padding: 24px; margin: 24px 0; }
        .steps-box h4 { font-size: 15px; font-weight: 700; color: #1e1b4b; margin-bottom: 16px; }
        .steps-box ol { padding-left: 20px; }
        .steps-box ol li { font-size: 14px; color: #475569; margin-bottom: 10px; line-height: 1.6; }
        .cta-btn { display: block; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white !important; text-decoration: none; text-align: center; padding: 18px 32px; border-radius: 16px; font-size: 16px; font-weight: 700; margin: 32px 0 24px; letter-spacing: 0.3px; }
        .footer { padding: 24px 40px; background: #f8faff; text-align: center; border-top: 1px solid #e0e7ff; }
        .footer p { font-size: 13px; color: #94a3b8; margin: 0; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            {{-- Header --}}
            <div class="header">
                <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
                <div class="badge">
                    ⚠️ &nbsp; Revisión Requerida
                </div>
            </div>

            {{-- Body --}}
            <div class="body">
                <div class="icon-circle">📋</div>

                <h1>Hola, {{ $stageName }}</h1>

                <p>
                    Hemos revisado el documento de identidad que enviaste y, lamentablemente,
                    no ha podido ser verificado en esta ocasión. No te preocupes, puedes
                    volver a intentarlo siguiendo las instrucciones a continuación.
                </p>

                {{-- Motivo del rechazo --}}
                <div class="reason-box">
                    <span class="label">Motivo indicado por el equipo de revisión:</span>
                    <p class="reason-text">"{{ $rejectionReason }}"</p>
                </div>

                {{-- Pasos para corregir --}}
                <div class="steps-box">
                    <h4>¿Qué puedo hacer ahora?</h4>
                    <ol>
                        <li>Toma una foto nueva de tu documento asegurándote de que sea legible, bien iluminada y sin recortes.</li>
                        <li>Asegúrate de que el archivo pese menos de 5 MB y sea formato JPG, PNG o PDF.</li>
                        <li>Entra a tu panel de validación y sube el nuevo documento.</li>
                        <li>Nuestro equipo lo revisará nuevamente en menos de 12 horas.</li>
                    </ol>
                </div>

                <a href="{{ $retryUrl }}" class="cta-btn">
                    Subir nuevo documento &rarr;
                </a>

                <p style="font-size: 14px; color: #94a3b8; text-align: center; margin: 0;">
                    Si crees que esto es un error, contáctanos respondiendo a este correo.
                </p>
            </div>

            {{-- Footer --}}
            <div class="footer">
                <p>
                    &copy; {{ date('Y') }} <strong>Armonihz</strong> &bull; Trust &amp; Safety Team<br>
                    Recibes este correo porque tu cuenta está registrada en nuestra plataforma.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
