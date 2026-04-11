@extends('layouts.admin')

@section('head')
<style>
    .page-header-premium {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        padding: 24px 32px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .header-info h2 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header-info p {
        font-size: 14.5px;
        color: #64748b;
        margin: 0;
    }

    .revenue-card {
        text-align: right;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        padding: 16px 24px;
        border-radius: 16px;
        border: 1px solid #a5b4fc;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.1);
    }

    .revenue-label {
        display: block;
        font-size: 12px;
        color: #4338ca;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .revenue-amount {
        font-size: 19px;
        font-weight: 800;
        color: #3730a3;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* TABS */
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; background: #ffffff; padding: 10px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
    .filter-tab {
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 18px; border-radius: 12px; font-size: 14px; font-weight: 600;
        color: #64748b; transition: all 0.2s; border: 1px solid transparent; background: transparent;
    }
    .filter-tab:hover { background: #f8fafc; color: #1e293b; }
    .filter-tab.active {
        background: #f1f5f9; color: #0f172a;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); border-color: #e2e8f0;
    }
    .tab-counter {
        background: #e2e8f0; color: #475569;
        padding: 2px 8px; border-radius: 999px; font-size: 11.5px; font-weight: 800; margin-left: 4px;
    }
    .tab-counter.pending  { background: #fef3c7; color: #b45309; }
    .tab-counter.reviewed { background: #dbeafe; color: #1d4ed8; }
    .tab-counter.resolved { background: #dcfce7; color: #15803d; }

    /* TABLE */
    .table-container {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .premium-table { width: 100%; border-collapse: collapse; text-align: left; }
    .premium-table th {
        background: #f8fafc; padding: 16px 24px; font-size: 13px; font-weight: 700;
        color: #475569; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .premium-table th div { display: flex; align-items: center; gap: 8px; }
    .premium-table td { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    .premium-table tr:last-child td { border-bottom: none; }
    .premium-table tbody tr { transition: background-color 0.2s ease; }
    .premium-table tbody tr:hover { background-color: #f8fafc; }

    /* CELLS */
    .user-cell { display: flex; align-items: center; gap: 12px; }
    .avatar-circle {
        width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center;
        justify-content: center; font-weight: 800 !important; flex-shrink: 0; box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .client-avatar  { background: linear-gradient(135deg,#818cf8,#6366f1); color: #fff; font-size: 14px; }
    .musician-avatar { background: linear-gradient(135deg,#f472b6,#ec4899); color: #fff; font-size: 14px; }
    .user-cell strong { font-size: 14.5px; color: #1e293b; display: block; margin-bottom: 2px; }
    .sub-text { font-size: 13.5px; color: #64748b; font-weight: 500; display: block; }

    /* MOTIVO */
    .reason-short, .reason-full { font-size: 14.5px; color: #334155; line-height: 1.6; margin: 0 0 8px; white-space: pre-wrap; word-break: break-word; }
    .expand-btn {
        display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #4f46e5;
        background: #e0e7ff; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;
    }
    .expand-btn:hover { background: #c7d2fe; color: #3730a3; }

    /* STATUS */
    .badge {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
        border-radius: 20px; font-size: 12.5px; font-weight: 700; letter-spacing: 0.02em;
    }
    .badge-pending { background: #fef3c7; color: #b45309; }
    .badge-reviewed { background: #dbeafe; color: #1d4ed8; }
    .badge-resolved { background: #dcfce7; color: #15803d; }
    .badge-default { background: #f1f5f9; color: #475569; }

    /* BUTTONS */
    .actions-col { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
    .btn-action {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
        border: none; cursor: pointer; transition: all 0.2s; width: 100%; white-space: nowrap;
    }
    .btn-reviewed { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .btn-reviewed:hover { background: #dbeafe; color: #1d4ed8; }
    .btn-resolved { background: #16a34a; color: white; }
    .btn-resolved:hover { background: #15803d; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2);}
    .closed-badge {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 13px; font-weight: 600; color: #64748b;
        padding: 8px 16px; background: #f8fafc; border-radius: 10px; border: 1px dashed #cbd5e1; width: 100%;
    }

    /* EMPTY STATE */
    .empty-state { text-align: center; padding: 80px 24px; }
    .empty-icon {
        width: 64px; height: 64px; border-radius: 16px; background: #f1f5f9; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;
    }
    .empty-text { font-size: 16px; font-weight: 600; color: #475569; margin: 0 0 4px 0; }
    .empty-subtext { font-size: 14px; color: #94a3b8; margin: 0; }

    .pagination-wrapper { padding: 20px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; }
    .text-dim { color: #64748b; font-size: 13px; }

    @media (max-width: 768px) {
        .page-header-premium { flex-direction: column; align-items: stretch; gap: 20px; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .premium-table th, .premium-table td { padding: 16px; }
        .revenue-card { text-align: left; }
    }
</style>
@endsection

@section('admin-content')

    {{-- CABECERA PREMIUM --}}
    <header class="page-header-premium">
        <div class="header-info">
            <h2>Gestión de Reportes <i data-lucide="flag" style="color: #ef4444; width: 26px; height: 26px; fill: #ef4444; opacity: 0.2;"></i></h2>
            <p>Revisa y resuelve los reportes enviados por los clientes desde la App Móvil.</p>
        </div>
        <div class="revenue-card">
            <span class="revenue-label">Plataforma</span>
            <span class="revenue-amount">
                <i data-lucide="smartphone" style="width: 20px; height: 20px;"></i> App Móvil
            </span>
        </div>
    </header>

    {{-- TABS ESTÉTICAS --}}
    <div class="filter-tabs">
        <a href="{{ route('admin.reports.index') }}" class="filter-tab {{ is_null($status) ? 'active' : '' }}">
            <i data-lucide="layout-grid" style="width:16px;height:16px;"></i>
            Todos <span class="tab-counter">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('admin.reports.index', 'pending') }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
            <i data-lucide="alert-circle" style="width:16px;height:16px;"></i>
            Pendientes <span class="tab-counter pending">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('admin.reports.index', 'reviewed') }}" class="filter-tab {{ $status === 'reviewed' ? 'active' : '' }}">
            <i data-lucide="eye" style="width:16px;height:16px;"></i>
            Revisados <span class="tab-counter reviewed">{{ $counts['reviewed'] }}</span>
        </a>
        <a href="{{ route('admin.reports.index', 'resolved') }}" class="filter-tab {{ $status === 'resolved' ? 'active' : '' }}">
            <i data-lucide="check-circle-2" style="width:16px;height:16px;"></i>
            Resueltos <span class="tab-counter resolved">{{ $counts['resolved'] }}</span>
        </a>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div style="background: rgba(22, 163, 74, 0.1); border: 1px solid rgba(22, 163, 74, 0.2); color: #16a34a; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="check-circle-2" style="width: 20px; height: 20px;"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #dc2626; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="x-circle" style="width: 20px; height: 20px;"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- TABLA --}}
    <div class="table-container">
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="min-width:180px;">
                            <div><i data-lucide="user" style="width:14px;height:14px;"></i> Cliente Emisor</div>
                        </th>
                        <th style="min-width:180px;">
                            <div><i data-lucide="music" style="width:14px;height:14px;"></i> Músico Reportado</div>
                        </th>
                        <th style="min-width:240px;">
                            <div><i data-lucide="message-square" style="width:14px;height:14px;"></i> Motivo</div>
                        </th>
                        <th style="min-width:120px;">
                            <div><i data-lucide="calendar" style="width:14px;height:14px;"></i> Fecha</div>
                        </th>
                        <th style="min-width:140px;">Estado</th>
                        <th style="min-width:130px; text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            {{-- CLIENTE --}}
                            <td>
                                <div class="user-cell">
                                    @php $ci = strtoupper(substr(($report->client->nombre ?? 'C'), 0, 2)); @endphp
                                    <div class="avatar-circle client-avatar">{{ $ci }}</div>
                                    <div>
                                        <strong>{{ $report->client->nombre ?? 'Desconocido' }} {{ $report->client->apellido ?? '' }}</strong>
                                        <span class="sub-text" style="font-size: 12.5px;">{{ Str::limit($report->client->email ?? '—', 28) }}</span>
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
                                        <strong>{{ $mName }}</strong>
                                        <span class="sub-text" style="font-size: 12.5px;">ID #{{ $report->musician_profile_id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- MOTIVO (truncado con expandir) --}}
                            <td>
                                @php $fullReason = $report->reason; $short = Str::limit($fullReason, 75); @endphp
                                <p class="reason-short" id="reason-short-{{ $report->id }}">{{ $short }}</p>
                                @if(strlen($fullReason) > 75)
                                    <p class="reason-full" id="reason-full-{{ $report->id }}" style="display:none;">{{ $fullReason }}</p>
                                    <button class="expand-btn" onclick="toggleReason({{ $report->id }})">
                                        <i data-lucide="chevron-down" style="width:14px;height:14px;" id="expand-icon-{{ $report->id }}"></i>
                                        <span id="expand-label-{{ $report->id }}">Ver completo</span>
                                    </button>
                                @endif
                            </td>

                            {{-- FECHA --}}
                            <td>
                                <strong style="font-size:14px;color:#334155;display:block;margin-bottom:2px;">{{ $report->created_at->format('d/m/Y') }}</strong>
                                <span class="sub-text">{{ $report->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- ESTADO --}}
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending'  => ['class' => 'badge-pending',  'icon' => 'clock',          'label' => 'Pendiente'],
                                        'reviewed' => ['class' => 'badge-reviewed', 'icon' => 'eye',            'label' => 'Revisado'],
                                        'resolved' => ['class' => 'badge-resolved', 'icon' => 'check-circle-2', 'label' => 'Resuelto'],
                                    ];
                                    $cfg = $statusConfig[$report->status] ?? ['class' => 'badge-default', 'icon' => 'minus', 'label' => ucfirst($report->status)];
                                @endphp
                                <span class="badge {{ $cfg['class'] }}">
                                    <i data-lucide="{{ $cfg['icon'] }}" style="width:14px;height:14px;"></i>
                                    {{ $cfg['label'] }}
                                </span>
                            </td>

                            {{-- ACCIONES --}}
                            <td>
                                <div class="actions-col">
                                    @if($report->status !== 'reviewed' && $report->status !== 'resolved')
                                        <form method="POST" action="{{ route('admin.reports.status', $report->id) }}" style="margin:0; width: 100%;">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="reviewed">
                                            <button type="submit" class="btn-action btn-reviewed" title="Marcar como Revisado">
                                                <i data-lucide="eye" style="width:16px;height:16px;"></i> Revisado
                                            </button>
                                        </form>
                                    @endif

                                    @if($report->status !== 'resolved')
                                        <form method="POST" action="{{ route('admin.reports.status', $report->id) }}" style="margin:0; width: 100%;">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit" class="btn-action btn-resolved" title="Marcar como Resuelto">
                                                <i data-lucide="check" style="width:16px;height:16px;"></i> Resuelto
                                            </button>
                                        </form>
                                    @endif

                                    @if($report->status === 'resolved')
                                        <span class="closed-badge">
                                            <i data-lucide="lock" style="width:14px;height:14px;"></i> Cerrado
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i data-lucide="flag-off" style="width:32px;height:32px;"></i>
                                    </div>
                                    <p class="empty-text">
                                        @if($status === 'pending') No hay reportes pendientes por revisar.
                                        @elseif($status === 'reviewed') No hay reportes marcados como revisados.
                                        @elseif($status === 'resolved') No hay reportes resueltos todavía.
                                        @else Aún no se han recibido reportes desde la App Móvil.
                                        @endif
                                    </p>
                                    <p class="empty-subtext">Cualquier reporte generado por un cliente aparecerá aquí automáticamente para su seguimiento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        @if($reports->hasPages())
            <div class="pagination-wrapper">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <span class="text-dim">
                        Mostrando {{ $reports->firstItem() }}–{{ $reports->lastItem() }} de {{ $reports->total() }} reportes
                    </span>
                    <div class="pagination">{{ $reports->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        @endif
    </div>

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
    label.textContent = isOpen ? 'Ver completo' : 'Ver menos';
    lucide.createIcons();
}
</script>

@endsection
