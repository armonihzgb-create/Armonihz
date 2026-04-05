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
                                $req->status === 'accepted'  ? 'green'  : 
                                ($req->status === 'completed' ? 'green'  : 
                                ($req->status === 'rejected'  ? 'red'    : 
                                ($req->status === 'cancelled' ? 'red'    : 'blue'))) 
                            }}"></span>
                            <div>
                                @if($user->role === 'musico')
                                    <strong>
                                        @if($req->status === 'pending')     Nueva solicitud
                                        @elseif($req->status === 'accepted')   Solicitud aceptada
                                        @elseif($req->status === 'completed')  Evento finalizado
                                        @elseif($req->status === 'cancelled')  Solicitud cancelada
                                        @elseif($req->status === 'rejected')   Solicitud rechazada
                                        @else {{ ucfirst($req->status) }}
                                        @endif
                                    </strong>
                                    de {{ $req->client->name ?? 'Cliente' }}
                                    — <em style="font-size:12px; color:var(--text-dim);">{{ Str::limit($req->description, 40) }}</em>
                                @else
                                    Solicitud a <strong>{{ $req->musicianProfile->stage_name ?? 'Músico' }}</strong>
                                    — Estado: <strong>
                                        @if($req->status === 'pending')     Pendiente
                                        @elseif($req->status === 'accepted')   Aceptada
                                        @elseif($req->status === 'completed')  Finalizado
                                        @elseif($req->status === 'cancelled')  Cancelado
                                        @elseif($req->status === 'rejected')   Rechazada
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
    <div id="welcome-overlay" style="
        position:fixed; inset:0; z-index:9999;
        background:rgba(10,10,30,0.65);
        backdrop-filter:blur(6px);
        display:flex; align-items:center; justify-content:center;
        padding:16px;
        animation: wm-fadein 0.35s ease;
    ">
        <div id="welcome-modal" style="
            background:linear-gradient(145deg,#ffffff,#f5f3ff);
            border-radius:20px;
            box-shadow:0 32px 80px rgba(108,63,197,0.22), 0 4px 16px rgba(0,0,0,0.08);
            max-width:520px; width:100%;
            max-height:90vh; overflow-y:auto;
            padding:36px 32px 28px;
            position:relative;
            animation: wm-slidein 0.4s cubic-bezier(0.34,1.56,0.64,1);
        ">
            {{-- Header --}}
            <div style="text-align:center; margin-bottom:24px;">
                <div style="
                    width:64px; height:64px; border-radius:50%;
                    background:linear-gradient(135deg,#6c3fc5,#2f93f5);
                    display:flex; align-items:center; justify-content:center;
                    margin:0 auto 16px;
                    box-shadow:0 8px 24px rgba(108,63,197,0.35);
                ">
                    <i data-lucide="music-2" style="width:28px;height:28px;color:#fff;"></i>
                </div>
                <h2 style="margin:0 0 6px;font-size:22px;font-weight:800;color:#0f172a;">
                    ¡Bienvenido a Armonihz,
                    {{ $user->musicianProfile->stage_name ?? $user->name }}! 🎶
                </h2>
                <p style="margin:0;font-size:14px;color:#64748b;line-height:1.6;">
                    Tu cuenta fue aprobada. Aquí tienes un resumen de lo que puedes hacer en tu panel.
                </p>
            </div>

            {{-- Funcionalidades del sistema --}}
            <div style="
                background:#f8f5ff; border-radius:14px; padding:18px 20px;
                border:1px solid #ede9fe; margin-bottom:20px;
            ">
                <p style="margin:0 0 12px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#6c3fc5;">
                    ¿Qué puedes hacer aquí?
                </p>
                <ul style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:10px;">
                    <li style="display:flex;align-items:center;gap:12px;font-size:13.5px;color:#334155;">
                        <i data-lucide="bell" style="width:16px;height:16px;color:#6c3fc5;flex-shrink:0;"></i>
                        <span>Recibe y gestiona <strong>solicitudes de contratación</strong> de clientes de la app móvil.</span>
                    </li>
                    <li style="display:flex;align-items:center;gap:12px;font-size:13.5px;color:#334155;">
                        <i data-lucide="calendar" style="width:16px;height:16px;color:#2f93f5;flex-shrink:0;"></i>
                        <span>Administra tu <strong>calendario de disponibilidad</strong> para bloquear fechas ocupadas.</span>
                    </li>
                    <li style="display:flex;align-items:center;gap:12px;font-size:13.5px;color:#334155;">
                        <i data-lucide="star" style="width:16px;height:16px;color:#f59e0b;flex-shrink:0;"></i>
                        <span>Consulta las <strong>reseñas y calificaciones</strong> que los clientes dejan de tus actuaciones.</span>
                    </li>
                    <li style="display:flex;align-items:center;gap:12px;font-size:13.5px;color:#334155;">
                        <i data-lucide="user" style="width:16px;height:16px;color:#10b981;flex-shrink:0;"></i>
                        <span>Optimiza tu <strong>perfil público</strong> para destacar entre otros músicos.</span>
                    </li>
                </ul>
            </div>

            {{-- Sugerencias dinámicas --}}
            @if(count($welcomeTips) > 0)
            <div style="margin-bottom:20px;">
                <p style="margin:0 0 12px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#ef4444;">
                    🔔 Acciones recomendadas para tu perfil
                </p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($welcomeTips as $tip)
                    <div style="
                        display:flex; align-items:center; gap:14px;
                        background:#fff; border-radius:12px; padding:14px 16px;
                        border:1px solid #f1f5f9;
                        box-shadow:0 2px 8px rgba(0,0,0,0.04);
                    ">
                        <div style="
                            width:36px; height:36px; border-radius:10px; flex-shrink:0;
                            background:{{ $tip['color'] }}18;
                            display:flex; align-items:center; justify-content:center;
                        ">
                            <i data-lucide="{{ $tip['icon'] }}" style="width:16px;height:16px;color:{{ $tip['color'] }};"></i>
                        </div>
                        <p style="margin:0;font-size:13px;color:#475569;flex-grow:1;line-height:1.5;">
                            {!! $tip['message'] !!}
                        </p>
                        <a href="{{ $tip['action'] }}" style="
                            display:inline-block; padding:6px 12px; border-radius:8px;
                            background:{{ $tip['color'] }}18; color:{{ $tip['color'] }};
                            font-size:12px; font-weight:600; text-decoration:none;
                            white-space:nowrap; flex-shrink:0;
                            transition:background .2s;
                        ">{{ $tip['label'] }}</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            {{-- Perfil completo --}}
            <div style="
                background:linear-gradient(135deg,#f0fdf4,#dcfce7);
                border:1px solid #86efac; border-radius:14px;
                padding:16px 20px; margin-bottom:20px;
                display:flex; align-items:center; gap:14px;
            ">
                <i data-lucide="badge-check" style="width:28px;height:28px;color:#16a34a;flex-shrink:0;"></i>
                <div>
                    <p style="margin:0 0 2px;font-weight:700;font-size:14px;color:#15803d;">¡Tu perfil está completo! 🎉</p>
                    <p style="margin:0;font-size:13px;color:#166534;">Estás listo para recibir solicitudes de contratación. ¡Mucho éxito!</p>
                </div>
            </div>
            @endif

            {{-- Botones --}}
            <div style="display:flex;gap:12px;justify-content:flex-end;flex-wrap:wrap;">
                <button onclick="closeWelcomeModal()" style="
                    padding:10px 22px; border-radius:10px;
                    border:2px solid #e5e7eb; background:#fff;
                    color:#475569; font-size:14px; font-weight:600;
                    cursor:pointer; transition:all .2s;
                " onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e5e7eb'">
                    Ir al panel
                </button>
                <a href="{{ route('profile') }}" style="
                    padding:10px 22px; border-radius:10px;
                    background:linear-gradient(135deg,#6c3fc5,#2f93f5);
                    color:#fff; font-size:14px; font-weight:600;
                    text-decoration:none; transition:opacity .2s;
                " onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    ✏️ Completar mi perfil
                </a>
            </div>

            {{-- Botón X --}}
            <button onclick="closeWelcomeModal()" style="
                position:absolute; top:16px; right:16px;
                background:none; border:none; cursor:pointer;
                color:#94a3b8; padding:4px; border-radius:6px;
                transition:color .2s;
            " onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94a3b8'">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
    </div>

    <style>
        @keyframes wm-fadein  { from { opacity:0; }          to { opacity:1; } }
        @keyframes wm-slidein { from { opacity:0; transform:translateY(24px) scale(.96); } to { opacity:1; transform:translateY(0) scale(1); } }
        #welcome-modal::-webkit-scrollbar { width:4px; }
        #welcome-modal::-webkit-scrollbar-thumb { background:#d8b4fe; border-radius:4px; }
    </style>
    <script>
        function closeWelcomeModal() {
            const overlay = document.getElementById('welcome-overlay');
            overlay.style.animation = 'wm-fadein .2s ease reverse';
            setTimeout(() => overlay.remove(), 200);
        }
        // Cerrar al hacer clic en el overlay (fuera del modal)
        document.getElementById('welcome-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeWelcomeModal();
        });
        // Re-render lucide para los iconos del modal
        if (window.lucide) lucide.createIcons();
    </script>
    @endif

@endsection