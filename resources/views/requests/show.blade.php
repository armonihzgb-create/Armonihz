@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- BREADCRUMB --}}
    <div class="rqs-breadcrumb">
        <a href="{{ url('/requests') }}" class="rqs-back-link">
            <i data-lucide="arrow-left" style="width:15px;height:15px;"></i>
            Solicitudes
        </a>
        <span class="rqs-breadcrumb-sep">/</span>
        <span>Solicitud #2045</span>
    </div>

    {{-- TOP BAR --}}
    <div class="rqs-topbar">
        <div>
            <div class="rqs-eyebrow">
                <i data-lucide="inbox" style="width:14px;height:14px;color:#6c3fc5;"></i>
                SOLICITUD #2045
            </div>
            <h1 class="rqs-title">Boda Civil — Juan Pérez</h1>
            <p class="rqs-subtitle">Recibida el 14 Oct, 2026 · 10:30 AM</p>
        </div>
        <div class="rqs-action-btns">
            <button class="rqs-reject-btn" onclick="alert('Próximamente')">
                <i data-lucide="x" style="width:15px;height:15px;"></i>
                Rechazar
            </button>
            <button class="rqs-accept-btn" onclick="alert('Próximamente')">
                <i data-lucide="check" style="width:15px;height:15px;"></i>
                Aceptar solicitud
            </button>
        </div>
    </div>

    <div class="rqs-layout">

        {{-- ── LEFT COLUMN ─────────────────────────── --}}
        <div class="rqs-left">

            {{-- Client card --}}
            <div class="rqs-section-card">
                <h3 class="rqs-card-title">
                    <i data-lucide="user" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Información del Cliente
                </h3>

                <div class="rqs-client-profile">
                    <div class="rqs-avatar">JP</div>
                    <div>
                        <span class="rqs-client-name">Juan Pérez</span>
                        <span class="rqs-client-verified">
                            <i data-lucide="badge-check" style="width:13px;height:13px;color:#2563eb;"></i>
                            Cliente Verificado
                        </span>
                        <span class="rqs-stars">⭐⭐⭐⭐⭐ <small>(5.0)</small></span>
                    </div>
                </div>

                <div class="rqs-client-stats">
                    <div class="rqs-client-stat">
                        <span class="rqs-stat-label">Eventos realizados</span>
                        <span class="rqs-stat-value">3</span>
                    </div>
                    <div class="rqs-client-stat">
                        <span class="rqs-stat-label">Ubicación</span>
                        <span class="rqs-stat-value">Guadalajara, Jal.</span>
                    </div>
                    <div class="rqs-client-stat">
                        <span class="rqs-stat-label">Miembro desde</span>
                        <span class="rqs-stat-value">Enero 2025</span>
                    </div>
                </div>
            </div>

            {{-- Event details --}}
            <div class="rqs-section-card">
                <h3 class="rqs-card-title">
                    <i data-lucide="calendar" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Datos del Evento
                </h3>

                <div class="rqs-detail-rows">
                    <div class="rqs-detail-row">
                        <div class="rqs-detail-icon" style="background:#ede9fe;">
                            <i data-lucide="calendar" style="width:16px;height:16px;color:#6c3fc5;"></i>
                        </div>
                        <div class="rqs-detail-info">
                            <span class="rqs-detail-label">Fecha y hora</span>
                            <span class="rqs-detail-value">15 Octubre, 2026 — 19:00 hrs</span>
                            <span class="rqs-detail-sub">Duración: 5 horas</span>
                        </div>
                    </div>
                    <div class="rqs-detail-row">
                        <div class="rqs-detail-icon" style="background:#eff6ff;">
                            <i data-lucide="map-pin" style="width:16px;height:16px;color:#2563eb;"></i>
                        </div>
                        <div class="rqs-detail-info">
                            <span class="rqs-detail-label">Ubicación</span>
                            <span class="rqs-detail-value">Salón "Los Arcos", Guadalajara</span>
                            <a href="#" class="rqs-map-link">Ver en Google Maps →</a>
                        </div>
                    </div>
                    <div class="rqs-detail-row">
                        <div class="rqs-detail-icon" style="background:#fdf4ff;">
                            <i data-lucide="music" style="width:16px;height:16px;color:#a855f7;"></i>
                        </div>
                        <div class="rqs-detail-info">
                            <span class="rqs-detail-label">Tipo de evento</span>
                            <span class="rqs-detail-value">Boda Civil</span>
                        </div>
                    </div>
                    <div class="rqs-detail-row rqs-budget-row">
                        <div class="rqs-detail-icon" style="background:#f0fdf4;">
                            <i data-lucide="banknote" style="width:16px;height:16px;color:#16a34a;"></i>
                        </div>
                        <div class="rqs-detail-info">
                            <span class="rqs-detail-label">Presupuesto ofrecido</span>
                            <span class="rqs-price">$18,000 <small>MXN</small></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT COLUMN ────────────────────────── --}}
        <div class="rqs-right">

            {{-- Status badge --}}
            <div class="rqs-status-banner">
                <span class="rqs-badge-lg rqs-badge--pending">⏳ En espera de respuesta</span>
                <span class="rqs-status-time">Hace 2 horas</span>
            </div>

            {{-- Client message --}}
            <div class="rqs-section-card">
                <h3 class="rqs-card-title">
                    <i data-lucide="message-square" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Mensaje del Cliente
                </h3>
                <div class="rqs-msg-bubble">
                    <p>Hola, me gustaría contratar sus servicios para mi boda civil. Sería en el Salón Los Arcos. Nos interesa mucho su repertorio de música clásica y también algo de mariachi para el final de la recepción.</p>
                    <p>¿Tienen disponibilidad para esa fecha? ¿Qué incluye su paquete estándar? Quedo atento a su respuesta.</p>
                </div>
            </div>

            {{-- Reply form --}}
            <div class="rqs-section-card">
                <h3 class="rqs-card-title">
                    <i data-lucide="send" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Tu Respuesta
                </h3>

                <form>
                    <textarea class="rqs-textarea" rows="5"
                        placeholder="Ej: Hola Juan, ¡muchas gracias por contactarnos! Sí tenemos disponibilidad para esa fecha..."></textarea>
                    <div class="rqs-form-footer">
                        <p class="rqs-form-hint">
                            <i data-lucide="info" style="width:12px;height:12px;"></i>
                            Al responder, el estado cambiará a <strong>Respondida</strong>.
                        </p>
                        <button type="button" class="rqs-send-btn" onclick="alert('Próximamente')">
                            <i data-lucide="send" style="width:14px;height:14px;"></i>
                            Enviar respuesta
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <style>
        /* ── Breadcrumb ──────────────────────────── */
        .rqs-breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #94a3b8; margin-bottom: 20px;
        }
        .rqs-back-link {
            display: flex; align-items: center; gap: 5px;
            color: #64748b; text-decoration: none; font-weight: 500; transition: color .2s;
        }
        .rqs-back-link:hover { color: #6c3fc5; }
        .rqs-breadcrumb-sep { color: #e2e8f0; }

        /* ── Top bar ─────────────────────────────── */
        .rqs-topbar {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 24px; padding-bottom: 22px;
            border-bottom: 1px solid #f1f5f9;
        }
        .rqs-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .rqs-title { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .rqs-subtitle { font-size: 13px; color: #94a3b8; margin: 0; }
        .rqs-action-btns { display: flex; gap: 10px; flex-shrink: 0; }
        .rqs-reject-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px; border-radius: 8px; border: 1.5px solid #fecaca;
            background: #fef2f2; color: #dc2626; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .2s;
        }
        .rqs-reject-btn:hover { background: #fee2e2; }
        .rqs-accept-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px; border-radius: 8px;
            background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; font-size: 13px; font-weight: 700; border: none;
            cursor: pointer; box-shadow: 0 4px 14px rgba(108,63,197,.25); transition: opacity .2s;
        }
        .rqs-accept-btn:hover { opacity: .9; }

        /* ── Layout ──────────────────────────────── */
        .rqs-layout { display: grid; grid-template-columns: 340px 1fr; gap: 22px; align-items: start; }
        .rqs-section-card {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 16px; padding: 22px; margin-bottom: 16px;
        }
        .rqs-card-title {
            display: flex; align-items: center; gap: 8px;
            font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 18px;
        }

        /* ── Client ──────────────────────────────── */
        .rqs-client-profile {
            display: flex; align-items: center; gap: 14px; margin-bottom: 18px;
        }
        .rqs-avatar {
            width: 52px; height: 52px; border-radius: 14px;
            background: #ede9fe; color: #6c3fc5;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800; flex-shrink: 0;
        }
        .rqs-client-name { display: block; font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 3px; }
        .rqs-client-verified { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #2563eb; font-weight: 500; margin-bottom: 3px; }
        .rqs-stars { font-size: 13px; color: #f59e0b; }
        .rqs-stars small { color: #64748b; font-weight: 400; }

        .rqs-client-stats {
            display: grid; grid-template-columns: repeat(3,1fr); gap: 10px;
            border-top: 1px solid #f8fafc; padding-top: 14px;
        }
        .rqs-client-stat { display: flex; flex-direction: column; gap: 2px; }
        .rqs-stat-label { font-size: 11px; color: #94a3b8; font-weight: 500; }
        .rqs-stat-value { font-size: 13px; font-weight: 700; color: #0f172a; }

        /* ── Event Details ───────────────────────── */
        .rqs-detail-rows { display: flex; flex-direction: column; gap: 0; }
        .rqs-detail-row {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 13px 0; border-bottom: 1px solid #f8fafc;
        }
        .rqs-detail-row:last-child { border-bottom: none; }
        .rqs-budget-row { background: #f0fdf4; border-radius: 10px; padding: 13px 12px; border: 1px solid #bbf7d0; margin-top: 4px; }
        .rqs-detail-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .rqs-detail-info { display: flex; flex-direction: column; gap: 2px; }
        .rqs-detail-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; }
        .rqs-detail-value { font-size: 14px; font-weight: 600; color: #0f172a; }
        .rqs-detail-sub { font-size: 12px; color: #94a3b8; }
        .rqs-map-link { font-size: 12px; color: #2563eb; text-decoration: none; }
        .rqs-map-link:hover { text-decoration: underline; }
        .rqs-price { font-size: 22px; font-weight: 900; color: #15803d; }
        .rqs-price small { font-size: 12px; font-weight: 400; color: #94a3b8; }

        /* ── Status banner ───────────────────────── */
        .rqs-status-banner {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 18px; background: #fefce8; border: 1.5px solid #fef08a;
            border-radius: 12px; margin-bottom: 16px;
        }
        .rqs-badge-lg { font-size: 13px; font-weight: 600; }
        .rqs-badge--pending { color: #ca8a04; }
        .rqs-badge--accepted { color: #16a34a; }
        .rqs-badge--rejected { color: #dc2626; }
        .rqs-status-time { font-size: 12px; color: #94a3b8; }

        /* ── Message ─────────────────────────────── */
        .rqs-msg-bubble {
            background: #f8fafc; border: 1px solid #f1f5f9;
            border-radius: 12px; padding: 16px 18px;
            font-size: 14.5px; color: #334155; line-height: 1.75;
        }
        .rqs-msg-bubble p { margin: 0 0 10px; }
        .rqs-msg-bubble p:last-child { margin: 0; }

        /* ── Reply ───────────────────────────────── */
        .rqs-textarea {
            width: 100%; padding: 13px 14px; border: 1.5px solid #e2e8f0;
            border-radius: 10px; font-family: inherit; font-size: 14px;
            line-height: 1.6; resize: vertical; box-sizing: border-box;
            transition: border-color .2s; color: #334155;
        }
        .rqs-textarea:focus { border-color: #6c3fc5; outline: none; }
        .rqs-form-footer {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 12px; gap: 12px;
        }
        .rqs-form-hint { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 5px; margin: 0; }
        .rqs-send-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; border-radius: 8px;
            background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; font-size: 13px; font-weight: 700; border: none;
            cursor: pointer; box-shadow: 0 4px 14px rgba(108,63,197,.2); transition: opacity .2s;
            white-space: nowrap;
        }
        .rqs-send-btn:hover { opacity: .9; }

        @media (max-width: 900px) {
            .rqs-layout { grid-template-columns: 1fr; }
            .rqs-topbar, .rqs-action-btns { flex-wrap: wrap; }
            .rqs-client-stats { grid-template-columns: 1fr 1fr; }
        }
    </style>

@endsection
