@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- CABECERA --}}
    <div class="requests-page-header">
        <div>
            <div class="requests-eyebrow">
                <i data-lucide="inbox" style="width:14px;height:14px;color:#6c3fc5;"></i>
                BANDEJA DE ENTRADA
            </div>
            <h1 class="requests-page-title">Solicitudes de Contratación</h1>
            <p class="requests-page-subtitle">Gestiona las propuestas que los clientes te han enviado.</p>
        </div>
        
        <div class="requests-header-badge">
            <i data-lucide="music" style="width:16px;height:16px;"></i>
            Eventos locales
        </div>
    </div>

    {{-- TABS + CONTADORES --}}
    <div style="margin-bottom: 24px;">
        <div class="filter-tabs">
            <a href="{{ route('requests.index') }}" class="filter-tab {{ is_null($status) ? 'active' : '' }}">
                <i data-lucide="layout-grid" style="width:15px;height:15px;"></i>
                Todas <span class="tab-counter">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('requests.index', 'pending') }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
                <i data-lucide="clock" style="width:15px;height:15px;"></i>
                Pendientes <span class="tab-counter pending">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('requests.index', 'accepted') }}" class="filter-tab {{ $status === 'accepted' ? 'active' : '' }}">
                <i data-lucide="check-circle-2" style="width:15px;height:15px;"></i>
                Confirmadas <span class="tab-counter resolved">{{ $counts['accepted'] }}</span>
            </a>
        </div>
    </div>

    @forelse($requests as $req)
        @if($loop->first)
            {{-- TABLA --}}
            <div class="dashboard-box requests-box">
                <div class="table-responsive">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th style="min-width:200px;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <i data-lucide="user" style="width:14px;height:14px;color:#94a3b8;"></i> Cliente
                                    </div>
                                </th>
                                <th style="min-width:250px;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <i data-lucide="map-pin" style="width:14px;height:14px;color:#94a3b8;"></i> Detalles del Evento
                                    </div>
                                </th>
                                <th style="min-width:140px;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <i data-lucide="calendar" style="width:14px;height:14px;color:#94a3b8;"></i> Fecha
                                    </div>
                                </th>
                                <th style="min-width:140px;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <i data-lucide="wallet" style="width:14px;height:14px;color:#94a3b8;"></i> Presupuesto
                                    </div>
                                </th>
                                <th style="min-width:130px;">Estado</th>
                                <th style="min-width:140px; text-align:right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
        @endif

                            <tr class="request-row">
                               {{-- CLIENTE --}}
                                <td>
                                    <div class="user-cell">
                                        @php 
                                            $initials = strtoupper(substr($req->client->nombre ?? 'C', 0, 1) . substr($req->client->apellido ?? 'A', 0, 1)); 
                                            $clientName = $req->client->nombre ? ($req->client->nombre . ' ' . $req->client->apellido) : 'Cliente Anónimo';
                                            
                                            // Definimos qué foto usar
                                            $imagenFinal = null;
                                            if (!empty($req->client->fotoPerfil)) {
                                                $cleanPath = ltrim($req->client->fotoPerfil, '/');
                                                $imagenFinal = url('/file/' . $cleanPath);
                                            } elseif (!empty($req->client->google_picture)) {
                                                $imagenFinal = $req->client->google_picture;
                                            }
                                        @endphp
                                        
                                        {{-- Mostrar la foto o las iniciales --}}
                                        <div class="avatar-circle client-avatar">
                                            @if($imagenFinal)
                                                <img src="{{ $imagenFinal }}" alt="{{ $clientName }}">
                                            @else
                                                {{ $initials }}
                                            @endif
                                        </div>

                                        <div class="client-info">
                                            <strong class="client-name">{{ $clientName }}</strong>
                                            <span class="client-time"><i data-lucide="clock-4" style="width:12px;height:12px;"></i>{{ $req->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- EVENTO (truncado con expandir) --}}
                                <td>
                                    @php $fullReason = $req->description; $short = Str::limit($fullReason, 100); @endphp
                                    <p class="reason-short" id="reason-short-{{ $req->id }}">{{ $short }}</p>
                                    @if(strlen($fullReason) > 100)
                                        <p class="reason-full" id="reason-full-{{ $req->id }}" style="display:none;">{{ $fullReason }}</p>
                                        <button class="expand-btn" onclick="toggleReason({{ $req->id }})">
                                            <i data-lucide="chevron-down" id="expand-icon-{{ $req->id }}"></i>
                                            <span id="expand-label-{{ $req->id }}">Leer más</span>
                                        </button>
                                    @endif
                                    <div class="event-location-text">
                                        <i data-lucide="map-pin"></i> {{ $req->event_location }}
                                    </div>
                                </td>

                                {{-- FECHA --}}
                                <td>
                                    <span class="event-date-primary">{{ $req->event_date->format('d/m/Y') }}</span>
                                    <span class="event-date-secondary">
                                        <i data-lucide="calendar-days" style="width:11px;height:11px;margin-right:3px;"></i>
                                        {{ strtoupper($req->event_date->translatedFormat('M')) }} {{ $req->event_date->format('d') }}
                                    </span>
                                </td>

                                {{-- PRESUPUESTO --}}
                                <td>
                                    <span class="budget-primary">${{ number_format($req->budget, 0) }} <small class="budget-secondary">MXN</small></span>
                                </td>

                                {{-- ESTADO --}}
                                <td>
                                    @php
                                        $statusConfig = [
                                            'pending'  => ['class' => 'warning',  'icon' => 'clock',          'label' => 'Pendiente'],
                                            'accepted' => ['class' => 'success',  'icon' => 'check-circle-2', 'label' => 'Confirmada'],
                                            'rejected' => ['class' => 'danger',   'icon' => 'x-circle',       'label' => 'Rechazada'],
                                            'completed' => ['class' => 'resolved', 'icon' => 'star',          'label' => 'Finalizada'],
                                            'counter_offer' => ['class' => 'reviewed', 'icon' => 'refresh-cw', 'label' => 'Contraoferta'],
                                        ];
                                        $cfg = $statusConfig[$req->status] ?? ['class' => 'secondary', 'icon' => 'minus', 'label' => ucfirst($req->status)];
                                    @endphp
                                    <span class="report-status-pill {{ $cfg['class'] }}">
                                        <i data-lucide="{{ $cfg['icon'] }}"></i>
                                        {{ $cfg['label'] }}
                                    </span>
                                </td>

                                {{-- ACCIONES --}}
                                <td>
                                    <div class="actions-col">
                                        <a href="{{ url('/requests/' . $req->id) }}" class="action-link">
                                            <button class="rpt-btn reviewed-btn" title="Ver Detalles">
                                                <i data-lucide="eye"></i> <span>Ver detalle</span>
                                            </button>
                                        </a>
                                    </div>
                                </td>
                            </tr>

        @if($loop->last)
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    @empty
        {{-- ESTADO VACÍO IDÉNTICO A PROMOCIONES --}}
        <div class="rq-empty-container">
            <div class="rq-empty-card">
                <div class="rq-empty-icon-wrap">
                    <i data-lucide="inbox" class="rq-empty-icon"></i>
                </div>
                
                <h1 class="rq-empty-title">
                    @if($status === 'pending') No hay pendientes
                    @elseif($status === 'accepted') Sin confirmar aún
                    @else Sin solicitudes
                    @endif
                </h1>
                <p class="rq-empty-subtitle">
                    Las nuevas propuestas de contratación de clientes aparecerán aquí. Un perfil 100% completo, con fotos y biografía tiene 4 veces más posibilidades de recibir propuestas.
                </p>

                <a href="{{ route('profile') }}" class="rq-preview-btn">
                    Mejorar mi perfil
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>
        </div>
    @endforelse

