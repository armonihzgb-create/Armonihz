<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armonihz — La plataforma para músicos profesionales</title>
    <meta name="description" content="Crea tu perfil artístico, gestiona tu agenda, aplica a castings y consigue más contratos. Armonihz es la plataforma que los músicos necesitan para crecer.">
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
            --dark: #09090b;
            --dark-2: #18181b;
            --text: #f8f6ff;
            --text-muted: rgba(248,246,255,.62);
            --border: rgba(255,255,255,.1);
        }

        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--dark); color: var(--text); overflow-x: hidden; }

        /* -- NAVBAR -- */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 999;
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 6%;
            background: rgba(15,10,30,.8); backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border); transition: background .3s;
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .nav-brand img { width: 32px; border-radius: 8px; filter: brightness(0) invert(1); }
        .nav-brand span { font-size: 20px; font-weight: 800; letter-spacing: -.5px; }
        .nav-links { display: flex; align-items: center; gap: 6px; }
        .nav-link { padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: var(--text-muted); transition: all .2s; }
        .nav-link:hover { color: var(--text); background: rgba(255,255,255,.07); }
        .nav-cta { padding: 9px 22px; border-radius: 8px; border: none; background: var(--purple); color: #fff; font-size: 14px; font-weight: 700; text-decoration: none; transition: all .2s; box-shadow: 0 4px 18px rgba(108,63,197,.4); }
        .nav-cta:hover { background: var(--purple-light); transform: translateY(-1px); }

        /* -- HERO -- */
        .hero {
            min-height: 100vh; display: flex; align-items: center;
            padding: 130px 6% 90px; position: relative; overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0; z-index: 0;
            background:
                radial-gradient(ellipse 70% 60% at 60% 0%, rgba(108,63,197,.45) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 10% 80%, rgba(47,147,245,.18) 0%, transparent 60%);
        }
        .hero-grid {
            position: absolute; inset: 0; z-index: 0;
            background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .hero-inner {
            max-width: 1100px; margin: 0 auto; position: relative; z-index: 1;
            display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(108,63,197,.15); border: 1px solid rgba(108,63,197,.4);
            color: #c4b5fd; font-size: 12px; font-weight: 700; letter-spacing: .04em;
            text-transform: uppercase; padding: 5px 14px; border-radius: 999px; margin-bottom: 24px;
        }
        h1.hero-title { font-size: clamp(2.4rem, 5vw, 3.6rem); font-weight: 900; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 20px; }
        .hero-title .accent { background: linear-gradient(135deg, #c084fc, #60b0fa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-sub { font-size: 17px; color: var(--text-muted); line-height: 1.75; margin-bottom: 36px; }
        .hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-primary { display: inline-flex; align-items: center; gap: 9px; padding: 14px 28px; border-radius: 12px; background: var(--purple); color: #fff; font-size: 15px; font-weight: 700; text-decoration: none; box-shadow: 0 8px 28px rgba(108,63,197,.45); transition: all .25s; }
        .btn-primary:hover { background: var(--purple-light); transform: translateY(-2px); box-shadow: 0 12px 36px rgba(108,63,197,.55); }
        .btn-secondary { display: inline-flex; align-items: center; gap: 9px; padding: 14px 28px; border-radius: 12px; background: rgba(255,255,255,.07); color: var(--text); border: 1px solid var(--border); font-size: 15px; font-weight: 600; text-decoration: none; transition: all .25s; }
        .btn-secondary:hover { background: rgba(255,255,255,.12); transform: translateY(-2px); }
        .hero-trust { display: flex; align-items: center; gap: 10px; margin-top: 28px; font-size: 13px; color: var(--text-muted); }
        .hero-avatars { display: flex; }
        .hero-avatars span { width: 32px; height: 32px; border-radius: 50%; border: 2px solid var(--dark); font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin-left: -10px; }
        .hero-avatars span:first-child { margin-left: 0; }

        /* Profile card mockup */
        .profile-mockup {
            background: rgba(255,255,255,.05); border: 1px solid var(--border); backdrop-filter: blur(16px);
            border-radius: 24px; padding: 28px; position: relative; overflow: hidden;
        }
        .profile-mockup::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, var(--purple), transparent); }
        .pm-header { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
        .pm-avatar { width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--purple), var(--blue)); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 900; color: #fff; flex-shrink: 0; }
        .pm-name { font-size: 17px; font-weight: 800; }
        .pm-genre { font-size: 12px; color: #a78bfa; font-weight: 600; margin-top: 2px; }
        .pm-badge { display: inline-flex; align-items: center; gap: 5px; background: rgba(22,163,74,.15); border: 1px solid rgba(22,163,74,.4); color: #86efac; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; margin-top: 4px; }
        .pm-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px; }
        .pm-stat { background: rgba(255,255,255,.05); border-radius: 12px; padding: 12px 8px; text-align: center; }
        .pm-stat .v { font-size: 18px; font-weight: 900; color: #c4b5fd; }
        .pm-stat .l { font-size: 10px; color: var(--text-muted); margin-top: 2px; }
        .pm-chips { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 20px; }
        .pm-chip { padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 600; background: rgba(108,63,197,.15); color: #c4b5fd; border: 1px solid rgba(108,63,197,.3); }
        .pm-calendar { background: rgba(255,255,255,.04); border-radius: 14px; padding: 14px; }
        .pm-calendar-title { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px; }
        .pm-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
        .pm-day { width: 100%; aspect-ratio: 1; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; background: rgba(255,255,255,.04); color: var(--text-muted); }
        .pm-day.busy { background: rgba(220,38,38,.25); color: #fca5a5; }
        .pm-day.available { background: rgba(22,163,74,.2); color: #86efac; }
        .pm-day.today { background: var(--purple); color: #fff; }

        /* -- SECTION -- */
        section { padding: 130px 6%; }
        .section-inner { max-width: 1100px; margin: 0 auto; }
        .section-eyebrow { display: inline-flex; align-items: center; gap: 7px; font-size: 11px; font-weight: 700; letter-spacing: .08em; color: #a78bfa; text-transform: uppercase; background: rgba(108,63,197,.12); border: 1px solid rgba(108,63,197,.3); padding: 4px 13px; border-radius: 999px; margin-bottom: 16px; }
        .section-title { font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 900; letter-spacing: -1px; margin-bottom: 12px; }
        .section-sub { font-size: 17px; color: var(--text-muted); line-height: 1.7; max-width: 540px; }

        /* -- FEATURES GRID -- */
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 56px; }
        .feat-card { background: rgba(255,255,255,.04); backdrop-filter: blur(12px); border: 1px solid var(--border); border-radius: 20px; padding: 28px; transition: all .3s; position: relative; overflow: hidden; }
        .feat-card::before { content: ''; position: absolute; top: -1px; left: -1px; right: -1px; height: 2px; background: linear-gradient(90deg, var(--purple), var(--blue)); border-radius: 20px 20px 0 0; opacity: 0; transition: opacity .3s; }
        .feat-card:hover { background: rgba(255,255,255,.07); border-color: rgba(108,63,197,.35); transform: translateY(-4px); }
        .feat-card:hover::before { opacity: 1; }
        .feat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 18px; }
        .feat-icon.purple { background: rgba(108,63,197,.2); color: #c4b5fd; }
        .feat-icon.blue   { background: rgba(47,147,245,.2); color: #93c5fd; }
        .feat-icon.green  { background: rgba(22,163,74,.2); color: #86efac; }
        .feat-icon.amber  { background: rgba(245,158,11,.2); color: #fcd34d; }
        .feat-icon.rose   { background: rgba(244,63,94,.18); color: #fda4af; }
        .feat-icon.teal   { background: rgba(20,184,166,.18); color: #99f6e4; }
        .feat-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feat-card p { font-size: 13px; color: var(--text-muted); line-height: 1.7; }

        /* -- HOW IT WORKS -- */
        .how { background: rgba(255,255,255,.015); }
        .how-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; margin-top: 60px; position: relative; }
        .how-steps::before { content: ''; position: absolute; top: 28px; left: 10%; right: 10%; height: 2px; background: linear-gradient(90deg, var(--purple), var(--blue)); z-index: 0; }
        .how-step { text-align: center; padding: 0 16px; position: relative; z-index: 1; }
        .how-num { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, var(--purple), var(--blue)); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 900; color: #fff; margin: 0 auto 20px; box-shadow: 0 0 0 6px var(--dark), 0 0 0 8px rgba(108,63,197,.25); }
        .how-step h3 { font-size: 15px; font-weight: 700; margin-bottom: 8px; }
        .how-step p { font-size: 13px; color: var(--text-muted); line-height: 1.65; }

        /* -- APP TEASER -- */
        .app-teaser { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; margin-top: 20px; }
        .app-text .section-title { margin-top: 16px; }
        .app-features { margin-top: 28px; display: flex; flex-direction: column; gap: 16px; }
        .app-feat { display: flex; align-items: center; gap: 12px; font-size: 14px; color: var(--text-muted); }
        .app-feat i { color: #a78bfa; font-size: 15px; width: 20px; text-align: center; }
        .app-phones { display: flex; gap: 20px; justify-content: center; }
        .app-phone { width: 140px; background: rgba(255,255,255,.05); border: 1px solid var(--border); border-radius: 28px; padding: 14px; }
        .app-phone-screen { background: #1a1230; border-radius: 18px; overflow: hidden; }
        .app-phone-header { background: linear-gradient(135deg, #1a0b38, #0d2452); padding: 12px; text-align: center; }
        .app-phone-header .app-name { font-size: 11px; font-weight: 800; color: #fff; }
        .app-phone-body { padding: 10px; }
        .app-musician-card { background: rgba(255,255,255,.06); border-radius: 10px; padding: 10px; margin-bottom: 8px; }
        .app-musician-card .amc-name { font-size: 10px; font-weight: 700; color: #fff; margin-bottom: 2px; }
        .app-musician-card .amc-genre { font-size: 9px; color: #a78bfa; margin-bottom: 6px; }
        .app-musician-card .amc-stars { font-size: 9px; color: #f59e0b; }

        /* -- TESTIMONIALS -- */
        .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 56px; }
        .testi-card { background: rgba(255,255,255,.04); backdrop-filter: blur(12px); border: 1px solid var(--border); border-radius: 20px; padding: 26px; transition: all .3s; }
        .testi-card:hover { border-color: rgba(108,63,197,.35); background: rgba(255,255,255,.07); }
        .testi-stars { color: #f59e0b; font-size: 13px; margin-bottom: 14px; }
        .testi-text { font-size: 14px; color: var(--text-muted); line-height: 1.75; font-style: italic; margin-bottom: 18px; }
        .testi-author { display: flex; align-items: center; gap: 12px; }
        .testi-avatar { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: #fff; flex-shrink: 0; }
        .testi-name { font-size: 14px; font-weight: 700; }
        .testi-role { font-size: 11px; color: var(--text-muted); }

        /* -- CTA -- */
        .cta-section { padding: 100px 6%; text-align: center; background: linear-gradient(135deg, rgba(108,63,197,.15) 0%, rgba(47,147,245,.08) 100%); border-top: 1px solid var(--border); }
        .cta-section h2 { font-size: clamp(2rem, 5vw, 3rem); font-weight: 900; letter-spacing: -1px; margin-bottom: 16px; }
        .cta-section p { font-size: 17px; color: var(--text-muted); max-width: 500px; margin: 0 auto 36px; line-height: 1.7; }
        .cta-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .cta-note { margin-top: 16px; font-size: 12px; color: var(--text-muted); }

        /* -- FOOTER -- */
        footer { padding: 36px 6%; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .footer-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .footer-brand img { width: 26px; border-radius: 6px; filter: brightness(0) invert(1); }
        .footer-brand span { font-size: 16px; font-weight: 800; }
        .footer-copy { font-size: 13px; color: var(--text-muted); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { font-size: 13px; color: var(--text-muted); text-decoration: none; transition: color .2s; }
        .footer-links a:hover { color: var(--text); }

        /* -- RESPONSIVE -- */
        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; gap: 50px; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .how-steps { grid-template-columns: 1fr 1fr; gap: 40px; }
            .how-steps::before { display: none; }
            .app-teaser { grid-template-columns: 1fr; gap: 40px; }
            .testi-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .nav-links .nav-link { display: none; }
            .features-grid { grid-template-columns: 1fr; }
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
            <a href="#funcionalidades" class="nav-link">Funcionalidades</a>
            <a href="#como-funciona" class="nav-link">Cómo funciona</a>
            @if(Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="nav-link">Mi dashboard</a>
                    <a href="{{ url('/profile') }}" class="nav-cta">Ver mi perfil</a>
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
        <div class="hero-inner">
            <div class="hero-left">
                <div class="hero-badge"><i class="fa-solid fa-guitar"></i> Plataforma para músicos</div>
                <h1 class="hero-title">
                    Tu carrera musical,<br>
                    en <span class="accent">un solo lugar</span>
                </h1>
                <p class="hero-sub">
                    Armonihz es la plataforma donde los músicos crean su perfil profesional, gestionan su agenda, aplican a castings y son descubiertos por miles de clientes a través de la app móvil.
                </p>
                <div class="hero-btns">
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary">
                            <i class="fa-solid fa-rocket"></i>
                            Crear mi perfil — Es gratis
                        </a>
                    @endif
                    @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="btn-secondary">
                            <i class="fa-solid fa-sign-in-alt"></i>
                            Ya tengo cuenta
                        </a>
                    @endif
                </div>
                <div class="hero-trust">
                    <div class="hero-avatars">
                        <span style="background:linear-gradient(135deg,#6c3fc5,#2f93f5);">JR</span>
                        <span style="background:linear-gradient(135deg,#16a34a,#0ea5e9);">AM</span>
                        <span style="background:linear-gradient(135deg,#d97706,#dc2626);">LS</span>
                        <span style="background:linear-gradient(135deg,#a855f7,#ec4899);">KP</span>
                    </div>
                    <span>Conectando talento musical directamente con clientes</span>
                </div>
            </div>

            <!-- Profile card mockup -->
            <div>
                <div class="profile-mockup">
                    <div class="pm-header">
                        <div class="pm-avatar">JR</div>
                        <div>
                            <div class="pm-name">Jesús Ramos</div>
                            <div class="pm-genre">🎸 Rock · Pop · Baladas</div>
                            <div class="pm-badge"><i class="fa-solid fa-circle" style="font-size:6px;"></i> Disponible</div>
                        </div>
                    </div>
                    <div class="pm-stats">
                        <div class="pm-stat"><div class="v">⭐ 5.0</div><div class="l">Calificación</div></div>
                        <div class="pm-stat"><div class="v">148</div><div class="l">Eventos</div></div>
                        <div class="pm-stat"><div class="v">$3,200</div><div class="l">MXN/hr</div></div>
                    </div>
                    <div class="pm-chips">
                        <span class="pm-chip">Guitarra</span>
                        <span class="pm-chip">Voz</span>
                        <span class="pm-chip">Piano</span>
                        <span class="pm-chip">Eventos privados</span>
                    </div>
                    <div class="pm-calendar">
                        <div class="pm-calendar-title"><i class="fa-solid fa-calendar-check" style="margin-right:5px;"></i> Disponibilidad — Marzo</div>
                        <div class="pm-days">
                            @foreach(['L','M','M','J','V','S','D'] as $d)
                            <div class="pm-day" style="background:transparent;color:rgba(248,246,255,.4);font-size:9px;font-weight:700;">{{ $d }}</div>
                            @endforeach
                            @php $days = [
                                ['',''],['',''],['busy',''],['',''],['available',''],['available',''],['available',''],
                                ['available',''],['busy',''],['busy',''],['today',''],['available',''],['available',''],['busy',''],
                                ['available',''],['available',''],['available',''],['busy',''],['busy',''],['available',''],['available',''],
                            ]; @endphp
                            @foreach($days as $d)
                            <div class="pm-day {{ $d[0] }}">{{ $loop->index + 1 }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section id="funcionalidades">
        <div class="section-inner">
            <div class="section-eyebrow"><i class="fa-solid fa-star"></i> Herramientas</div>
            <h2 class="section-title">Todo lo que necesitas para crecer</h2>
            <p class="section-sub">Una sola plataforma con todas las herramientas que un músico profesional necesita.</p>
            <div class="features-grid">
                <div class="feat-card">
                    <div class="feat-icon purple"><i class="fa-solid fa-id-card"></i></div>
                    <h3>Perfil artístico profesional</h3>
                    <p>Crea tu bio, sube fotos y videos de actuaciones. Tu perfil es tu carta de presentación ante miles de clientes.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon blue"><i class="fa-solid fa-calendar-days"></i></div>
                    <h3>Gestión de disponibilidad</h3>
                    <p>Bloquea días completos o rangos de horas en tu agenda. Los clientes ven tu disponibilidad en tiempo real desde la app.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon green"><i class="fa-solid fa-microphone-lines"></i></div>
                    <h3>Castings y audiciones</h3>
                    <p>Aplica a castings activos publicados por productoras y organizadores de eventos. Nuevas oportunidades cada semana.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon amber"><i class="fa-solid fa-file-invoice"></i></div>
                    <h3>Gestión de solicitudes</h3>
                    <p>Recibe, revisa y acepta solicitudes de contratación directamente desde tu dashboard. Todo en un solo lugar.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon rose"><i class="fa-solid fa-star"></i></div>
                    <h3>Reseñas y reputación</h3>
                    <p>Construye tu reputación con las reseñas de clientes satisfechos. Una buena calificación genera más contratos.</p>
                </div>
                <div class="feat-card">
                    <div class="feat-icon teal"><i class="fa-solid fa-bullhorn"></i></div>
                    <h3>Promoción de perfil</h3>
                    <p>Amplifica tu alcance con campañas de promoción. Aparece en los primeros resultados que los clientes ven en la app.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section id="como-funciona" class="how">
        <div class="section-inner">
            <div class="section-eyebrow"><i class="fa-solid fa-list-check"></i> Proceso</div>
            <h2 class="section-title">Empieza en minutos</h2>
            <p class="section-sub">Registrarte y tener tu perfil activo es rápido y completamente gratis.</p>
            <div class="how-steps">
                <div class="how-step">
                    <div class="how-num">1</div>
                    <h3>Crea tu cuenta</h3>
                    <p>Regístrate en segundos. Solo necesitas tu correo y una contraseña.</p>
                </div>
                <div class="how-step">
                    <div class="how-num">2</div>
                    <h3>Completa tu perfil</h3>
                    <p>Agrega tu bio, géneros, videos y define tu tarifa y zona de cobertura.</p>
                </div>
                <div class="how-step">
                    <div class="how-num">3</div>
                    <h3>Gestiona tu agenda</h3>
                    <p>Marca tus días disponibles para que los clientes sepan cuándo puedes tocar.</p>
                </div>
                <div class="how-step">
                    <div class="how-num">4</div>
                    <h3>¡Consigue contratos!</h3>
                    <p>Los clientes en la app te encuentran, te contactan y te contratan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- APP TEASER -->
    <section>
        <div class="section-inner">
            <div class="app-teaser">
                <div class="app-text">
                    <div class="section-eyebrow"><i class="fa-solid fa-mobile-alt"></i> App Móvil</div>
                    <h2 class="section-title">Sé visible donde los clientes te buscan</h2>
                    <p class="section-sub">Los clientes que buscan músicos para sus eventos usan la app móvil de Armonihz. Tu perfil, actualizado desde la web, aparece directamente en la app.</p>
                    <div class="app-features">
                        <div class="app-feat"><i class="fa-solid fa-check-circle"></i> Tu perfil visible para miles de usuarios de la app</div>
                        <div class="app-feat"><i class="fa-solid fa-check-circle"></i> Las solicitudes de contratación llegan a tu dashboard</div>
                        <div class="app-feat"><i class="fa-solid fa-check-circle"></i> La disponibilidad que configuras en la web se sincroniza en la app</div>
                        <div class="app-feat"><i class="fa-solid fa-check-circle"></i> Mayor exposición con promociones dentro de la app</div>
                    </div>
                </div>
                <div class="app-phones">
                    <div class="app-phone">
                        <div class="app-phone-screen">
                            <div class="app-phone-header">
                                <div class="app-name">Armonihz</div>
                            </div>
                            <div class="app-phone-body">
                                <div style="font-size:9px;font-weight:700;color:rgba(248,246,255,.5);margin-bottom:8px;text-transform:uppercase;letter-spacing:.04em;">Músicos cerca de ti</div>
                                <div class="app-musician-card">
                                    <div class="amc-name">Jesús Ramos</div>
                                    <div class="amc-genre">🎸 Rock · Pop</div>
                                    <div class="amc-stars">★★★★★ <span style="color:rgba(248,246,255,.4);font-size:8px;">5.0</span></div>
                                </div>
                                <div class="app-musician-card">
                                    <div class="amc-name">Ana Martínez</div>
                                    <div class="amc-genre">🎻 Cuarteto Clásico</div>
                                    <div class="amc-stars">★★★★★ <span style="color:rgba(248,246,255,.4);font-size:8px;">4.9</span></div>
                                </div>
                                <div class="app-musician-card" style="opacity:.6;">
                                    <div class="amc-name">Luis Soria</div>
                                    <div class="amc-genre">🎹 Jazz & Blues</div>
                                    <div class="amc-stars">★★★★★ <span style="color:rgba(248,246,255,.4);font-size:8px;">4.8</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section style="background:rgba(255,255,255,.015);">
        <div class="section-inner">
            <div class="section-eyebrow"><i class="fa-solid fa-heart"></i> Músicos</div>
            <h2 class="section-title">Lo que dicen los músicos</h2>
            <p class="section-sub">Únete a los pioneros que están revolucionando las contrataciones en vivo.</p>
            <div class="testi-grid">
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"Armonihz cambió mi carrera por completo. Antes tenía 3 o 4 eventos al mes. Hoy tengo la agenda llena y puedo elegir mis contratos."</p>
                    <div class="testi-author">
                        <div class="testi-avatar" style="background:linear-gradient(135deg,#6c3fc5,#2f93f5);">JR</div>
                        <div>
                            <div class="testi-name">Jesús Ramos</div>
                            <div class="testi-role">Guitarrista · Guadalajara</div>
                        </div>
                    </div>
                </div>
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"La sección de castings es increíble. Apliqué a una convocatoria a través de la plataforma y conseguí un contrato de 6 meses con una productora."</p>
                    <div class="testi-author">
                        <div class="testi-avatar" style="background:linear-gradient(135deg,#16a34a,#0ea5e9);">AM</div>
                        <div>
                            <div class="testi-name">Ana Martínez</div>
                            <div class="testi-role">Violinista · CDMX</div>
                        </div>
                    </div>
                </div>
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"Subí mis videos al perfil y en la primera semana ya tenía mensajes de clientes interesados. La plataforma es muy fácil de usar y el soporte es excelente."</p>
                    <div class="testi-author">
                        <div class="testi-avatar" style="background:linear-gradient(135deg,#d97706,#dc2626);">LS</div>
                        <div>
                            <div class="testi-name">Luis Soria</div>
                            <div class="testi-role">Pianista · Monterrey</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="cta-section">
        <div class="section-eyebrow" style="margin-bottom:20px;"><i class="fa-solid fa-guitar"></i> Únete hoy</div>
        <h2>¿Listo para hacer crecer tu carrera?</h2>
        <p>Crea tu perfil artístico en Armonihz y empieza a ser descubierto por clientes que buscan músicos como tú.</p>
        <div class="cta-btns">
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="btn-primary">
                    <i class="fa-solid fa-user-plus"></i>
                    Crear mi cuenta gratis
                </a>
            @endif
            @if(Route::has('login'))
                <a href="{{ route('login') }}" class="btn-secondary">
                    <i class="fa-solid fa-sign-in-alt"></i>
                    Ya soy músico en Armonihz
                </a>
            @endif
        </div>
        <p class="cta-note"><i class="fa-solid fa-shield-halved" style="margin-right:4px;"></i> Gratis para siempre · Sin tarjeta de crédito · Cancela cuando quieras</p>
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
        window.addEventListener('scroll', () => {
            document.querySelector('.nav').style.background =
                window.scrollY > 40 ? 'rgba(15,10,30,.97)' : 'rgba(15,10,30,.8)';
        });
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const t = document.querySelector(a.getAttribute('href'));
                if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
            });
        });
    </script>
</body>
</html>
