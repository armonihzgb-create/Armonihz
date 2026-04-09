<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ayuda — Armonihz</title>
    <meta name="description" content="Resuelve tus dudas sobre Armonihz: cómo crear tu perfil, cómo contratar músicos, castings, verificación y más.">
    <link rel="icon" type="image/png" href="{{ asset('images/Armonihz_logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --purple: #6c3fc5; --purple-light: #8b5cf6; --dark: #09090b; --dark-2: #18181b; --text: #f8f6ff; --text-muted: rgba(248,246,255,.62); --border: rgba(255,255,255,.1); }
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

        /* LAYOUT */
        .help-layout { max-width: 900px; margin: 0 auto; padding: 60px 6% 100px; }

        /* CATEGORY TABS */
        .category-tabs { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 48px; }
        .cat-tab { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; background: rgba(255,255,255,.05); border: 1px solid var(--border); color: var(--text-muted); font-size: 14px; font-weight: 600; cursor: pointer; transition: all .25s; text-decoration: none; }
        .cat-tab:hover, .cat-tab.active { background: rgba(139,92,246,.15); border-color: rgba(139,92,246,.4); color: var(--purple-light); }
        .cat-tab i { font-size: 13px; }

        /* SECTION HEADER */
        .help-section { margin-bottom: 56px; scroll-margin-top: 100px; }
        .section-header { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border); }
        .section-header-icon { width: 44px; height: 44px; border-radius: 12px; background: rgba(139,92,246,.15); border: 1px solid rgba(139,92,246,.25); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .section-header-icon i { font-size: 18px; color: var(--purple-light); }
        .section-header h2 { font-size: 1.4rem; font-weight: 800; }
        .section-header p { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        /* FAQ ACCORDION */
        .faq-item { background: rgba(255,255,255,.04); backdrop-filter: blur(12px); border: 1px solid var(--border); border-radius: 14px; margin-bottom: 10px; overflow: hidden; transition: all .3s; }
        .faq-item:hover { border-color: rgba(108,63,197,.35); background: rgba(255,255,255,.06); }
        .faq-item.active { border-color: rgba(139,92,246,.4); background: rgba(139,92,246,.06); }
        .faq-question { padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; color: var(--text); font-weight: 600; font-size: 15px; cursor: pointer; gap: 16px; }
        .faq-question span { flex: 1; }
        .faq-answer { padding: 0 22px 18px; color: var(--text-muted); font-size: 14px; line-height: 1.7; display: none; }
        .faq-answer ul, .faq-answer ol { padding-left: 20px; margin-top: 10px; }
        .faq-answer li { margin-bottom: 6px; }
        .faq-answer strong { color: var(--text); }
        .faq-answer .step-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(139,92,246,.15); border: 1px solid rgba(139,92,246,.25); border-radius: 6px; padding: 2px 10px; font-size: 12px; font-weight: 700; color: var(--purple-light); margin-right: 6px; }
        .faq-item.active .faq-answer { display: block; }
        .faq-icon { transition: transform .3s; color: var(--purple-light); flex-shrink: 0; }
        .faq-item.active .faq-icon { transform: rotate(45deg); }

        /* CONTACT CTA */
        .contact-cta { background: linear-gradient(135deg, rgba(108,63,197,.2), rgba(47,147,245,.1)); border: 1px solid rgba(139,92,246,.3); border-radius: 20px; padding: 40px; text-align: center; margin-top: 56px; }
        .contact-cta h3 { font-size: 1.4rem; font-weight: 800; margin-bottom: 10px; }
        .contact-cta p { color: var(--text-muted); margin-bottom: 24px; font-size: 15px; }
        .contact-cta a { display: inline-flex; align-items: center; gap: 9px; padding: 12px 28px; border-radius: 10px; background: var(--purple); color: #fff; font-size: 15px; font-weight: 700; text-decoration: none; transition: all .25s; }
        .contact-cta a:hover { background: var(--purple-light); transform: translateY(-2px); }

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

        @media (max-width: 768px) { .help-layout { padding: 40px 5% 80px; } .page-hero { padding: 110px 5% 40px; } footer { flex-direction: column; text-align: center; gap: 20px; } .contact-cta { padding: 28px 22px; } }
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
        <div class="page-eyebrow"><i class="fa-solid fa-circle-question"></i> Soporte</div>
        <h1 class="page-title">Centro de Ayuda</h1>
        <p class="page-subtitle">Encuentra respuestas a las preguntas más frecuentes sobre cómo funciona Armonihz.</p>
    </div>

    <div class="help-layout">

        <!-- TABS -->
        <div class="category-tabs">
            <a href="#musicos" class="cat-tab"><i class="fa-solid fa-guitar"></i> Para músicos</a>
            <a href="#clientes" class="cat-tab"><i class="fa-solid fa-users"></i> Para clientes</a>
            <a href="#contrataciones" class="cat-tab"><i class="fa-solid fa-file-signature"></i> Contrataciones</a>
            <a href="#castings" class="cat-tab"><i class="fa-solid fa-microphone-lines"></i> Castings</a>
            <a href="#cuenta" class="cat-tab"><i class="fa-solid fa-user-gear"></i> Mi cuenta</a>
            <a href="#app" class="cat-tab"><i class="fa-solid fa-mobile-alt"></i> App Móvil</a>
        </div>

        <!-- MÚSICOS -->
        <div class="help-section" id="musicos">
            <div class="section-header">
                <div class="section-header-icon"><i class="fa-solid fa-guitar"></i></div>
                <div>
                    <h2>Para músicos</h2>
                    <p>Cómo crear tu perfil, verificarte y gestionar tu presencia</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo creo mi perfil en Armonihz?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>El proceso es sencillo:</p>
                    <ol>
                        <li><strong>Regístrate</strong> en el panel web con tu correo y una contraseña.</li>
                        <li><strong>Verifica tu correo</strong> a través del enlace que te enviamos.</li>
                        <li>Entra a tu <strong>perfil</strong> y completa tu información: nombre artístico, biografía, ubicación, tarifa por hora y géneros musicales.</li>
                        <li><strong>Sube fotos y videos</strong> de tus actuaciones en la sección de Multimedia.</li>
                        <li>Añade tus <strong>redes sociales</strong> (Instagram, TikTok, YouTube, Spotify) para que los clientes te conozcan mejor.</li>
                    </ol>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Por qué tengo que verificar mi identidad?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>La verificación de identidad es obligatoria para aparecer en la app móvil donde los clientes te buscan. Esto garantiza que todos los perfiles son de músicos reales, lo que genera confianza en los clientes y mantiene la calidad de la comunidad.</p>
                    <p>Tu documento (INE, pasaporte u otro ID oficial) <strong>nunca es visible para los clientes</strong>. Solo el equipo administrativo de Armonihz lo revisa para confirmar tu identidad.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo subo mi documento de verificación?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Ve a la sección <strong>"Verificar Identidad"</strong> en tu panel web (o desde el aviso en tu dashboard). Sube una imagen clara de tu documento oficial (INE, pasaporte, cédula profesional, etc.).</p>
                    <p>El estado de tu verificación puede ser:</p>
                    <ul>
                        <li><strong>Sin verificar</strong>: aún no has subido tu documento</li>
                        <li><strong>En revisión</strong>: el equipo está revisando tu documento</li>
                        <li><strong>Aprobado</strong>: ¡tu perfil ya es visible en la app!</li>
                        <li><strong>Rechazado</strong>: el documento no fue aceptado; recibirás el motivo por correo</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo me encuentran los clientes?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Los clientes usan la <strong>app móvil de Armonihz</strong> para buscar músicos. Pueden filtrar por género musical, ubicación y calificación. Tu perfil aparecerá en estos resultados una vez que esté verificado y aprobado.</p>
                    <p>Consejos para mejorar tu visibilidad:</p>
                    <ul>
                        <li>Completa el 100% de tu perfil (bio, foto, géneros, tarifa, ubicación)</li>
                        <li>Sube videos de buena calidad de tus actuaciones</li>
                        <li>Mantén una buena calificación respondiendo solicitudes a tiempo</li>
                        <li>Activa una <strong>Promoción</strong> para aparecer primero en los resultados</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo gestiono mi disponibilidad?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>En la sección de <strong>Disponibilidad</strong> del panel web puedes bloquear días o rangos de horas específicos en los que <strong>no</strong> estás disponible para eventos.</p>
                    <p>Los clientes ven tu disponibilidad en tiempo real en la app. El sistema valida automáticamente que no se solicite una contratación en un horario que ya tienes comprometido.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo subo fotos y videos a mi perfil?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Ve a la sección de <strong>Multimedia</strong> en el panel web. Puedes subir tanto <strong>imágenes</strong> como <strong>videos</strong> de tus actuaciones. Puedes marcar algunos como "destacados" para que aparezcan de forma prominente en tu perfil.</p>
                    <p>Los clientes verán tu galería directamente al ver tu perfil en la app móvil.</p>
                </div>
            </div>
        </div>

        <!-- CLIENTES -->
        <div class="help-section" id="clientes">
            <div class="section-header">
                <div class="section-header-icon"><i class="fa-solid fa-users"></i></div>
                <div>
                    <h2>Para clientes</h2>
                    <p>Cómo encontrar músicos y gestionar tus eventos</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo descargo y uso la app de Armonihz?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>La app de Armonihz está disponible para <strong>Android</strong>. Puedes descargar el archivo APK directamente desde la página principal de Armonihz en el botón "Descargar para Android".</p>
                    <p>Para ingresar, puedes hacerlo con tu <strong>cuenta de Google</strong> o con correo y contraseña.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo busco y filtro músicos?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>En la app puedes navegar por los perfiles de músicos disponibles. Puedes filtrar por:</p>
                    <ul>
                        <li><strong>Género musical</strong> (jazz, rock, clásico, etc.)</li>
                        <li><strong>Ubicación</strong> (ciudad o zona)</li>
                        <li><strong>Calificación</strong> (músicos mejor evaluados)</li>
                    </ul>
                    <p>También puedes buscar por nombre directamente. Al tocar un perfil, verás su galería multimedia, tarifas, redes sociales y reseñas de otros clientes.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo guardo músicos favoritos?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Desde el perfil de un músico en la app, toca el ícono de <strong>corazón</strong> para guardarlo como favorito. Puedes acceder a tu lista de favoritos desde el menú de la app para encontrar rápidamente a los músicos que más te gustan.</p>
                </div>
            </div>
        </div>

        <!-- CONTRATACIONES -->
        <div class="help-section" id="contrataciones">
            <div class="section-header">
                <div class="section-header-icon"><i class="fa-solid fa-file-signature"></i></div>
                <div>
                    <h2>Contrataciones directas</h2>
                    <p>Todo sobre el proceso de solicitar un músico para tu evento</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo envío una solicitud de contratación?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Desde el perfil del músico en la app, toca el botón <strong>"Solicitar contratación"</strong>. Luego deberás indicar:</p>
                    <ul>
                        <li>Fecha y horario del evento</li>
                        <li>Lugar del evento</li>
                        <li>Descripción del evento (boda, cumpleaños, corporativo, etc.)</li>
                        <li>Tu presupuesto propuesto</li>
                    </ul>
                    <p>El músico recibirá una notificación y tendrá la oportunidad de aceptar, rechazar o proponer un precio diferente (contraoferta).</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Qué pasa si el músico hace una contraoferta?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Si el músico no está de acuerdo con tu presupuesto, puede enviarte una <strong>contraoferta</strong> con un precio diferente y un mensaje explicando el motivo.</p>
                    <p>Tú como cliente puedes <strong>Aceptar</strong> la contraoferta (el nuevo precio se vuelve el acuerdo oficial) o <strong>Rechazarla</strong> (la solicitud se cancela).</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo se realiza el pago?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Armonihz <strong>no procesa ni gestiona pagos</strong>. La plataforma facilita el acuerdo (fecha, lugar, precio), pero el pago se realiza <strong>directamente entre tú y el músico</strong> mediante el método que acuerden (efectivo, transferencia, etc.).</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Qué pasa si necesito cancelar un evento?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Si la solicitud aún está <strong>pendiente</strong>, puedes cancelarla sin consecuencias dentro de la plataforma.</p>
                    <p>Si el evento ya fue <strong>aceptado</strong>, comunícate con el músico con la mayor anticipación posible. Recuerda que el músico puede haber bloqueado esa fecha en su agenda. Te recomendamos llegar a un acuerdo directo con el músico para gestionar la cancelación.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo dejo una reseña al músico?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Una vez que tu evento se ha marcado como <strong>completado</strong>, aparecerá la opción de dejar una reseña desde el historial de tus solicitudes en la app.</p>
                    <p>Puedes dar una <strong>calificación del 1 al 5</strong> y escribir un comentario sobre tu experiencia. Las reseñas son públicas y visibles para otros clientes.</p>
                </div>
            </div>
        </div>

        <!-- CASTINGS -->
        <div class="help-section" id="castings">
            <div class="section-header">
                <div class="section-header-icon"><i class="fa-solid fa-microphone-lines"></i></div>
                <div>
                    <h2>Sistema de Castings</h2>
                    <p>Convocatorias abiertas para que múltiples músicos apliquen</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Qué es un casting en Armonihz?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Un casting es una <strong>convocatoria abierta</strong> que publicas como cliente. En lugar de contactar a un músico en específico, describes tu evento y permites que varios músicos interesados apliquen proponiendo su precio.</p>
                    <p>Es ideal cuando quieres explorar opciones o no tienes un músico en mente.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo publico un casting?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <ol>
                        <li>Desde la app, ve a la sección de <strong>Eventos</strong>.</li>
                        <li>Toca <strong>"Publicar evento"</strong> y llena los detalles: título, tipo de música, fecha, horario, ubicación, descripción y presupuesto de referencia.</li>
                        <li>Una vez publicado, los músicos verificados podrán verlo y aplicar.</li>
                        <li>Revisa las aplicaciones, compara propuestas y selecciona al músico que prefieran.</li>
                    </ol>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo aplico a un casting? (Para músicos)</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>En la sección de <strong>Castings</strong> del panel web puedes ver los eventos activos publicados por clientes. Cuando encuentres uno que te interese, puedes aplicar indicando:</p>
                    <ul>
                        <li>Tu <strong>precio propuesto</strong> para el evento</li>
                        <li>Un <strong>mensaje</strong> presentándote y explicando por qué eres la mejor opción</li>
                    </ul>
                    <p>Recibirás una notificación cuando el cliente tome una decisión sobre tu candidatura.</p>
                </div>
            </div>
        </div>

        <!-- CUENTA -->
        <div class="help-section" id="cuenta">
            <div class="section-header">
                <div class="section-header-icon"><i class="fa-solid fa-user-gear"></i></div>
                <div>
                    <h2>Mi cuenta</h2>
                    <p>Configuración, seguridad y administración de tu cuenta</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo cambio mi contraseña?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p><strong>Músicos (panel web):</strong> Ve a tu perfil y busca la sección de <strong>"Cambiar contraseña"</strong>. Necesitarás ingresar tu contraseña actual y la nueva.</p>
                    <p><strong>Clientes (app):</strong> Desde la pantalla de inicio de sesión, toca <strong>"Olvidé mi contraseña"</strong>. Recibirás un correo de recuperación para establecer una nueva contraseña.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Mi cuenta se puede suspender?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Sí. Las cuentas pueden suspenderse por violar los Términos y Condiciones de la plataforma, incluyendo:</p>
                    <ul>
                        <li>Información falsa en el perfil o documento de identidad</li>
                        <li>Comportamiento abusivo hacia otros usuarios</li>
                        <li>Reportes verificados de conducta inapropiada</li>
                    </ul>
                    <p>Una cuenta suspendida no puede iniciar sesión ni usar ninguna función de la plataforma.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo elimino mi cuenta?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p><strong>Clientes:</strong> La app incluye una opción para <strong>eliminar tu cuenta</strong> desde la sección de configuración. Esta acción es permanente y elimina todos tus datos asociados.</p>
                    <p><strong>Músicos:</strong> Si deseas eliminar tu cuenta, puedes contactar al equipo de Armonihz y procesaremos tu solicitud en un plazo máximo de 5 días hábiles.</p>
                </div>
            </div>
        </div>

        <!-- APP -->
        <div class="help-section" id="app">
            <div class="section-header">
                <div class="section-header-icon"><i class="fa-solid fa-mobile-alt"></i></div>
                <div>
                    <h2>Aplicación Móvil</h2>
                    <p>Todo lo que necesitas saber sobre la app de Armonihz</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿En qué dispositivos funciona la app?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>La aplicación móvil de Armonihz está disponible para <strong>Android</strong> a través de un archivo APK descargable directamente desde esta página web. No requiere pasar por la Google Play Store.</p>
                    <p><strong>La app es solo para clientes.</strong> Los músicos gestionan su perfil y solicitudes desde el panel web de Armonihz.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Por qué no recibo notificaciones push?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Las notificaciones push se envían a través de <strong>Firebase Cloud Messaging (FCM)</strong>. Si no las recibes, verifica que:</p>
                    <ul>
                        <li>Las notificaciones de la app estén habilitadas en la configuración de tu dispositivo</li>
                        <li>Tengas conexión a internet activa</li>
                        <li>Hayas iniciado sesión correctamente en la app</li>
                    </ul>
                    <p>Además, recibirás notificaciones por <strong>correo electrónico</strong> para los eventos más importantes (nuevas solicitudes, aprobación de verificación, etc.)</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>¿Cómo reporto a un músico desde la app?</span>
                    <i class="fa-solid fa-plus faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Desde el perfil del músico en la app, busca el ícono de <strong>tres puntos (menú)</strong> o el botón de opciones y selecciona <strong>"Reportar perfil"</strong>. Indica el motivo del reporte.</p>
                    <p>El equipo de Armonihz revisará cada reporte y tomará las acciones necesarias para mantener la seguridad de la comunidad.</p>
                </div>
            </div>
        </div>

        <!-- CONTACT CTA -->
        <div class="contact-cta">
            <h3>¿No encontraste lo que buscabas?</h3>
            <p>Si tienes una pregunta que no está cubierta aquí, nuestro equipo está disponible para ayudarte personalmente en <a href="mailto:armonihzgb@gmail.com" style="color:#ffffff; text-decoration:underline;">armonihzgb@gmail.com</a></p>
            <a href="{{ route('welcome') }}">
                <i class="fa-solid fa-arrow-left"></i>
                Volver al inicio
            </a>
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
            <a href="{{ route('legal.terminos') }}">Términos</a>
            <a href="{{ route('legal.ayuda') }}" class="active">Ayuda</a>
        </div>
    </footer>

    <script>
        document.querySelectorAll('.faq-question').forEach(q => {
            q.addEventListener('click', () => {
                const item = q.parentElement;
                const wasActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
                if (!wasActive) item.classList.add('active');
            });
        });
        document.querySelectorAll('.cat-tab').forEach(tab => {
            tab.addEventListener('click', e => {
                document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            });
        });
    </script>
</body>
</html>
