<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profile->stage_name ?? $user->name }} — Armonihz</title>
    <meta name="description" content="{{ Str::limit($profile->bio ?? 'Músico profesional en Armonihz.', 155) }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --purple: #6c3fc5;
            --blue:   #2f93f5;
            --green:  #22c55e;
            --bg:     #f4f6fb;
            --card:   #ffffff;
            --text:   #111827;
            --dim:    #6b7280;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── TOP BAR ── */
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 800;
            color: var(--purple);
            text-decoration: none;
        }
        .logo svg { width: 28px; height: 28px; }
        .topbar-actions { display: flex; gap: 10px; align-items: center; }
        .btn-outline {
            padding: 8px 18px;
            border: 1.5px solid var(--purple);
            border-radius: 8px;
            color: var(--purple);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-outline:hover { background: var(--purple); color: #fff; }
        .btn-solid {
            padding: 8px 18px;
            background: linear-gradient(135deg, var(--purple), var(--blue));
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: opacity .2s;
        }
        .btn-solid:hover { opacity: .85; }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #1a0b38 0%, #0d2452 100%);
            padding: 60px 32px 80px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('https://images.unsplash.com/photo-1501386761578-eac5c94b800a?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
            opacity: .12;
        }
        .hero-content {
            max-width: 860px;
            margin: 0 auto;
            position: relative;
            display: flex;
            align-items: center;
            gap: 32px;
            flex-wrap: wrap;
        }
        .hero-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,.3);
            object-fit: cover;
            flex-shrink: 0;
            box-shadow: 0 8px 32px rgba(0,0,0,.4);
        }
        .hero-avatar-initial {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,.3);
            background: linear-gradient(135deg, var(--purple), var(--blue));
            color: #fff;
            font-size: 42px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .hero-info { flex: 1; }
        .hero-info h1 {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 8px;
        }
        .hero-meta {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }
        .hero-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: rgba(255,255,255,.75);
        }
        .hero-meta svg { width: 15px; height: 15px; }
        .genre-chips { display: flex; gap: 8px; flex-wrap: wrap; }
        .chip {
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(255,255,255,.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,.25);
            backdrop-filter: blur(4px);
        }
        .hero-rate {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(34,197,94,.15);
            border: 1px solid rgba(34,197,94,.35);
            color: #4ade80;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
        }

        /* ── MAIN LAYOUT ── */
        .main {
            max-width: 860px;
            margin: -32px auto 60px;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 24px;
            position: relative;
        }

        /* ── CARDS ── */
        .card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-header svg { width: 18px; height: 18px; color: var(--purple); }
        .card-header h3 { font-size: 15px; font-weight: 700; }
        .card-body { padding: 20px 24px; }

        /* Bio */
        .bio-text {
            font-size: 15px;
            line-height: 1.75;
            color: #374151;
        }
        .bio-empty {
            font-size: 14px;
            color: var(--dim);
            font-style: italic;
        }

        /* Contact card */
        .contact-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }
        .contact-row:last-child { border-bottom: none; }
        .contact-row svg { width: 16px; height: 16px; color: var(--purple); flex-shrink: 0; }
        .contact-label { color: var(--dim); font-size: 12px; font-weight: 600; display: block; }
        .contact-value { color: var(--text); font-weight: 500; }
        .contact-value a { color: var(--purple); text-decoration: none; font-weight: 600; }
        .contact-value a:hover { text-decoration: underline; }

        /* CTA card */
        .cta-card {
            background: linear-gradient(135deg, var(--purple), var(--blue));
            border-radius: 16px;
            padding: 28px 24px;
            text-align: center;
            color: #fff;
        }
        .cta-card h3 { font-size: 18px; font-weight: 800; margin-bottom: 8px; }
        .cta-card p { font-size: 13px; opacity: .8; margin-bottom: 20px; line-height: 1.5; }
        .cta-btn {
            display: block;
            background: #fff;
            color: var(--purple);
            font-weight: 700;
            font-size: 14px;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            transition: transform .2s;
        }
        .cta-btn:hover { transform: translateY(-2px); }

        /* Rating placeholder */
        .rating-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fefce8;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 8px;
            margin-top: 16px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 32px 16px;
            font-size: 13px;
            color: var(--dim);
        }
        .footer a { color: var(--purple); text-decoration: none; font-weight: 600; }

        @media (max-width: 660px) {
            .main { grid-template-columns: 1fr; }
            .hero-content { flex-direction: column; align-items: flex-start; }
            .topbar { padding: 12px 16px; }
            .hero { padding: 40px 16px 70px; }
        }
    </style>
