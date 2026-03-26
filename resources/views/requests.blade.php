@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- HEADER --}}
    <div class="req-header">
        <div class="req-eyebrow">
            <i data-lucide="inbox" style="width:14px;height:14px;color:#6c3fc5;"></i>
            BANDEJA DE ENTRADA
        </div>
        <h1 class="req-title">Solicitudes de Contratación</h1>
        <p class="req-subtitle">Gestiona las propuestas que los clientes te han enviado.</p>
    </div>

    {{-- FILTERS (Aún no funcionales, pero visuales) --}}
    <div class="req-filters">
        <button class="req-filter-btn req-filter--active">
            Todas <span class="req-count">{{ $requests->count() }}</span>
        </button>
        <button class="req-filter-btn">
            Pendientes <span class="req-count req-count--yellow">{{ $requests->where('status', 'pending')->count() }}</span>
        </button>
        <button class="req-filter-btn">
            Confirmadas <span class="req-count req-count--green">{{ $requests->where('status', 'accepted')->count() }}</span>
        </button>
    </div>

    {{-- REQUESTS LIST --}}
    <div class="req-list">
        
        @if($requests->isEmpty())
            <div style="padding: 40px; text-align: center; color: #64748b;">
                <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <h3>Aún no tienes solicitudes</h3>
                <p>Las propuestas de contratación aparecerán aquí.</p>
            </div>
        @else
            @foreach($requests as $req)
                @php
                    // Preparamos los datos del cliente
                    $clientName = $req->client->nombre ? ($req->client->nombre . ' ' . $req->client->apellido) : 'Cliente Anónimo';
                    $initials = strtoupper(substr($req->client->nombre ?? 'C', 0, 1) . substr($req->client->apellido ?? 'A', 0, 1));
                    
                    // Colores de estado
                    $statusColor = match($req->status) {
                        'pending' => 'yellow',
                        'accepted' => 'green',
                        'rejected' => 'red',
                        default => 'gray'
                    };
                    
                    $statusText = match($req->status) {
                        'pending' => 'Pendiente',
                        'accepted' => 'Confirmada',
                        'rejected' => 'Rechazada',
                        default => ucfirst($req->status)
                    };
                    
                    $statusIcon = match($req->status) {
                        'pending' => 'hourglass',
                        'accepted' => 'check',
                        'rejected' => 'x',
                        default => 'help-circle'
                    };
                @endphp

                <div class="req-card {{ $req->status === 'accepted' ? 'req-card--accepted' : '' }}">
                    
                    {{-- 1. Client Info --}}
                    <div class="req-client">
                        <div class="req-avatar" style="background:#ede9fe; color:#6c3fc5;">
                            {{ $initials }}
                        </div>
                        <div>
                            <span class="req-client-name">{{ $clientName }}</span>
                            <span class="req-time">
                                <i data-lucide="clock" style="width:11px;height:11px;"></i>
                                {{ $req->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    {{-- 2. Event Info --}}
                    <div class="req-event">
                        <span class="req-event-type">
                            <i data-lucide="music" style="width:13px;height:13px;color:#6c3fc5;"></i>
                            Evento a la medida
                        </span>
                        <span class="req-event-desc">{{ \Illuminate\Support\Str::limit($req->description, 60) }}</span>
                        <span class="req-event-loc">
                            <i data-lucide="map-pin" style="width:12px;height:12px;"></i>
                            {{ $req->event_location }}
                        </span>
                    </div>

                    {{-- 3. Date --}}
                    <div class="req-date">
                        <span class="req-month">{{ strtoupper($req->event_date->translatedFormat('M')) }}</span>
                        <span class="req-day">{{ $req->event_date->format('d') }}</span>
                    </div>

                    {{-- 4. Budget --}}
                    <div class="req-budget">
                        <span class="req-budget-label">Presupuesto</span>
                        <span class="req-price">${{ number_format($req->budget, 0) }} <small>MXN</small></span>
                    </div>

                    {{-- 5. Actions --}}
                    <div class="req-actions">
                        <span class="req-badge req-badge--{{ $statusColor }}">
                            <i data-lucide="{{ $statusIcon }}" style="width:12px;height:12px;"></i>
                            {{ $statusText }}
                        </span>
                        <a href="{{ url('/requests/' . $req->id) }}" class="req-detail-btn">
                            Ver detalle <i data-lucide="arrow-right" style="width:13px;height:13px;"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        @endif

    </div>

    <style>
        /* ── Typography & Header ─────────────────── */
        .req-header { margin-bottom: 28px; }
        .req-eyebrow {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 8px;
        }
        .req-title { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 6px; letter-spacing: -.02em; }
        .req-subtitle { font-size: 14px; color: #64748b; margin: 0; }

        /* ── Filters ─────────────────────────────── */
        .req-filters {
            display: flex; gap: 10px; margin-bottom: 24px;
            overflow-x: auto; padding-bottom: 4px;
        }
        .req-filter-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 18px; border-radius: 10px; border: 1.5px solid transparent;
            background: #2f93f5; color: #fff; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .2s; white-space: nowrap;
        }
        .req-filter--active { background: #2563eb; }
        .req-count {
            display: flex; align-items: center; justify-content: center;
            min-width: 18px; height: 18px; padding: 0 4px; border-radius: 20px;
            background: rgba(255,255,255,.2); font-size: 10.5px; font-weight: 800;
        }
        .req-count--yellow { background: #fde047; color: #854d0e; }
        .req-count--green { background: #86efac; color: #166534; }

        /* ── List Layout ─────────────────────────── */
        .req-list { display: flex; flex-direction: column; gap: 14px; }

        /* ── Card ────────────────────────────────── */
        .req-card {
            display: grid; grid-template-columns: 200px 1fr 60px 140px 130px;
            align-items: center; gap: 20px; padding: 18px 22px;
            background: #fff; border: 1.5px solid #f1f5f9; border-radius: 16px;
            box-shadow: 0 2px 8px rgba(15,23,42,.02); transition: all .2s ease;
        }
        .req-card:hover { border-color: #e2e8f0; box-shadow: 0 8px 24px rgba(15,23,42,.04); transform: translateY(-1px); }
        .req-card--accepted { border-color: #bbf7d0; background: #f0fdf4; }

        /* ── Columns inside Card ─────────────────── */
        
        /* 1. Client */
        .req-client { display: flex; align-items: center; gap: 12px; }
        .req-avatar {
            width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 800; letter-spacing: .05em;
        }
        .req-client-name { display: block; font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .req-time { display: flex; align-items: center; gap: 4px; font-size: 11.5px; color: #94a3b8; font-weight: 500; }

        /* 2. Event */
        .req-event { display: flex; flex-direction: column; gap: 3px; min-width: 0; }
        .req-event-type { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; color: #6c3fc5; text-transform: uppercase; letter-spacing: .05em; }
        .req-event-desc { font-size: 13.5px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .req-event-loc { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #64748b; }

        /* 3. Date */
        .req-date { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0 10px; border-left: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; }
        .req-month { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .1em; margin-bottom: -2px; }
        .req-day { font-size: 24px; font-weight: 900; color: #0f172a; letter-spacing: -.03em; }

        /* 4. Budget */
        .req-budget { display: flex; flex-direction: column; align-items: flex-end; }
        .req-budget-label { font-size: 11px; color: #94a3b8; font-weight: 500; margin-bottom: 1px; }
        .req-price { font-size: 17px; font-weight: 800; color: #15803d; }
        .req-price small { font-size: 10px; font-weight: 600; color: #86efac; }

        /* 5. Actions */
        .req-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
        .req-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 6px; font-size: 10.5px; font-weight: 700;
        }
        .req-badge--yellow { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .req-badge--green { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .req-badge--red { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .req-badge--gray { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        
        .req-detail-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1.5px solid #e2e8f0;
            color: #475569; font-size: 11.5px; font-weight: 600; text-decoration: none;
            transition: all .2s;
        }
        .req-detail-btn:hover { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }

        /* ── Responsive ──────────────────────────── */
        @media (max-width: 1024px) {
            .req-card { grid-template-columns: 180px 1fr 120px; grid-template-areas: "client event actions" "date budget actions"; gap: 10px 15px; }
            .req-client { grid-area: client; }
            .req-event { grid-area: event; }
            .req-actions { grid-area: actions; justify-content: center; }
            .req-date { grid-area: date; border: none; align-items: flex-start; padding: 0; flex-direction: row; gap: 6px; }
            .req-month { margin-bottom: 0; }
            .req-budget { grid-area: budget; align-items: flex-start; }
        }
        
        @media (max-width: 640px) {
            .req-card { display: flex; flex-direction: column; align-items: flex-start; gap: 14px; }
            .req-date, .req-budget, .req-actions { align-items: flex-start; width: 100%; }
            .req-actions { flex-direction: row; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 14px; }
            .req-event-desc { white-space: normal; }
        }
    </style>

@endsection