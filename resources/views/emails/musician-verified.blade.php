<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Perfil Verificado! - Armonihz</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f4ff; padding: 40px 16px; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 0 auto; }
        .card { background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.06); }
        .header { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); padding: 48px 40px; text-align: center; }
        .header img { height: 48px; filter: brightness(0) invert(1); margin-bottom: 24px; }
        .badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); color: white; padding: 8px 16px; border-radius: 50px; font-size: 13px; font-weight: 700; border: 1px solid rgba(255,255,255,0.2); }
        .body { padding: 48px 40px; }
        .icon-circle { width: 80px; height: 80px; background: #f0fdf4; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 32px; font-size: 40px; }
        h1 { font-size: 28px; font-weight: 800; color: #1e1b4b; text-align: center; margin-bottom: 16px; letter-spacing: -0.5px; }
        p { font-size: 16px; line-height: 1.7; color: #475569; margin-bottom: 20px; }
        .highlight-box { background: #f8faff; border: 1px solid #e0e7ff; border-radius: 16px; padding: 24px; margin: 32px 0; }
        .highlight-box ul { list-style: none; padding: 0; }
        .highlight-box ul li { padding: 10px 0; border-bottom: 1px solid #e0e7ff; font-size: 15px; color: #334155; display: flex; align-items: center; gap: 12px; }
        .highlight-box ul li:last-child { border-bottom: none; }
        .checkmark { color: #10b981; font-weight: 900; font-size: 18px; }
        .cta-btn { display: block; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white !important; text-decoration: none; text-align: center; padding: 18px 32px; border-radius: 16px; font-size: 16px; font-weight: 700; margin: 32px 0 24px; letter-spacing: 0.3px; }
        .footer { padding: 24px 40px; background: #f8faff; text-align: center; border-top: 1px solid #e0e7ff; }
        .footer p { font-size: 13px; color: #94a3b8; margin: 0; line-height: 1.6; }
        .footer a { color: #6366f1; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            {{-- Header --}}
            <div class="header">
                <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
                <div class="badge">
                    ✅ &nbsp; Identidad Verificada
                </div>
            </div>

            {{-- Body --}}
            <div class="body">
                <div class="icon-circle">🎵</div>

                <h1>¡Felicidades, {{ $stageName }}!</h1>

                <p>
                    Tu identidad ha sido verificada exitosamente por nuestro equipo de seguridad.
                    A partir de ahora, tu perfil cuenta con la insignia de verificación que genera
                    confianza en los clientes y te dará visibilidad prioritaria en Armonihz.
                </p>

                <div class="highlight-box">
                    <ul>
                        <li><span class="checkmark">✓</span> Acceso completo para postularte a castings</li>
                        <li><span class="checkmark">✓</span> Insignia de "Músico Verificado" en tu perfil</li>
                        <li><span class="checkmark">✓</span> Mayor visibilidad ante clientes potenciales</li>
                        <li><span class="checkmark">✓</span> Acceso a solicitudes de contratación directa</li>
                    </ul>
                </div>

                <p>¿Listo para comenzar? Entra a tu dashboard y actualiza tu perfil para destacar.</p>

                <a href="{{ $dashboardUrl }}" class="cta-btn">
                    Ir a mi Dashboard &rarr;
                </a>

                <p style="font-size: 14px; color: #94a3b8; text-align: center; margin: 0;">
                    Si tienes alguna duda, contáctanos respondiendo a este correo.
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
