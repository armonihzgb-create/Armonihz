@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- PAGE HEADER --}}
    <div class="rq-header">
        <div>
            <div class="rq-eyebrow">
                <i data-lucide="inbox" style="width:14px;height:14px;color:#6c3fc5;"></i>
                BANDEJA DE ENTRADA
            </div>
            <h1 class="rq-title">Solicitudes de Contratación</h1>
            <p class="rq-subtitle">Gestiona las propuestas que los clientes te han enviado.</p>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="rq-filter-bar">
        <button class="rq-filter-chip active">Todas <span class="rq-chip-count">5</span></button>
        <button class="rq-filter-chip">Pendientes <span class="rq-chip-count" style="background:#fef9c3;color:#ca8a04;">3</span></button>
        <button class="rq-filter-chip">Respondidas <span class="rq-chip-count" style="background:#eff6ff;color:#2563eb;">1</span></button>
        <button class="rq-filter-chip">Confirmadas <span class="rq-chip-count" style="background:#f0fdf4;color:#16a34a;">1</span></button>
    </div>

    {{-- REQUESTS LIST --}}
    @php
        $samples = [
            [
                'id'       => 1,
                'name'     => 'Juan Pérez',
                'initials' => 'JP',
                'color'    => '#ede9fe',
                'tcolor'   => '#6c3fc5',
                'event'    => 'Boda Civil',
                'desc'     => 'Música para boda de 150 personas en salón',
                'location' => 'Guadalajara, Jal.',
                'date_d'   => '15',
                'date_m'   => 'OCT',
                'budget'   => '$18,000',
                'status'   => 'pending',
                'slabel'   => '⏳ Pendiente',
                'sclass'   => 'rq-badge--yellow',
                'received' => 'Hace 2 horas',
            ],
            [
                'id'       => 2,
                'name'     => 'María Rodríguez',
                'initials' => 'MR',
                'color'    => '#f0fdf4',
                'tcolor'   => '#16a34a',
                'event'    => 'Fiesta de Quinceañera',
                'desc'     => 'Banda para fiesta de 80 personas al aire libre',
                'location' => 'Puebla, Pue.',
                'date_d'   => '20',
                'date_m'   => 'OCT',
                'budget'   => '$12,000',
                'status'   => 'accepted',
                'slabel'   => '✓ Confirmada',
                'sclass'   => 'rq-badge--green',
                'received' => 'Ayer',
            ],
            [
                'id'       => 3,
                'name'     => 'Carlos Sánchez',
                'initials' => 'CS',
                'color'    => '#eff6ff',
                'tcolor'   => '#2563eb',
                'event'    => 'Cumpleaños empresarial',
                'desc'     => 'Música en vivo para evento corporativo',
                'location' => 'Ciudad de México',
                'date_d'   => '28',
                'date_m'   => 'OCT',
                'budget'   => '$25,000',
                'status'   => 'replied',
                'slabel'   => '💬 Respondida',
                'sclass'   => 'rq-badge--blue',
                'received' => 'Hace 3 días',
            ],
            [
                'id'       => 4,
                'name'     => 'Ana López',
                'initials' => 'AL',
                'color'    => '#fefce8',
                'tcolor'   => '#ca8a04',
                'event'    => 'Boda Religiosa',
                'desc'     => 'Repertorio clásico para ceremonia y recepción',
                'location' => 'Tehuacán, Pue.',
                'date_d'   => '5',
                'date_m'   => 'NOV',
                'budget'   => '$15,000',
                'status'   => 'pending',
                'slabel'   => '⏳ Pendiente',
                'sclass'   => 'rq-badge--yellow',
                'received' => 'Hace 4 días',
            ],
            [
                'id'       => 5,
                'name'     => 'Roberto Díaz',
                'initials' => 'RD',
                'color'    => '#fef2f2',
                'tcolor'   => '#dc2626',
                'event'    => 'Festival de barrio',
                'desc'     => 'Tocada en plaza pública, 3 horas de duración',
                'location' => 'Oaxaca, Oax.',
                'date_d'   => '12',
                'date_m'   => 'NOV',
                'budget'   => '$8,000',
                'status'   => 'pending',
                'slabel'   => '⏳ Pendiente',
                'sclass'   => 'rq-badge--yellow',
                'received' => 'Hace 5 días',
            ],
        ];
    @endphp

    <div class="rq-list">
        @foreach($samples as $req)
        <div class="rq-card {{ $req['status'] === 'accepted' ? 'rq-card--accepted' : '' }}">

            {{-- Avatar + name --}}
            <div class="rq-card-client">
                <div class="rq-avatar" style="background:{{ $req['color'] }};color:{{ $req['tcolor'] }};">
                    {{ $req['initials'] }}
                </div>
                <div>
                    <span class="rq-client-name">{{ $req['name'] }}</span>
                    <span class="rq-client-meta">
                        <i data-lucide="clock" style="width:11px;height:11px;"></i>
                        {{ $req['received'] }}
                    </span>
                </div>
            </div>

            {{-- Event info --}}
            <div class="rq-card-event">
                <span class="rq-event-type">
                    <i data-lucide="music" style="width:12px;height:12px;color:#6c3fc5;"></i>
                    {{ $req['event'] }}
                </span>
                <span class="rq-event-desc">{{ $req['desc'] }}</span>
                <span class="rq-event-location">
                    <i data-lucide="map-pin" style="width:12px;height:12px;"></i>
                    {{ $req['location'] }}
                </span>
            </div>

            {{-- Date --}}
            <div class="rq-date-box">
                <span class="rq-date-m">{{ $req['date_m'] }}</span>
                <span class="rq-date-d">{{ $req['date_d'] }}</span>
            </div>

            {{-- Budget --}}
            <div class="rq-budget">
                <span class="rq-budget-label">Presupuesto</span>
                <span class="rq-budget-value">{{ $req['budget'] }} <small>MXN</small></span>
            </div>

            {{-- Status + action --}}
            <div class="rq-card-actions">
                <span class="rq-badge {{ $req['sclass'] }}">{{ $req['slabel'] }}</span>
                <a href="/requests/{{ $req['id'] }}" class="rq-view-btn">
                    Ver detalle <i data-lucide="arrow-right" style="width:13px;height:13px;"></i>
                </a>
            </div>

        </div>
        @endforeach
    </div>

    <style>
        /* ── Header ─────────────────────────────── */
        .rq-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 24px; padding-bottom: 22px;
            border-bottom: 1px solid #f1f5f9;
        }
        .rq-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .rq-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .rq-subtitle { font-size: 14px; color: #64748b; margin: 0; }

        /* ── Filter Bar ─────────────────────────── */
        .rq-filter-bar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
        .rq-filter-chip {
            padding: 7px 16px; border-radius: 999px; font-size: 13px; font-weight: 500;
            border: 1.5px solid #e2e8f0; color: #64748b; background: #fff;
            cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 6px;
        }
        .rq-filter-chip:hover { border-color: #6c3fc5; color: #6c3fc5; }
        .rq-filter-chip.active { background: #6c3fc5; color: #fff; border-color: #6c3fc5; }
        .rq-chip-count {
            background: rgba(255,255,255,.25); border-radius: 999px;
            padding: 1px 7px; font-size: 11px; font-weight: 700;
        }
        .rq-filter-chip:not(.active) .rq-chip-count {
            background: #f1f5f9; color: #64748b;
        }

        /* ── Cards ──────────────────────────────── */
        .rq-list { display: flex; flex-direction: column; gap: 12px; }
        .rq-card {
            display: grid;
            grid-template-columns: 200px 1fr 70px 130px 160px;
            gap: 16px;
            align-items: center;
            background: #fff;
            border: 1.5px solid #e8edf3;
            border-radius: 14px;
            padding: 18px 22px;
            transition: box-shadow .2s, border-color .2s;
        }
        .rq-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.05); border-color: #d1d5db; }
        .rq-card--accepted { border-color: #bbf7d0; background: linear-gradient(140deg, rgba(22,163,74,.03), #fff); }

        /* Client */
        .rq-card-client { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .rq-avatar {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; flex-shrink: 0;
        }
        .rq-client-name { display: block; font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
        .rq-client-meta { display: flex; align-items: center; gap: 4px; font-size: 11.5px; color: #94a3b8; }

        /* Event */
        .rq-card-event { display: flex; flex-direction: column; gap: 3px; min-width: 0; }
        .rq-event-type { display: flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 700; color: #6c3fc5; }
        .rq-event-desc { font-size: 13.5px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .rq-event-location { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #94a3b8; }

        /* Date */
        .rq-date-box {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            width: 52px; height: 54px; border-radius: 10px;
            background: #f8fafc; border: 1.5px solid #f1f5f9;
        }
        .rq-date-m { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; }
        .rq-date-d { font-size: 22px; font-weight: 900; color: #0f172a; line-height: 1.1; }

        /* Budget */
        .rq-budget { display: flex; flex-direction: column; }
        .rq-budget-label { font-size: 11px; color: #94a3b8; font-weight: 500; margin-bottom: 2px; }
        .rq-budget-value { font-size: 17px; font-weight: 800; color: #15803d; }
        .rq-budget-value small { font-size: 11px; font-weight: 400; color: #94a3b8; }

        /* Actions */
        .rq-card-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
        .rq-badge {
            font-size: 11px; font-weight: 600; padding: 4px 11px;
            border-radius: 999px; white-space: nowrap;
        }
        .rq-badge--yellow { background: #fefce8; color: #ca8a04; border: 1px solid #fef08a; }
        .rq-badge--green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .rq-badge--blue   { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .rq-view-btn {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 600; color: #6c3fc5; text-decoration: none;
            padding: 6px 14px; border-radius: 7px; border: 1.5px solid #e2e8f0;
            background: #f9fafb; transition: all .2s;
        }
        .rq-view-btn:hover { border-color: #6c3fc5; background: #f5f3ff; }

        @media (max-width: 1100px) {
            .rq-card { grid-template-columns: 1fr 1fr; }
            .rq-date-box, .rq-budget { display: none; }
        }
        @media (max-width: 640px) {
            .rq-card { grid-template-columns: 1fr; }
            .rq-card-actions { flex-direction: row; justify-content: space-between; }
        }
    </style>

@endsection
