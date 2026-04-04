@extends('layouts.admin')

@section('admin-content')

    <div class="page-header">
        <div>
            <h2>Gestión de Reportes</h2>
            <p class="dashboard-subtitle">Administra los reportes enviados por los clientes desde la App Móvil.</p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div class="filter-tabs" style="margin: 0;">
                <a href="{{ route('admin.reports.index') }}" class="filter-tab {{ !$status ? 'active' : '' }}">
                    Todos <span class="counter">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
                    Pendientes <span class="counter pending-counter">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'reviewed']) }}" class="filter-tab {{ $status === 'reviewed' ? 'active' : '' }}">
                    Revisados <span class="counter">{{ $counts['reviewed'] }}</span>
                </a>
                <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" class="filter-tab {{ $status === 'resolved' ? 'active' : '' }}">
                    Resueltos <span class="counter">{{ $counts['resolved'] }}</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #10b981; color: #047857; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
            <span style="font-size: 14px; font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    <div class="dashboard-box">
        <div class="table-responsive">
            <table class="admin-table reports-table">
                <thead>
                    <tr>
                        <th style="min-width:160px;">Cliente</th>
                        <th style="min-width:160px;">Músico Reportado</th>
                        <th>Motivo del Reporte</th>
                        <th style="min-width:130px;">Fecha</th>
                        <th style="min-width:120px;">Estado</th>
                        <th class="text-right" style="min-width:160px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            {{-- Cliente --}}
                            <td>
                                <div class="user-cell">
                                    @php $initials = strtoupper(substr(($report->client->nombre ?? 'C'), 0, 2)); @endphp
                                    <div class="avatar-circle">{{ $initials }}</div>
                                    <div>
                                        <strong>{{ $report->client->nombre ?? 'N/A' }} {{ $report->client->apellido ?? '' }}</strong>
                                        <span class="sub-text">{{ $report->client->email ?? '—' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Músico Reportado --}}
                            <td>
                                <div class="user-cell">
                                    @php
                                        $musicianName = $report->musicianProfile->stage_name ?? 'Músico desconocido';
                                        $mInitials = strtoupper(substr($musicianName, 0, 2));
                                    @endphp
                                    <div class="avatar-circle blue">{{ $mInitials }}</div>
                                    <div>
                                        <strong>{{ $musicianName }}</strong>
                                        <span class="sub-text">ID #{{ $report->musician_profile_id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Motivo --}}
                            <td>
                                <p class="reason-text">{{ $report->reason }}</p>
                            </td>

                            {{-- Fecha --}}
                            <td>
                                <span style="font-size: 13px; color: #475569;">{{ $report->created_at->format('d/m/Y') }}</span>
                                <span class="sub-text">{{ $report->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- Estado --}}
                            <td>
                                @php
                                    $statusClasses = [
                                        'pending'  => 'warning',
                                        'reviewed' => 'info',
                                        'resolved' => 'success',
                                    ];
                                    $statusLabels = [
                                        'pending'  => 'Pendiente',
                                        'reviewed' => 'Revisado',
                                        'resolved' => 'Resuelto',
                                    ];
                                @endphp
                                <span class="status-pill {{ $statusClasses[$report->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$report->status] ?? $report->status }}
                                </span>
                            </td>

                            {{-- Acciones --}}
                            <td class="text-right">
                                <div style="display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap;">
                                    @if($report->status !== 'reviewed')
                                        <form method="POST" action="{{ route('admin.reports.status', $report->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="reviewed">
                                            <button type="submit" class="action-btn reviewed-btn" title="Marcar como Revisado">
                                                <i data-lucide="eye" style="width:14px;height:14px;"></i> Revisado
                                            </button>
                                        </form>
                                    @endif
                                    @if($report->status !== 'resolved')
                                        <form method="POST" action="{{ route('admin.reports.status', $report->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit" class="action-btn resolved-btn" title="Marcar como Resuelto">
                                                <i data-lucide="check-circle-2" style="width:14px;height:14px;"></i> Resuelto
                                            </button>
                                        </form>
                                    @endif
                                    @if($report->status === 'resolved')
                                        <span style="font-size:12px;color:#10b981;display:flex;align-items:center;gap:4px;">
                                            <i data-lucide="check-circle-2" style="width:14px;height:14px;"></i> Caso Cerrado
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 56px 0; color: #94a3b8;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                                    <i data-lucide="flag-off" style="width: 48px; height: 48px; opacity: 0.2;"></i>
                                    <p style="margin: 0; font-size: 14px;">
                                        @if($status === 'pending') No hay reportes pendientes por revisar.
                                        @elseif($status === 'reviewed') No hay reportes en estado "Revisado".
                                        @elseif($status === 'resolved') No hay reportes resueltos todavía.
                                        @else No se han recibido reportes aún.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="table-footer">
                <span class="text-dim text-small">Mostrando {{ $reports->firstItem() }}-{{ $reports->lastItem() }} de {{ $reports->total() }}</span>
                <div class="pagination">
                    {{ $reports->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

    <style>
        .filter-tab {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .filter-tab.active {
            background: white;
            color: #1e293b;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #e2e8f0;
        }
        .filter-tab .counter {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
        }
        .filter-tab.active .counter {
            background: #e0e7ff;
            color: #4338ca;
        }
        .counter.pending-counter {
            background: #fef9c3;
            color: #ca8a04;
        }

        /* Tabla de Reportes */
        .reports-table td { vertical-align: top; }
        .reason-text {
            font-size: 13px;
            color: #334155;
            line-height: 1.6;
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* Status pills */
        .status-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .status-pill.warning  { background: #fef9c3; color: #ca8a04; }
        .status-pill.info     { background: #dbeafe; color: #1d4ed8; }
        .status-pill.success  { background: #dcfce7; color: #16a34a; }
        .status-pill.secondary { background: #f1f5f9; color: #64748b; }

        /* Action buttons */
        .action-btn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 10px; border-radius: 8px; font-size: 12px;
            font-weight: 600; cursor: pointer; border: 1.5px solid transparent;
            transition: all 0.15s;
        }
        .reviewed-btn {
            background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;
        }
        .reviewed-btn:hover { background: #dbeafe; }
        .resolved-btn {
            background: #f0fdf4; color: #16a34a; border-color: #bbf7d0;
        }
        .resolved-btn:hover { background: #dcfce7; }

        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0 0;
            border-top: 1px solid #f1f5f9;
            margin-top: 16px;
        }
        .text-small { font-size: 12px; }
        .pagination { margin: 0; }
        .page-link { border-radius: 8px !important; margin: 0 2px; border: none; font-size: 14px; }
        .page-item.active .page-link { background-color: #6366f1; }
    </style>

@endsection
