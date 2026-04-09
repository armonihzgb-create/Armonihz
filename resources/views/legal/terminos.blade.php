<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones — Armonihz</title>
    <meta name="description" content="Lee los términos y condiciones de uso de la plataforma Armonihz para músicos y clientes.">
    <link rel="icon" type="image/png" href="{{ asset('images/Armonihz_logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --purple: #6c3fc5; --purple-light: #8b5cf6; --dark: #09090b;
            --dark-2: #18181b; --text: #f8f6ff; --text-muted: rgba(248,246,255,.62);
            --border: rgba(255,255,255,.1);
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--dark); color: var(--text); overflow-x: hidden; line-height: 1.7; }
        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 999; display: flex; align-items: center; justify-content: space-between; padding: 16px 6%; background: rgba(15,10,30,.95); backdrop-filter: blur(16px); border-bottom: 1px solid var(--border); }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .nav-brand img { width: 30px; border-radius: 7px; filter: brightness(0) invert(1); }
        .nav-brand span { font-size: 18px; font-weight: 800; }
        .nav-back { display: inline-flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 8px; background: rgba(255,255,255,.07); border: 1px solid var(--border); color: var(--text-muted); font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s; }
        .nav-back:hover { background: rgba(255,255,255,.12); color: var(--text); }
        .page-hero { padding: 130px 6% 60px; text-align: center; background: radial-gradient(ellipse 70% 50% at 50% 0%, rgba(108,63,197,.35) 0%, transparent 70%); border-bottom: 1px solid var(--border); }
        .page-eyebrow { display: inline-flex; align-items: center; gap: 8px; background: rgba(139,92,246,.15); border: 1px solid rgba(139,92,246,.3); color: var(--purple-light); font-size: 12px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; padding: 5px 14px; border-radius: 999px; margin-bottom: 20px; }
        .page-title { font-size: clamp(2rem, 5vw, 3rem); font-weight: 900; letter-spacing: -1px; margin-bottom: 14px; }
        .page-subtitle { font-size: 16px; color: var(--text-muted); max-width: 580px; margin: 0 auto; }
        .page-date { margin-top: 16px; font-size: 13px; color: var(--text-muted); }
        .legal-layout { max-width: 860px; margin: 0 auto; padding: 60px 6% 100px; }
        .toc { background: rgba(255,255,255,.04); border: 1px solid var(--border); border-radius: 16px; padding: 28px 32px; margin-bottom: 56px; }
        .toc h3 { font-size: 14px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 16px; }
        .toc ol { list-style: decimal; padding-left: 20px; }
        .toc li { margin-bottom: 8px; }
        .toc a { color: var(--purple-light); text-decoration: none; font-size: 14px; font-weight: 500; transition: color .2s; }
        .toc a:hover { color: #fff; }
        .legal-section { margin-bottom: 52px; scroll-margin-top: 100px; }
        .legal-section h2 { font-size: 1.5rem; font-weight: 800; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
        .legal-section h2 .section-icon { width: 36px; height: 36px; border-radius: 10px; background: rgba(139,92,246,.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .legal-section h2 .section-icon i { font-size: 15px; color: var(--purple-light); }
        .legal-section h3 { font-size: 1.05rem; font-weight: 700; margin: 24px 0 10px; color: var(--text); }
        .legal-section p { color: var(--text-muted); margin-bottom: 14px; font-size: 15px; }
        .legal-section ul, .legal-section ol { color: var(--text-muted); padding-left: 22px; margin-bottom: 14px; font-size: 15px; }
        .legal-section li { margin-bottom: 6px; }
        .divider { border: none; border-top: 1px solid var(--border); margin: 52px 0; }
        .highlight-box { background: rgba(139,92,246,.08); border: 1px solid rgba(139,92,246,.25); border-radius: 12px; padding: 18px 22px; margin: 20px 0; font-size: 14px; color: var(--text-muted); }
        .highlight-box i { color: var(--purple-light); margin-right: 8px; }
        .warning-box { background: rgba(239,68,68,.08); border: 1px solid rgba(239,68,68,.25); border-radius: 12px; padding: 18px 22px; margin: 20px 0; font-size: 14px; color: var(--text-muted); }
        .warning-box i { color: #ef4444; margin-right: 8px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; margin-left: 6px; }
        .badge-pending { background: rgba(245,158,11,.15); color: #f59e0b; }
        .badge-accepted { background: rgba(34,197,94,.15); color: #22c55e; }
        .badge-rejected { background: rgba(239,68,68,.15); color: #ef4444; }
        footer { padding: 36px 6%; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .footer-brand img { width: 24px; border-radius: 6px; filter: brightness(0) invert(1); }
        .footer-brand span { font-size: 15px; font-weight: 800; }
        .footer-copy { font-size: 12px; color: var(--text-muted); }
        .footer-links { display: flex; gap: 20px; flex-wrap: wrap; }
        .footer-links a { font-size: 13px; color: var(--text-muted); text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: var(--text); }
        .footer-links a.active { color: var(--purple-light); font-weight: 600; }
        @media (max-width: 768px) { .legal-layout { padding: 40px 5% 80px; } .page-hero { padding: 110px 5% 40px; } .toc { padding: 20px; } footer { flex-direction: column; text-align: center; gap: 20px; } }
    </style>
</head>
<body>
    <nav class="nav">
        <a class="nav-brand" href="{{ route('welcome') }}">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <span>Armonihz</span>
        </a>
        <a href="{{ route('welcome') }}" class="nav-back">
            <i class="fa-solid fa-arrow-left"></i> Volver al inicio
        </a>
    </nav>

    <div class="page-hero">
        <div class="page-eyebrow"><i class="fa-solid fa-file-contract"></i> Legal</div>
        <h1 class="page-title">Términos y Condiciones</h1>
        <p class="page-subtitle">Reglas claras para músicos y clientes que garantizan una comunidad de confianza y profesionalismo.</p>
        <p class="page-date">Última actualización: Abril 2026</p>
    </div>

    <div class="legal-layout">

        <div class="toc">
            <h3>Contenido</h3>
            <ol>
                <li><a href="#aceptacion">Aceptación de los términos</a></li>
                <li><a href="#descripcion">Descripción del servicio</a></li>
                <li><a href="#musicos">Reglas para músicos</a></li>
                <li><a href="#clientes">Reglas para clientes</a></li>
                <li><a href="#contrataciones">Proceso de contratación</a></li>
                <li><a href="#castings">Sistema de castings</a></li>
                <li><a href="#pagos">Pagos y tarifas</a></li>
                <li><a href="#cancelaciones">Cancelaciones</a></li>
                <li><a href="#resenas">Reseñas y reputación</a></li>
                <li><a href="#uso-indebido">Uso indebido y suspensiones</a></li>
                <li><a href="#responsabilidades">Limitación de responsabilidad</a></li>
                <li><a href="#cambios">Cambios a estos términos</a></li>
            </ol>
        </div>

        <!-- 1 -->
        <div class="legal-section" id="aceptacion">
            <h2><span class="section-icon"><i class="fa-solid fa-handshake"></i></span> 1. Aceptación de los términos</h2>
            <p>Al crear una cuenta en Armonihz —ya sea como músico en el panel web o como cliente en la app móvil— aceptas estos Términos y Condiciones en su totalidad. Si no estás de acuerdo con alguna parte, debes abstenerte de usar la plataforma.</p>
            <p>Estos términos aplican tanto al panel web de músicos (<strong>armonihz.com</strong>) como a la aplicación móvil de clientes.</p>
        </div>

        <hr class="divider">

        <!-- 2 -->
        <div class="legal-section" id="descripcion">
            <h2><span class="section-icon"><i class="fa-solid fa-circle-info"></i></span> 2. Descripción del servicio</h2>
            <p>Armonihz es un <strong>marketplace de talento musical</strong>. Nuestra plataforma facilita la conexión entre:</p>
            <ul>
                <li><strong>Músicos</strong>: profesionales que crean perfiles artísticos y gestionan su disponibilidad y solicitudes de contratación.</li>
                <li><strong>Clientes</strong>: personas o empresas que buscan músicos para sus eventos mediante la app móvil.</li>
            </ul>
            <p>Armonihz actúa como un intermediario digital. <strong>No somos empleadores de los músicos ni organizadores de eventos</strong>. Los acuerdos se establecen directamente entre músico y cliente.</p>
        </div>

        <hr class="divider">

        <!-- 3 -->
        <div class="legal-section" id="musicos">
            <h2><span class="section-icon"><i class="fa-solid fa-guitar"></i></span> 3. Reglas para músicos</h2>
            <h3>3.1 Registro y perfil</h3>
            <ul>
                <li>La información de tu perfil debe ser <strong>verídica y actual</strong>. Está prohibido publicar información falsa, engañosa o que no corresponda a tu práctica musical real.</li>
                <li>Las fotos y videos que subas deben ser de tu autoría o que tengas los derechos de uso. No puedes subir contenido de terceros sin permiso.</li>
                <li>Tu perfil solo será <strong>visible para los clientes en la app móvil</strong> una vez que hayas subido tu documento de identidad y un administrador lo haya aprobado.</li>
            </ul>

            <h3>3.2 Verificación de identidad</h3>
            <ul>
                <li>Debes subir un documento de identidad oficial y vigente (INE, pasaporte, etc.) para ser verificado.</li>
                <li>El equipo de Armonihz revisará el documento y te notificará por correo si fue aprobado o rechazado, incluyendo el motivo en caso de rechazo.</li>
                <li>La suplantación de identidad resultará en la suspensión permanente de la cuenta.</li>
            </ul>

            <h3>3.3 Compromisos profesionales</h3>
            <ul>
                <li>Al aceptar una solicitud de contratación, adquieres un <strong>compromiso profesional</strong> con el cliente. Se espera que cumplas con la fecha, horario y lugar acordados.</li>
                <li>Si necesitas cancelar un evento ya confirmado, debes notificar al cliente con la mayor anticipación posible a través de los canales de comunicación acordados.</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 4 -->
        <div class="legal-section" id="clientes">
            <h2><span class="section-icon"><i class="fa-solid fa-users"></i></span> 4. Reglas para clientes</h2>
            <ul>
                <li>Al enviar una solicitud de contratación, debes proporcionar información <strong>real y precisa</strong> sobre el evento (fecha, lugar, presupuesto).</li>
                <li>No puedes usar la información de los músicos (teléfono, redes sociales) para fines distintos a la contratación musical.</li>
                <li>Al publicar un casting, te comprometes a que la convocatoria sea real y a notificar a los músicos que aplicaron sobre el resultado.</li>
                <li>Está prohibido establecer contacto con músicos para servicios que violen la ley o las normas de esta plataforma.</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 5 -->
        <div class="legal-section" id="contrataciones">
            <h2><span class="section-icon"><i class="fa-solid fa-file-signature"></i></span> 5. Proceso de contratación directa</h2>
            <p>El flujo de una solicitud de contratación en Armonihz funciona así:</p>
            <ol>
                <li>El cliente envía una solicitud al músico con fecha, lugar, horario y presupuesto propuesto. <span class="status-badge badge-pending">pendiente</span></li>
                <li>El músico puede <strong>aceptar</strong> la solicitud, <strong>rechazarla</strong>, o bien proponer una <strong>contraoferta</strong> con un precio diferente. <span class="status-badge badge-pending">pendiente</span></li>
                <li>Si hay contraoferta, el cliente puede <strong>aceptarla o rechazarla</strong>. Al aceptar la contraoferta, el presupuesto oficial pasa a ser el monto negociado. <span class="status-badge badge-accepted">aceptada</span></li>
                <li>Una vez que el evento se realiza, el cliente puede marcar la solicitud como completada y dejar una reseña. <span class="status-badge badge-accepted">completada</span></li>
            </ol>
            <div class="highlight-box">
                <i class="fa-solid fa-circle-check"></i> El sistema valida automáticamente la disponibilidad del músico. Si ya tiene una contratación aceptada en ese horario, no se podrá enviar una nueva solicitud para ese mismo período.
            </div>
        </div>

        <hr class="divider">

        <!-- 6 -->
        <div class="legal-section" id="castings">
            <h2><span class="section-icon"><i class="fa-solid fa-microphone-lines"></i></span> 6. Sistema de castings</h2>
            <p>Los castings son convocatorias abiertas que los clientes publican y a las que los músicos pueden aplicar:</p>
            <ol>
                <li>El cliente publica un casting con los detalles del evento y un presupuesto de referencia.</li>
                <li>Los músicos aplican indicando un precio propuesto y un mensaje.</li>
                <li>El cliente revisa las aplicaciones y selecciona al músico que prefiera.</li>
                <li>Se notifica a todos los músicos que aplicaron sobre el resultado de su candidatura.</li>
            </ol>
            <p>Los músicos solo pueden aplicar a castings si su perfil está verificado y aprobado por un administrador.</p>
        </div>

        <hr class="divider">

        <!-- 7 -->
        <div class="legal-section" id="pagos">
            <h2><span class="section-icon"><i class="fa-solid fa-money-bill-wave"></i></span> 7. Pagos y tarifas</h2>
            <div class="warning-box">
                <i class="fa-solid fa-triangle-exclamation"></i> <strong>Importante:</strong> Armonihz <strong>NO procesa pagos</strong>. La plataforma es un facilitador de conexión, no un intermediario financiero.
            </div>
            <ul>
                <li>Los acuerdos económicos entre músico y cliente se establecen a través de los campos de presupuesto y contraoferta dentro de la plataforma, pero el <strong>pago en sí se realiza directamente entre las partes</strong>, fuera de Armonihz.</li>
                <li>Armonihz no cobra comisiones sobre las contrataciones en este momento.</li>
                <li>El uso básico de la plataforma para músicos es <strong>completamente gratuito</strong>: registrarse, crear perfil, subir multimedia, aplicar a castings y recibir solicitudes no tiene costo.</li>
                <li>Las funciones de <strong>promoción de perfil</strong> pueden tener costo según las condiciones que se indiquen al momento de activarlas.</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 8 -->
        <div class="legal-section" id="cancelaciones">
            <h2><span class="section-icon"><i class="fa-solid fa-ban"></i></span> 8. Cancelaciones</h2>
            <p>Armonihz no gestiona reembolsos ya que no procesa pagos. Sin embargo, los siguientes lineamientos aplican para el uso de la plataforma:</p>
            <ul>
                <li>Un músico puede <strong>rechazar</strong> una solicitud si aún está en estado <em>pendiente</em>. Una vez aceptada, el rechazo implica una ruptura del acuerdo profesional.</li>
                <li>Si un músico cancela un evento ya confirmado, esto puede reflejarse negativamente en su historial y estar sujeto a no recibir reseñas positivas.</li>
                <li>Los clientes pueden cancelar eventos aún no aceptados sin consecuencias dentro de la plataforma.</li>
                <li>Para castings, el cliente puede cerrar la convocatoria en cualquier momento antes de seleccionar un músico.</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 9 -->
        <div class="legal-section" id="resenas">
            <h2><span class="section-icon"><i class="fa-solid fa-star"></i></span> 9. Reseñas y reputación</h2>
            <ul>
                <li>Solo los clientes que tuvieron una contratación o casting completado con un músico pueden dejar una reseña.</li>
                <li>Las reseñas deben ser <strong>honestas e imparciales</strong>. Está prohibido publicar reseñas falsas o difamatorias.</li>
                <li>Los músicos pueden <strong>responder</strong> a las reseñas de manera profesional.</li>
                <li>El equipo de Armonihz se reserva el derecho de eliminar reseñas que incumplan estas reglas o que sean claramente fraudulentas.</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 10 -->
        <div class="legal-section" id="uso-indebido">
            <h2><span class="section-icon"><i class="fa-solid fa-shield-halved"></i></span> 10. Uso indebido y suspensiones</h2>
            <p>Las siguientes conductas están estrictamente prohibidas y pueden resultar en la suspensión temporal o permanente de la cuenta:</p>
            <ul>
                <li>Crear perfiles falsos o hacerse pasar por otra persona.</li>
                <li>Usar datos de otros usuarios con fines distintos a los acordados en la plataforma.</li>
                <li>Publicar contenido inapropiado, ofensivo, ilegal o que infrinja derechos de propiedad intelectual.</li>
                <li>Intentar bypassear el sistema de verificación de identidad.</li>
                <li>Enviar solicitudes de contratación o castings con información fraudulenta.</li>
                <li>Acosar, amenazar o defraudar a otros usuarios.</li>
            </ul>
            <p>Los usuarios pueden reportar comportamientos indebidos directamente desde el perfil del músico en la app. El equipo de Armonihz revisará cada reporte y tomará las medidas necesarias.</p>
            <div class="warning-box">
                <i class="fa-solid fa-ban"></i> Una cuenta suspendida (<code>is_active = false</code>) no puede iniciar sesión ni acceder a ninguna función de la plataforma.
            </div>
        </div>

        <hr class="divider">

        <!-- 11 -->
        <div class="legal-section" id="responsabilidades">
            <h2><span class="section-icon"><i class="fa-solid fa-scale-balanced"></i></span> 11. Limitación de responsabilidad</h2>
            <ul>
                <li>Armonihz facilita la conexión entre músicos y clientes, pero <strong>no garantiza</strong> la calidad, puntualidad o cumplimiento de los acuerdos establecidos entre las partes.</li>
                <li>No somos responsables por los pagos realizados directamente entre músicos y clientes fuera de la plataforma.</li>
                <li>No somos responsables por pérdidas o daños derivados del incumplimiento de contratos entre músicos y clientes.</li>
                <li>Hacemos nuestro mejor esfuerzo para mantener la plataforma disponible, pero no garantizamos disponibilidad del 100% del tiempo.</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 12 -->
        <div class="legal-section" id="cambios">
            <h2><span class="section-icon"><i class="fa-solid fa-rotate"></i></span> 12. Cambios a estos términos</h2>
            <p>Armonihz puede actualizar estos términos en cualquier momento. Cuando haya cambios relevantes, lo notificaremos mediante un correo electrónico o un aviso visible dentro de la plataforma.</p>
            <p>El uso continuado de la plataforma después de la notificación implica la aceptación de los nuevos términos.</p>
        </div>

    </div>

    <footer>
        <a class="footer-brand" href="{{ route('welcome') }}">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <span>Armonihz</span>
        </a>
        <p class="footer-copy">© 2026 Armonihz. Todos los derechos reservados.</p>
        <div class="footer-links">
            <a href="{{ route('legal.privacidad') }}">Privacidad</a>
            <a href="{{ route('legal.terminos') }}" class="active">Términos</a>
            <a href="{{ route('legal.ayuda') }}">Ayuda</a>
        </div>
    </footer>
</body>
</html>
