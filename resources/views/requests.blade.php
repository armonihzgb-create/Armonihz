@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- CABECERA (ESTILO PROMOCIONES) --}}
    <div class="promo-page-header">
        <div>
            <div class="promo-eyebrow">
                <i data-lucide="inbox" style="width:14px;height:14px;color:#6c3fc5;"></i>
                BANDEJA DE ENTRADA
            </div>
            <h1 class="promo-page-title">Solicitudes de Contratación</h1>
            <p class="promo-page-subtitle">Gestiona las propuestas que los clientes te han enviado.</p>
        </div>
        
        <div style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;font-size:13px;color:#64748b;height:fit-content;">
            <i data-lucide="music" style="width:14px;height:14px;color:#6366f1;"></i>
            Eventos a la medida
        </div>
    </div>

    {{-- TABS + CONTADORES --}}
    <div style="margin-bottom: 24px;">
        <div class="filter-tabs">
            <a href="{{ route('requests.index') }}" class="filter-tab {{ is_null($status) ? 'active' : '' }}">
                <i data-lucide="layout-grid" style="width:14px;height:14px;"></i>
                Todas <span class="tab-counter">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('requests.index', 'pending') }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
                <i data-lucide="alert-circle" style="width:14px;height:14px;"></i>
                Pendientes <span class="tab-counter pending">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('requests.index', 'accepted') }}" class="filter-tab {{ $status === 'accepted' ? 'active' : '' }}">
                <i data-lucide="shield-check" style="width:14px;height:14px;"></i>
                Confirmadas <span class="tab-counter resolved">{{ $counts['accepted'] }}</span>
            </a>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="dashboard-box" style="overflow: hidden;">
        <div class="table-responsive">
            <table class="admin-table reports-table">
                <thead>
                    <tr>
                        <th style="min-width:150px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i data-lucide="user" style="width:13px;height:13px;color:#94a3b8;"></i> Cliente
                            </div>
                        </th>
                        <th style="min-width:200px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i data-lucide="map-pin" style="width:13px;height:13px;color:#94a3b8;"></i> Evento / Detalles
                            </div>
                        </th>
                        <th style="min-width:110px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i data-lucide="calendar" style="width:13px;height:13px;color:#94a3b8;"></i> Fecha
                            </div>
                        </th>
                        <th style="min-width:110px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i data-lucide="dollar-sign" style="width:13px;height:13px;color:#94a3b8;"></i> Presupuesto
                            </div>
                        </th>
                        <th style="min-width:110px;">Estado</th>
                        <th style="min-width:130px; text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr class="report-row">
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
                                    <div class="avatar-circle client-avatar" style="overflow: hidden; padding: 0;">
                                        @if($imagenFinal)
                                            <img src="{{ $imagenFinal }}" alt="{{ $clientName }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>

                                    <div>
                                        <strong style="font-size:13px;">{{ $clientName }}</strong>
                                        <span class="sub-text" style="display:block;">{{ $req->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- EVENTO (truncado con expandir) --}}
                            <td>
                                <div style="margin-bottom: 4px; display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; color: #6c3fc5; text-transform: uppercase;">
                                    <i data-lucide="music" style="width:11px;height:11px;"></i> Evento local
                                </div>
                                @php $fullReason = $req->description; $short = Str::limit($fullReason, 80); @endphp
                                <p class="reason-short" id="reason-short-{{ $req->id }}">{{ $short }}</p>
                                @if(strlen($fullReason) > 80)
                                    <p class="reason-full" id="reason-full-{{ $req->id }}" style="display:none;">{{ $fullReason }}</p>
                                    <button class="expand-btn" onclick="toggleReason({{ $req->id }})">
                                        <i data-lucide="chevron-down" style="width:12px;height:12px;" id="expand-icon-{{ $req->id }}"></i>
                                        <span id="expand-label-{{ $req->id }}">Ver más</span>
                                    </button>
                                @endif
                                <div style="margin-top: 6px; font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 4px;">
                                    <i data-lucide="map-pin" style="width:12px;height:12px;"></i> {{ $req->event_location }}
                                </div>
                            </td>

                            {{-- FECHA --}}
                            <td>
                                <span style="font-size:13px;font-weight:600;color:#334155;display:block;">{{ $req->event_date->format('d/m/Y') }}</span>
                                <span class="sub-text">{{ strtoupper($req->event_date->translatedFormat('M')) }} {{ $req->event_date->format('d') }}</span>
                            </td>

                            {{-- PRESUPUESTO --}}
                            <td>
                                <span style="font-size:14px;font-weight:800;color:#15803d;display:block;">${{ number_format($req->budget, 0) }}</span>
                                <span class="sub-text" style="color: #86efac; font-weight: 600;">MXN</span>
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
                                    <i data-lucide="{{ $cfg['icon'] }}" style="width:11px;height:11px;"></i>
                                    {{ $cfg['label'] }}
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="actions-col">
                                    <a href="{{ url('/requests/' . $req->id) }}" style="text-decoration: none; display: inline-flex; width: 100%;">
                                        <button class="rpt-btn reviewed-btn" title="Ver Detalles">
                                            <i data-lucide="eye" style="width:13px;height:13px;"></i> Ver detalle
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:64px 24px;color:#94a3b8;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:14px;">
                                    <div style="width:64px;height:64px;background:#f8fafc;border-radius:16px;display:flex;align-items:center;justify-content:center;border:1px solid #e2e8f0;">
                                        <i data-lucide="inbox" style="width:28px;height:28px;opacity:0.3;"></i>
                                    </div>
                                    <p style="margin:0;font-size:14px;font-weight:600;color:#475569;">
                                        @if($status === 'pending') No tienes solicitudes pendientes.
                                        @elseif($status === 'accepted') No tienes solicitudes confirmadas.
                                        @else Aún no tienes solicitudes
                                        @endif
                                    </p>
                                    <p style="margin:0;font-size:12px;color:#94a3b8;">Las propuestas de contratación aparecerán aquí.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </div>

