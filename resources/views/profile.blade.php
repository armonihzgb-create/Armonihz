@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- ── CSS VARIABLES (pre-render) ────────── --}}
    <style>
        :root {
            --pr-primary:   #6c3fc5;
            --pr-primary-h: #5b32a8;
            --pr-blue:      #2f93f5;
            --pr-text:      #0f172a;
            --pr-dim:       #64748b;
            --pr-muted:     #94a3b8;
            --pr-border:    #e2e8f0;
            --pr-surface:   #f8fafc;
            --pr-success:   #16a34a;
        }
        /* Prevent FOUC on avatar overlap */
        .nbf-avatar-container { position:absolute; left:36px; top:-90px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,0.15); backdrop-filter:blur(10px); padding:8px; z-index:10; box-shadow:0 12px 40px rgba(0,0,0,.25); }
        .nbf-info-bar { display:flex; align-items:flex-start; padding:0 36px 28px 220px; position:relative; min-height:100px; }
        @media(max-width:768px){.nbf-info-bar{flex-direction:column;padding:100px 20px 24px;align-items:center;}.nbf-avatar-container{left:50%;transform:translateX(-50%)!important;top:-80px;}}
    </style>


    {{-- ── PERFIL PRINCIPAL ── --}}
    <div class="nbf-layout">

        {{-- 1. HEADER / COVER AREA --}}
        <div class="nbf-header">
            <div class="nbf-cover" style="background-image: url('https://images.unsplash.com/photo-1514525253161-7a46d19cd819?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">
                {{-- Overlay degradado moderno --}}
                <div class="nbf-cover-overlay"></div>
            </div>

            {{-- Info Bar (under cover) --}}
            <div class="nbf-info-bar">
                <div class="nbf-avatar-container">
                    @if($profile && $profile->profile_picture)
                        @if(Str::startsWith($profile->profile_picture, ['http://', 'https://']))
                            <img src="{{ $profile->profile_picture }}" alt="Foto de perfil">
                        @else
                            <img src="{{ $profile->profilePictureUrl() }}" alt="Foto de perfil">
                        @endif
                    @else
                        <div class="nbf-avatar-initials">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <div class="nbf-user-info">
                    <h1 class="nbf-name">{{ $profile->stage_name ?? $user->name }}</h1>
                    <div class="nbf-stats-links">
                        <span class="nbf-stat">{{ $acceptedRequests }} eventos</span>
                        <span class="nbf-dot">•</span>
                        <span class="nbf-stat">{{ $profile->profile_views ?? 0 }} vistas</span>
                        <span class="nbf-dot">•</span>
                        <span class="nbf-stat">{{ number_format($profile->averageRating(), 1) }} ⭐</span>
                    </div>
                </div>

                <div class="nbf-header-actions">
                    <button type="button" class="nbf-btn-primary" onclick="openEditModal()">
                        <i data-lucide="pencil" style="width:14px;height:14px;"></i> Editar perfil
                    </button>
                    <button type="button" class="nbf-btn-secondary" onclick="openPreviewModal()">
                        <i data-lucide="smartphone" style="width:14px;height:14px;"></i> Vista Previa
                    </button>
                </div>
            </div>
        </div>

        {{-- 2. CONTENT AREA --}}
        <div class="nbf-content">

            {{-- Profile Completion --}}
            <div class="nbf-completion-card">
                <div class="nbf-completion-icon {{ $completion >= 80 ? 'good' : ($completion >= 50 ? 'mid' : 'low') }}">
                    <i data-lucide="{{ $completion >= 80 ? 'check-circle' : 'alert-circle' }}" style="width:18px;height:18px;"></i>
                </div>
                <div class="nbf-completion-body">
                    <div class="nbf-completion-top">
                        <span class="nbf-completion-label">
                            @if($completion < 50) Completa tu perfil para recibir más contrataciones
                            @elseif($completion < 80) ¡Buen avance! Agrega más detalles para destacar
                            @else Tu perfil está listo para recibir solicitudes
                            @endif
                        </span>
                        <span class="nbf-completion-badge {{ $completion >= 80 ? 'good' : ($completion >= 50 ? 'mid' : 'low') }}">{{ $completion }}%</span>
                    </div>
                    <div class="nbf-completion-bar">
                        <div class="nbf-completion-fill" style="width:{{ $completion }}%;"></div>
                    </div>
                </div>
                @if($completion < 80)
                <button type="button" onclick="openEditModal()" class="nbf-completion-cta">Completar</button>
                @endif
            </div>

            {{-- Personal Details (Bento Grid) --}}
            <div class="nbf-section">
                <div class="nbf-section-header">
                    <div class="nbf-section-icon" style="background:linear-gradient(135deg, rgba(108,63,197,.15), rgba(47,147,245,.15));">
                        <i data-lucide="user-circle" style="width:18px;height:18px;color:#6c3fc5;"></i>
                    </div>
                    <h2 class="nbf-section-title">Datos Personales</h2>
                </div>

                <div class="nbf-bento-grid nbf-bento-grid-2">
                    <div class="nbf-bento-box">
                        <div class="nbf-bento-icon"><i data-lucide="badge"></i></div>
                        <div class="nbf-bento-content">
                            <span class="nbf-bento-label">Nombre Artístico</span>
                            <span class="nbf-bento-value">{{ $profile->stage_name ?? $user->name }}</span>
                        </div>
                    </div>
                    
                    <div class="nbf-bento-box">
                        <div class="nbf-bento-icon"><i data-lucide="mail"></i></div>
                        <div class="nbf-bento-content">
                            <span class="nbf-bento-label">Correo Electrónico</span>
                            <span class="nbf-bento-value">{{ $user->email }}</span>
                        </div>
                    </div>
                    
                    <div class="nbf-bento-box">
                        <div class="nbf-bento-icon"><i data-lucide="map-pin"></i></div>
                        <div class="nbf-bento-content">
                            <span class="nbf-bento-label">Ubicación</span>
                            <span class="nbf-bento-value {{ !$profile->location ? 'nbf-empty' : '' }}">
                                {{ $profile->location ?? 'Agrega tu ciudad' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="nbf-bento-box">
                        <div class="nbf-bento-icon"><i data-lucide="phone"></i></div>
                        <div class="nbf-bento-content">
                            <span class="nbf-bento-label">Teléfono / WhatsApp</span>
                            <span class="nbf-bento-value {{ !$profile->phone ? 'nbf-empty' : '' }}">
                                {{ $profile->phone ?? 'Agrega tu número' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Social Cards --}}
                <div class="nbf-social-grid">
                    <a href="{{ $profile->instagram ? 'https://instagram.com/'.ltrim($profile->instagram,'@') : '#' }}"
                       target="{{ $profile->instagram ? '_blank' : '_self' }}"
                       class="nbf-social-card {{ $profile->instagram ? 'active' : 'inactive' }}">
                        <div class="nbf-social-icon" style="background:rgba(225,48,108,0.1);">
                            <i class="fa-brands fa-instagram" style="color:#E1306C;font-size:18px;"></i>
                        </div>
                        <div class="nbf-social-info">
                            <span class="nbf-social-name">Instagram</span>
                            <span class="nbf-social-handle">{{ $profile->instagram ? '@'.ltrim($profile->instagram,'@') : 'No configurado' }}</span>
                        </div>
                        @if($profile->instagram)<i data-lucide="arrow-up-right" style="width:13px;height:13px;color:#94a3b8;margin-left:auto;flex-shrink:0;"></i>@endif
                    </a>
                    <a href="{{ $profile->facebook ? (Str::startsWith($profile->facebook,['http','https']) ? $profile->facebook : 'https://'.$profile->facebook) : '#' }}"
                       target="{{ $profile->facebook ? '_blank' : '_self' }}"
                       class="nbf-social-card {{ $profile->facebook ? 'active' : 'inactive' }}">
                        <div class="nbf-social-icon" style="background:rgba(24,119,242,0.1);">
                            <i class="fa-brands fa-facebook-f" style="color:#1877F2;font-size:18px;"></i>
                        </div>
                        <div class="nbf-social-info">
                            <span class="nbf-social-name">Facebook</span>
                            <span class="nbf-social-handle">{{ $profile->facebook ? 'Ver página' : 'No configurado' }}</span>
                        </div>
                        @if($profile->facebook)<i data-lucide="arrow-up-right" style="width:13px;height:13px;color:#94a3b8;margin-left:auto;flex-shrink:0;"></i>@endif
                    </a>
                    <a href="{{ $profile->youtube ? (Str::startsWith($profile->youtube,['http','https']) ? $profile->youtube : 'https://'.$profile->youtube) : '#' }}"
                       target="{{ $profile->youtube ? '_blank' : '_self' }}"
                       class="nbf-social-card {{ $profile->youtube ? 'active' : 'inactive' }}">
                        <div class="nbf-social-icon" style="background:rgba(255,0,0,0.1);">
                            <i class="fa-brands fa-youtube" style="color:#FF0000;font-size:18px;"></i>
                        </div>
                        <div class="nbf-social-info">
                            <span class="nbf-social-name">YouTube</span>
                            <span class="nbf-social-handle">{{ $profile->youtube ? 'Ver canal' : 'No configurado' }}</span>
                        </div>
                        @if($profile->youtube)<i data-lucide="arrow-up-right" style="width:13px;height:13px;color:#94a3b8;margin-left:auto;flex-shrink:0;"></i>@endif
                    </a>
                </div>
            </div>

            <hr class="nbf-divider">

            {{-- Professional Details (Bento Grid) --}}
            <div class="nbf-section">
                <div class="nbf-section-header">
                    <div class="nbf-section-icon" style="background:linear-gradient(135deg, rgba(47,147,245,.15), rgba(45,212,191,.15));">
                        <i data-lucide="briefcase" style="width:18px;height:18px;color:#2f93f5;"></i>
                    </div>
                    <h2 class="nbf-section-title">Datos Profesionales</h2>
                </div>

                <div class="nbf-bento-grid nbf-bento-grid-2">
                    <div class="nbf-bento-box bento-span-2">
                        <div class="nbf-bento-icon"><i data-lucide="file-text"></i></div>
                        <div class="nbf-bento-content">
                            <span class="nbf-bento-label">Biografía / Acerca de</span>
                            <span class="nbf-bento-value" style="white-space:pre-wrap;font-weight:400;line-height:1.6;margin-top:4px;display:block;">@if(empty($profile->bio))<span class="nbf-empty">Escribe algo sobre tu trayectoria e instrumentos...</span>@else{!! nl2br(e(trim($profile->bio))) !!}@endif</span>
                        </div>
                    </div>

                    <div class="nbf-bento-box">
                        <div class="nbf-bento-icon" style="color:#16a34a; background:rgba(22,163,74,0.1);"><i data-lucide="dollar-sign"></i></div>
                        <div class="nbf-bento-content">
                            <span class="nbf-bento-label">Tarifa Base</span>
                            <span class="nbf-bento-value">
                                @if($profile->hourly_rate)
                                    <span class="nbf-rate-badge">${{ number_format($profile->hourly_rate,0) }} <small>MXN / hr</small></span>
                                @else
                                    <span class="nbf-empty">Por acordar</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="nbf-bento-box">
                        <div class="nbf-bento-icon" style="color:#16a34a; background:rgba(22,163,74,0.1);"><i data-lucide="activity"></i></div>
                        <div class="nbf-bento-content">
                            <span class="nbf-bento-label">Estado</span>
                            <span class="nbf-availability-badge available" style="margin-top:3px;">
                                <span class="nbf-availability-dot"></span> Disponible
                            </span>
                        </div>
                    </div>

                    <div class="nbf-bento-box bento-span-2">
                        <div class="nbf-bento-icon"><i data-lucide="map"></i></div>
                        <div class="nbf-bento-content">
                            <span class="nbf-bento-label">Zona de Cobertura</span>
                            <span class="nbf-bento-value {{ !$profile->coverage_notes ? 'nbf-empty' : '' }}" style="font-weight:400; display:block; margin-top:2px;">
                                {{ $profile->coverage_notes ?? 'Especifica hasta dónde puedes viajar para tocar.' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="nbf-divider">

            {{-- Services & Genres (Bento Grid) --}}
            <div class="nbf-section">
                <div class="nbf-section-header">
                    <div class="nbf-section-icon" style="background:linear-gradient(135deg, rgba(245,158,11,.15), rgba(217,119,6,.15));">
                        <i data-lucide="music-2" style="width:18px;height:18px;color:#d97706;"></i>
                    </div>
                    <h2 class="nbf-section-title">Servicios Destacados</h2>
                </div>
                
                <div class="nbf-bento-grid nbf-bento-grid-3">
                    {{-- Agrupación Card --}}
                    <div class="nbf-service-card">
                        <div class="nbf-service-card-header">
                            <div class="nbf-service-icon" style="background:rgba(217,119,6,.1); color:#b45309;"><i data-lucide="users"></i></div>
                            <h4 class="nbf-service-title">Agrupación</h4>
                        </div>
                        <div class="nbf-service-chips">
                            @forelse($profile->groupTypes ?? [] as $group)
                                <div class="nbf-genre-chip" style="background:linear-gradient(135deg,rgba(217,119,6,.06),rgba(245,158,11,.08)); border-color:rgba(217,119,6,.18); color:#b45309;">
                                    {{ $group->name }}
                                </div>
                            @empty
                                <span class="nbf-empty" style="font-size:13px;">No especificado</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Eventos Card --}}
                    <div class="nbf-service-card">
                        <div class="nbf-service-card-header">
                            <div class="nbf-service-icon" style="background:rgba(22,163,74,.1); color:#15803d;"><i data-lucide="calendar-heart"></i></div>
                            <h4 class="nbf-service-title">Tipos de Evento</h4>
                        </div>
                        <div class="nbf-service-chips">
                            @forelse($profile->eventTypes ?? [] as $event)
                                <div class="nbf-genre-chip" style="background:linear-gradient(135deg,rgba(22,163,74,.06),rgba(34,197,94,.08)); border-color:rgba(22,163,74,.18); color:#15803d;">
                                    {{ $event->name }}
                                </div>
                            @empty
                                <span class="nbf-empty" style="font-size:13px;">No especificado</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Géneros Card --}}
                    <div class="nbf-service-card bento-span-3">
                        <div class="nbf-service-card-header">
                            <div class="nbf-service-icon" style="background:rgba(47,147,245,.1); color:#2f93f5;"><i data-lucide="music"></i></div>
                            <h4 class="nbf-service-title">Géneros Musicales</h4>
                        </div>
                        <div class="nbf-service-chips">
                            @forelse($profile->genres ?? [] as $genre)
                                <div class="nbf-genre-chip">
                                    {{ $genre->name }}
                                </div>
                            @empty
                                <div class="nbf-empty-genres">
                                    <i data-lucide="music-2" style="width:18px;height:18px;color:#cbd5e1;"></i>
                                    <span>Agrega tus géneros para ser encontrado más fácilmente</span>
                                    <button onclick="openEditModal()" class="nbf-add-genres-btn">+ Agregar géneros</button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        {{-- Multimedia Section (Creative Layout) --}}
        @if(isset($media) && count($media) > 0)
        <div class="nbf-section" style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e8edf3;">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 20px;">
                <div>
                    <h2 class="nbf-section-title" style="margin-bottom:4px;">Portafolio Multimedia</h2>
                    <p style="font-size:14px; color:#64748b; margin:0;">Tus mejores momentos y presentaciones.</p>
                </div>
                <a href="/multimedia" class="nbf-btn-secondary" style="font-size:13px; padding:8px 16px;">Gestionar</a>
            </div>

            <div class="nbf-media-showcase">
                {{-- Photos --}}
                @if($media->where('type', 'photo')->count() > 0)
                <div class="nbf-media-grid photos-grid">
                    @foreach($media->where('type', 'photo')->take(6) as $photo)
                    <div class="nbf-media-item" onclick="openViewModal('{{ $photo->url() }}', 'photo')">
                        <img src="{{ $photo->url() }}" alt="Foto">
                        <div class="nbf-media-overlay">
                            <i data-lucide="zoom-in" style="width:24px;height:24px;color:#fff;"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Videos --}}
                @if($media->where('type', 'video')->count() > 0)
                <div class="nbf-media-grid videos-grid" style="margin-top: 16px;">
                    @foreach($media->where('type', 'video')->take(4) as $video)
                    <div class="nbf-media-item video-item" onclick="openViewModal('{{ $video->url() }}', 'video')">
                        <video src="{{ $video->url() }}" preload="metadata"></video>
                        <div class="nbf-play-indicator">
                            <i data-lucide="play" style="width:20px;height:20px;color:#fff;margin-left:2px;"></i>
                        </div>
                        @if($video->is_featured)
                        <div class="nbf-featured-badge">
                            <i data-lucide="star" style="width:13px;height:13px;fill:currentColor;"></i>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <style>
        /* ═══════════════════════════════════════════════════════
           NBF PROFILE — CSS REFACTORIZADO (PREMIUM)
           ═══════════════════════════════════════════════════════ */

        .nbf-layout {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05);
            margin-bottom: 24px;
        }

        /* ── COVER ── */
        .nbf-header { width:100%; position:relative; }
        .nbf-cover {
            width:100%; height:320px; /* Aumentado para dar aire tipo Spotify */
            background-color: #e2e8f0;
            background-size:cover; background-position:center;
            filter:grayscale(100%);
            position:relative;
        }
        .nbf-cover-overlay {
            position:absolute; inset:0;
            background: linear-gradient(to bottom, rgba(0,0,0,0) 20%, rgba(0,0,0,0.85) 100%);
        }

        /* ── INFO BAR ── */
        .nbf-info-bar {
            display:flex; align-items:flex-start;
            padding:0 36px 28px 200px;
            position:relative; min-height:100px;
            border-bottom:1px solid var(--pr-border);
            gap:16px;
        }
        @media(max-width:768px) {
            .nbf-info-bar { flex-direction:column; padding:80px 20px 24px; align-items:center; text-align:center; }
        }

        /* ── AVATAR ── */
        .nbf-avatar-container {
            position:absolute; left:36px; top:-90px;
            width:160px; height:160px;
            border-radius:50%; background:rgba(255,255,255,0.15);
            backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);
            padding:8px; z-index:10;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
        }
        .nbf-avatar-container img, .nbf-avatar-initials {
            width:100%; height:100%; border-radius:50%; object-fit:cover;
        }
        .nbf-avatar-initials {
            display:flex; align-items:center; justify-content:center;
            background:linear-gradient(135deg,#6c3fc5,#2f93f5);
            color:#fff; font-size:52px; font-weight:800;
        }
        @media(max-width:768px) {
            .nbf-avatar-container { left:50%; transform:translateX(-50%); top:-80px; }
        }

        /* ── NOMBRE E INFO TIPO SPOTIFY ── */
        .nbf-user-info { padding-top:22px; flex:1; min-width:0; }
        .nbf-name {
            font-size:42px; font-weight:900; color:var(--pr-text);
            margin:0 0 4px; line-height:1.05; letter-spacing:-1.5px;
        }
        @media(max-width:768px) { .nbf-name { font-size:32px; letter-spacing:-1px; } }
        .nbf-stats-links { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:8px; }
        @media(max-width:768px) { .nbf-stats-links { justify-content:center; margin-bottom:16px; } }
        .nbf-stat { font-size:14px; color:var(--pr-primary); font-weight:700; }
        .nbf-dot { color:#cbd5e1; font-size:10px; }

        /* ── BOTONES DE HEADER ── */
        .nbf-header-actions { padding-top:26px; display:flex; gap:10px; flex-shrink:0; }
        @media(max-width:768px) { .nbf-header-actions { width:100%; flex-direction:column; } }

        .nbf-btn-primary {
            display:inline-flex; align-items:center; gap:7px;
            padding:10px 20px; border-radius:10px;
            background:var(--pr-primary); color:#fff; border:none;
            font-size:13px; font-weight:700; cursor:pointer;
            transition:all .2s; box-shadow:0 4px 14px rgba(108,63,197,.25);
        }
        .nbf-btn-primary:hover { background:var(--pr-primary-h); transform:translateY(-1px); }
        .nbf-btn-secondary {
            display:inline-flex; align-items:center; gap:7px;
            padding:10px 20px; border-radius:10px;
            background:#fff; color:var(--pr-dim); border:1.5px solid var(--pr-border);
            font-size:13px; font-weight:600; cursor:pointer; transition:all .2s;
        }
        .nbf-btn-secondary:hover { border-color:#c4b5fd; color:var(--pr-primary); background:#faf5ff; }

        /* ── ÁREA DE CONTENIDO ── */
        .nbf-content { padding:36px 40px; background: transparent; }
        @media(max-width:768px) { .nbf-content { padding:24px 16px; } }

        /* ── TARJETA COMPLETITUD ── */
        .nbf-completion-card {
            display:flex; align-items:center; gap:14px;
            background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius:14px; padding:16px 20px; margin-bottom:32px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
        }
        .nbf-completion-icon {
            width:40px; height:40px; border-radius:10px; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
        }
        .nbf-completion-icon.good { background:#f0fdf4; color:#16a34a; }
        .nbf-completion-icon.mid  { background:#fffbeb; color:#d97706; }
        .nbf-completion-icon.low  { background:#fef2f2; color:#dc2626; }
        .nbf-completion-body { flex:1; min-width:0; }
        .nbf-completion-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; gap:12px; }
        .nbf-completion-label { font-size:13px; font-weight:600; color:var(--pr-dim); }
        .nbf-completion-badge { font-size:12px; font-weight:800; padding:3px 10px; border-radius:99px; flex-shrink:0; }
        .nbf-completion-badge.good { color:#16a34a; background:#f0fdf4; }
        .nbf-completion-badge.mid  { color:#d97706; background:#fffbeb; }
        .nbf-completion-badge.low  { color:#dc2626; background:#fef2f2; }
        .nbf-completion-bar { height:5px; background:#e2e8f0; border-radius:99px; overflow:hidden; }
        .nbf-completion-fill { height:100%; background:linear-gradient(90deg,#6c3fc5,#2f93f5); border-radius:99px; transition:width .6s ease; }
        .nbf-completion-cta {
            padding:8px 16px; border-radius:8px; border:none; background:var(--pr-primary);
            color:#fff; font-size:12px; font-weight:700; cursor:pointer; flex-shrink:0; transition:opacity .2s;
        }
        .nbf-completion-cta:hover { opacity:.85; }

        /* ── SECCIONES ── */
        .nbf-section { margin-bottom:32px; }
        .nbf-divider { border:0; height:1px; background:var(--pr-border); margin:32px 0; }
        .nbf-section-header { display:flex; align-items:center; gap:10px; margin-bottom:20px; }
        .nbf-section-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .nbf-section-title { font-size:15px; font-weight:800; color:var(--pr-text); margin:0; }
        .nbf-subsection-title { font-size:11.5px; font-weight:700; color:var(--pr-muted); text-transform:uppercase; letter-spacing:.07em; margin:0 0 12px; display:flex; align-items:center; gap:6px; }

        /* ── BENTO GRID CARDS ── */
        .nbf-bento-grid {
            display: grid; gap: 16px; margin-bottom: 24px;
        }
        .nbf-bento-grid-2 { grid-template-columns: repeat(2, 1fr); }
        .nbf-bento-grid-3 { grid-template-columns: repeat(2, 1fr); }
        
        @media(max-width: 768px) {
            .nbf-bento-grid-2, .nbf-bento-grid-3 { grid-template-columns: 1fr; }
            .bento-span-2, .bento-span-3 { grid-column: span 1 !important; }
        }

        .bento-span-2 { grid-column: span 2; }
        .bento-span-3 { grid-column: span 2; }

        .nbf-bento-box {
            background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 14px; padding: 18px 20px;
            display: flex; gap: 14px; align-items: flex-start;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02); transition: all 0.2s ease;
        }
        .nbf-bento-box:hover {
            background: rgba(255, 255, 255, 0.7);
            border-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 24px rgba(108,63,197,0.06);
            transform: translateY(-2px);
        }
        
        .nbf-bento-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(100, 116, 139, 0.08); color: var(--pr-dim);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .nbf-bento-icon i { width: 18px; height: 18px; }
        .nbf-bento-box:hover .nbf-bento-icon { background: rgba(108,63,197, 0.1); color: var(--pr-primary); }
        
        .nbf-bento-content { display: flex; flex-direction: column; flex: 1; min-width: 0; }
        .nbf-bento-label { font-size: 13px; color: var(--pr-dim); font-weight: 600; margin-bottom: 2px; }
        .nbf-bento-value { font-size: 14.5px; color: var(--pr-text); font-weight: 600; word-break: break-word; }
        .nbf-empty { color:var(--pr-muted) !important; font-style:italic; font-weight:400; }

        /* ── SERVICE CARDS (BENTO) ── */
        .nbf-service-card {
            background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 14px; padding: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02); transition: all 0.2s ease;
            display: flex; flex-direction: column;
        }
        .nbf-service-card:hover {
            background: rgba(255, 255, 255, 0.65);
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            border-color: rgba(255, 255, 255, 0.9);
        }
        .nbf-service-card-header {
            display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
        }
        .nbf-service-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .nbf-service-icon i { width: 16px; height: 16px; }
        .nbf-service-title { font-size: 14.5px; font-weight: 700; color: var(--pr-text); margin: 0; }
        .nbf-service-chips { display: flex; flex-wrap: wrap; gap: 10px; }

        /* ── BADGES DE DISPONIBILIDAD Y TARIFA ── */
        .nbf-rate-badge { display:inline-flex; align-items:baseline; gap:4px; color:#15803d; font-size:17px; font-weight:800; }
        .nbf-rate-badge small { font-size:11px; font-weight:400; color:var(--pr-dim); }
        .nbf-availability-badge { display:inline-flex; align-items:center; gap:7px; font-size:13px; font-weight:600; padding:5px 13px; border-radius:99px; }
        .nbf-availability-badge.available { background:#f0fdf4; color:#16a34a; }
        .nbf-availability-dot { width:7px; height:7px; border-radius:50%; background:currentColor; animation:pulse-dot 2s infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.35} }

        /* ── REDES SOCIALES ── */
        .nbf-social-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:16px; }
        @media(max-width:768px) { .nbf-social-grid { grid-template-columns:1fr; } }
        .nbf-social-card {
            display:flex; align-items:center; gap:12px;
            padding:12px 14px; border-radius:12px;
            background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-decoration:none; transition:all .3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        .nbf-social-card.active:hover {
            border-color: rgba(108,63,197,0.4); transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 24px rgba(108,63,197,0.15);
            background: rgba(255, 255, 255, 0.7);
        }
        .nbf-social-card.inactive { opacity:.5; pointer-events:none; }
        .nbf-social-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .nbf-social-info { display:flex; flex-direction:column; }
        .nbf-social-name { font-size:12px; font-weight:700; color:#374151; }
        .nbf-social-handle { font-size:11px; color:var(--pr-muted); }

        /* ── GENRE CHIPS ── */
        .nbf-genre-chip {
            display:inline-flex; align-items:center; gap:7px;
            padding:8px 18px; border-radius:99px; font-size:13px; font-weight:600;
            background: linear-gradient(135deg, rgba(255,255,255,0.7), rgba(255,255,255,0.4));
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.8);
            color: var(--pr-primary);
            transition:all .3s ease; cursor:default;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .nbf-genre-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(108,63,197,0.18);
            background: linear-gradient(135deg, rgba(108,63,197,0.1), rgba(47,147,245,0.1));
            border-color: rgba(108,63,197,0.3);
        }

        /* ── EMPTY STATES ── */
        .nbf-empty-genres {
            display:flex; align-items:center; gap:12px; width:100%;
            padding:16px 20px; background:var(--pr-surface);
            border:1.5px dashed var(--pr-border); border-radius:12px;
            color:var(--pr-muted); font-size:14px;
        }
        .nbf-add-genres-btn {
            background:linear-gradient(135deg,#6c3fc5,#2f93f5); color:#fff;
            border:none; padding:7px 14px; border-radius:8px;
            font-size:12px; font-weight:700; cursor:pointer;
            white-space:nowrap; margin-left:auto; transition:opacity .2s;
        }
        .nbf-add-genres-btn:hover { opacity:.85; }

        /* ── MULTIMEDIA ── */
        .nbf-media-showcase { width:100%; }
        .nbf-media-grid { display:grid; gap:12px; }
        /* Masonry Support for photos */
        .photos-grid { display: block; columns: 2 200px; gap: 12px; }
        .videos-grid { grid-template-columns:repeat(auto-fill, minmax(320px,1fr)); margin-top:16px; }
        .nbf-media-item {
            position:relative; border-radius:14px; overflow:hidden;
            background: rgba(255,255,255,0.4); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.6);
            cursor:pointer; box-shadow:0 4px 16px rgba(0,0,0,.04); transition:all .3s ease;
        }
        .nbf-media-item:hover { box-shadow:0 12px 32px rgba(108,63,197,.15); transform: translateY(-4px) scale(1.02); z-index: 2; }
        .photos-grid .nbf-media-item { aspect-ratio: auto; min-height: 150px; display: inline-block; width: 100%; margin-bottom: 12px; }
        .videos-grid .nbf-media-item { aspect-ratio:16/9; }
        .nbf-media-item img, .nbf-media-item video { width:100%; height:100%; object-fit:cover; display:block; transition:transform .4s ease; border-radius: 12px; }
        .nbf-media-item:hover img, .nbf-media-item:hover video { transform:scale(1.05); }
        .nbf-media-overlay {
            position:absolute; inset:0; background:rgba(0,0,0,.3);
            display:flex; align-items:center; justify-content:center;
            opacity:0; transition:opacity .3s;
        }
        .nbf-media-item:hover .nbf-media-overlay { opacity:1; }
        .nbf-play-indicator {
            position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
            width:56px; height:56px; border-radius:50%;
            background: rgba(255,255,255,0.25); border: 2px solid rgba(255,255,255,0.8);
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            display:flex; align-items:center; justify-content:center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15); transition:all .3s ease;
        }
        .video-item:hover .nbf-play-indicator { transform:translate(-50%,-50%) scale(1.15); background: rgba(255,255,255,0.4); border-color: #fff; }
        .nbf-featured-badge {
            position:absolute; top:10px; left:10px;
            background:linear-gradient(135deg,#f59e0b,#d97706);
            color:#fff; font-size:11px; font-weight:700;
            padding:4px 10px; border-radius:20px;
            display:flex; align-items:center; gap:4px;
            box-shadow:0 4px 12px rgba(245,158,11,.4); z-index:2;
        }

        /* ── BOTONES GLOBALES MODAL ── */
        .primary-btn {
            display:inline-flex; align-items:center; gap:8px;
            background:var(--pr-primary); color:#fff; border:none;
            padding:11px 24px; border-radius:10px; font-size:14px; font-weight:600;
            cursor:pointer; transition:all .2s;
        }
        .primary-btn:hover { background:var(--pr-primary-h); transform:translateY(-1px); }
        .primary-btn i, .primary-btn svg { width:15px; height:15px; }
        .secondary-btn {
            display:inline-flex; align-items:center; gap:8px;
            background:#f1f5f9; color:#475569; border:1.5px solid var(--pr-border);
            padding:11px 24px; border-radius:10px; font-size:14px; font-weight:600;
            cursor:pointer; transition:all .2s;
        }
        .secondary-btn:hover { background:#e2e8f0; border-color:#cbd5e1; }
        .danger-btn {
            display:inline-flex; align-items:center; gap:8px;
            background:#dc2626; color:#fff; border:none;
            padding:11px 24px; border-radius:10px; font-size:14px; font-weight:600;
            cursor:pointer; transition:all .2s;
        }
        .danger-btn:hover { background:#b91c1c; }

        /* ── MODAL MEDIA VIEWER ── */
        #view-media-modal button svg { display:inline-block !important; flex-shrink:0 !important; visibility:visible !important; }
        .mm-modal-close {
            position:absolute !important; top:24px !important; right:24px !important;
            background:rgba(255,255,255,.1) !important; border:none !important;
            width:48px !important; height:48px !important; border-radius:50% !important;
            color:#fff !important; display:flex !important; align-items:center !important;
            justify-content:center !important; cursor:pointer !important;
            transition:background .2s !important; z-index:10000 !important;
            box-shadow:none !important; margin:0 !important; padding:0 !important;
        }
        .mm-modal-close:hover { background:rgba(255,255,255,.2) !important; transform:scale(1.1) !important; }
    </style>



    {{-- ══════════════  MODAL EDITAR PERFIL  ══════════════ --}}
    <div id="edit-modal-overlay" style="
        display:none;position:fixed;inset:0;z-index:1000;
        background:rgba(0,0,0,.45);backdrop-filter:blur(3px);
        align-items:center;justify-content:center;
    ">
        <div style="
            background:#fff;border-radius:16px;width:100%;max-width:640px;
            max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.2);
            animation:slideUp .28s ease;
        ">
            {{-- Header del modal --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f0f0f0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <i data-lucide="edit-3" style="width:20px;color:var(--accent-blue);"></i>
                    <h3 style="margin:0;font-size:17px;">Editar Perfil</h3>
                </div>
                <button onclick="closeEditModal()" style="
                    background:transparent;border:none;cursor:pointer;
                    color:#64748b;padding:6px;border-radius:6px;
                    display:flex;align-items:center;justify-content:center;
                    transition: background 0.2s;
                    width:36px; height:36px;
                " onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                    <i class="fa-solid fa-xmark" style="font-size:22px;"></i>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('profile.update') }}" style="padding:24px;" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div style="background:#fef2f2;color:#dc2626;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13px;">
                        <ul style="margin:0;padding-left:18px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ── Foto de perfil ─────────────────────────────────── --}}
                <div class="form-group" style="text-align:center;margin-bottom:24px;">
                    <label style="display:block;margin-bottom:10px;">Foto de Perfil</label>
                    <div id="photo-preview-wrap" style="position:relative;display:inline-block;cursor:pointer;" onclick="document.getElementById('photo-input').click()">
                        @if($profile && $profile->profile_picture)
                            @if(Str::startsWith($profile->profile_picture, ['http://', 'https://']))
                                <img id="photo-preview" src="{{ $profile->profile_picture }}"
                                     style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid var(--accent-blue);">
                            @else
                                <img id="photo-preview" src="{{ $profile->profilePictureUrl() }}" 
                                     style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid var(--accent-blue);">
                            @endif
                        @else
                            <div id="photo-preview" style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#6c3fc5,#2f93f5);color:#fff;font-size:28px;font-weight:700;display:flex;align-items:center;justify-content:center;border:3px solid var(--accent-blue);">
                                {{ strtoupper(substr($user->name,0,2)) }}
                            </div>
                        @endif
                        <div style="position:absolute;bottom:0;right:0;background:var(--accent-blue);border-radius:50%;padding:5px;line-height:0;border:2px solid #fff;">
                            <i data-lucide="camera" style="width:13px;height:13px;color:#fff;"></i>
                        </div>
                    </div>
                    <input type="file" id="photo-input" name="profile_picture" accept="image/*" style="display:none;" onchange="previewPhoto(event)">
                    <p style="font-size:11px;color:var(--text-dim);margin-top:6px;">JPG, PNG o WEBP · máx. 3 MB</p>
                </div>

                {{-- Nombre artístico --}}
                <div class="form-group">
                    <label>Nombre Artístico *</label>
                    <input type="text" name="stage_name"
                           value="{{ old('stage_name', $profile->stage_name ?? $user->name) }}"
                           required>
                </div>

                {{-- Ubicación y Tarifa en grid --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>Ciudad / Ubicación</label>
                        <input type="text" name="location"
                               value="{{ old('location', $profile->location) }}"
                               placeholder="Ej. Guadalajara, Jal."
                               oninput="this.value = this.value.replace(/[0-9]/g, '')">
                    </div>
                    <div class="form-group">
                        <label>Tarifa por hora (MXN)</label>
                        <input type="number" name="hourly_rate" min="0" step="50"
                               value="{{ old('hourly_rate', $profile->hourly_rate) }}"
                               placeholder="Ej. 3500"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                </div>

                {{-- Teléfono --}}
                <div class="form-group">
                    <label>Teléfono / WhatsApp</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $profile->phone) }}"
                           placeholder="+52 000 000 0000"
                           oninput="formatPhone(this)">
                </div>

                {{-- Biografía --}}
                <div class="form-group">
                    <label>Biografía</label>
                    <textarea name="bio" rows="4"
                              placeholder="Describe tu trayectoria, estilo musical, experiencia..."
                              style="resize:vertical;">{{ old('bio', $profile->bio) }}</textarea>
                </div>

                {{-- ── Géneros musicales ───────────────────────────────── --}}
                @if(isset($genres) && $genres->isNotEmpty())
                    <h4 style="margin: 32px 0 16px; font-size: 16px; color: var(--text-main); font-weight: 700; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;">Preferencias Musicales</h4>
                    @foreach($genres->groupBy('category') as $category => $categoryGenres)
                        <div class="form-group" style="margin-top: 20px;">
                            <label style="color:var(--accent-blue);font-weight:700;"><i data-lucide="music" style="width:14px;height:14px;margin-right:4px;"></i> {{ $category ?: 'Otros Géneros' }}</label>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:6px;">
                                @foreach($categoryGenres as $genre)
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;font-size:13px;background:#f8f9fa;padding:8px 10px;border-radius:8px;border:1.5px solid {{ $profile && $profile->genres && $profile->genres->contains($genre->id) ? 'var(--accent-blue)' : '#e5e7eb' }};transition:border .2s;">
                                        <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                                               {{ $profile && $profile->genres && $profile->genres->contains($genre->id) ? 'checked' : '' }}
                                               style="width:15px;height:15px;accent-color:var(--accent-blue);">
                                        {{ $genre->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
                
                {{-- ── Tipos de Agrupación ───────────────────────────────── --}}
                @if(isset($groupTypes) && $groupTypes->isNotEmpty())
                <div class="form-group" style="margin-top: 32px; padding-top: 16px; border-top: 1px dashed #f1f5f9;">
                    <label style="color:#d97706;font-weight:700;font-size:15px;margin-bottom:12px;"><i data-lucide="users" style="width:16px;height:16px;margin-right:6px;"></i> Formato / Tipo de Agrupación</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:6px;">
                        @foreach($groupTypes as $group)
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;font-size:13px;background:#fffbeb;padding:8px 10px;border-radius:8px;border:1.5px solid {{ $profile && $profile->groupTypes && $profile->groupTypes->contains($group->id) ? '#d97706' : '#fde68a' }};transition:border .2s;">
                                <input type="checkbox" name="group_types[]" value="{{ $group->id }}"
                                       {{ $profile && $profile->groupTypes && $profile->groupTypes->contains($group->id) ? 'checked' : '' }}
                                       style="width:15px;height:15px;accent-color:#d97706;">
                                {{ $group->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
                
                {{-- ── Tipos de Eventos ───────────────────────────────── --}}
                @if(isset($eventTypes) && $eventTypes->isNotEmpty())
                <div class="form-group" style="margin-top: 32px; padding-top: 16px; border-top: 1px dashed #f1f5f9;">
                    <label style="color:#16a34a;font-weight:700;font-size:15px;margin-bottom:12px;"><i data-lucide="calendar-heart" style="width:16px;height:16px;margin-right:6px;"></i> Eventos en los que toca</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:6px;">
                        @foreach($eventTypes as $event)
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;font-size:13px;background:#f0fdf4;padding:8px 10px;border-radius:8px;border:1.5px solid {{ $profile && $profile->eventTypes && $profile->eventTypes->contains($event->id) ? '#16a34a' : '#bbf7d0' }};transition:border .2s;">
                                <input type="checkbox" name="event_types[]" value="{{ $event->id }}"
                                       {{ $profile && $profile->eventTypes && $profile->eventTypes->contains($event->id) ? 'checked' : '' }}
                                       style="width:15px;height:15px;accent-color:#16a34a;">
                                {{ $event->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Notas de Cobertura --}}
                <div class="form-group">
                    <label>Notas de Zona de Cobertura</label>
                    <input type="text" name="coverage_notes"
                           value="{{ old('coverage_notes', $profile->coverage_notes) }}"
                           placeholder="Ej. Viáticos para zonas fuera de Puebla">
                </div>

                <p style="font-size:13px;font-weight:600;color:var(--text-dim);margin:20px 0 12px;">Redes Sociales</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label><i data-lucide="instagram" style="width:13px;vertical-align:middle;"></i> Instagram</label>
                        <input type="text" name="instagram"
                               value="{{ old('instagram', $profile->instagram) }}"
                               placeholder="@tuusuario">
                    </div>
                    <div class="form-group">
                        <label><i data-lucide="facebook" style="width:13px;vertical-align:middle;"></i> Facebook</label>
                        <input type="text" name="facebook"
                               value="{{ old('facebook', $profile->facebook) }}"
                               placeholder="URL o nombre de página">
                    </div>
                    <div class="form-group">
                        <label><i data-lucide="youtube" style="width:13px;vertical-align:middle;"></i> YouTube</label>
                        <input type="text" name="youtube"
                               value="{{ old('youtube', $profile->youtube) }}"
                               placeholder="URL de tu canal">
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid #f0f0f0;">
                    <button type="button" onclick="closeEditModal()" class="secondary-btn">Cancelar</button>
                    <button type="submit" class="primary-btn">
                        <i data-lucide="save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── ESTILOS FORMULARIO + MODAL CONTRASEÑA ─── --}}
    <style>
        /* ── Form ── */
        .form-group { margin-bottom:16px; }
        .form-group label { display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--pr-text); }
        .form-group input, .form-group textarea { width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;color:var(--pr-text);background:#fafafa;box-sizing:border-box;transition:border .2s; }
        .form-group input:focus, .form-group textarea:focus { border-color:var(--pr-primary);outline:none;background:#fff;box-shadow:0 0 0 3px rgba(108,63,197,.08); }
        @keyframes slideUp { from { opacity:0;transform:translateY(30px); } to { opacity:1;transform:translateY(0); } }

        /* ── Password strength ── */
        .strength-bar-wrap { height:4px; background:#e5e7eb; border-radius:2px; margin-top:8px; overflow:hidden; }
        .strength-bar { height:100%; width:0%; border-radius:2px; transition:width .3s, background .3s; }
        .strength-label { font-size:11px; font-weight:600; margin-top:4px; display:block; }
        .pwd-requirements { display:none; flex-direction:column; gap:4px; margin-top:10px; padding:10px 12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; }
        .pwd-requirements.visible { display:flex; }
        .req-item { font-size:12px; color:#6b7280; display:flex; align-items:center; gap:7px; transition:color .2s; }
        .req-item .req-dot { font-size:7px; flex-shrink:0; }
        .req-item.ok { color:#16a34a; }
        .req-item.ok .req-dot { color:#16a34a; }
        .req-item.fail { color:#dc2626; }
        .req-item.fail .req-dot { color:#dc2626; }
    </style>


    {{-- ══════════════  MODAL VISTA MÓVIL  ══════════════ --}}
    <div id="preview-modal-overlay" style="
        display:none;position:fixed;inset:0;z-index:2000;
        background:rgba(10,10,20,.7);backdrop-filter:blur(8px);
        align-items:flex-start;justify-content:center;
        overflow-y:auto;padding:24px 16px;
    ">
        <div style="position:relative;margin:auto;">
            {{-- Cierre --}}
            <button onclick="closePreviewModal()" style="
                position:absolute;top:-14px;right:-14px;z-index:10;
                width:32px;height:32px;border-radius:50%;
                background:#fff;border:1px solid #e2e8f0;cursor:pointer;
                box-shadow:0 2px 10px rgba(0,0,0,.15);
                display:flex;align-items:center;justify-content:center;
                transition: transform 0.2s, background 0.2s;
            " onmouseover="this.style.background='#f8fafc'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#fff'; this.style.transform='scale(1)';">
                <i class="fa-solid fa-xmark" style="font-size:16px;color:#334155;"></i>
            </button>

            {{-- Badge --}}
            <div style="text-align:center;margin-bottom:14px;">
                <span style="background:rgba(255,255,255,.15);color:#fff;font-size:12px;font-weight:600;
                    padding:5px 14px;border-radius:999px;letter-spacing:.5px;">
                    <i data-lucide="smartphone" style="width:13px;height:13px;vertical-align:middle;margin-right:4px;"></i>
                    Vista desde la app móvil
                </span>
            </div>

            {{-- Marco del teléfono --}}
            <div class="phone-frame">
                {{-- Notch --}}
                <div class="phone-notch">
                    <div class="phone-camera"></div>
                </div>

                {{-- Pantalla --}}
                <div class="phone-screen">

                    {{-- Status bar --}}
                    <div class="app-statusbar">
                        <span style="font-size:11px;font-weight:700;">9:41</span>
                        <div style="display:flex;gap:5px;align-items:center;">
                            <i data-lucide="signal" style="width:12px;height:12px;"></i>
                            <i data-lucide="wifi" style="width:12px;height:12px;"></i>
                            <i data-lucide="battery-full" style="width:12px;height:12px;"></i>
                        </div>
                    </div>

                    {{-- App topbar --}}
                    <div class="app-topbar">
                        <button style="background:none;border:none;color:#fff;padding:4px;cursor:pointer;">
                            <i data-lucide="chevron-left" style="width:20px;height:20px;"></i>
                        </button>
                        <span style="font-size:14px;font-weight:700;color:#fff;">Perfil</span>
                        <button style="background:none;border:none;color:#fff;padding:4px;cursor:pointer;">
                            <i data-lucide="share-2" style="width:18px;height:18px;"></i>
                        </button>
                    </div>

                    {{-- Contenido scrollable --}}
                    <div class="app-body">

                        {{-- Cover / Hero --}}
                        <div class="app-hero">
                            @if($profile && $profile->profile_picture)
                                @if(Str::startsWith($profile->profile_picture, ['http://', 'https://']))
                                    <img src="{{ $profile->profile_picture }}"
                                         style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.3);">
                                @else
                                    <img src="{{ $profile->profilePictureUrl() }}"
                                         style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.3);">
                                @endif
                            @else
                                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#6c3fc5,#2f93f5);color:#fff;font-size:26px;font-weight:800;display:flex;align-items:center;justify-content:center;border:3px solid #fff;">
                                    {{ strtoupper(substr($profile->stage_name ?? $user->name,0,2)) }}
                                </div>
                            @endif
                            <h2 style="margin:10px 0 2px;font-size:17px;font-weight:800;color:#fff;">
                                {{ $profile->stage_name ?? $user->name }}
                            </h2>
                            <p style="font-size:12px;color:rgba(255,255,255,.75);margin:0;">
                                @if($profile->location)
                                    📍 {{ $profile->location }}
                                @else
                                    Músico profesional
                                @endif
                            </p>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:center;margin-top:10px;">
                                @foreach($profile->genres ?? [] as $g)
                                    <span style="background:rgba(255,255,255,.2);color:#fff;font-size:10px;font-weight:600;padding:3px 10px;border-radius:999px;">{{ $g->name }}</span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Stats rápidos --}}
                        <div style="display:flex;border-bottom:1px solid #f0f0f0;">
                            <div style="flex:1;text-align:center;padding:12px 0;">
                                <div style="font-size:16px;font-weight:800;color:#6c3fc5;">⭐ 5.0</div>
                                <div style="font-size:10px;color:#9ca3af;margin-top:2px;">Calificación</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:12px 0;border-left:1px solid #f0f0f0;border-right:1px solid #f0f0f0;">
                                <div style="font-size:16px;font-weight:800;color:#6c3fc5;">{{ $profile->profile_views ?? 0 }}</div>
                                <div style="font-size:10px;color:#9ca3af;margin-top:2px;">Vistas</div>
                            </div>
                            <div style="flex:1;text-align:center;padding:12px 0;">
                                <div style="font-size:16px;font-weight:800;color:#6c3fc5;">
                                    @if($profile->hourly_rate) ${{ number_format($profile->hourly_rate,0) }} @else — @endif
                                </div>
                                <div style="font-size:10px;color:#9ca3af;margin-top:2px;">MXN/hr</div>
                            </div>
                        </div>

                        {{-- Bio --}}
                        @if($profile->bio)
                        <div style="padding:14px 16px;border-bottom:1px solid #f0f0f0;">
                            <h4 style="font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;">Acerca de</h4>
                            <p style="font-size:13px;color:#4b5563;line-height:1.6;">{{ $profile->bio }}</p>
                        </div>
                        @endif

                        {{-- Zona de cobertura --}}
                        @if($profile->coverage_notes)
                        <div style="padding:14px 16px;border-bottom:1px solid #f0f0f0;">
                            <h4 style="font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;">Zona de Cobertura</h4>
                            <p style="font-size:12px;color:#6b7280;line-height:1.6;">📍 {{ $profile->location }} — {{ $profile->coverage_notes }}</p>
                        </div>
                        @endif

                        {{-- Contacto completo --}}
                        <div style="padding:14px 16px;border-bottom:1px solid #f0f0f0;">
                            <h4 style="font-size:12px;font-weight:700;color:#374151;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">Contacto</h4>
                            @if($profile->phone)
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                <span style="width:28px;height:28px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <i data-lucide="phone" style="width:14px;height:14px;color:#16a34a;"></i>
                                </span>
                                <span style="font-size:12px;color:#374151;font-weight:500;">{{ $profile->phone }}</span>
                            </div>
                            @endif
                            @if($profile->instagram)
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                <span style="width:28px;height:28px;background:#fdf4ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <i data-lucide="instagram" style="width:14px;height:14px;color:#a855f7;"></i>
                                </span>
                                <span style="font-size:12px;color:#374151;font-weight:500;">{{ '@' . ltrim($profile->instagram,'@') }}</span>
                            </div>
                            @endif
                            @if($profile->facebook)
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                <span style="width:28px;height:28px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <i data-lucide="facebook" style="width:14px;height:14px;color:#2563eb;"></i>
                                </span>
                                <span style="font-size:12px;color:#374151;font-weight:500;">Facebook</span>
                            </div>
                            @endif
                            @if($profile->youtube)
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="width:28px;height:28px;background:#fef2f2;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <i data-lucide="youtube" style="width:14px;height:14px;color:#dc2626;"></i>
                                </span>
                                <span style="font-size:12px;color:#374151;font-weight:500;">YouTube</span>
                            </div>
                            @endif
                            @if(!$profile->phone && !$profile->instagram && !$profile->facebook && !$profile->youtube)
                                <p style="font-size:12px;color:#9ca3af;font-style:italic;">Sin información de contacto.</p>
                            @endif
                        </div>

                        {{-- Multimedia --}}
                        @if(isset($media) && count($media) > 0)
                        <div style="padding:14px 16px;border-bottom:1px solid #f0f0f0;">
                            <h4 style="font-size:12px;font-weight:700;color:#374151;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">Portafolio</h4>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:5px;">
                                @foreach($media->take(6) as $m)
                                    @if($m->type === 'photo')
                                        <div style="aspect-ratio:1;border-radius:7px;overflow:hidden;position:relative;">
                                            <img src="{{ $m->url() }}" alt="Foto" style="width:100%;height:100%;object-fit:cover;">
                                            @if($m->is_featured)
                                                <span style="position:absolute;top:2px;left:2px;background:rgba(245,158,11,.9);border-radius:4px;padding:2px 3px;display:flex;align-items:center;">
                                                    <i data-lucide="star" style="width:8px;height:8px;fill:#fff;color:#fff;"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div style="aspect-ratio:1;border-radius:7px;overflow:hidden;background:#1a0b38;position:relative;display:flex;align-items:center;justify-content:center;">
                                            <i data-lucide="play-circle" style="width:22px;height:22px;color:rgba(255,255,255,.8);"></i>
                                            @if($m->is_featured)
                                                <span style="position:absolute;top:2px;left:2px;background:rgba(245,158,11,.9);border-radius:4px;padding:2px 3px;display:flex;align-items:center;">
                                                    <i data-lucide="star" style="width:8px;height:8px;fill:#fff;color:#fff;"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Botón contratar (app) --}}
                        <div style="padding:16px 16px 24px;">
                            <button style="
                                width:100%;padding:13px;
                                background:linear-gradient(135deg,#6c3fc5,#2f93f5);
                                color:#fff;border:none;border-radius:12px;
                                font-size:14px;font-weight:700;cursor:pointer;
                                box-shadow:0 4px 16px rgba(108,63,197,.35);
                            ">Solicitar contratación</button>
                        </div>

                    </div>{{-- /app-body --}}

                    {{-- Home indicator --}}
                    <div class="phone-home-indicator"></div>

                </div>{{-- /phone-screen --}}
            </div>{{-- /phone-frame --}}

        </div>
    </div>

    <style>
        /* ─── PHONE FRAME ─────────────────────────────────────── */
        .phone-frame {
            width: 290px;
            background: #1a1a2e;
            border-radius: 36px;
            padding: 12px 10px;
            box-shadow:
                0 0 0 2px #333,
                0 0 0 4px #555,
                0 20px 60px rgba(0,0,0,.6);
            position: relative;
            margin: auto; /* Removed extra margin to save vertical space */
        }
        .phone-notch {
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 20px;
            background: #1a1a2e;
            border-radius: 0 0 14px 14px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .phone-camera {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #333;
            border: 1px solid #555;
            margin-left: 50px; 
        }
        .phone-screen {
            background: #fff;
            border-radius: 26px;
            overflow: hidden;
            height: 540px;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .app-statusbar {
            background: #1a0b38;
            color: #fff;
            padding: 12px 20px 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .app-topbar {
            background: linear-gradient(135deg,#1a0b38,#0d2452);
            color: #fff;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .app-hero {
            position: relative;
            padding: 20px 16px 24px;
            text-align: center;
            flex-shrink: 0;
            overflow: hidden;
            z-index: 0;
        }
        .app-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1514525253161-7a46d19cd819?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            filter: grayscale(100%);
            z-index: -2;
        }
        .app-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.45), rgba(0,0,0,0.75));
            z-index: -1;
        }
        .app-body {
            flex: 1;
            overflow-y: auto;
            background: #fff;
            scrollbar-width: none;
        }
        .app-body::-webkit-scrollbar { display: none; }
        .phone-home-indicator {
            height: 20px;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .phone-home-indicator::after {
            content: '';
            width: 80px;
            height: 4px;
            background: #d1d5db;
            border-radius: 2px;
        }
    </style>


    <script>
        function openEditModal() {
            const overlay = document.getElementById('edit-modal-overlay');
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeEditModal() {
            const overlay = document.getElementById('edit-modal-overlay');
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
        function openPreviewModal() {
            const overlay = document.getElementById('preview-modal-overlay');
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
        function closePreviewModal() {
            const overlay = document.getElementById('preview-modal-overlay');
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
        // Close on overlay click
        document.getElementById('edit-modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
        document.getElementById('preview-modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closePreviewModal();
        });
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { closeEditModal(); closePreviewModal(); }
        });
        // Auto-open edit modal if there are validation errors (excluding password errors)
        @if($errors->any() && !$errors->has('current_password') && !$errors->has('password'))
            document.addEventListener('DOMContentLoaded', () => openEditModal());
        @endif

        // Live photo preview
        function previewPhoto(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrap = document.getElementById('photo-preview');
                // Replace whatever is there with an img tag
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid var(--accent-blue);';
                wrap.replaceWith(img);
                img.id = 'photo-preview';
            };
            reader.readAsDataURL(file);
        }

        // Highlight genre checkboxes on change
        document.querySelectorAll('input[name="genres[]"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const label = this.closest('label');
                label.style.borderColor = this.checked ? 'var(--accent-blue)' : '#e5e7eb';
                label.style.background  = this.checked ? 'rgba(47,147,245,.07)' : '#f8f9fa';
            });
        });
    </script>

    {{-- ══════════════ AJUSTES DE CUENTA (Contraseña & Eliminar) ══════════════ --}}
    <div style="margin-top: 40px; text-align: right; padding: 0 24px 24px; display: flex; justify-content: flex-end; gap: 16px; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 24px;">
        
        {{-- Botón Cambiar Contraseña --}}
        @if($user->google_id)
            <div title="Tu cuenta está vinculada con Google." style="cursor:not-allowed;">
                <button type="button" disabled class="secondary-btn" style="opacity:.5;cursor:not-allowed;">
                    <i data-lucide="key" style="width:16px;height:16px;"></i> Cambiar contraseña
                </button>
            </div>
        @else
            <button type="button" onclick="openPasswordModal()" class="secondary-btn">
                <i data-lucide="key" style="width:16px;height:16px;"></i> Cambiar contraseña
            </button>
        @endif

        {{-- Botón Eliminar Cuenta --}}
        <button type="button" onclick="openDeleteModal()" class="danger-btn">
            <i data-lucide="trash-2" style="width:16px;height:16px;"></i> Eliminar cuenta
        </button>
    </div>

    {{-- MODAL CAMBIAR CONTRASEÑA --}}
    @if(!$user->google_id)
    <div id="password-modal-overlay" style="
        display:none;position:fixed;inset:0;z-index:3000;
        background:rgba(0,0,0,.6);backdrop-filter:blur(3px);
        align-items:center;justify-content:center;
    ">
        <div style="background:#fff;border-radius:12px;width:100%;max-width:400px;padding:24px;box-shadow:0 10px 40px rgba(0,0,0,.2);animation:slideUp .2s ease;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h3 style="margin:0;color:#1e293b;font-size:18px;display:flex;align-items:center;gap:8px;">
                    <i data-lucide="key" style="width:20px;color:#3b82f6;"></i>
                    Cambiar Contraseña
                </h3>
                <button onclick="closePasswordModal()" style="background:transparent;border:none;color:#94a3b8;cursor:pointer;font-size:20px;line-height:1;">&times;</button>
            </div>
            
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#475569;">Contraseña Actual</label>
                    <div style="position:relative;">
                        <input type="password" id="profile-current-pwd" name="current_password" required style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:14px;box-sizing:border-box;">
                    </div>
                    @error('current_password')<span style="display:block;color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                </div>
                
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#475569;">Nueva Contraseña</label>
                    <div style="position:relative;">
                        <input type="password" id="profile-new-pwd" name="password" required placeholder="Mínimo 8 caracteres" oninput="checkProfileStrength(this.value); checkProfileMatch();" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:14px;box-sizing:border-box;">
                    </div>
                    
                    <div class="strength-bar-wrap"><div class="strength-bar" id="profile-strength-bar"></div></div>
                    <span class="strength-label" id="profile-strength-label"></span>
                    
                    <div class="pwd-requirements" id="profile-pwd-requirements">
                        <span id="prof-req-length"  class="req-item"><i class="fa-solid fa-circle req-dot"></i> Mínimo 8 caracteres</span>
                        <span id="prof-req-upper"   class="req-item"><i class="fa-solid fa-circle req-dot"></i> Una letra mayúscula</span>
                        <span id="prof-req-number"  class="req-item"><i class="fa-solid fa-circle req-dot"></i> Un número</span>
                        <span id="prof-req-special" class="req-item"><i class="fa-solid fa-circle req-dot"></i> Un carácter especial</span>
                    </div>

                    @error('password')<span style="display:block;color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                </div>
                
                <div style="margin-bottom:24px;" id="profile-confirm-group">
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#475569;">Confirmar Nueva Contraseña</label>
                    <div style="position:relative;">
                        <input type="password" id="profile-confirm-pwd" name="password_confirmation" required oninput="checkProfileMatch()" onpaste="return false;" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:14px;box-sizing:border-box;">
                    </div>
                    <span style="display:none;color:#ef4444;font-size:12px;margin-top:4px;" id="profile-match-error">Las contraseñas no coinciden.</span>
                </div>
                
                <div style="display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" onclick="closePasswordModal()" class="secondary-btn">Cancelar</button>
                    <button type="submit" class="primary-btn">
                        <i data-lucide="key" style="width:15px;height:15px;"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL ELIMINAR CUENTA --}}
    <div id="delete-modal-overlay" style="
        display:none;position:fixed;inset:0;z-index:3000;
        background:rgba(0,0,0,.6);backdrop-filter:blur(3px);
        align-items:center;justify-content:center;
    ">
        <div style="background:#fff;border-radius:12px;width:100%;max-width:400px;padding:24px;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,.2);animation:slideUp .2s ease;">
            <div style="width:48px;height:48px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i data-lucide="alert-triangle" style="color:#dc2626;width:24px;height:24px;"></i>
            </div>
            <h3 style="margin:0 0 12px;color:#1e293b;font-size:18px;">¿Eliminar tu cuenta?</h3>
            <p style="color:#64748b;font-size:14px;margin-bottom:24px;line-height:1.5;">
                Toda tu información, perfil, fotos y solicitudes pendientes se perderán para siempre. Esta acción no se puede deshacer.
            </p>
            <form action="{{ route('profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button type="button" onclick="closeDeleteModal()" class="secondary-btn" style="flex:1;">Cancelar</button>
                    <button type="submit" class="danger-btn" style="flex:1;">
                        <i data-lucide="trash-2" style="width:15px;height:15px;"></i> Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            document.getElementById('delete-modal-overlay').style.display = 'flex';
        }
        function closeDeleteModal() {
            document.getElementById('delete-modal-overlay').style.display = 'none';
        }
        @if(!$user->google_id)
        function openPasswordModal() {
            document.getElementById('password-modal-overlay').style.display = 'flex';
        }
        function closePasswordModal() {
            document.getElementById('password-modal-overlay').style.display = 'none';
        }
        @if($errors->has('current_password') || $errors->has('password'))
            document.addEventListener('DOMContentLoaded', () => openPasswordModal());
        @endif


        function checkProfileStrength(val) {
            const bar = document.getElementById('profile-strength-bar');
            const lbl = document.getElementById('profile-strength-label');
            const req = document.getElementById('profile-pwd-requirements');

            const rules = [
                { id:'prof-req-length',  ok: val.length >= 8 },
                { id:'prof-req-upper',   ok: /[A-Z]/.test(val) },
                { id:'prof-req-number',  ok: /[0-9]/.test(val) },
                { id:'prof-req-special', ok: /[^A-Za-z0-9]/.test(val) },
            ];
            
            if (val.length > 0) {
                req.classList.add('visible');
                rules.forEach(r => {
                    const el = document.getElementById(r.id);
                    if(el){
                        el.classList.toggle('ok',   r.ok);
                        el.classList.toggle('fail', !r.ok);
                    }
                });
            } else {
                req.classList.remove('visible');
                rules.forEach(r => {
                    const el = document.getElementById(r.id);
                    if(el) el.classList.remove('ok','fail');
                });
            }

            let score = rules.filter(r => r.ok).length;
            const levels = [
                { w:'25%',  bg:'#ef4444', txt:'Muy débil', color:'#ef4444' },
                { w:'50%',  bg:'#f97316', txt:'Débil',     color:'#f97316' },
                { w:'75%',  bg:'#eab308', txt:'Regular',   color:'#eab308' },
                { w:'100%', bg:'#22c55e', txt:'Fuerte',    color:'#22c55e' },
            ];
            
            if (val.length === 0) { bar.style.width='0'; lbl.textContent=''; return; }
            const lvl = levels[Math.max(0, score - 1)];
            bar.style.width      = lvl.w;
            bar.style.background = lvl.bg;
            lbl.textContent      = lvl.txt;
            lbl.style.color      = lvl.color;

            checkProfileMatch();
        }

        function checkProfileMatch() {
            const p1  = document.getElementById('profile-new-pwd').value;
            const p2  = document.getElementById('profile-confirm-pwd').value;
            const err = document.getElementById('profile-match-error');
            const grp = document.getElementById('profile-confirm-pwd'); // The input
            
            if (p2.length === 0) { 
                err.style.display='none'; 
                grp.style.borderColor = '#e2e8f0';
                return; 
            }
            
            const ok = p1 === p2;
            err.style.display = ok ? 'none' : 'block';
            grp.style.borderColor = ok ? '#22c55e' : '#ef4444';
            
            // disable submit button if match fails or requirements not met?
            // The backend handles the hard validation, but this gives visual feedback.
        }
        }
        @endif

        function formatPhone(input) {
            // 1. Solo permite +, numeros, espacios y guiones
            let val = input.value.replace(/[^\+0-9\s\-]/g, '');

            // 2. Si hay un '+', solo puede estar al principio
            if (val.indexOf('+') > 0) {
                val = val.replace(/\+/g, (match, offset) => offset === 0 ? '+' : '');
            }

            // 3. Limitar a máximo 10 NÚMEROS
            let digitCount = 0;
            let cutIndex = val.length;
            for (let i = 0; i < val.length; i++) {
                if (/[0-9]/.test(val[i])) {
                    digitCount++;
                    if (digitCount > 10) {
                        cutIndex = i;
                        break;
                    }
                }
            }
            
            input.value = val.substring(0, cutIndex);
        }
    </script>

    {{-- VIEW MEDIA MODAL --}}
    <div id="view-media-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,.95); backdrop-filter:blur(8px); align-items:center; justify-content:center; flex-direction:column; padding:20px;">
        <button onclick="hideViewModal()" class="mm-modal-close">
            <i data-lucide="x" style="width:24px;height:24px; display:inline-block; visibility:visible;"></i>
        </button>
        <div id="view-media-container" style="max-width:90vw; max-height:85vh; border-radius:12px; overflow:hidden; box-shadow:0 24px 80px rgba(0,0,0,.6); position: relative;">
            <!-- content injected via js -->
        </div>
    </div>

    <script>
        function openViewModal(url, type) {
            const container = document.getElementById('view-media-container');
            container.innerHTML = ''; 
            
            if(type === 'photo') {
                const img = document.createElement('img');
                img.src = url;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '85vh';
                img.style.display = 'block';
                img.style.objectFit = 'contain';
                container.appendChild(img);
            } else {
                const vid = document.createElement('video');
                vid.src = url;
                vid.controls = true;
                vid.autoplay = true;
                vid.style.maxWidth = '100%';
                vid.style.maxHeight = '85vh';
                vid.style.display = 'block';
                vid.style.backgroundColor = '#000';
                vid.style.outline = 'none';
                container.appendChild(vid);
            }
            
            document.getElementById('view-media-modal').style.display = 'flex';
            if (window.lucide) { lucide.createIcons(); }
        }

        function hideViewModal() {
            const container = document.getElementById('view-media-container');
            container.innerHTML = ''; // This stops the video from playing
            document.getElementById('view-media-modal').style.display = 'none';
        }
    </script>
@endsection
