@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- PAGE HEADER --}}
    <div class="pm-header">
        <div>
            <div class="pm-eyebrow">
                <i data-lucide="bar-chart-2" style="width:14px;height:14px;color:#6c3fc5;"></i>
                MIS CAMPAÑAS
            </div>
            <h1 class="pm-title">Mis Promociones</h1>
            <p class="pm-subtitle">Historial y estadísticas de tus campañas publicitarias</p>
        </div>
        <a href="{{ route('promotions.create') }}" class="pm-new-btn">
            <i data-lucide="zap" style="width:15px;height:15px;"></i>
            Nueva campaña
        </a>
    </div>

    {{-- IMPACT STATS --}}
    <div class="pm-stats-row">
        <div class="pm-stat-card">
            <div class="pm-stat-icon" style="background:#ede9fe;">
                <i data-lucide="eye" style="width:20px;height:20px;color:#6c3fc5;"></i>
            </div>
            <div>
                <div class="pm-stat-value">1,240</div>
                <div class="pm-stat-label">Visualizaciones extra</div>
            </div>
        </div>
        <div class="pm-stat-card">
            <div class="pm-stat-icon" style="background:#eff6ff;">
                <i data-lucide="mouse-pointer-click" style="width:20px;height:20px;color:#2563eb;"></i>
            </div>
            <div>
                <div class="pm-stat-value" style="color:#2563eb;">45</div>
                <div class="pm-stat-label">Clics en perfil</div>
            </div>
        </div>
        <div class="pm-stat-card">
            <div class="pm-stat-icon" style="background:#f0fdf4;">
                <i data-lucide="phone-call" style="width:20px;height:20px;color:#16a34a;"></i>
            </div>
            <div>
                <div class="pm-stat-value" style="color:#16a34a;">8</div>
                <div class="pm-stat-label">Contactos directos</div>
            </div>
        </div>
        <div class="pm-stat-card">
            <div class="pm-stat-icon" style="background:#fefce8;">
                <i data-lucide="megaphone" style="width:20px;height:20px;color:#ca8a04;"></i>
            </div>
            <div>
                <div class="pm-stat-value" style="color:#ca8a04;">2</div>
                <div class="pm-stat-label">Campañas totales</div>
            </div>
        </div>
    </div>

    {{-- CAMPAIGNS LIST --}}
    <div class="pm-section-header">
        <h2 class="pm-section-title">Historial de campañas</h2>
    </div>

    <div class="pm-campaigns">

        {{-- Active campaign --}}
        <div class="pm-campaign-card pm-campaign-active">
            <div class="pm-campaign-left">
                <div class="pm-campaign-status-dot pm-dot-active"></div>
                <div class="pm-campaign-icon" style="background:#ede9fe;">
                    <i data-lucide="user" style="width:20px;height:20px;color:#6c3fc5;"></i>
                </div>
                <div>
                    <span class="pm-campaign-name">Perfil Principal</span>
                    <div class="pm-campaign-meta">
                        <span><i data-lucide="map-pin" style="width:12px;height:12px;"></i> Regional</span>
                        <span><i data-lucide="calendar" style="width:12px;height:12px;"></i> 01 Feb – 15 Feb</span>
                    </div>
                </div>
            </div>
            <div class="pm-campaign-right">
                <div class="pm-countdown">
                    <i data-lucide="clock" style="width:13px;height:13px;"></i>
                    Quedan <strong>6 días</strong>
                </div>
                <span class="pm-status-badge pm-badge-active">Activa</span>
                <span class="pm-campaign-price">$250 MXN</span>
            </div>
        </div>

        {{-- Past campaign --}}
        <div class="pm-campaign-card">
            <div class="pm-campaign-left">
                <div class="pm-campaign-status-dot pm-dot-done"></div>
                <div class="pm-campaign-icon" style="background:#f1f5f9;">
                    <i data-lucide="video" style="width:20px;height:20px;color:#94a3b8;"></i>
                </div>
                <div>
                    <span class="pm-campaign-name" style="color:#64748b;">Video "Boda Civil..."</span>
                    <div class="pm-campaign-meta">
                        <span><i data-lucide="map-pin" style="width:12px;height:12px;"></i> Local</span>
                        <span><i data-lucide="calendar" style="width:12px;height:12px;"></i> 10 Ene – 17 Ene</span>
                    </div>
                </div>
            </div>
            <div class="pm-campaign-right">
                <span class="pm-status-badge pm-badge-done">Finalizada</span>
                <span class="pm-campaign-price" style="color:#94a3b8;">$150 MXN</span>
            </div>
        </div>

    </div>

    {{-- EMPTY STATE - shown when no campaigns --}}
    {{-- Uncomment this block and remove the .pm-campaigns div when there are no campaigns
    <div class="pm-empty">
        <div class="pm-empty-icon"><i data-lucide="megaphone" style="width:40px;height:40px;color:#cbd5e1;"></i></div>
        <h3>Sin campañas todavía</h3>
        <p>Crea tu primera campaña para aparecer destacado ante más clientes.</p>
        <a href="{{ route('promotions.create') }}" class="pm-new-btn" style="display:inline-flex;">
            <i data-lucide="zap" style="width:15px;height:15px;"></i> Crear campaña
        </a>
    </div>
    --}}

    <style>
        /* ── Header ─────────────────────────────── */
        .pm-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 28px; padding-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
        }
        .pm-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .pm-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .pm-subtitle { font-size: 14px; color: #64748b; margin: 0; }
        .pm-new-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; border-radius: 8px;
            background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; font-size: 13px; font-weight: 700; text-decoration: none;
            box-shadow: 0 4px 16px rgba(108,63,197,.25); transition: opacity .2s;
            white-space: nowrap;
        }
        .pm-new-btn:hover { opacity: .9; }

        /* ── Stats ─────────────────────────────── */
        .pm-stats-row {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 28px;
        }
        .pm-stat-card {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 14px; padding: 18px 20px;
            display: flex; align-items: center; gap: 14px;
        }
        .pm-stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .pm-stat-value { font-size: 22px; font-weight: 900; color: #0f172a; line-height: 1; margin-bottom: 3px; }
        .pm-stat-label { font-size: 12px; color: #64748b; font-weight: 500; }

        /* ── Section Header ─────────────────────── */
        .pm-section-header { margin-bottom: 14px; }
        .pm-section-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }

        /* ── Campaign Cards ─────────────────────── */
        .pm-campaigns { display: flex; flex-direction: column; gap: 12px; }
        .pm-campaign-card {
            display: flex; justify-content: space-between; align-items: center;
            gap: 16px; background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 14px; padding: 18px 22px; transition: box-shadow .2s;
        }
        .pm-campaign-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.04); }
        .pm-campaign-active { border-color: #c4b5fd; background: linear-gradient(140deg, rgba(108,63,197,.03), #fff); }

        .pm-campaign-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
        .pm-campaign-status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .pm-dot-active { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.2); }
        .pm-dot-done   { background: #d1d5db; }
        .pm-campaign-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .pm-campaign-name { display: block; font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
        .pm-campaign-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: #64748b; }
        .pm-campaign-meta span { display: flex; align-items: center; gap: 4px; }

        .pm-campaign-right { display: flex; align-items: center; gap: 14px; flex-shrink: 0; }
        .pm-countdown { font-size: 12px; color: #d97706; font-weight: 600; display: flex; align-items: center; gap: 5px; background: #fef9c3; padding: 4px 10px; border-radius: 999px; }
        .pm-status-badge { font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 999px; }
        .pm-badge-active { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .pm-badge-done   { background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; }
        .pm-campaign-price { font-size: 15px; font-weight: 800; color: #0f172a; }

        /* ── Empty ────────────────────────────── */
        .pm-empty { text-align: center; padding: 80px 0; color: #94a3b8; }
        .pm-empty-icon { margin-bottom: 16px; }
        .pm-empty h3  { font-size: 18px; color: #64748b; margin: 0 0 8px; }
        .pm-empty p   { font-size: 14px; margin: 0 0 20px; }

        @media (max-width: 900px) { .pm-stats-row { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) {
            .pm-header, .pm-campaign-card { flex-direction: column; align-items: flex-start; }
            .pm-campaign-right { flex-wrap: wrap; }
            .pm-stats-row { grid-template-columns: 1fr 1fr; }
        }
    </style>

@endsection