<style>
/* ── ESTILOS DE LA VISTA GENERAL (CABECERA) ─────────────────────────────────────────────────── */
.promo-page-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    gap: 20px; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9;
}
.promo-eyebrow {
    display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800;
    letter-spacing: .08em; color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
}
.promo-page-title { font-size: 26px; font-weight: 900; color: #0f172a; margin: 0 0 6px; letter-spacing: -0.5px; }
.promo-page-subtitle { font-size: 15px; color: #64748b; margin: 0; }

@media (max-width: 640px) {
    .promo-page-header { flex-direction: column; }
}

/* ── TABS ─────────────────────────────────────────────────── */
.filter-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
.filter-tab {
    text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 15px; border-radius: 10px; font-size: 13px; font-weight: 600;
    color: #64748b; transition: all 0.2s; border: 1px solid transparent; background: transparent;
}
.filter-tab:hover { background: #f8fafc; color: #1e293b; }
.filter-tab.active {
    background: #fff; color: #1e293b;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07); border-color: #e2e8f0;
}
.tab-counter {
    background: #f1f5f9; color: #64748b;
    padding: 2px 7px; border-radius: 999px; font-size: 11px; font-weight: 700;
}
.tab-counter.pending  { background: #fef9c3; color: #b45309; }
.tab-counter.reviewed { background: #dbeafe; color: #1d4ed8; }
.tab-counter.resolved { background: #dcfce7; color: #15803d; }

/* ── TABLA ────────────────────────────────────────────────── */
.reports-table { table-layout: auto; width: 100%; border-collapse: collapse; }
.reports-table thead th {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.05em; color: #94a3b8; text-align: left;
    padding: 14px 16px; border-bottom: 2px solid #f1f5f9;
}
.report-row { transition: background 0.15s; border-bottom: 1px solid #f1f5f9; }
.report-row:hover { background: #f8fafc; }
.report-row td { vertical-align: top; padding: 16px; }
.report-row:last-child { border-bottom: none; }

/* Avatares */
.user-cell { display: flex; align-items: center; gap: 12px; }
.avatar-circle {
    width: 40px; height: 40px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;
}
.client-avatar  { background: linear-gradient(135deg,#818cf8,#6366f1); color: #fff; font-size: 13px; }
.sub-text { font-size: 12px; color: #94a3b8; }

/* Motivo con expandir */
.reason-short, .reason-full {
    font-size: 13px; color: #334155; line-height: 1.55;
    margin: 0 0 4px; white-space: pre-wrap; word-break: break-word;
}
.expand-btn {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; color: #6366f1;
    background: none; border: none; padding: 0; cursor: pointer;
    transition: opacity 0.15s;
}
.expand-btn:hover { opacity: 0.75; }

/* Status pill */
.report-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700;
}
.report-status-pill.warning  { background: #fef9c3; color: #b45309; }
.report-status-pill.danger   { background: #fee2e2; color: #991b1b; }
.report-status-pill.success  { background: #dcfce7; color: #15803d; }
.report-status-pill.secondary { background: #f1f5f9; color: #64748b; }

/* Botones de acción */
.actions-col {
    display: flex; flex-direction: column; align-items: flex-end; gap: 6px;
}
.rpt-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
    cursor: pointer; border: 1.5px solid transparent; transition: all 0.15s;
    white-space: nowrap; width: 100%;  justify-content: center;
}
.reviewed-btn { background: #f8fafc; color: #334155; border-color: #e2e8f0; }
.reviewed-btn:hover { background: #f1f5f9; }

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
    label.textContent = isOpen ? 'Ver más' : 'Ver menos';
    lucide.createIcons();
}
</script>

@endsection