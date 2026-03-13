@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- SUCCESS FLASH --}}
    @if(session('success'))
        <div id="flash-msg" style="
            position: fixed; top: 20px; right: 24px; z-index: 9999;
            background: #22c55e; color: #fff;
            padding: 14px 24px; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
            display: flex; align-items: center; gap: 10px;
        ">
            <i data-lucide="check-circle" style="width:18px;height:18px;"></i>
            {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3500);</script>
    @endif

    {{-- ── NBF COVER + FORM LAYOUT ─────────────────────────────────────────────────── --}}
    <div class="nbf-layout">

        {{-- 1. HEADER / COVER AREA --}}
        <div class="nbf-header">
            {{-- B&W Placeholder Cover Image --}}
            <div class="nbf-cover" style="background-image: url('https://images.unsplash.com/photo-1514525253161-7a46d19cd819?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">
                {{-- Opcional: overlay para oscurecer un poco si es necesario --}}
                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.1);"></div>
                
                {{-- Cover image visual element only --}}
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
                        <span class="nbf-stat">5.0 ⭐</span>
                    </div>
                </div>

                <div class="nbf-header-actions">
                    <button type="button" class="nbf-action-btn primary" onclick="openEditModal()">Editar perfil</button>
                    <button type="button" class="nbf-action-btn secondary" onclick="openPreviewModal()">Vista Previa</button>
                </div>
            </div>
        </div>

        {{-- 2. CONTENT AREA (Form Style) --}}
        <div class="nbf-content">
            
            {{-- Personal Details Section --}}
            <div class="nbf-section">
                <h2 class="nbf-section-title">Datos Personales</h2>
                
                <div class="nbf-detail-card">
                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label">Nombre Artístico</span>
                        <span class="nbf-detail-value">{{ $profile->stage_name ?? $user->name }}</span>
                    </div>
                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label">Ubicación</span>
                        <span class="nbf-detail-value">{{ $profile->location ?? 'No especificada' }}</span>
                    </div>
                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label">Teléfono</span>
                        <span class="nbf-detail-value">{{ $profile->phone ?? '+52 ...' }}</span>
                    </div>
                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label">Correo Electrónico</span>
                        <span class="nbf-detail-value">{{ $user->email }}</span>
                    </div>
                </div>

                <h3 class="nbf-subsection-title">Redes Sociales</h3>
                <div class="nbf-detail-card">
                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label"><i data-lucide="instagram" style="width: 18px; height: 18px; vertical-align:middle; margin-right:6px; color: #E1306C;"></i> Instagram</span>
                        <span class="nbf-detail-value">
                            @if($profile->instagram)
                                <a href="https://instagram.com/{{ ltrim($profile->instagram, '@') }}" target="_blank" class="nbf-social-link">{{ '@' . ltrim($profile->instagram, '@') }}</a>
                            @else
                                <span style="color:#a0aec0;">No configurado</span>
                            @endif
                        </span>
                    </div>
                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label"><i data-lucide="facebook" style="width: 18px; height: 18px; vertical-align:middle; margin-right:6px; color: #1877F2;"></i> Facebook</span>
                        <span class="nbf-detail-value">
                            @if($profile->facebook)
                                <a href="{{ Str::startsWith($profile->facebook, ['http://', 'https://']) ? $profile->facebook : 'https://' . $profile->facebook }}" target="_blank" class="nbf-social-link">Visitar Perfil</a>
                            @else
                                <span style="color:#a0aec0;">No configurado</span>
                            @endif
                        </span>
                    </div>
                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label"><i data-lucide="youtube" style="width: 18px; height: 18px; vertical-align:middle; margin-right:6px; color: #FF0000;"></i> YouTube</span>
                        <span class="nbf-detail-value">
                            @if($profile->youtube)
                                <a href="{{ Str::startsWith($profile->youtube, ['http://', 'https://']) ? $profile->youtube : 'https://' . $profile->youtube }}" target="_blank" class="nbf-social-link">Ver Canal</a>
                            @else
                                <span style="color:#a0aec0;">No configurado</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <hr class="nbf-divider">

            {{-- Professional Details Section --}}
            <div class="nbf-section">
                <h2 class="nbf-section-title">Datos Profesionales</h2>
                
                <div class="nbf-detail-card">
                    <div class="nbf-detail-row is-vertical">
                        <span class="nbf-detail-label">Biografía / Acerca de</span>
                        <span class="nbf-detail-value" style="white-space: pre-wrap; font-weight: 400; line-height: 1.6;">
                            @if(empty($profile->bio))
                                <span style="color:#a0aec0;">Escribe algo sobre tu trayectoria e instrumentos...</span>
                            @else
                                {!! nl2br(e($profile->bio)) !!}
                            @endif
                        </span>
                    </div>

                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label">Tarifa Base (MXN / hr)</span>
                        <span class="nbf-detail-value">
                            {{ $profile->hourly_rate ? '$' . number_format($profile->hourly_rate, 0) : 'Por acordar' }}
                        </span>
                    </div>
                    <div class="nbf-detail-row">
                        <span class="nbf-detail-label">Estado de Disponibilidad</span>
                        <span class="nbf-detail-value" style="color: #2b6cb0;">Disponible</span>
                    </div>
                    
                    <div class="nbf-detail-row is-vertical">
                        <span class="nbf-detail-label">Notas de Cobertura</span>
                        <span class="nbf-detail-value" style="font-weight: 400;">
                            {{ $profile->coverage_notes ?? 'Especifica hasta dónde puedes viajar para tocar.' }}
                        </span>
                    </div>
                </div>
            </div>

            <hr class="nbf-divider">

            {{-- Services / Genres Section --}}
            <div class="nbf-section">
                <h2 class="nbf-section-title">Servicios y Categorías</h2>
                
                <div style="display:flex; flex-wrap:wrap; gap:12px;">
                    @forelse($profile->genres ?? [] as $genre)
                        <div class="inline-tag" style="background:#fafafa; border:1px solid #e2e8f0; border-radius:6px;">
                            <i data-lucide="music" style="width:14px;color:#cbd5e1;"></i> {{ $genre->name }}
                        </div>
                    @empty
                        <div style="color:#a0aec0; width:100%; padding: 12px 16px; background:#fafafa; border:1px solid #e2e8f0; border-radius:6px; font-size: 14px;">No hay géneros asignados.</div>
                    @endforelse
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
                            <i data-lucide="star" style="width:12px;height:12px;fill:currentColor;"></i> Destacado
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
        /* 
         * NBF COVER + FORM LAYOUT SYSTEM
         * Strict constraints: 
         * Backgrounds: White (#ffffff) for body content, subtle greys (#f8f9fa) for inputs.
         * Structure: Wide cover image, overlapping avatar, clean labels and boxy inputs.
         */

        .nbf-layout {
            background: #fff;
            border-radius: 8px; /* Optional, depending on main dashboard container */
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            margin-bottom: 24px;
        }

        /* ── HEADER & COVER ── */
        .nbf-header {
            width: 100%;
            position: relative;
        }

        .nbf-cover {
            width: 100%;
            height: 240px;
            background-color: #e2e8f0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            /* Filter to ensure it's black and white as requested */
            filter: grayscale(100%);
        }

        /* Action button on cover */
        .cover-edit {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.5);
            color: #fff;
            border-radius: 50%;
            width: 36px; height: 36px;
        }

        /* Info Bar underneath cover */
        .nbf-info-bar {
            display: flex;
            align-items: flex-start;
            padding: 0 40px 24px 180px; /* Space for absolute avatar */
            position: relative;
            min-height: 100px;
            border-bottom: 1px solid #edf2f7;
        }

        @media (max-width: 768px) {
            .nbf-info-bar {
                flex-direction: column;
                padding: 70px 24px 24px 24px;
                align-items: center;
                text-align: center;
            }
        }

        /* Avatar */
        .nbf-avatar-container {
            position: absolute;
            left: 40px;
            top: -60px; /* Overlaps cover by 60px */
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #fff;
            padding: 4px; /* White border effect */
            z-index: 10;
        }

        @media (max-width: 768px) {
            .nbf-avatar-container { left: 50%; transform: translateX(-50%); }
        }

        .nbf-avatar-container img, .nbf-avatar-initials {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #eef2fb;
        }
        
        .nbf-avatar-initials {
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; font-weight: 700; color: #fff; background: #2b6cb0;
        }

        .avatar-edit {
            position: absolute;
            bottom: 4px;
            right: 4px;
            background: #fff;
            color: #4a5568;
            border-radius: 50%;
            width: 32px; height: 32px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        /* Shared Edit Button style */
        .nbf-edit-btn {
            border: none;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .nbf-edit-btn i { width: 14px; height: 14px; }
        .nbf-edit-btn:hover { transform: scale(1.05); }

        /* User Info (Name & Links) */
        .nbf-user-info {
            padding-top: 20px;
            flex: 1;
        }

        .nbf-name {
            font-size: 24px;
            font-weight: 600;
            color: #1a202c;
            margin: 0 0 8px 0;
            line-height: 1;
        }

        .nbf-stats-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        @media (max-width: 768px) {
            .nbf-stats-links { justify-content: center; margin-bottom: 20px; }
        }

        .nbf-stat {
            font-size: 13px;
            color: #3182ce;
            font-weight: 500;
        }

        .nbf-dot { color: #cbd5e1; font-size: 10px; }

        /* Header Actions (Buttons) */
        .nbf-header-actions {
            padding-top: 24px;
            display: flex;
            gap: 12px;
        }

        .nbf-action-btn {
            padding: 10px 20px;
            border-radius: 24px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
        }

        .nbf-action-btn.primary {
            border: 1px solid #3182ce;
            color: #3182ce;
        }
        .nbf-action-btn.primary:hover { background: #ebf8ff; }

        .nbf-action-btn.secondary {
            border: 1px solid #e2e8f0;
            color: #4a5568;
        }
        .nbf-action-btn.secondary:hover { background: #f7fafc; }

        /* ── CONTENT AREA (FORM GRID) ── */
        .nbf-content {
            padding: 40px;
            background: #fff;
        }

        @media (max-width: 768px) {
            .nbf-content { padding: 24px; }
        }

        .nbf-section {
            margin-bottom: 32px;
        }

        .nbf-divider {
            border: 0;
            height: 1px;
            background: #f1f5f9;
            margin: 32px 0;
        }

        .nbf-section-title {
            font-size: 18px;
            color: #a0aec0;
            font-weight: 500;
            margin: 0 0 24px 0;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }

        .nbf-detail-card {
            background: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .nbf-detail-row {
            display: grid;
            grid-template-columns: 220px 1fr;
            align-items: center;
            gap: 24px;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .nbf-detail-row.is-vertical {
            grid-template-columns: 1fr;
            align-items: flex-start;
            gap: 8px;
        }
        .nbf-detail-row:last-child {
            border-bottom: none;
        }
        .nbf-detail-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 600;
        }
        .nbf-detail-value {
            font-size: 14px;
            color: #1e293b;
            font-weight: 500;
            text-align: left;
            word-break: break-word;
        }
        .nbf-social-link {
            color: #2b6cb0;
            text-decoration: none;
            font-weight: 600;
        }
        .nbf-social-link:hover {
            text-decoration: underline;
        }
        .nbf-subsection-title {
            font-size: 15px;
            color: #4a5568;
            font-weight: 700;
            margin: 0 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .nbf-detail-row {
                grid-template-columns: 1fr;
                gap: 6px;
            }
        }

        .inline-tag {
            width: auto;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            font-weight: 500;
            color: #2b6cb0;
        }

        .nbf-btn-secondary {
            border-radius: 6px;
            font-size: 13px; font-weight: 600;
            padding: 10px 16px; 
            border: 1px solid #ced4da;
            background: #fff; color: #495057;
            cursor: pointer; transition: all .2s;
            text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
        }

        /* ── Media Showcase ─────────────────────────────── */
        .nbf-media-showcase {
            width: 100%;
        }
        .nbf-media-grid {
            display: grid; gap: 12px;
        }
        .photos-grid {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        }
        .videos-grid {
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        }
        .nbf-media-item {
            position: relative; border-radius: 12px; overflow: hidden;
            background: #f1f5f9; cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,.05);
        }
        .photos-grid .nbf-media-item {
            aspect-ratio: 1; /* Squarish for photos */
        }
        .videos-grid .nbf-media-item {
            aspect-ratio: 16/9; /* Widescreen for videos */
        }
        .nbf-media-item img, .nbf-media-item video {
            width: 100%; height: 100%; object-fit: cover;
            display: block; transition: transform .4s ease;
        }
        .nbf-media-item:hover img, .nbf-media-item:hover video {
            transform: scale(1.05);
        }
        .nbf-media-overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,.3);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity .3s;
        }
        .nbf-media-item:hover .nbf-media-overlay {
            opacity: 1;
        }
        .nbf-play-indicator {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 48px; height: 48px; border-radius: 50%;
            background: rgba(108,63,197,.9); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(0,0,0,.3); transition: transform .2s;
        }
        .video-item:hover .nbf-play-indicator {
            transform: translate(-50%, -50%) scale(1.1);
        }
        .nbf-featured-badge {
            position: absolute; top: 12px; left: 12px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff; font-size: 11px; font-weight: 700;
            padding: 4px 10px; border-radius: 20px;
            display: flex; align-items: center; gap: 4px;
            box-shadow: 0 4px 12px rgba(245,158,11,.4);
            z-index: 2;
        }

        /* Modal Button Overrides */
        #view-media-modal button svg {
            display: inline-block !important;
            flex-shrink: 0 !important;
            visibility: visible !important;
        }
        .mm-modal-close {
            position:absolute !important; top:24px !important; right:24px !important; background:rgba(255,255,255,.1) !important;
            border:none !important; width:48px !important; height:48px !important; border-radius:50% !important; color:#fff !important;
            display:flex !important; align-items:center !important; justify-content:center !important; cursor:pointer !important;
            transition:background .2s !important; z-index: 10000 !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important;
        }
        .mm-modal-close:hover { background: rgba(255,255,255,.2) !important; transform: scale(1.1) !important; }
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
                @if($genres->isNotEmpty())
                <div class="form-group">
                    <label>Géneros Musicales</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:6px;">
                        @foreach($genres as $genre)
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;font-size:13px;background:#f8f9fa;padding:8px 10px;border-radius:8px;border:1.5px solid {{ $profile && $profile->genres->contains($genre->id) ? 'var(--accent-blue)' : '#e5e7eb' }};transition:border .2s;">
                                <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                                       {{ $profile && $profile->genres->contains($genre->id) ? 'checked' : '' }}
                                       style="width:15px;height:15px;accent-color:var(--accent-blue);">
                                {{ $genre->name }}
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

    {{-- ─── ESTILOS LOCALES ─────────────────────────────────────── --}}
    <style>
        .profile-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
        .skills-list { display:flex;flex-wrap:wrap;gap:8px; }
        .skills-list span { background:rgba(47,147,245,.1);color:var(--accent-blue);padding:6px 14px;border-radius:20px;font-size:13px;font-weight:500; }
        .genre-badge { background:#f3f4f6;color:var(--text-dim);padding:4px 10px;border-radius:4px;font-size:12px; }
        .social-list { list-style:none;padding:0;margin:0; }
        .social-list li { display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px dashed var(--border-light); }
        .social-list li:last-child { border-bottom:none; }
        .social-list a { color:var(--text-main);text-decoration:none;font-size:14px; }
        .social-list a:hover { color:var(--accent-blue); }
        .social-list i { width:18px;height:18px;color:var(--text-dim); }
        .contact-item { margin-bottom:18px; }
        .contact-item label { display:block;font-size:11px;text-transform:uppercase;color:var(--text-dim);font-weight:700;margin-bottom:4px; }
        .contact-item p { margin:0;font-size:15px; }
        .map-placeholder { background:#e5e7eb;height:140px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-dim);font-size:13px; }
        .avatar-initials { display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#6c3fc5,#2f93f5);color:#fff;font-size:28px;font-weight:700; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--text-main); }
        .form-group input, .form-group textarea { width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;color:var(--text-main);background:#fafafa;box-sizing:border-box;transition:border .2s; }
        .form-group input:focus, .form-group textarea:focus { border-color:var(--accent-blue);outline:none;background:#fff; }
        @keyframes slideUp { from { opacity:0;transform:translateY(30px); } to { opacity:1;transform:translateY(0); } }
        @media (max-width: 768px) { .profile-grid { grid-template-columns:1fr; } }

        /* Estilos para el Cambio de Contraseña */
        .strength-bar-wrap { height:4px; background:#e5e7eb; border-radius:2px; margin-top:8px; overflow:hidden; }
        .strength-bar { height:100%; width:0%; border-radius:2px; transition:width .3s, background .3s; }
        .strength-label { font-size:11px; font-weight:600; margin-top:4px; display:block; }
        .pwd-requirements { display: none; flex-direction: column; gap: 4px; margin-top: 10px; padding: 10px 12px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; }
        .pwd-requirements.visible { display: flex; }
        .req-item { font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 7px; transition: color .2s; }
        .req-item .req-dot { font-size: 7px; flex-shrink: 0; }
        .req-item.ok { color: #16a34a; }
        .req-item.ok .req-dot { color: #16a34a; }
        .req-item.fail { color: #dc2626; }
        .req-item.fail .req-dot { color: #dc2626; }
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
                                <span style="font-size:12px;color:#374151;font-weight:500;">{{ $profile->facebook }}</span>
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

                        {{-- Botón contratar (app) --}}
                        <div style="padding:0 16px 24px;">
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
            background: linear-gradient(180deg,#1a0b38 0%,#6c3fc5 100%);
            padding: 20px 16px 24px;
            text-align: center;
            flex-shrink: 0;
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
            <div title="Tu cuenta está vinculada con Google. La contraseña se gestiona desde tu cuenta de Google." style="cursor: not-allowed;">
                <button type="button" disabled style="
                    display: inline-flex; align-items: center; gap: 8px;
                    background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0;
                    padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; 
                    cursor: not-allowed; opacity: 0.7;
                ">
                    <i data-lucide="key" style="width:16px; height:16px;"></i>
                    Cambiar contraseña
                </button>
            </div>
        @else
            <button type="button" onclick="openPasswordModal()" style="
                display: inline-flex; align-items: center; gap: 8px;
                background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
                padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; 
                cursor: pointer; transition: all 0.2s;
            " onmouseover="this.style.background='#e2e8f0';" onmouseout="this.style.background='#f1f5f9';">
                <i data-lucide="key" style="width:16px; height:16px;"></i>
                Cambiar contraseña
            </button>
        @endif

        {{-- Botón Eliminar Cuenta --}}
        <button type="button" onclick="openDeleteModal()" style="
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff0f0; color: #e11d48; border: 1px solid #ffe4e6;
            padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; 
            cursor: pointer; transition: all 0.2s;
        " onmouseover="this.style.background='#ffe4e6'; this.style.borderColor='#fecdd3';" 
           onmouseout="this.style.background='#fff0f0'; this.style.borderColor='#ffe4e6';">
            <i data-lucide="trash-2" style="width:16px; height:16px;"></i>
            Eliminar cuenta permanentemente
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
                    <button type="button" onclick="closePasswordModal()" style="background:#f1f5f9;color:#475569;border:none;padding:10px 16px;border-radius:6px;font-weight:600;cursor:pointer;">Cancelar</button>
                    <button type="submit" style="background:#3b82f6;color:#fff;border:none;padding:10px 16px;border-radius:6px;font-weight:600;cursor:pointer;">Actualizar</button>
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
                    <button type="button" onclick="closeDeleteModal()" style="flex:1;background:#f1f5f9;color:#475569;border:none;padding:10px;border-radius:6px;font-weight:600;cursor:pointer;">Cancelar</button>
                    <button type="submit" style="flex:1;background:#dc2626;color:#fff;border:none;padding:10px;border-radius:6px;font-weight:600;cursor:pointer;">Sí, eliminar</button>
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