<style>
/* ── ESTILOS GENERALES ─────────────────────────────────────────────────── */
.requests-page-header {
    display: flex; justify-content: space-between; align-items: center;
    gap: 20px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e2e8f0;
}
.requests-eyebrow {
    display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 800;
    letter-spacing: .1em; color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
}
.requests-page-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
.requests-page-subtitle { font-size: 14.5px; color: #64748b; margin: 0; }

.requests-header-badge {
    display:inline-flex; align-items:center; gap:8px; padding:10px 16px; 
    background: #0f172a; border:none; border-radius:12px; 
    font-size:14px; font-weight:600; color:#fff; height:fit-content;
    box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.15);
}
.requests-header-badge i { color: #fff; }

@media (max-width: 640px) {
    .requests-page-header { flex-direction: column; align-items: flex-start; }
    .requests-header-badge { width: 100%; justify-content: center; }
}

/* ── TABS ─────────────────────────────────────────────────── */
.filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
.filter-tab {
    text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px; border-radius: 12px; font-size: 14px; font-weight: 600;
    color: #475569; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); border: 1.5px solid transparent; background: transparent;
}
.filter-tab:hover { background: #f8fafc; color: #0f172a; border-color: #e2e8f0; }
.filter-tab.active {
    background: #fff; color: #0f172a;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-color: #cbd5e1;
}
.tab-counter {
    background: #e2e8f0; color: #475569;
    padding: 2px 8px; border-radius: 999px; font-size: 11.5px; font-weight: 800;
}
.tab-counter.pending  { background: #fef08a; color: #854d0e; }
.tab-counter.resolved { background: #bbf7d0; color: #166534; }

/* ── TABLA ────────────────────────────────────────────────── */
.requests-box { overflow: hidden; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); background: #fff; }
.requests-table { table-layout: auto; width: 100%; border-collapse: collapse; text-align: left; }
.requests-table thead th {
    font-size: 13px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.05em; color: #64748b; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.request-row { transition: all 0.2s ease; border-bottom: 1px solid #f1f5f9; }
.request-row:hover { background: #f8fafc; }
.request-row td { vertical-align: top; padding: 16px 24px; font-size: 14.5px; color: #334155;}
.request-row:last-child { border-bottom: none; }

/* Avatares e Info de Cliente */
.user-cell { display: flex; align-items: center; gap: 14px; }
.avatar-circle {
    width: 44px; height: 44px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;
    overflow: hidden; padding: 0; font-size: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
.client-avatar  { background: linear-gradient(135deg,#818cf8,#6366f1); color: #fff; }
.client-info { display: flex; flex-direction: column; gap: 3px; }
.client-name { font-size: 14.5px; font-weight: 700; color: #0f172a; }
.client-time { font-size: 12.5px; color: #64748b; display: flex; align-items: center; gap: 4px; }

/* Motivo y Evento */
.reason-short, .reason-full {
    font-size: 14.5px; color: #334155; line-height: 1.5;
    margin: 0 0 8px; white-space: pre-wrap; word-break: break-word;
}
.expand-btn {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 700; color: #6366f1;
    background: none; border: none; padding: 0; cursor: pointer;
    transition: color 0.2s; margin-bottom: 8px;
}
.expand-btn i { width: 14px; height: 14px; }
.expand-btn:hover { color: #4f46e5; }

.event-location-text {
    font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px; font-weight: 500;
}
.event-location-text i { width: 14px; height: 14px; }

/* Fecha y Presupuesto */
.event-date-primary { font-size: 14.5px; font-weight: 700; color: #0f172a; display: block; margin-bottom: 2px; }
.event-date-secondary { font-size: 13px; color: #64748b; display: flex; align-items: center; }

.budget-primary { font-size: 15.5px; font-weight: 800; color: #16a34a; }
.budget-secondary { color: #4ade80; font-weight: 700; font-size: 12px; margin-left: 2px; }

/* Status pill */
.report-status-pill {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; letter-spacing: 0.3px;
}
.report-status-pill i { width: 13px; height: 13px; }
.report-status-pill.warning  { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
.report-status-pill.danger   { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
.report-status-pill.success  { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
.report-status-pill.secondary { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.report-status-pill.reviewed { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
.report-status-pill.resolved { background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; }

/* Botones de acción */
.actions-col { display: flex; align-items: flex-end; justify-content: flex-end; }
.action-link { text-decoration: none; width: 100%; display: block; }
.rpt-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px 14px; border-radius: 10px; font-size: 13.5px; font-weight: 600;
    cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%; border: 1.5px solid transparent; background: #fff;
}
.rpt-btn i { width: 15px; height: 15px; }
.reviewed-btn { color: #6366f1; border-color: #c7d2fe; box-shadow: 0 2px 4px rgba(99, 102, 241, 0.05); }
.reviewed-btn:hover { background: #6366f1; color: #fff; border-color: #6366f1; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2); }

/* ESTADO VACÍO (Estilo Promociones) */
.rq-empty-container { display: flex; align-items: center; justify-content: center; min-height: 50vh; padding: 20px; }
.rq-empty-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 24px; max-width: 580px; width: 100%; padding: 48px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.03); position: relative; overflow: hidden; }
.rq-empty-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #6c3fc5, #3b82f6, #06b6d4); }
.rq-empty-icon-wrap { width: 84px; height: 84px; background: linear-gradient(135deg, rgba(108,63,197,0.1), rgba(59,130,246,0.1)); border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; box-shadow: inset 0 0 0 1px rgba(108,63,197,0.15); }
.rq-empty-icon { width: 40px; height: 40px; color: #6c3fc5; }
.rq-empty-title { font-size: 26px; font-weight: 900; color: #0f172a; margin: 0 0 12px; line-height: 1.2; letter-spacing: -0.5px; }
.rq-empty-subtitle { font-size: 15px; color: #64748b; margin: 0 0 36px; line-height: 1.5; }
.rq-preview-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #0f172a; color: #fff; padding: 16px; border-radius: 14px; font-size: 15px; font-weight: 700; text-decoration: none; transition: all 0.2s; box-shadow: 0 10px 20px rgba(15,23,42,0.15); }
.rq-preview-btn:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 12px 24px rgba(15,23,42,0.2); }
.rq-preview-btn i { width: 16px; height: 16px; }

</style>

<script>
function toggleReason(id) {
    const short  = document.getElementById('reason-short-' + id);
    const full   = document.getElementById('reason-full-'  + id);
    const icon   = document.getElementById('expand-icon-'  + id);
    const label  = document.getElementById('expand-label-' + id);
    if(!short || !full) return;
    const isOpen = full.style.display !== 'none';

    short.style.display = isOpen ? 'block' : 'none';
    full.style.display  = isOpen ? 'none'  : 'block';
    icon.setAttribute('data-lucide', isOpen ? 'chevron-down' : 'chevron-up');
    label.textContent = isOpen ? 'Leer más' : 'Ocultar';
    lucide.createIcons();
}
</script>

@endsection