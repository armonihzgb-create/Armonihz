@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- PAGE HEADER --}}
    <div class="ma-header">
        <div>
            <div class="ma-eyebrow">
                <i data-lucide="file-text" style="width:14px;height:14px;color:#6c3fc5;"></i>
                MIS POSTULACIONES
            </div>
            <h1 class="ma-title">Historial de Postulaciones</h1>
            <p class="ma-subtitle">Todos los castings a los que te has postulado</p>
        </div>
        <a href="{{ route('castings.index') }}" class="ma-back-btn">
            <i data-lucide="megaphone" style="width:15px;height:15px;"></i>
            Ver castings disponibles
        </a>
    </div>

    @if($applications->isEmpty())
        <div class="ma-empty">
            <div class="ma-empty-icon"><i data-lucide="inbox" style="width:40px;height:40px;color:#cbd5e1;"></i></div>
            <h3>Sin postulaciones todavía</h3>
            <p>Aún no te has postulado a ningún casting. ¡Encuentra una oportunidad!</p>
            <a href="{{ route('castings.index') }}" class="ma-explore-btn">
                <i data-lucide="megaphone" style="width:15px;height:15px;"></i> Explorar castings
            </a>
        </div>
    @else
        @php
            $pending  = $applications->where('status', 'pending')->count();
            $accepted = $applications->where('status', 'accepted')->count();
            $rejected = $applications->where('status', 'rejected')->count();
            $total    = $applications->count();
        @endphp

        {{-- Stats Row --}}
        <div class="ma-stats">
            <div class="ma-stat-card">
                <div class="ma-stat-number">{{ $total }}</div>
                <div class="ma-stat-label">Total enviadas</div>
            </div>
            <div class="ma-stat-card ma-stat-yellow">
                <div class="ma-stat-number" style="color:#ca8a04;">{{ $pending }}</div>
                <div class="ma-stat-label">⏳ En revisión</div>
            </div>
            <div class="ma-stat-card ma-stat-green">
                <div class="ma-stat-number" style="color:#16a34a;">{{ $accepted }}</div>
                <div class="ma-stat-label">✓ Aceptadas</div>
            </div>
            <div class="ma-stat-card ma-stat-red">
                <div class="ma-stat-number" style="color:#dc2626;">{{ $rejected }}</div>
                <div class="ma-stat-label">✗ No seleccionado</div>
            </div>
        </div>

        {{-- Section title --}}
        <div class="ma-section-header">
            <h2 class="ma-section-title">Todas las postulaciones</h2>
            <span class="ma-section-count">{{ $total }} {{ $total === 1 ? 'resultado' : 'resultados' }}</span>
        </div>

        {{-- List --}}
        <div class="ma-list">
            @foreach($applications as $app)
                @php
                    $isAccepted  = $app->status === 'accepted';
                    $isCancelled = $app->status === 'cancelled';
                    $isRejected  = $app->status === 'rejected';
                    $statusLabel = $isAccepted ? '✓ Aceptado' : ($isCancelled ? '✗ Contratación cancelada' : ($isRejected ? '✗ No seleccionado' : '⏳ En revisión'));
                    $statusClass = $isAccepted ? 'ma-status--green' : ($isCancelled ? 'ma-status--grey' : ($isRejected ? 'ma-status--red' : 'ma-status--yellow'));
                    $cardClass   = $isAccepted ? 'ma-card--accepted' : ($isCancelled ? 'ma-card--cancelled' : '');
                @endphp
                <div class="ma-card {{ $cardClass }}">

                    {{-- Left: icon --}}
                    <div class="ma-card-icon {{ $isAccepted ? 'ma-icon--green' : ($isCancelled ? 'ma-icon--grey' : ($isRejected ? 'ma-icon--red' : 'ma-icon--yellow')) }}">
                        {{ $isAccepted ? '🎉' : ($isCancelled ? '🚫' : ($isRejected ? '😔' : '⏳')) }}
                    </div>

                    {{-- Center: event info --}}
                    <div class="ma-card-body">
                        <a href="{{ route('castings.show', $app->client_event_id) }}" class="ma-card-title">
                            {{ $app->event->titulo ?? 'Evento eliminado' }}
                        </a>
                        <div class="ma-card-meta-row">
                            @if($app->event)
                                <span class="ma-meta-chip">
                                    <i data-lucide="music" style="width:12px;height:12px;"></i>
                                    {{ $app->event->tipo_musica }}
                                </span>
                                <span class="ma-meta-chip">
                                    <i data-lucide="map-pin" style="width:12px;height:12px;"></i>
                                    {{ $app->event->ubicacion }}
                                </span>
                                <span class="ma-meta-chip">
                                    <i data-lucide="calendar" style="width:12px;height:12px;"></i>
                                    {{ $app->event->fecha }}
                                </span>
                            @endif
                            <span class="ma-meta-chip ma-meta-time">
                                <i data-lucide="clock" style="width:12px;height:12px;"></i>
                                Enviada {{ $app->created_at->diffForHumans() }}
                            </span>
                        </div>
                        {{-- Truncated message preview --}}
                        <p class="ma-card-message">{{ Str::limit($app->message, 100) }}</p>
                    </div>

                    {{-- Right: price + status --}}
                    <div class="ma-card-right">
                        <div class="ma-card-price">${{ number_format($app->proposed_price, 0) }}<small> MXN</small></div>
                        <span class="ma-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        <a href="{{ route('castings.show', $app->client_event_id) }}" class="ma-view-link">
                            Ver detalles <i data-lucide="arrow-right" style="width:12px;height:12px;"></i>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    <style>
        /* ── Page Header ──────────────────────────── */
        .ma-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 28px; padding-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
        }
        .ma-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .ma-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .ma-subtitle { font-size: 14px; color: #64748b; margin: 0; }
        .ma-back-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px; border-radius: 8px; background: #f1f5f9;
            color: #475569; font-size: 13px; font-weight: 600; text-decoration: none;
            border: 1.5px solid #e2e8f0; transition: all .2s; white-space: nowrap;
        }
        .ma-back-btn:hover { background: #e2e8f0; }

        /* ── Stats ──────────────────────────────────── */
        .ma-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
            margin-bottom: 28px;
        }
        .ma-stat-card {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 14px; padding: 18px 20px;
        }
        .ma-stat-yellow { background: #fefce8; border-color: #fef08a; }
        .ma-stat-green  { background: #f0fdf4; border-color: #bbf7d0; }
        .ma-stat-red    { background: #fef2f2; border-color: #fecaca; }
        .ma-stat-number { font-size: 30px; font-weight: 900; color: #0f172a; line-height: 1; margin-bottom: 4px; }
        .ma-stat-label  { font-size: 12px; font-weight: 500; color: #6b7280; }

        /* ── Section Header ──────────────────────────── */
        .ma-section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 14px;
        }
        .ma-section-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
        .ma-section-count { font-size: 13px; color: #94a3b8; font-weight: 500; }

        /* ── Application Card ────────────────────────── */
        .ma-list { display: flex; flex-direction: column; gap: 12px; }
        .ma-card {
            display: grid;
            grid-template-columns: 54px 1fr auto;
            gap: 18px;
            align-items: center;
            background: #fff;
            border: 1.5px solid #e8edf3;
            border-radius: 14px;
            padding: 20px 24px;
            transition: box-shadow .2s, border-color .2s;
        }
        .ma-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.05); border-color: #d1d5db; }
        .ma-card--accepted { border-color: #bbf7d0; background: linear-gradient(140deg, #f0fdf4, #fff); }
        .ma-card--cancelled { border-color: #e2e8f0; background: #f8fafc; opacity: 0.85; }

        .ma-card-icon {
            width: 54px; height: 54px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .ma-icon--yellow { background: #fefce8; border: 1.5px solid #fef08a; }
        .ma-icon--green  { background: #f0fdf4; border: 1.5px solid #bbf7d0; }
        .ma-icon--red    { background: #fef2f2; border: 1.5px solid #fecaca; }
        .ma-icon--grey   { background: #f1f5f9; border: 1.5px solid #e2e8f0; }

        .ma-card-body { min-width: 0; }
        .ma-card-title {
            font-size: 15px; font-weight: 700; color: #0f172a;
            text-decoration: none; display: block; margin-bottom: 6px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .ma-card-title:hover { color: #6c3fc5; }
        .ma-card-meta-row {
            display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px;
        }
        .ma-meta-chip {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11.5px; color: #64748b; background: #f8fafc;
            border: 1px solid #f1f5f9; border-radius: 6px; padding: 3px 8px;
        }
        .ma-meta-time { color: #94a3b8; }
        .ma-card-message {
            font-size: 13px; color: #64748b; margin: 0; line-height: 1.5;
            font-style: italic;
        }

        .ma-card-right {
            display: flex; flex-direction: column; align-items: flex-end;
            gap: 8px; flex-shrink: 0;
        }
        .ma-card-price {
            font-size: 19px; font-weight: 800; color: #0f172a; white-space: nowrap;
        }
        .ma-card-price small { font-size: 11px; font-weight: 400; color: #94a3b8; }
        .ma-status-badge {
            font-size: 11px; font-weight: 600; padding: 4px 12px;
            border-radius: 999px; white-space: nowrap;
        }
        .ma-status--yellow { background: #fefce8; color: #ca8a04; border: 1px solid #fef08a; }
        .ma-status--green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .ma-status--red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .ma-status--grey   { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
        .ma-view-link {
            font-size: 12px; font-weight: 600; color: #6c3fc5; text-decoration: none;
            display: flex; align-items: center; gap: 3px;
        }
        .ma-view-link:hover { text-decoration: underline; }

        /* ── Empty ───────────────────────────────────── */
        .ma-empty {
            text-align: center; padding: 80px 0; color: #94a3b8;
        }
        .ma-empty-icon { margin-bottom: 16px; }
        .ma-empty h3 { font-size: 18px; color: #64748b; margin: 0 0 8px; }
        .ma-empty p  { font-size: 14px; margin: 0 0 20px; }
        .ma-explore-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 11px 24px; background: #6c3fc5; color: #fff;
            border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none;
        }

        @media (max-width: 768px) {
            .ma-header { flex-direction: column; }
            .ma-stats { grid-template-columns: repeat(2,1fr); }
            .ma-card { grid-template-columns: 44px 1fr; }
            .ma-card-right { grid-column: span 2; flex-direction: row; justify-content: space-between; }
        }
        @media (max-width: 480px) {
            .ma-stats { grid-template-columns: 1fr 1fr; }
            .ma-card { grid-template-columns: 1fr; }
        }
    </style>

@endsection
