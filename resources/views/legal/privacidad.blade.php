<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad — Armonihz</title>
    <meta name="description" content="Conoce cómo Armonihz recolecta, usa y protege tu información personal como músico o cliente en la plataforma.">
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

        /* NAV */
        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 999; display: flex; align-items: center; justify-content: space-between; padding: 16px 6%; background: rgba(15,10,30,.95); backdrop-filter: blur(16px); border-bottom: 1px solid var(--border); }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .nav-brand img { width: 30px; border-radius: 7px; filter: brightness(0) invert(1); }
        .nav-brand span { font-size: 18px; font-weight: 800; }
        .nav-back { display: inline-flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 8px; background: rgba(255,255,255,.07); border: 1px solid var(--border); color: var(--text-muted); font-size: 14px; font-weight: 600; text-decoration: none; transition: all .2s; }
        .nav-back:hover { background: rgba(255,255,255,.12); color: var(--text); }

        /* HERO */
        .page-hero { padding: 130px 6% 60px; text-align: center; background: radial-gradient(ellipse 70% 50% at 50% 0%, rgba(108,63,197,.35) 0%, transparent 70%); border-bottom: 1px solid var(--border); }
        .page-eyebrow { display: inline-flex; align-items: center; gap: 8px; background: rgba(139,92,246,.15); border: 1px solid rgba(139,92,246,.3); color: var(--purple-light); font-size: 12px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; padding: 5px 14px; border-radius: 999px; margin-bottom: 20px; }
        .page-title { font-size: clamp(2rem, 5vw, 3rem); font-weight: 900; letter-spacing: -1px; margin-bottom: 14px; }
        .page-subtitle { font-size: 16px; color: var(--text-muted); max-width: 580px; margin: 0 auto; }
        .page-date { margin-top: 16px; font-size: 13px; color: var(--text-muted); }

        /* LAYOUT */
        .legal-layout { max-width: 860px; margin: 0 auto; padding: 60px 6% 100px; }
        .toc { background: rgba(255,255,255,.04); border: 1px solid var(--border); border-radius: 16px; padding: 28px 32px; margin-bottom: 56px; }
        .toc h3 { font-size: 14px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 16px; }
        .toc ol { list-style: decimal; padding-left: 20px; }
        .toc li { margin-bottom: 8px; }
        .toc a { color: var(--purple-light); text-decoration: none; font-size: 14px; font-weight: 500; transition: color .2s; }
        .toc a:hover { color: #fff; }

        /* SECTIONS */
        .legal-section { margin-bottom: 52px; scroll-margin-top: 100px; }
        .legal-section h2 { font-size: 1.5rem; font-weight: 800; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
        .legal-section h2 .section-icon { width: 36px; height: 36px; border-radius: 10px; background: rgba(139,92,246,.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .legal-section h2 .section-icon i { font-size: 15px; color: var(--purple-light); }
        .legal-section h3 { font-size: 1.05rem; font-weight: 700; margin: 24px 0 10px; color: var(--text); }
        .legal-section p { color: var(--text-muted); margin-bottom: 14px; font-size: 15px; }
        .legal-section ul, .legal-section ol { color: var(--text-muted); padding-left: 22px; margin-bottom: 14px; font-size: 15px; }
        .legal-section li { margin-bottom: 6px; }
        .divider { border: none; border-top: 1px solid var(--border); margin: 52px 0; }

        /* DATA TABLE */
        .data-table { width: 100%; border-collapse: collapse; margin: 16px 0 24px; font-size: 14px; }
        .data-table th { text-align: left; padding: 10px 14px; background: rgba(139,92,246,.12); color: var(--purple-light); font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid var(--border); }
        .data-table td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,.05); color: var(--text-muted); vertical-align: top; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table td strong { color: var(--text); }

        /* HIGHLIGHT BOX */
        .highlight-box { background: rgba(139,92,246,.08); border: 1px solid rgba(139,92,246,.25); border-radius: 12px; padding: 18px 22px; margin: 20px 0; font-size: 14px; color: var(--text-muted); }
        .highlight-box i { color: var(--purple-light); margin-right: 8px; }

        /* FOOTER */
        footer { padding: 36px 6%; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .footer-brand img { width: 24px; border-radius: 6px; filter: brightness(0) invert(1); }
        .footer-brand span { font-size: 15px; font-weight: 800; }
        .footer-copy { font-size: 12px; color: var(--text-muted); }
        .footer-links { display: flex; gap: 20px; flex-wrap: wrap; }
        .footer-links a { font-size: 13px; color: var(--text-muted); text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: var(--text); }
        .footer-links a.active { color: var(--purple-light); font-weight: 600; }

        @media (max-width: 768px) {
            .legal-layout { padding: 40px 5% 80px; }
            .page-hero { padding: 110px 5% 40px; }
            .toc { padding: 20px; }
            footer { flex-direction: column; text-align: center; gap: 20px; }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="nav">
        <a class="nav-brand" href="{{ route('welcome') }}">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <span>Armonihz</span>
        </a>
        <a href="{{ route('welcome') }}" class="nav-back">
            <i class="fa-solid fa-arrow-left"></i> Volver al inicio
        </a>
    </nav>

    <!-- HERO -->
    <div class="page-hero">
        <div class="page-eyebrow"><i class="fa-solid fa-shield-halved"></i> Legal</div>
        <h1 class="page-title">Política de Privacidad</h1>
        <p class="page-subtitle">Queremos que entiendas exactamente qué información recopilamos, para qué la usamos y cómo la protegemos.</p>
        <p class="page-date">Última actualización: Abril 2026</p>
    </div>

    <!-- CONTENT -->
    <div class="legal-layout">

        <!-- TABLE OF CONTENTS -->
        <div class="toc">
            <h3>Contenido</h3>
            <ol>
                <li><a href="#quienes-somos">¿Quiénes somos?</a></li>
                <li><a href="#datos-recolectados">Datos que recolectamos</a></li>
                <li><a href="#como-usamos">Cómo usamos tu información</a></li>
                <li><a href="#compartir-datos">¿Compartimos tus datos?</a></li>
                <li><a href="#almacenamiento">Almacenamiento y seguridad</a></li>
                <li><a href="#servicios-terceros">Servicios de terceros</a></li>
                <li><a href="#cookies">Cookies y sesiones</a></li>
                <li><a href="#tus-derechos">Tus derechos</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ol>
        </div>

        <!-- 1 -->
        <div class="legal-section" id="quienes-somos">
            <h2><span class="section-icon"><i class="fa-solid fa-building"></i></span> 1. ¿Quiénes somos?</h2>
            <p><strong>Armonihz</strong> es una plataforma digital que conecta músicos profesionales con clientes que buscan talento musical para sus eventos. Operamos mediante un panel web para músicos y una aplicación móvil para clientes.</p>
            <p>Al registrarte y usar la plataforma, aceptas que procesamos tus datos conforme a esta política.</p>
        </div>

        <hr class="divider">

        <!-- 2 -->
        <div class="legal-section" id="datos-recolectados">
            <h2><span class="section-icon"><i class="fa-solid fa-database"></i></span> 2. Datos que recolectamos</h2>

            <h3>2.1 Si eres un Músico (registro en el panel web)</h3>
            <table class="data-table">
                <thead><tr><th>Tipo de dato</th><th>Por qué se recolecta</th></tr></thead>
                <tbody>
                    <tr><td><strong>Nombre y correo electrónico</strong></td><td>Identificación de la cuenta y comunicación</td></tr>
                    <tr><td><strong>Contraseña (cifrada)</strong></td><td>Acceso seguro a la plataforma. Nunca se almacena en texto plano.</td></tr>
                    <tr><td><strong>Nombre artístico, biografía, ubicación</strong></td><td>Construir tu perfil público visible para los clientes</td></tr>
                    <tr><td><strong>Tarifa por hora</strong></td><td>Información de referencia para los clientes en la app</td></tr>
                    <tr><td><strong>Foto de perfil y multimedia (imágenes/videos)</strong></td><td>Presentación artística en tu perfil público</td></tr>
                    <tr><td><strong>Teléfono de contacto</strong></td><td>Comunicación directa con clientes interesados</td></tr>
                    <tr><td><strong>Redes sociales</strong> (Instagram, Facebook, YouTube, TikTok, Spotify)</td><td>Enriquecer tu perfil artístico y facilitar que los clientes te conozcan mejor</td></tr>
                    <tr><td><strong>Documento de identidad (INE, pasaporte, etc.)</strong></td><td>Verificación de identidad requerida para aparecer en la app. Se almacena en un disco privado del servidor, no accesible públicamente.</td></tr>
                    <tr><td><strong>Token FCM</strong></td><td>Envío de notificaciones push cuando recibes solicitudes o actualizaciones</td></tr>
                </tbody>
            </table>

            <h3>2.2 Si eres un Cliente (registro en la app móvil)</h3>
            <table class="data-table">
                <thead><tr><th>Tipo de dato</th><th>Por qué se recolecta</th></tr></thead>
                <tbody>
                    <tr><td><strong>Nombre, apellido, correo</strong></td><td>Identificación de la cuenta</td></tr>
                    <tr><td><strong>Teléfono</strong></td><td>Contacto asociado a tus eventos o castings publicados</td></tr>
                    <tr><td><strong>Foto de perfil</strong></td><td>Personalización de tu cuenta; puede sincronizarse desde Google</td></tr>
                    <tr><td><strong>UID de Firebase</strong></td><td>Autenticación segura mediante Google (OAuth 2.0)</td></tr>
                    <tr><td><strong>Token FCM</strong></td><td>Envío de notificaciones push sobre tus solicitudes y castings</td></tr>
                </tbody>
            </table>

            <h3>2.3 Datos generados por el uso</h3>
            <ul>
                <li>Solicitudes de contratación: fecha, lugar, presupuesto, descripción del evento, mensajes y contraofertas entre músico y cliente.</li>
                <li>Castings publicados: título, tipo de música, fecha, ubicación, presupuesto y datos de contacto del evento.</li>
                <li>Reseñas y calificaciones: comentario y puntuación que los clientes dejan sobre los músicos tras un evento.</li>
                <li>Reportes: razón por la que un cliente reporta a un músico (moderación de contenido).</li>
                <li>Contador de vistas de perfil: número de veces que tu perfil ha sido visto desde la app.</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 3 -->
        <div class="legal-section" id="como-usamos">
            <h2><span class="section-icon"><i class="fa-solid fa-gears"></i></span> 3. Cómo usamos tu información</h2>
            <ul>
                <li><strong>Mostrar tu perfil artístico</strong> a los clientes en la app móvil (solo cuando tu cuenta está verificada y aprobada).</li>
                <li><strong>Gestionar el flujo de contrataciones</strong>: notificar al músico de nuevas solicitudes, registrar aceptaciones/rechazos y contraofertas.</li>
                <li><strong>Enviar notificaciones push</strong> (Firebase Cloud Messaging) y correos electrónicos informativos sobre tu cuenta o actividad.</li>
                <li><strong>Proceso de verificación de identidad</strong>: el documento que subes es revisado únicamente por un administrador de la plataforma para confirmar tu identidad. No se comparte con terceros.</li>
                <li><strong>Moderar reportes</strong> de comportamiento inadecuado para mantener la calidad y seguridad de la comunidad.</li>
                <li><strong>Mejorar la plataforma</strong> mediante el análisis de patrones de uso (sin identificar usuarios individualmente).</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 4 -->
        <div class="legal-section" id="compartir-datos">
            <h2><span class="section-icon"><i class="fa-solid fa-share-nodes"></i></span> 4. ¿Compartimos tus datos?</h2>
            <p><strong>No vendemos, alquilamos ni cedemos tu información personal a terceros con fines comerciales.</strong></p>
            <p>Tus datos pueden ser accesibles en los siguientes contextos limitados:</p>
            <ul>
                <li><strong>Entre usuarios de la plataforma</strong>: tu perfil público (nombre artístico, bio, multimedia, géneros, tarifa, redes sociales) es visible para los clientes en la app. Tu correo, contraseña y documento de identidad nunca se comparten.</li>
                <li><strong>Firebase (Google)</strong>: usamos Firebase para la autenticación de clientes via Google y para el envío de notificaciones push. Firebase opera bajo las políticas de privacidad de Google.</li>
                <li><strong>Exigencia legal</strong>: si una autoridad competente nos lo requiere mediante un proceso legal válido, podríamos estar obligados a compartir información específica.</li>
            </ul>
        </div>

        <hr class="divider">

        <!-- 5 -->
        <div class="legal-section" id="almacenamiento">
            <h2><span class="section-icon"><i class="fa-solid fa-lock"></i></span> 5. Almacenamiento y seguridad</h2>
            <ul>
                <li>Las <strong>contraseñas se almacenan cifradas</strong> usando bcrypt. Nunca podemos ver tu contraseña en texto plano.</li>
                <li>Los <strong>documentos de identidad</strong> (INE, pasaporte, etc.) se almacenan en un directorio privado del servidor, completamente inaccesible desde el navegador sin autenticación de administrador.</li>
                <li>Las <strong>fotos y videos de tu galería</strong> se almacenan en el servidor de la plataforma y se sirven a través de rutas autenticadas.</li>
                <li>Usamos tokens de sesión seguros (Laravel Sanctum) para proteger el acceso autenticado al panel web.</li>
                <li>Implementamos protección CSRF en todos los formularios del panel web.</li>
            </ul>
            <div class="highlight-box">
                <i class="fa-solid fa-triangle-exclamation"></i> Ningún sistema es 100% seguro. Si detectas una vulnerabilidad en nuestra plataforma, por favor repórtala de inmediato a nuestro equipo.
            </div>
        </div>

        <hr class="divider">

        <!-- 6 -->
        <div class="legal-section" id="servicios-terceros">
            <h2><span class="section-icon"><i class="fa-solid fa-plug"></i></span> 6. Servicios de terceros</h2>
            <p>Armonihz integra los siguientes servicios externos que tienen sus propias políticas de privacidad:</p>
            <ul>
                <li><strong>Firebase (Google)</strong>: autenticación OAuth con Google, notificaciones push (FCM) y verificación de correo electrónico para clientes.</li>
                <li><strong>Google Fonts</strong>: cargamos la tipografía Inter desde los servidores de Google para asegurar la consistencia visual.</li>
                <li><strong>Font Awesome</strong>: íconos servidos desde CDN de Cloudflare.</li>
            </ul>
            <p>La plataforma <strong>no procesa pagos</strong>. Los acuerdos económicos entre músicos y clientes se realizan directamente y de manera externa a Armonihz.</p>
        </div>

        <hr class="divider">

        <!-- 7 -->
        <div class="legal-section" id="cookies">
            <h2><span class="section-icon"><i class="fa-solid fa-cookie-bite"></i></span> 7. Cookies y sesiones</h2>
            <p>Armonihz usa cookies esenciales para el funcionamiento del sistema:</p>
            <ul>
                <li><strong>Cookie de sesión</strong> (<code>armonihz_session</code>): necesaria para mantener tu sesión activa mientras navegas por el panel web. Se elimina al cerrar el navegador o al cerrar sesión.</li>
                <li><strong>Cookie CSRF</strong> (<code>XSRF-TOKEN</code>): protege los formularios del panel web contra ataques de falsificación de solicitudes. Es estrictamente técnica.</li>
            </ul>
            <p>No usamos cookies de seguimiento, analíticas ni de publicidad de terceros. No implementamos tecnologías de seguimiento entre sitios.</p>
        </div>

        <hr class="divider">

        <!-- 8 -->
        <div class="legal-section" id="tus-derechos">
            <h2><span class="section-icon"><i class="fa-solid fa-user-shield"></i></span> 8. Tus derechos</h2>
            <p>Tienes derecho a:</p>
            <ul>
                <li><strong>Acceder</strong> a los datos que tenemos sobre ti en cualquier momento desde tu perfil.</li>
                <li><strong>Corregir</strong> información incorrecta actualizando tu perfil desde el panel web.</li>
                <li><strong>Eliminar tu cuenta</strong>: puedes solicitar la eliminación de tu cuenta y todos sus datos asociados. La app móvil incluye una opción directa para ello.</li>
                <li><strong>Retirar el consentimiento</strong> para el uso de tus datos en cualquier momento, sabiendo que esto puede implicar la desactivación de ciertas funciones.</li>
            </ul>
            <div class="highlight-box">
                <i class="fa-solid fa-info-circle"></i> Los datos asociados a solicitudes de contratación completadas pueden conservarse por razones de integridad del historial durante un período razonable.
            </div>
        </div>

        <hr class="divider">

        <!-- 9 -->
        <div class="legal-section" id="contacto">
            <h2><span class="section-icon"><i class="fa-solid fa-envelope"></i></span> 9. Contacto</h2>
            <p>Si tienes preguntas sobre este aviso de privacidad, deseas ejercer tus derechos o reportar un problema de seguridad, puedes contactarnos a través de la sección de Ayuda de la plataforma o directamente con el equipo de Armonihz.</p>
            <p>Nos comprometemos a responder en un plazo máximo de 5 días hábiles.</p>
        </div>

    </div>

    <!-- FOOTER -->
    <footer>
        <a class="footer-brand" href="{{ route('welcome') }}">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <span>Armonihz</span>
        </a>
        <p class="footer-copy">© 2026 Armonihz. Todos los derechos reservados.</p>
        <div class="footer-links">
            <a href="{{ route('legal.privacidad') }}" class="active">Privacidad</a>
            <a href="{{ route('legal.terminos') }}">Términos</a>
            <a href="{{ route('legal.ayuda') }}">Ayuda</a>
        </div>
    </footer>
</body>
</html>
