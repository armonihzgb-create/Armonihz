@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- ── STYLES FIRST to prevent FOUC ──────────────────────── --}}
    <style>
        /* ── Page Header ─────────────────────────────── */
        .casting-page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
        }
        .casting-page-eyebrow {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            color: #6c3fc5;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .casting-page-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px;
        }
        .casting-page-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }
        .casting-secondary-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid #e2e8f0;
            transition: all .2s;
            white-space: nowrap;
        }
        .casting-secondary-btn:hover { background: #e2e8f0; border-color: #cbd5e1; }

        /* ── Filter Bar ──────────────────────────────── */
        .casting-filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .casting-filter-chip {
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            border: 1.5px solid #e2e8f0;
            color: #64748b;
            text-decoration: none;
            background: #fff;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .casting-filter-chip:hover { border-color: #6c3fc5; color: #6c3fc5; }
        .casting-filter-chip.active { background: #6c3fc5; color: #fff; border-color: #6c3fc5; }
        .casting-filter-count {
            background: rgba(255,255,255,.25);
            border-radius: 999px;
            padding: 1px 7px;
            font-size: 11px;
            font-weight: 700;
        }
        .casting-filter-chip:not(.active) .casting-filter-count {
            background: #f1f5f9;
            color: #64748b;
        }

        /* ── Grid ────────────────────────────────────── */
        .casting-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        /* ── Card ────────────────────────────────────── */
        .casting-card {
            background: #fff;
            border: 1.5px solid #e8edf3;
            border-radius: 16px;
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 0;
            transition: box-shadow .2s, transform .2s, border-color .2s;
        }
        .casting-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(108,63,197,.08);
            border-color: #c4b5fd;
        }
        .casting-card--applied {
            border-color: #bfdbfe;
            background: linear-gradient(135deg, #f0f7ff 0%, #fff 60%);
        }

        .casting-card-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .casting-tag {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            background: rgba(108,63,197,.08);
            color: #6c3fc5;
            letter-spacing: .02em;
        }
        .casting-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 999px;
        }
        .casting-badge--match { background: #f0fdf4; color: #16a34a; }
        .casting-badge--applied { background: #eff6ff; color: #2563eb; }

        .casting-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 16px;
            line-height: 1.4;
        }

        .casting-card-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }
        .casting-card-detail-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            color: #475569;
        }

        .casting-card-budget {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 11px 14px;
            margin-bottom: 16px;
        }
        .casting-card-budget-label { font-size: 12px; color: #94a3b8; font-weight: 500; }
        .casting-card-budget-amount { font-size: 17px; font-weight: 800; color: #15803d; }
        .casting-card-budget-amount small { font-size: 11px; font-weight: 400; color: #6b7280; }

        .casting-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
            margin-top: auto;
        }
        .casting-card-meta {
            font-size: 12px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .casting-card-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #6c3fc5;
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }
        .casting-card-btn:hover { background: #5b32a8; }

        /* ── Empty state ─────────────────────────────── */
        .casting-empty-state {
            text-align: center;
            padding: 80px 0;
            color: #94a3b8;
        }
        .casting-empty-icon { margin-bottom: 16px; }
        .casting-empty-state h3 { font-size: 18px; color: #64748b; margin: 0 0 8px; }
        .casting-empty-state p { font-size: 14px; margin: 0; }

        @media (max-width: 640px) {
            .casting-page-header { flex-direction: column; }
            .casting-grid { grid-template-columns: 1fr; }
        }
    </style>

    {{-- FLASH --}}
    @if(session('success'))
        <div id="flash-msg" style="position:fixed;top:20px;right:24px;z-index:9999;background:#22c55e;color:#fff;padding:14px 24px;border-radius:10px;font-size:14px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);display:flex;align-items:center;gap:10px;">
            <i data-lucide="check-circle" style="width:18px;height:18px;"></i> {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 4000);</script>
    @endif

    {{-- PAGE HEADER --}}
    <div class="casting-page-header">
        <div>
            <div class="casting-page-eyebrow">
                <i data-lucide="megaphone" style="width:16px;height:16px;color:#6c3fc5;"></i>
                <span>OPORTUNIDADES</span>
            </div>
            <h1 class="casting-page-title">Castings Activos</h1>
            <p class="casting-page-subtitle">Eventos publicados por clientes — postúlate y gana contratos</p>
        </div>
        <a href="{{ route('castings.my-applications') }}" class="casting-secondary-btn">
            <i data-lucide="file-text" style="width:15px;height:15px;"></i>
            Mis postulaciones
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="casting-filter-bar">
        <a href="{{ route('castings.index') }}" class="casting-filter-chip {{ $filterType === 'all' ? 'active' : '' }}">
            Todos <span class="casting-filter-count">{{ $events->count() }}</span>
        </a>
        @foreach($types as $type)
            <a href="{{ route('castings.index', ['type' => $type]) }}" class="casting-filter-chip {{ $filterType === $type ? 'active' : '' }}">
                {{ $type }}
            </a>
        @endforeach
    </div>

    {{-- EVENTS GRID --}}
    @if($events->isEmpty())
        <div class="casting-empty-state">
            <div class="casting-empty-icon"><i data-lucide="calendar-x" style="width:40px;height:40px;color:#94a3b8;"></i></div>
            <h3>Sin eventos disponibles</h3>
            <p>Aún no hay castings activos{{ $filterType !== 'all' ? " de tipo \"$filterType\"" : '' }}. ¡Vuelve pronto!</p>
        </div>
    @else
        <div class="casting-grid">
            @foreach($events as $event)
                <div class="casting-card {{ $event->already_applied ? 'casting-card--applied' : '' }}">

                    {{-- Card Top Bar --}}
                    <div class="casting-card-topbar">
                      <span class="casting-tag">{{ $event->genre->name ?? $event->tipo_musica }}</span>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($event->match_score > 0)
                                <span class="casting-badge casting-badge--match">
                                    <i data-lucide="zap" style="width:10px;height:10px;"></i> Compatible
                                </span>
                            @endif
                            @if($event->already_applied)
                                <span class="casting-badge casting-badge--applied">
                                    ✓ Postulado
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Title --}}
                    <h3 class="casting-card-title">{{ $event->titulo }}</h3>

                    {{-- Details --}}
                    <div class="casting-card-details">
                        <div class="casting-card-detail-row" style="font-weight: 600; color: #334155; margin-bottom: 4px;">
                            <i data-lucide="user" style="width:14px;height:14px;color:#6c3fc5;flex-shrink:0;"></i>
                            <span>{{ $event->nombre_cliente }}</span>
                        </div>
                        <div class="casting-card-detail-row">
                            <i data-lucide="map-pin" style="width:14px;height:14px;color:#6c3fc5;flex-shrink:0;"></i>
                            <span>{{ $event->ubicacion }}</span>
                        </div>
                        <div class="casting-card-detail-row">
                            <i data-lucide="calendar" style="width:14px;height:14px;color:#6c3fc5;flex-shrink:0;"></i>
                            <span>{{ $event->fecha }}</span>
                        </div>
                        <div class="casting-card-detail-row">
                            <i data-lucide="clock" style="width:14px;height:14px;color:#6c3fc5;flex-shrink:0;"></i>
                            <span>{{ $event->duracion }}</span>
                        </div>
                    </div>

                    {{-- Budget Highlight --}}
                    <div class="casting-card-budget">
                        <span class="casting-card-budget-label">Presupuesto del cliente</span>
                        <span class="casting-card-budget-amount">${{ number_format($event->presupuesto, 0) }} <small>MXN</small></span>
                    </div>

                    {{-- Footer --}}
                    <div class="casting-card-footer">
                        <span class="casting-card-meta">
                            <i data-lucide="users" style="width:13px;height:13px;"></i>
                            {{ $event->applications_count }} {{ $event->applications_count === 1 ? 'propuesta' : 'propuestas' }}
                            &nbsp;·&nbsp; {{ $event->created_at->diffForHumans() }}
                        </span>
                        <a href="{{ route('castings.show', $event->id) }}" class="casting-card-btn">
                            {{ $event->already_applied ? 'Ver mi propuesta' : 'Ver detalles' }}
                            <i data-lucide="arrow-right" style="width:14px;height:14px;"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
