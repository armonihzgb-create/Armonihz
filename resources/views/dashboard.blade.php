@extends('layouts.dashboard')

@section('dashboard-content')
    <header class="dashboard-header"
        style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div class="stat-icon purple" style="width:56px; height:56px; flex-shrink:0;">
                <i data-lucide="sparkles" style="width:24px; height:24px;"></i>
            </div>
            <div>
                <h2 style="margin:0;">Bienvenido,
                    {{ ($user->role === 'musico' && $user->musicianProfile) ? ($user->musicianProfile->stage_name ?? $user->name) : $user->name }}
                </h2>
                <p class="dashboard-subtitle" style="margin:0;">Aquí tienes un resumen de tu actividad reciente</p>
            </div>
        </div>
        @if($user->role === 'musico' && $user->musicianProfile && $user->musicianProfile->profile_picture)
            @if(Str::startsWith($user->musicianProfile->profile_picture, ['http://', 'https://']))
                <img src="{{ $user->musicianProfile->profile_picture }}" alt="Perfil"
                    style="width:64px; height:64px; border-radius:50%; object-fit:cover; border: 2px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
            @else
                <img src="{{ $user->musicianProfile->profilePictureUrl() }}" alt="Perfil"
                    style="width:64px; height:64px; border-radius:50%; object-fit:cover; border: 2px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
            @endif
        @else
            <div
                style="width:64px; height:64px; border-radius:50%; background:var(--bg-secondary); border: 2px solid #e5e7eb; display:flex; align-items:center; justify-content:center; color:var(--text-dim);">
                <i data-lucide="user" style="width:28px; height:28px;"></i>
            </div>
        @endif
    </header>

    {{-- TARJETAS DE ESTADÍSTICAS --}}
    <div class="stats-grid">
        <div class="card stat-card hover-lift">
            <div class="stat-icon blue">
                <i data-lucide="bell"></i>
            </div>
            <div>
                <h4>Solicitudes pendientes</h4>
                <span class="stat-number">{{ $stats['pending_requests'] }}</span>
                <p class="stat-meta">Sin responder</p>
            </div>
        </div>

        <div class="card stat-card hover-lift">
            <div class="stat-icon orange">
                <i data-lucide="calendar-check-2"></i>
            </div>
            <div>
                <h4>Contratos Aceptados</h4>
                <span class="stat-number">{{ $stats['accepted_requests'] }}</span>
                <p class="stat-meta">Eventos confirmados</p>
            </div>
        </div>

        <div class="card stat-card hover-lift">
            <div class="stat-icon green">
                <i data-lucide="check-circle"></i>
            </div>
            <div>
                <h4>Perfil</h4>
                <span class="stat-number">{{ $stats['profile_completion'] }}%</span>
                <p class="stat-meta">Completado</p>
            </div>
        </div>

        <div class="card stat-card hover-lift">
            <div class="stat-icon purple" style="background: rgba(179, 1, 255, 0.1); color: var(--accent-purple);">
                <i data-lucide="megaphone"></i>
            </div>
            <div>
                <h4>Vistas en Promoción</h4>
                <span class="stat-number">{{ $stats['promo_views'] ?? 0 }}</span>
                <p class="stat-meta">Campañas activas</p>
            </div>
        </div>

        <div class="card stat-card hover-lift">
            <div class="stat-icon" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">
                <i data-lucide="mouse-pointer-2"></i>
            </div>
            <div>
                <h4>Vistas de Perfil</h4>
                <span class="stat-number">{{ $stats['profile_views'] }}</span>
                <p class="stat-meta">Desde app móvil</p>
            </div>
        </div>
    </div>

    {{-- SECCIÓN INFERIOR (2 COLUMNAS) --}}
    <div class="content-grid">

        {{-- ACTIVIDAD RECIENTE desde HiringRequests --}}
        <div class="dashboard-box">
            <div class="box-header">
                <h3>Actividad reciente</h3>
                @if($recentRequests->isNotEmpty())
                    <a href="{{ route('requests.index') }}"
                        style="font-size: 13px; color: var(--accent-blue); text-decoration: none;">
                        Ver todas →
                    </a>
                @endif
            </div>

            @if($recentRequests->isEmpty() && $recentActivity->isEmpty())
                <p style="color: var(--text-dim); padding: 16px 0; font-size: 14px;">
                    Aún no tienes actividad reciente.
                </p>
            @else
                <ul class="activity-list">
                    {{-- Solicitudes de contratación recientes --}}
                    @foreach($recentRequests as $req)
                        <li>
                            <span class="dot {{ 
                                            $req->status === 'accepted' ? 'green' :
                        ($req->status === 'completed' ? 'green' :
                            ($req->status === 'rejected' ? 'red' :
                                ($req->status === 'cancelled' ? 'red' : 'blue'))) 
                                        }}"></span>
                            <div>
                                @if($user->role === 'musico')
                                    <strong>
                                        @if($req->status === 'pending') Nueva solicitud
                                        @elseif($req->status === 'accepted') Solicitud aceptada
                                        @elseif($req->status === 'completed') Evento finalizado
                                        @elseif($req->status === 'cancelled') Solicitud cancelada
                                        @elseif($req->status === 'rejected') Solicitud rechazada
                                        @elseif($req->status === 'counter_offer') Contraoferta enviada
                                        @else {{ ucfirst($req->status) }}
                                        @endif
                                    </strong>
                                    de {{ $req->client->name ?? 'Cliente' }}
                                    — <em style="font-size:12px; color:var(--text-dim);">{{ Str::limit($req->description, 40) }}</em>
                                @else
                                    Solicitud a <strong>{{ $req->musicianProfile->stage_name ?? 'Músico' }}</strong>
                                    — Estado: <strong>
                                        @if($req->status === 'pending') Pendiente
                                        @elseif($req->status === 'accepted') Aceptada
                                        @elseif($req->status === 'completed') Finalizado
                                        @elseif($req->status === 'cancelled') Cancelado
                                        @elseif($req->status === 'rejected') Rechazada
                                        @elseif($req->status === 'counter_offer') Contraoferta
                                        @else {{ ucfirst($req->status) }}
                                        @endif
                                    </strong>
                                @endif
                                <span class="time">{{ $req->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach

                    {{-- Notificaciones del sistema --}}
                    @foreach($recentActivity as $notification)
                        <li>
                            <span class="dot orange"></span>
                            <div>
                                {{ $notification->data['message'] ?? 'Nueva notificación' }}
                                <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- RECOMENDACIONES DINÁMICAS según estado del perfil --}}
        <div class="dashboard-box">
            <div class="box-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;">Próximos Eventos</h3>
                @if(isset($upcomingEvents) && $upcomingEvents->isNotEmpty())
                    <span
                        style="font-size:12px; background:var(--bg-secondary); padding:4px 8px; border-radius:12px; font-weight:600;">{{ $upcomingEvents->count() }}
                        Confirmados</span>
                @endif
            </div>

            @if(!isset($upcomingEvents) || $upcomingEvents->isEmpty())
                <div style="text-align:center; padding: 24px 0; color:var(--text-dim);">
                    <i data-lucide="calendar-x-2" style="width:32px; height:32px; opacity:0.5; margin-bottom:12px;"></i>
                    <p style="margin:0; font-size:14px;">No tienes eventos próximos programados.</p>
                </div>
            @else
                <ul class="activity-list">
                    @foreach($upcomingEvents as $event)
                        <li style="align-items:flex-start;">
                            <div
                                style="background:var(--bg-secondary); border-radius:8px; padding:8px 12px; text-align:center; min-width:60px; border:1px solid var(--border-light);">
                                <span
                                    style="display:block; font-size:11px; font-weight:700; color:var(--accent-orange); text-transform:uppercase;">{{ $event->date->translatedFormat('M') }}</span>
                                <span
                                    style="display:block; font-size:18px; font-weight:800; color:var(--text-main); line-height:1.2;">{{ $event->date->format('d') }}</span>
                            </div>
                            <div style="flex-grow:1;">
                                <strong
                                    style="display:flex; align-items:center; gap:6px; color:var(--text-main); margin-bottom:4px;">
                                    @if($event->type === 'casting')
                                        <i data-lucide="mic-2" style="width:14px;height:14px;color:var(--accent-purple);"></i>
                                    @elseif($event->type === 'blocked')
                                        <i data-lucide="lock" style="width:14px;height:14px;color:#94a3b8;"></i>
                                    @else
                                        <i data-lucide="user-check" style="width:14px;height:14px;color:var(--accent-blue);"></i>
                                    @endif
                                    {{ Str::limit($event->title, 40) }}
                                </strong>
                                <span
                                    style="display:flex; align-items:center; gap:4px; font-size:12px; color:var(--text-dim); margin-bottom:4px;">
                                    <i data-lucide="map-pin" style="width:12px;height:12px;"></i>
                                    {{ Str::limit($event->location, 35) }}
                                </span>
                                <span style="display:flex; align-items:center; gap:4px; font-size:12px; color:var(--text-dim);">
                                    <i data-lucide="clock" style="width:12px;height:12px;"></i> {{ $event->date->format('H:i') }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
    MODAL DE BIENVENIDA - Solo aparece al iniciar sesión
    ═══════════════════════════════════════════════════════ --}}
    @if(session('show_welcome_modal') && $user->role === 'musico')
        <style>
            @keyframes wm-fadein {
                from {
                    opacity: 0
                }

                to {
                    opacity: 1
                }
            }

            @keyframes wm-slidein {
                from {
                    opacity: 0;
                    transform: translateY(20px) scale(.97)
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1)
                }
            }

            #welcome-overlay {
                position: fixed;
                inset: 0;
                z-index: 9999;
                background: rgba(8, 8, 20, 0.55);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
                animation: wm-fadein .3s ease;
            }

            #welcome-modal {
                background: rgba(255, 255, 255, 0.82);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.6);
                border-radius: 22px;
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.14), 0 1px 0 rgba(255, 255, 255, 0.9) inset;
                max-width: 500px;
                width: 100%;
                max-height: 90vh;
                overflow-y: auto;
                padding: 32px 28px 24px;
                position: relative;
                animation: wm-slidein .4s cubic-bezier(0.34, 1.45, 0.64, 1);
            }

            #welcome-modal::-webkit-scrollbar {
                width: 4px;
            }

            #welcome-modal::-webkit-scrollbar-thumb {
                background: rgba(108, 63, 197, 0.25);
                border-radius: 4px;
            }

            .wm-icon-wrap {
                width: 52px;
                height: 52px;
                border-radius: 16px;
                background: rgba(108, 63, 197, 0.1);
                border: 1px solid rgba(108, 63, 197, 0.15);
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
            }

            .wm-features {
                list-style: none;
                margin: 0;
                padding: 0;
                display: flex;
                flex-direction: column;
                gap: 9px;
            }

            .wm-feature-item {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 13.5px;
                color: #374151;
                line-height: 1.45;
            }

            .wm-feature-icon {
                width: 32px;
                height: 32px;
                border-radius: 9px;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .wm-tip-card {
                display: flex;
                align-items: center;
                gap: 12px;
                background: rgba(255, 255, 255, 0.7);
                border: 1px solid rgba(0, 0, 0, 0.06);
                border-radius: 12px;
                padding: 12px 14px;
                backdrop-filter: blur(6px);
            }

            .wm-tip-icon {
                width: 34px;
                height: 34px;
                border-radius: 9px;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .wm-tip-action {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 5px 11px;
                border-radius: 7px;
                font-size: 12px;
                font-weight: 600;
                text-decoration: none;
                white-space: nowrap;
                flex-shrink: 0;
                transition: opacity .2s;
            }

            .wm-tip-action:hover {
                opacity: .75;
            }

            .wm-btn-secondary {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 10px 20px;
                border-radius: 11px;
                border: 1.5px solid #e2e8f0;
                background: rgba(255, 255, 255, 0.7);
                color: #475569;
                font-size: 13.5px;
                font-weight: 600;
                cursor: pointer;
                transition: border-color .2s, background .2s;
            }

            .wm-btn-secondary:hover {
                border-color: #c7d2e0;
                background: #fff;
            }

            .wm-btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 10px 20px;
                border-radius: 11px;
                background: #6c3fc5;
                border: none;
                color: #fff;
                font-size: 13.5px;
                font-weight: 600;
                text-decoration: none;
                transition: opacity .2s;
            }

            .wm-btn-primary:hover {
                opacity: .85;
            }

            .wm-close-btn {
                position: absolute;
                top: 14px;
                right: 14px;
                background: rgba(0, 0, 0, 0.04);
                border: none;
                cursor: pointer;
                color: #94a3b8;
                padding: 6px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background .2s, color .2s;
            }

            .wm-close-btn:hover {
                background: rgba(0, 0, 0, 0.08);
                color: #475569;
            }

            .wm-divider {
                height: 1px;
                background: rgba(0, 0, 0, 0.07);
                margin: 18px 0;
            }

            .wm-section-label {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .9px;
                margin: 0 0 10px;
            }
        </style>

        <div id="welcome-overlay">
            <div id="welcome-modal">

                {{-- Botón X --}}
                <button onclick="closeWelcomeModal()" class="wm-close-btn">
                    <i data-lucide="x" style="width:18px;height:18px;"></i>
                </button>

                {{-- Header --}}
                <div style="text-align:center; margin-bottom:22px;">
                    <div class="wm-icon-wrap">
                        <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz"
                            style="width:28px; height:28px; object-fit:contain;">
                    </div>
                    <h2 style="margin:0 0 6px;font-size:20px;font-weight:800;color:#0f172a;letter-spacing:-.3px;">
                        ¡Bienvenido, {{ $user->musicianProfile->stage_name ?? $user->name }}!
                    </h2>
                    <p style="margin:0;font-size:13.5px;color:#64748b;line-height:1.55;">
                        Aquí tienes un resumen de lo que puedes hacer en tu panel.
                    </p>
                </div>

                <div class="wm-divider"></div>

                {{-- Funcionalidades del sistema --}}
                <p class="wm-section-label" style="color:#6c3fc5;">¿Qué puedes hacer aquí?</p>
                <ul class="wm-features">
                    <li class="wm-feature-item">
                        <div class="wm-feature-icon" style="background:rgba(108,63,197,0.08);">
                            <i data-lucide="bell" style="width:15px;height:15px;color:#6c3fc5;"></i>
                        </div>
                        Recibe y gestiona <strong style="margin-left:3px;">solicitudes de contratación</strong>&nbsp;de la app
                        móvil.
                    </li>
                    <li class="wm-feature-item">
                        <div class="wm-feature-icon" style="background:rgba(47,147,245,0.08);">
                            <i data-lucide="calendar-days" style="width:15px;height:15px;color:#2f93f5;"></i>
                        </div>
                        Administra tu <strong style="margin-left:3px;">calendario de disponibilidad</strong>&nbsp;y bloquea
                        fechas.
                    </li>
                    <li class="wm-feature-item">
                        <div class="wm-feature-icon" style="background:rgba(245,158,11,0.08);">
                            <i data-lucide="star" style="width:15px;height:15px;color:#f59e0b;"></i>
                        </div>
                        Consulta las <strong style="margin-left:3px;">reseñas y calificaciones</strong>&nbsp;de tus actuaciones.
                    </li>
                    <li class="wm-feature-item">
                        <div class="wm-feature-icon" style="background:rgba(16,185,129,0.08);">
                            <i data-lucide="user-circle" style="width:15px;height:15px;color:#10b981;"></i>
                        </div>
                        Optimiza tu <strong style="margin-left:3px;">perfil público</strong>&nbsp;para destacar entre otros
                        músicos.
                    </li>
                </ul>

                {{-- Sugerencias dinámicas --}}
                @if(count($welcomeTips) > 0)
                    <div class="wm-divider"></div>
                    <p class="wm-section-label" style="color:#dc2626;">
                        <i data-lucide="alert-circle"
                            style="width:12px;height:12px;display:inline-block;vertical-align:middle;margin-right:4px;"></i>
                        Acciones recomendadas
                    </p>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($welcomeTips as $tip)
                            <div class="wm-tip-card">
                                <div class="wm-tip-icon" style="background:{{ $tip['color'] }}14;">
                                    <i data-lucide="{{ $tip['icon'] }}" style="width:15px;height:15px;color:{{ $tip['color'] }};"></i>
                                </div>
                                <p style="margin:0;font-size:13px;color:#475569;flex-grow:1;line-height:1.45;">
                                    {!! $tip['message'] !!}
                                </p>
                                <a href="{{ $tip['action'] }}" class="wm-tip-action"
                                    style="background:{{ $tip['color'] }}12;color:{{ $tip['color'] }};">
                                    <i data-lucide="arrow-right" style="width:11px;height:11px;"></i>
                                    {{ $tip['label'] }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Perfil completo --}}
                    <div class="wm-divider"></div>
                    <div style="
                            background:rgba(240,253,244,0.8); border:1px solid #a7f3d0;
                            border-radius:12px; padding:14px 16px;
                            display:flex; align-items:center; gap:12px;
                        ">
                        <i data-lucide="badge-check" style="width:24px;height:24px;color:#16a34a;flex-shrink:0;"></i>
                        <div>
                            <p style="margin:0 0 2px;font-weight:700;font-size:13.5px;color:#15803d;">¡Tu perfil está completo!</p>
                            <p style="margin:0;font-size:12.5px;color:#166534;">Estás listo para recibir solicitudes de
                                contratación.</p>
                        </div>
                    </div>
                @endif

                {{-- Botones --}}
                <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin-top:22px;">
                    <button onclick="closeWelcomeModal()" class="wm-btn-secondary">
                        <i data-lucide="layout-dashboard" style="width:15px;height:15px;"></i>
                        Ir al panel
                    </button>
                    <a href="{{ route('profile') }}" class="wm-btn-primary">
                        <i data-lucide="user-pen" style="width:15px;height:15px;"></i>
                        Completar mi perfil
                    </a>
                </div>
            </div>
        </div>

        <script>
            function closeWelcomeModal() {
                const overlay = document.getElementById('welcome-overlay');
                overlay.style.opacity = '0';
                overlay.style.transition = 'opacity .2s ease';
                setTimeout(() => overlay.remove(), 200);
            }
            document.getElementById('welcome-overlay').addEventListener('click', function (e) {
                if (e.target === this) closeWelcomeModal();
            });
            if (window.lucide) lucide.createIcons();
        </script>
    @endif

@endsection