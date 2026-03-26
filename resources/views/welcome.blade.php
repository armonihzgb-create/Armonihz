<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armonihz — Conecta con músicos profesionales</title>
    <meta name="description" content="Armonihz conecta músicos talentosos con clientes que buscan el artista perfecto para su evento. Bodas, quinceañeras, eventos corporativos y más.">
    <link rel="icon" type="image/png" href="{{ asset('images/Armonihz_logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --purple: #6c3fc5;
            --purple-light: #8b5cf6;
            --blue: #2f93f5;
            --dark: #0f0a1e;
            --dark-2: #1a1230;
            --text: #f8f6ff;
            --text-muted: rgba(248,246,255,.65);
            --border: rgba(255,255,255,.1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 999;
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 5%;
            background: rgba(15,10,30,.8);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            transition: background .3s;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; color: var(--text);
        }
        .nav-brand img { width: 32px; height: 32px; border-radius: 8px; }
        .nav-brand span { font-size: 20px; font-weight: 800; letter-spacing: -.5px; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-link {
            padding: 8px 18px; border-radius: 8px;
            font-size: 14px; font-weight: 600; text-decoration: none;
            color: var(--text-muted); transition: all .2s;
        }
        .nav-link:hover { color: var(--text); background: rgba(255,255,255,.07); }
        .nav-cta {
            padding: 9px 22px; border-radius: 8px; border: none;
            background: var(--purple); color: #fff;
            font-size: 14px; font-weight: 700; text-decoration: none;
            transition: all .2s; box-shadow: 0 4px 20px rgba(108,63,197,.4);
        }
        .nav-cta:hover { background: var(--purple-light); transform: translateY(-1px); box-shadow: 0 6px 28px rgba(108,63,197,.5); }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            text-align: center;
            padding: 120px 5% 80px;
            position: relative; overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0; z-index: 0;
            background:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(108,63,197,.4) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 60%, rgba(47,147,245,.2) 0%, transparent 60%);
        }
        .hero-grid {
            position: absolute; inset: 0; z-index: 0;
            background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .hero-content { position: relative; z-index: 1; max-width: 800px; margin: 0 auto; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(108,63,197,.15); border: 1px solid rgba(108,63,197,.4);
            color: #c4b5fd; font-size: 13px; font-weight: 600;
            padding: 6px 16px; border-radius: 999px; margin-bottom: 28px;
        }
        .hero-badge i { font-size: 11px; }
        h1.hero-title {
            font-size: clamp(2.6rem, 7vw, 5rem);
            font-weight: 900; line-height: 1.1;
            letter-spacing: -2px; margin-bottom: 24px;
        }
        .hero-title .accent {
            background: linear-gradient(135deg, #a78bfa, #60b0fa);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            font-size: 18px; color: var(--text-muted); line-height: 1.7;
            max-width: 600px; margin: 0 auto 40px;
        }
        .hero-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 9px;
            padding: 14px 32px; border-radius: 12px;
            background: var(--purple); color: #fff;
            font-size: 15px; font-weight: 700; text-decoration: none;
            box-shadow: 0 8px 32px rgba(108,63,197,.45);
            transition: all .25s;
        }
        .btn-primary:hover { background: var(--purple-light); transform: translateY(-2px); box-shadow: 0 12px 40px rgba(108,63,197,.55); }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 9px;
            padding: 14px 32px; border-radius: 12px;
            background: rgba(255,255,255,.07); color: var(--text);
            border: 1px solid var(--border);
            font-size: 15px; font-weight: 600; text-decoration: none;
            transition: all .25s;
        }
        .btn-secondary:hover { background: rgba(255,255,255,.12); transform: translateY(-2px); }

        /* ── STATS BAR ── */
        .stats-bar {
            padding: 40px 5%;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,.02);
        }
        .stats-grid {
            max-width: 1100px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 0;
        }
        .stat-item {
            text-align: center;
            padding: 16px 0;
            border-right: 1px solid var(--border);
        }
        .stat-item:last-child { border-right: none; }
        .stat-num { font-size: 2rem; font-weight: 900; background: linear-gradient(135deg, #a78bfa, #60b0fa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stat-label { font-size: 13px; color: var(--text-muted); margin-top: 4px; font-weight: 500; }

        /* ── SECTION ── */
        section { padding: 100px 5%; }
        .section-inner { max-width: 1100px; margin: 0 auto; }
        .section-eyebrow {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 700; letter-spacing: .08em;
            color: #a78bfa; text-transform: uppercase;
            background: rgba(108,63,197,.12); border: 1px solid rgba(108,63,197,.3);
            padding: 5px 14px; border-radius: 999px; margin-bottom: 18px;
        }
        .section-title { font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 900; letter-spacing: -1px; margin-bottom: 14px; }
        .section-sub { font-size: 17px; color: var(--text-muted); line-height: 1.7; max-width: 560px; }

        /* ── HOW IT WORKS ── */
        .how-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 24px; margin-top: 60px;
        }
        .how-card {
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius: 20px; padding: 32px 28px;
            transition: all .3s;
            position: relative; overflow: hidden;
        }
        .how-card::before {
            content: '';
            position: absolute; top: -1px; left: -1px; right: -1px; height: 3px;
            background: linear-gradient(90deg, var(--purple), var(--blue));
            border-radius: 20px 20px 0 0; opacity: 0;
            transition: opacity .3s;
        }
        .how-card:hover { background: rgba(255,255,255,.07); border-color: rgba(108,63,197,.4); transform: translateY(-4px); }
        .how-card:hover::before { opacity: 1; }
        .how-num {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, rgba(108,63,197,.3), rgba(47,147,245,.3));
            border: 1px solid rgba(108,63,197,.4);
            color: #c4b5fd; font-size: 16px; font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .how-card h3 { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
        .how-card p { font-size: 14px; color: var(--text-muted); line-height: 1.7; }

        /* ── FEATURES ── */
        .features { background: rgba(255,255,255,.015); }
        .features-layout {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 80px; align-items: center; margin-top: 20px;
        }
        .feature-list { display: flex; flex-direction: column; gap: 28px; margin-top: 40px; }
        .feature-item { display: flex; gap: 16px; align-items: flex-start; }
        .feature-icon {
            width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .feature-icon.purple { background: rgba(108,63,197,.15); color: #c4b5fd; }
        .feature-icon.blue   { background: rgba(47,147,245,.15); color: #93c5fd; }
        .feature-icon.green  { background: rgba(22,163,74,.15); color: #86efac; }
        .feature-icon.amber  { background: rgba(245,158,11,.15); color: #fcd34d; }
        .feature-item h4 { font-size: 15px; font-weight: 700; margin-bottom: 5px; }
        .feature-item p { font-size: 13px; color: var(--text-muted); line-height: 1.6; }
        .mockup-card {
            background: rgba(255,255,255,.05);
            border: 1px solid var(--border);
            border-radius: 24px; padding: 28px;
            position: relative; overflow: hidden;
        }
        .mockup-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--purple), transparent);
        }
        .mockup-avatar {
            width: 72px; height: 72px; border-radius: 50%;
            background: linear-gradient(135deg, var(--purple), var(--blue));
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 800; color: #fff;
            margin-bottom: 16px;
        }
        .mockup-name { font-size: 20px; font-weight: 800; margin-bottom: 4px; }
        .mockup-genre { font-size: 13px; color: #a78bfa; font-weight: 600; margin-bottom: 16px; }
        .mockup-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
        .mockup-chip {
            padding: 4px 12px; border-radius: 999px;
            font-size: 12px; font-weight: 600;
            background: rgba(108,63,197,.15); color: #c4b5fd;
            border: 1px solid rgba(108,63,197,.3);
        }
        .mockup-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
        .mockup-stat {
            background: rgba(255,255,255,.05); border-radius: 12px; padding: 12px;
            text-align: center;
        }
        .mockup-stat .val { font-size: 18px; font-weight: 900; color: #a78bfa; }
        .mockup-stat .lbl { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .mockup-btn {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, var(--purple), var(--blue));
            border: none; border-radius: 12px;
            color: #fff; font-size: 14px; font-weight: 700;
            cursor: pointer; box-shadow: 0 6px 24px rgba(108,63,197,.35);
        }

        /* ── GENRES ── */
        .genre-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 16px; margin-top: 60px;
        }
        .genre-card {
            padding: 28px 20px; border-radius: 18px; text-align: center;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.03);
            transition: all .3s; cursor: default;
        }
        .genre-card:hover { transform: translateY(-4px); border-color: rgba(108,63,197,.4); background: rgba(108,63,197,.08); }
        .genre-icon { font-size: 2rem; margin-bottom: 12px; }
        .genre-name { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
        .genre-count { font-size: 12px; color: var(--text-muted); }

        /* ── TESTIMONIALS ── */
        .testimonials { background: radial-gradient(ellipse 70% 50% at 50% 100%, rgba(108,63,197,.12) 0%, transparent 70%); }
        .testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 60px; }
        .testi-card {
            background: rgba(255,255,255,.04); border: 1px solid var(--border);
            border-radius: 20px; padding: 28px; transition: all .3s;
        }
        .testi-card:hover { border-color: rgba(108,63,197,.35); background: rgba(255,255,255,.07); }
        .testi-stars { color: #f59e0b; font-size: 13px; margin-bottom: 14px; }
        .testi-text { font-size: 14px; color: var(--text-muted); line-height: 1.75; font-style: italic; margin-bottom: 20px; }
        .testi-author { display: flex; align-items: center; gap: 12px; }
        .testi-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; color: #fff;
        }
        .testi-name { font-size: 14px; font-weight: 700; }
        .testi-event { font-size: 12px; color: var(--text-muted); }

        /* ── CTA ── */
        .cta-section {
            padding: 100px 5%;
            background: linear-gradient(135deg, rgba(108,63,197,.15) 0%, rgba(47,147,245,.1) 100%);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            text-align: center;
        }
        .cta-section h2 { font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 900; letter-spacing: -1px; margin-bottom: 16px; }
        .cta-section p { font-size: 17px; color: var(--text-muted); margin-bottom: 40px; max-width: 520px; margin-left: auto; margin-right: auto; margin-bottom: 40px; }
        .cta-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

        /* ── FOOTER ── */
        footer {
            padding: 40px 5%;
            border-top: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 16px;
        }
        .footer-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .footer-brand img { width: 26px; border-radius: 6px; }
        .footer-brand span { font-size: 16px; font-weight: 800; }
        .footer-copy { font-size: 13px; color: var(--text-muted); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { font-size: 13px; color: var(--text-muted); text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: var(--text); }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .how-grid { grid-template-columns: 1fr; }
            .features-layout { grid-template-columns: 1fr; gap: 40px; }
            .genre-grid { grid-template-columns: repeat(2, 1fr); }
            .testimonials-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .stat-item:nth-child(2) { border-right: none; }
            .stat-item:nth-child(3) { border-right: 1px solid var(--border); border-top: 1px solid var(--border); }
            .stat-item:nth-child(4) { border-top: 1px solid var(--border); border-right: none; }
        }
        @media (max-width: 600px) {
            .nav-links .nav-link { display: none; }
            .genre-grid { grid-template-columns: 1fr 1fr; }
            footer { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="nav">
        <a class="nav-brand" href="/">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <span>Armonihz</span>
        </a>
        <div class="nav-links">
            <a href="#como-funciona" class="nav-link">Cómo funciona</a>
            <a href="#generos" class="nav-link">Géneros</a>
            @if(Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="nav-cta">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Iniciar sesión</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="nav-cta">Registrarme gratis</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-grid"></div>
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fa-solid fa-music"></i>
                La plataforma líder de músicos en México
            </div>
            <h1 class="hero-title">
                El músico perfecto<br>
                para tu <span class="accent">momento especial</span>
            </h1>
            <p class="hero-subtitle">
                Conectamos músicos talentosos con personas que buscan el artista ideal para bodas, quinceañeras, eventos corporativos y más. Simple, rápido y sin complicaciones.
            </p>
            <div class="hero-btns">
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">
                        <i class="fa-solid fa-rocket"></i>
                        Empieza ahora — Es gratis
                    </a>
                @endif
                <a href="#como-funciona" class="btn-secondary">
                    <i class="fa-solid fa-play"></i>
                    Ver cómo funciona
                </a>
            </div>
        </div>
    </section>

    <!-- STATS BAR -->
    <div class="stats-bar">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-num">2,800+</div>
                <div class="stat-label">Músicos registrados</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">9,400+</div>
                <div class="stat-label">Eventos realizados</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">4.8 ⭐</div>
                <div class="stat-label">Calificación promedio</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">30+</div>
                <div class="stat-label">Géneros musicales</div>
            </div>
        </div>
    </div>

    <!-- HOW IT WORKS -->
    <section id="como-funciona">
        <div class="section-inner">
            <div class="section-eyebrow"><i class="fa-solid fa-list-check"></i> Proceso</div>
            <h2 class="section-title">¿Cómo funciona?</h2>
            <p class="section-sub">En 3 pasos encontrarás al músico ideal para tu evento.</p>
            <div class="how-grid">
                <div class="how-card">
                    <div class="how-num">1</div>
                    <h3>Explora músicos</h3>
                    <p>Navega perfiles de músicos verificados con videos, fotos, reseñas y tarifa por hora. Filtra por género, ciudad y disponibilidad.</p>
                </div>
                <div class="how-card">
                    <div class="how-num">2</div>
                    <h3>Contacta o reserva</h3>
                    <p>Envía una solicitud de contratación directamente desde el perfil del músico. El artista recibe tu petición y puede aceptar al instante.</p>
                </div>
                <div class="how-card">
                    <div class="how-num">3</div>
                    <h3>¡Disfruta tu evento!</h3>
                    <p>Confirma los detalles, coordina los últimos preparativos y relájate. Nosotros nos aseguramos de que todo esté listo para brillar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="features">
        <div class="section-inner">
            <div class="features-layout">
                <div>
                    <div class="section-eyebrow"><i class="fa-solid fa-star"></i> Para músicos</div>
                    <h2 class="section-title">Todo lo que necesitas para crecer</h2>
                    <p class="section-sub">Armonihz le da a los músicos las herramientas para competir en el mercado y conseguir más contratos.</p>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon purple"><i class="fa-solid fa-user"></i></div>
                            <div>
                                <h4>Perfil profesional</h4>
                                <p>Crea tu bio, sube fotos y videos de tus actuaciones, y muestra tu portafolio al mundo.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon blue"><i class="fa-solid fa-calendar-check"></i></div>
                            <div>
                                <h4>Calendario de disponibilidad</h4>
                                <p>Gestiona tu agenda fácilmente. Marca días completos u horarios específicos como ocupados.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon green"><i class="fa-solid fa-trophy"></i></div>
                            <div>
                                <h4>Castings y oportunidades</h4>
                                <p>Aplica a castings activos y accede a oportunidades exclusivas para músicos en tu área.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon amber"><i class="fa-solid fa-bullhorn"></i></div>
                            <div>
                                <h4>Promociona tu perfil</h4>
                                <p>Aumenta tu visibilidad con campañas de promoción y llega a más clientes potenciales.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mockup-card">
                        <div class="mockup-avatar">JR</div>
                        <div class="mockup-name">Jesús Ramos</div>
                        <div class="mockup-genre">🎸 Rock / Pop / Ballads</div>
                        <div class="mockup-chips">
                            <span class="mockup-chip">Guitarra</span>
                            <span class="mockup-chip">Voz</span>
                            <span class="mockup-chip">Piano</span>
                            <span class="mockup-chip">Disponible</span>
                        </div>
                        <div class="mockup-stats">
                            <div class="mockup-stat">
                                <div class="val">5.0</div>
                                <div class="lbl">Calificación</div>
                            </div>
                            <div class="mockup-stat">
                                <div class="val">148</div>
                                <div class="lbl">Eventos</div>
                            </div>
                            <div class="mockup-stat">
                                <div class="val">$3,200</div>
                                <div class="lbl">MXN/hr</div>
                            </div>
                        </div>
                        <button class="mockup-btn">
                            <i class="fa-solid fa-envelope" style="margin-right:8px;"></i>
                            Solicitar contratación
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GENRES -->
    <section id="generos">
        <div class="section-inner">
            <div class="section-eyebrow"><i class="fa-solid fa-music"></i> Géneros</div>
            <h2 class="section-title">Música para cada ocasión</h2>
            <p class="section-sub">Desde música clásica para ceremonias hasta DJs para fiestas. Tenemos músicos para todos los gustos.</p>
            <div class="genre-grid">
                <div class="genre-card"><div class="genre-icon">🎸</div><div class="genre-name">Rock & Pop</div><div class="genre-count">+420 músicos</div></div>
                <div class="genre-card"><div class="genre-icon">🎹</div><div class="genre-name">Clásica</div><div class="genre-count">+185 músicos</div></div>
                <div class="genre-card"><div class="genre-icon">🎺</div><div class="genre-name">Jazz & Blues</div><div class="genre-count">+230 músicos</div></div>
                <div class="genre-card"><div class="genre-icon">🥁</div><div class="genre-name">Banda & Norteño</div><div class="genre-count">+310 músicos</div></div>
                <div class="genre-card"><div class="genre-icon">🎻</div><div class="genre-name">Romántica</div><div class="genre-count">+270 músicos</div></div>
                <div class="genre-card"><div class="genre-icon">🎤</div><div class="genre-name">Baladas</div><div class="genre-count">+360 músicos</div></div>
                <div class="genre-card"><div class="genre-icon">🎷</div><div class="genre-name">Tropical & Salsa</div><div class="genre-count">+195 músicos</div></div>
                <div class="genre-card"><div class="genre-icon">🎧</div><div class="genre-name">DJ & Electrónica</div><div class="genre-count">+280 músicos</div></div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testimonials">
        <div class="section-inner">
            <div class="section-eyebrow"><i class="fa-solid fa-heart"></i> Testimonios</div>
            <h2 class="section-title">Lo que dicen nuestros usuarios</h2>
            <p class="section-sub">Miles de eventos exitosos respaldan nuestra plataforma.</p>
            <div class="testimonials-grid">
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"Armonihz me ayudó a encontrar al cuarteto de cuerdas perfecto para mi boda. El proceso fue increíblemente sencillo y la música fue espectacular."</p>
                    <div class="testi-author">
                        <div class="testi-avatar" style="background:linear-gradient(135deg,#6c3fc5,#2f93f5);">ML</div>
                        <div>
                            <div class="testi-name">María López</div>
                            <div class="testi-event">Boda Civil · Guadalajara</div>
                        </div>
                    </div>
                </div>
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"Como músico, Armonihz cambió mi carrera. Pasé de tener pocos eventos al mes a tener agenda llena gracias al perfil y las herramientas que ofrecen."</p>
                    <div class="testi-author">
                        <div class="testi-avatar" style="background:linear-gradient(135deg,#16a34a,#0ea5e9);">CR</div>
                        <div>
                            <div class="testi-name">Carlos Reyes</div>
                            <div class="testi-event">Guitarrista profesional · CDMX</div>
                        </div>
                    </div>
                </div>
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"Para el evento de fin de año de nuestra empresa contratamos un trío de jazz. Llegaron puntuales, fueron muy profesionales. ¡Los volvemos a contratar!"</p>
                    <div class="testi-author">
                        <div class="testi-avatar" style="background:linear-gradient(135deg,#d97706,#dc2626);">AG</div>
                        <div>
                            <div class="testi-name">Ana Gutiérrez</div>
                            <div class="testi-event">Evento Corporativo · Monterrey</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="section-eyebrow" style="margin-bottom:24px;"><i class="fa-solid fa-guitar"></i> Únete hoy</div>
        <h2>¿Listo para encontrar tu músico ideal?</h2>
        <p>Regístrate gratis y descubre a cientos de músicos profesionales disponibles en tu ciudad.</p>
        <div class="cta-btns">
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="btn-primary">
                    <i class="fa-solid fa-user-plus"></i>
                    Crear cuenta gratis
                </a>
            @endif
            @if(Route::has('login'))
                <a href="{{ route('login') }}" class="btn-secondary">
                    <i class="fa-solid fa-sign-in-alt"></i>
                    Ya tengo cuenta
                </a>
            @endif
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <a class="footer-brand" href="/">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <span>Armonihz</span>
        </a>
        <p class="footer-copy">© 2026 Armonihz. Todos los derechos reservados.</p>
        <div class="footer-links">
            <a href="#">Privacidad</a>
            <a href="#">Términos</a>
            <a href="#">Ayuda</a>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.nav');
            if (window.scrollY > 40) {
                nav.style.background = 'rgba(15,10,30,.95)';
            } else {
                nav.style.background = 'rgba(15,10,30,.8)';
            }
        });

        // Smooth scroll for anchors
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
            });
        });
    </script>
</body>
</html>
