@extends('layouts.admin')

@section('admin-content')

    {{-- CABECERA --}}
    <div class="page-header">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
            <div>
                <h2 style="display: flex; align-items: center; gap: 10px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:linear-gradient(135deg,#f43e5c,#ff8c5a);border-radius:10px;">
                        <i data-lucide="flag" style="width:18px;height:18px;color:#fff;"></i>
                    </span>
                    Gestión de Reportes
                </h2>
                <p class="dashboard-subtitle">Revisa y resuelve los reportes enviados por los clientes desde la App Móvil.</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;font-size:13px;color:#64748b;">
                <i data-lucide="smartphone" style="width:14px;height:14px;color:#6366f1;"></i>
                Reportes enviados desde la App
            </div>
        </div>
    </div>

    {{-- TABS + CONTADORES --}}
    <div style="margin-bottom: 24px;">
        <div class="filter-tabs">
            <a href="{{ route('admin.reports.index') }}" class="filter-tab {{ is_null($status) ? 'active' : '' }}">
                <i data-lucide="layout-grid" style="width:14px;height:14px;"></i>
                Todos <span class="tab-counter">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('admin.reports.index', 'pending') }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
                <i data-lucide="alert-circle" style="width:14px;height:14px;"></i>
                Pendientes <span class="tab-counter pending">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('admin.reports.index', 'reviewed') }}" class="filter-tab {{ $status === 'reviewed' ? 'active' : '' }}">
                <i data-lucide="clipboard-check" style="width:14px;height:14px;"></i>
                Revisados <span class="tab-counter reviewed">{{ $counts['reviewed'] }}</span>
            </a>
            <a href="{{ route('admin.reports.index', 'resolved') }}" class="filter-tab {{ $status === 'resolved' ? 'active' : '' }}">
                <i data-lucide="shield-check" style="width:14px;height:14px;"></i>
                Resueltos <span class="tab-counter resolved">{{ $counts['resolved'] }}</span>
            </a>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert-banner success">
            <i data-lucide="check-circle-2" style="width:16px;height:16px;flex-shrink:0;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-banner danger">
            <i data-lucide="x-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

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
                        <th style="min-width:150px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i data-lucide="music" style="width:13px;height:13px;color:#94a3b8;"></i> Músico Reportado
                            </div>
                        </th>
                        <th style="min-width:220px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i data-lucide="message-square" style="width:13px;height:13px;color:#94a3b8;"></i> Motivo
                            </div>
                        </th>
                        <th style="min-width:110px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i data-lucide="calendar" style="width:13px;height:13px;color:#94a3b8;"></i> Fecha
                            </div>
                        </th>
                        <th style="min-width:110px;">Estado</th>
                        <th style="min-width:130px; text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr class="report-row">
                            {{-- CLIENTE --}}
                            <td>
                                <div class="user-cell">
                                    @php $ci = strtoupper(substr(($report->client->nombre ?? 'C'), 0, 2)); @endphp
                                    <div class="avatar-circle client-avatar">{{ $ci }}</div>
                                    <div>
                                        <strong style="font-size:13px;">{{ $report->client->nombre ?? 'Desconocido' }} {{ $report->client->apellido ?? '' }}</strong>
                                        <span class="sub-text">{{ Str::limit($report->client->email ?? '—', 28) }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- MÚSICO --}}
                            <td>
                                <div class="user-cell">
                                    @php
                                        $mName = $report->musicianProfile->stage_name ?? 'N/A';
                                        $mi = strtoupper(substr($mName, 0, 2));
                                    @endphp
                                    <div class="avatar-circle musician-avatar">{{ $mi }}</div>
                                    <div>
                                        <strong style="font-size:13px;">{{ $mName }}</strong>
                                        <span class="sub-text">ID #{{ $report->musician_profile_id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- MOTIVO (truncado con expandir) --}}
                            <td>
                                @php $fullReason = $report->reason; $short = Str::limit($fullReason, 90); @endphp
                                <p class="reason-short" id="reason-short-{{ $report->id }}">{{ $short }}</p>
                                @if(strlen($fullReason) > 90)
                                    <p class="reason-full" id="reason-full-{{ $report->id }}" style="display:none;">{{ $fullReason }}</p>
                                    <button class="expand-btn" onclick="toggleReason({{ $report->id }})">
                                        <i data-lucide="chevron-down" style="width:12px;height:12px;" id="expand-icon-{{ $report->id }}"></i>
                                        <span id="expand-label-{{ $report->id }}">Ver más</span>
                                    </button>
                                @endif
                            </td>

                            {{-- FECHA --}}
                            <td>
                                <span style="font-size:13px;font-weight:600;color:#334155;display:block;">{{ $report->created_at->format('d/m/Y') }}</span>
                                <span class="sub-text">{{ $report->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- ESTADO --}}
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending'  => ['class' => 'warning',  'icon' => 'clock',          'label' => 'Pendiente'],
                                        'reviewed' => ['class' => 'info',     'icon' => 'eye',            'label' => 'Revisado'],
                                        'resolved' => ['class' => 'success',  'icon' => 'check-circle-2', 'label' => 'Resuelto'],
                                    ];
                                    $cfg = $statusConfig[$report->status] ?? ['class' => 'secondary', 'icon' => 'minus', 'label' => $report->status];
                                @endphp
                                <span class="report-status-pill {{ $cfg['class'] }}">
                                    <i data-lucide="{{ $cfg['icon'] }}" style="width:11px;height:11px;"></i>
                                    {{ $cfg['label'] }}
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="actions-col">
                                    @if($report->status !== 'reviewed' && $report->status !== 'resolved')
                                        <form method="POST" action="{{ route('admin.reports.status', $report->id) }}" style="margin:0;">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="reviewed">
                                            <button type="submit" class="rpt-btn reviewed-btn" title="Marcar como Revisado">
                                                <i data-lucide="eye" style="width:13px;height:13px;"></i> Revisado
                                            </button>
                                        </form>
                                    @endif

                                    @if($report->status !== 'resolved')
                                        <form method="POST" action="{{ route('admin.reports.status', $report->id) }}" style="margin:0;">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit" class="rpt-btn resolved-btn" title="Marcar como Resuelto">
                                                <i data-lucide="check-circle-2" style="width:13px;height:13px;"></i> Resuelto
                                            </button>
                                        </form>
                                    @endif

                                    @if($report->status === 'resolved')
                                        <span class="closed-badge">
                                            <i data-lucide="lock" style="width:11px;height:11px;"></i> Cerrado
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:64px 24px;color:#94a3b8;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:14px;">
                                    <div style="width:64px;height:64px;background:#f8fafc;border-radius:16px;display:flex;align-items:center;justify-content:center;border:1px solid #e2e8f0;">
                                        <i data-lucide="flag-off" style="width:28px;height:28px;opacity:0.3;"></i>
                                    </div>
                                    <p style="margin:0;font-size:14px;font-weight:600;color:#475569;">
                                        @if($status === 'pending') No hay reportes pendientes por revisar.
                                        @elseif($status === 'reviewed') No hay reportes marcados como revisados.
                                        @elseif($status === 'resolved') No hay reportes resueltos todavía.
                                        @else Aún no se han recibido reportes desde la App.
                                        @endif
                                    </p>
                                    <p style="margin:0;font-size:12px;color:#94a3b8;">Los reportes de los clientes aparecerán aquí automáticamente.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        @if($reports->hasPages())
            <div class="table-footer">
                <span class="text-dim text-small">
                    Mostrando {{ $reports->firstItem() }}–{{ $reports->lastItem() }} de {{ $reports->total() }} reportes
                </span>
                <div class="pagination">{{ $reports->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif
    </div>

<style>
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

/* ── ALERT BANNERS ────────────────────────────────────────── */
.alert-banner {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; border-radius: 12px; font-size: 14px;
    font-weight: 500; margin-bottom: 20px;
}
.alert-banner.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.alert-banner.danger  { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

/* ── TABLA ────────────────────────────────────────────────── */
.reports-table { table-layout: auto; }
.reports-table thead th {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.05em; color: #94a3b8;
}
.report-row { transition: background 0.15s; }
.report-row:hover { background: #f8fafc; }
.report-row td { vertical-align: top; padding-top: 14px; padding-bottom: 14px; }

/* Avatares */
.client-avatar  { background: linear-gradient(135deg,#818cf8,#6366f1); color: #fff; font-size: 13px; }
.musician-avatar { background: linear-gradient(135deg,#f472b6,#ec4899); color: #fff; font-size: 13px; }

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
.report-status-pill.info     { background: #dbeafe; color: #1d4ed8; }
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
.reviewed-btn { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.reviewed-btn:hover { background: #dbeafe; }
.resolved-btn { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.resolved-btn:hover { background: #dcfce7; }
.closed-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700; color: #94a3b8;
    padding: 4px 8px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;
}

/* Pie de tabla */
.table-footer {
    display: flex; justify-content: space-between; align-items: center;
    padding: 16px 0 0; border-top: 1px solid #f1f5f9; margin-top: 8px;
}
.text-small { font-size: 12px; }
.pagination { margin: 0; }
.page-link { border-radius: 8px !important; margin: 0 2px; border: none; font-size: 13px; }
.page-item.active .page-link { background-color: #6366f1; }
</style>

<script>
function toggleReason(id) {
    const short  = document.getElementById('reason-short-' + id);
    const full   = document.getElementById('reason-full-'  + id);
    const icon   = document.getElementById('expand-icon-'  + id);
    const label  = document.getElementById('expand-label-' + id);
    const isOpen = full.style.display !== 'none';

    short.style.display = isOpen ? 'block' : 'none';
    full.style.display  = isOpen ? 'none'  : 'block';
    icon.setAttribute('data-lucide', isOpen ? 'chevron-down' : 'chevron-up');
    label.textContent = isOpen ? 'Ver más' : 'Ver menos';
    lucide.createIcons();
}
</script>

@endsection
