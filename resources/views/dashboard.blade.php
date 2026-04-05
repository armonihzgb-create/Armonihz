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
@endsection