</head>
<body>

    {{-- ── TOP BAR ── --}}
    <nav class="topbar">
        <a href="{{ url('/') }}" class="logo">
            <svg viewBox="0 0 40 40" fill="none">
                <circle cx="20" cy="20" r="20" fill="#6c3fc5"/>
                <path d="M12 27V16l8-4 8 4v11l-8 4-8-4z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
                <circle cx="20" cy="20" r="3" fill="#fff"/>
            </svg>
            Armonihz
        </a>
        <div class="topbar-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-outline">Mi Panel</a>
            @else
                <a href="{{ route('login') }}" class="btn-outline">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn-solid">Registrarse</a>
            @endauth
        </div>
    </nav>

    {{-- ── HERO ── --}}
    <section class="hero">
        <div class="hero-content">
            @if($profile->profile_picture)
                <img src="{{ $profile->profilePictureUrl() }}"
                     alt="{{ $profile->stage_name }}" class="hero-avatar">
            @else
                <div class="hero-avatar-initial">
                    {{ strtoupper(substr($profile->stage_name ?? $user->name, 0, 2)) }}
                </div>
            @endif

            <div class="hero-info">
                <h1>{{ $profile->stage_name ?? $user->name }}</h1>
                <div class="hero-meta">
                    @if($profile->location)
                        <span>
                            <i data-lucide="map-pin"></i>
                            {{ $profile->location }}
                        </span>
                    @endif
                    <span>
                        <i data-lucide="music"></i>
                        Músico profesional
                    </span>
                </div>
                <div class="genre-chips">
                    @forelse($profile->genres as $genre)
                        <span class="chip">{{ $genre->name }}</span>
                    @empty
                        <span class="chip">Sin género</span>
                    @endforelse
                </div>
                @if($profile->hourly_rate)
                    <div class="hero-rate">
                        <i data-lucide="dollar-sign" style="width:16px;height:16px;"></i>
                        Desde ${{ number_format($profile->hourly_rate, 0) }} MXN / hora
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ── MAIN GRID ── --}}
    <div class="main">

        {{-- COLUMNA IZQUIERDA --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Acerca de --}}
            <div class="card">
                <div class="card-header">
                    <i data-lucide="user"></i>
                    <h3>Acerca de</h3>
                </div>
                <div class="card-body">
                    @if($profile->bio)
                        <p class="bio-text">{{ $profile->bio }}</p>
                    @else
                        <p class="bio-empty">Este músico aún no ha agregado una descripción.</p>
                    @endif

                    <div class="rating-badge">
                        ⭐ 5.0 · Sin reseñas aún
                    </div>
                </div>
            </div>

            {{-- Géneros / Servicios --}}
            <div class="card">
                <div class="card-header">
                    <i data-lucide="music-2"></i>
                    <h3>Servicios Destacados</h3>
                </div>
                <div class="card-body">
                    
                    @if(isset($profile->groupTypes) && $profile->groupTypes->count())
                    <div style="margin-bottom:16px;">
                        <h4 style="font-size:13px;color:var(--dim);text-transform:uppercase;letter-spacing:0.5px;font-weight:700;margin-bottom:8px;"><i data-lucide="users" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Agrupación</h4>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            @foreach($profile->groupTypes as $group)
                                <span style="
                                    background:linear-gradient(135deg,rgba(217,119,6,.1),rgba(245,158,11,.1));
                                    border:1px solid rgba(217,119,6,.18);
                                    color:#b45309;
                                    padding:4px 12px;
                                    border-radius:999px;
                                    font-size:12px;
                                    font-weight:600;
                                ">{{ $group->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($profile->eventTypes) && $profile->eventTypes->count())
                    <div style="margin-bottom:16px;">
                        <h4 style="font-size:13px;color:var(--dim);text-transform:uppercase;letter-spacing:0.5px;font-weight:700;margin-bottom:8px;"><i data-lucide="calendar-heart" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Tipos de Evento</h4>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            @foreach($profile->eventTypes as $event)
                                <span style="
                                    background:linear-gradient(135deg,rgba(22,163,74,.1),rgba(34,197,94,.1));
                                    border:1px solid rgba(22,163,74,.18);
                                    color:#15803d;
                                    padding:4px 12px;
                                    border-radius:999px;
                                    font-size:12px;
                                    font-weight:600;
                                ">{{ $event->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($profile->genres->count())
                    <div>
                        <h4 style="font-size:13px;color:var(--dim);text-transform:uppercase;letter-spacing:0.5px;font-weight:700;margin-bottom:8px;"><i data-lucide="music" style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Géneros Musicales</h4>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            @foreach($profile->genres as $genre)
                                <span style="
                                    background:rgba(244,246,251,1);
                                    border:1px solid var(--border);
                                    color:var(--text);
                                    padding:4px 12px;
                                    border-radius:999px;
                                    font-size:12px;
                                    font-weight:600;
                                ">{{ $genre->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            {{-- Zona de cobertura --}}
            @if($profile->location || $profile->coverage_notes)
            <div class="card">
                <div class="card-header">
                    <i data-lucide="map-pin"></i>
                    <h3>Zona de Cobertura</h3>
                </div>
                <div class="card-body">
                    <p style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:8px;">
                        📍 {{ $profile->location ?? 'No especificada' }}
                    </p>
                    @if($profile->coverage_notes)
                        <p style="font-size:14px;color:var(--dim);line-height:1.6;">
                            {{ $profile->coverage_notes }}
                        </p>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- COLUMNA DERECHA (sidebar) --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- CTA --}}
            <div class="cta-card">
                <h3>¿Te interesa contratar?</h3>
                <p>Regístrate o inicia sesión para enviar una solicitud de contratación a este músico.</p>
                @auth
                    <a href="#" class="cta-btn">Enviar solicitud</a>
                @else
                    <a href="{{ route('register') }}" class="cta-btn">Crear cuenta gratis</a>
                    <a href="{{ route('login') }}" style="display:block;margin-top:10px;font-size:13px;opacity:.8;color:#fff;text-decoration:underline;">
                        Ya tengo cuenta
                    </a>
                @endauth
            </div>

            {{-- Contacto --}}
            <div class="card">
                <div class="card-header">
                    <i data-lucide="contact"></i>
                    <h3>Contacto</h3>
                </div>
                <div class="card-body" style="padding:8px 24px 16px;">
                    @if($profile->phone)
                    <div class="contact-row">
                        <i data-lucide="phone"></i>
                        <div>
                            <span class="contact-label">TELÉFONO / WHATSAPP</span>
                            <span class="contact-value">
                                <a href="https://wa.me/{{ preg_replace('/\D/','',$profile->phone) }}" target="_blank">
                                    {{ $profile->phone }}
                                </a>
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($profile->instagram)
                    <div class="contact-row">
                        <i data-lucide="instagram"></i>
                        <div>
                            <span class="contact-label">INSTAGRAM</span>
                            <span class="contact-value">
                                <a href="https://instagram.com/{{ ltrim($profile->instagram,'@') }}" target="_blank">
                                    {{ '@' . ltrim($profile->instagram,'@') }}
                                </a>
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($profile->facebook)
                    <div class="contact-row">
                        <i data-lucide="facebook"></i>
                        <div>
                            <span class="contact-label">FACEBOOK</span>
                            <span class="contact-value">
                                <a href="{{ $profile->facebook }}" target="_blank">Ver página</a>
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($profile->youtube)
                    <div class="contact-row">
                        <i data-lucide="youtube"></i>
                        <div>
                            <span class="contact-label">YOUTUBE</span>
                            <span class="contact-value">
                                <a href="{{ $profile->youtube }}" target="_blank">Ver canal</a>
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($profile->tiktok)
                    <div class="contact-row">
                        <i class="fa-brands fa-tiktok" style="font-size:16px;color:var(--purple);flex-shrink:0;"></i>
                        <div>
                            <span class="contact-label">TIKTOK</span>
                            <span class="contact-value">
                                <a href="{{ $profile->tiktok }}" target="_blank">Ver perfil</a>
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($profile->spotify)
                    <div class="contact-row">
                        <i class="fa-brands fa-spotify" style="font-size:16px;color:var(--purple);flex-shrink:0;"></i>
                        <div>
                            <span class="contact-label">SPOTIFY</span>
                            <span class="contact-value">
                                <a href="{{ $profile->spotify }}" target="_blank">Ver perfil</a>
                            </span>
                        </div>
                    </div>
                    @endif

                    @if(!$profile->phone && !$profile->instagram && !$profile->facebook && !$profile->youtube && !$profile->tiktok && !$profile->spotify)
                    <p style="font-size:13px;color:var(--dim);padding:8px 0;font-style:italic;">
                        Músico no ha agregado información de contacto.
                    </p>
                    @endif
                </div>
            </div>

            {{-- Tarifa --}}
            @if($profile->hourly_rate)
            <div class="card">
                <div class="card-header">
                    <i data-lucide="dollar-sign"></i>
                    <h3>Tarifa</h3>
                </div>
                <div class="card-body" style="text-align:center;">
                    <p style="font-size:28px;font-weight:800;color:var(--purple);">
                        ${{ number_format($profile->hourly_rate, 0) }} <span style="font-size:14px;font-weight:500;color:var(--dim);">MXN</span>
                    </p>
                    <p style="font-size:13px;color:var(--dim);margin-top:4px;">por hora / evento</p>
                </div>
            </div>
            @endif

        </div>
    </div>

    <footer class="footer">
        <div>Perfil en <a href="{{ url('/') }}">Armonihz</a> · La plataforma que conecta músicos con eventos</div>
        <div style="margin-top: 10px; font-size: 11px; opacity: 0.7;">
            <a href="{{ route('legal.privacidad') }}" style="margin: 0 8px;">Privacidad</a> ·
            <a href="{{ route('legal.terminos') }}" style="margin: 0 8px;">Términos</a> ·
            <a href="{{ route('legal.ayuda') }}" style="margin: 0 8px;">Ayuda</a>
        </div>
    </footer>

    <script>lucide.createIcons();</script>

</body>
</html>
