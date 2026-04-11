@extends('layouts.admin')

@section('admin-content')

    <div class="page-header-premium">
        <div class="header-info">
            <h2>Gestión de Eventos <i data-lucide="shield" style="color: #6366f1; width: 26px; height: 26px; opacity: 0.2;"></i></h2>
            <p>Moderación y supervisión integral de castings publicados por clientes</p>
        </div>
    </div>

    <div class="filter-wrapper-premium">
        <div class="filter-tabs-premium">
            <a href="{{ route('admin.castings.index') }}" class="filter-tab-premium {{ empty($status) ? 'active' : '' }}">
                <span>Todos</span>
                <span class="count-badge grey">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('admin.castings.index', ['status' => 'open']) }}" class="filter-tab-premium {{ $status === 'open' ? 'active' : '' }}">
                <span>En Curso</span>
                <span class="count-badge emerald">{{ $counts['open'] }}</span>
            </a>
            <a href="{{ route('admin.castings.index', ['status' => 'completed']) }}" class="filter-tab-premium {{ $status === 'completed' ? 'active' : '' }}">
                <span>Completados</span>
                <span class="count-badge blue">{{ $counts['completed'] }}</span>
            </a>
            <a href="{{ route('admin.castings.index', ['status' => 'canceled']) }}" class="filter-tab-premium {{ in_array($status, ['canceled', 'inactive']) ? 'active' : '' }}">
                <span>Inactivos / Cancel</span>
                <span class="count-badge red">{{ $counts['other'] }}</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-premium success animate-fade-in">
            <i data-lucide="check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="table-container-premium shadow-sm">
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="padding-left: 32px;">Detalles del Evento</th>
                        <th>Cliente / Autor</th>
                        <th>Fecha Programada</th>
                        <th>Estado</th>
                        <th style="padding-right: 32px; text-align: right;">Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td style="padding-left: 32px;">
                            <div class="event-cell-fancy">
                                <div class="event-icon-bg">
                                    <i data-lucide="music-2"></i>
                                </div>
                                <div class="event-meta">
                                    <span class="event-title">{{ $event->titulo }}</span>
                                    <span class="event-subs"><i data-lucide="users"></i> {{ $event->applications->count() }} postulaciones</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="client-cell">
                                <i data-lucide="user-circle"></i>
                                <span>{{ $event->client ? $event->client->nombre : 'Usuario Anónimo' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $displayDate = $event->fecha;
                                if (is_string($displayDate) && !empty($displayDate)) {
                                    try {
                                        $displayDate = \Carbon\Carbon::parse(str_replace('/', '-', $displayDate))->locale('es')->isoFormat('D MMM, YYYY');
                                    } catch (\Exception $e) { }
                                } elseif ($displayDate instanceof \Carbon\Carbon) {
                                    $displayDate = $displayDate->locale('es')->isoFormat('D MMM, YYYY');
                                }
                            @endphp
                            <div class="date-box-premium">
                                <i data-lucide="calendar"></i>
                                <span>{{ $displayDate ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'open'      => ['class' => 'badge-premium-success', 'label' => 'Abierto', 'icon' => 'globe'],
                                    'completed' => ['class' => 'badge-premium-blue',    'label' => 'Completado', 'icon' => 'check-circle'],
                                    'canceled'  => ['class' => 'badge-premium-danger',  'label' => 'Cancelado', 'icon' => 'slash'],
                                    'inactive'  => ['class' => 'badge-premium-default', 'label' => 'Inactivo', 'icon' => 'eye-off'],
                                ];
                                $st = $statusMap[$event->status] ?? ['class' => 'badge-premium-warning', 'label' => ucfirst($event->status), 'icon' => 'help-circle'];
                            @endphp
                            <span class="badge-fancy {{ $st['class'] }}">
                                <i data-lucide="{{ $st['icon'] }}"></i>
                                {{ $st['label'] }}
                            </span>
                        </td>
                        <td style="padding-right: 32px; text-align: right;">
                            <div class="actions-group-premium">
                                <a href="{{ route('admin.castings.show', $event->id) }}" class="btn-action-premium ghost" title="Ver detalles">
                                    <i data-lucide="eye"></i>
                                </a>

                                @if($event->status === 'open')
                                    <form action="{{ route('admin.castings.status', $event->id) }}" method="POST" class="inline-form" onsubmit="return confirm('¿Seguro que deseas cancelar este evento?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="canceled">
                                        <button type="submit" class="btn-action-premium danger-ghost" title="Cancelar">
                                            <i data-lucide="x-octagon"></i>
                                        </button>
                                    </form>
                                @elseif($event->status === 'canceled' || $event->status === 'inactive')
                                    <form action="{{ route('admin.castings.status', $event->id) }}" method="POST" class="inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="open">
                                        <button type="submit" class="btn-action-premium success-ghost" title="Reactivar">
                                            <i data-lucide="play-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('admin.castings.destroy', $event->id) }}" method="POST" class="inline-form" onsubmit="return confirm('¿Estás SEGURO de eliminar este evento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-premium ghost-red" title="Eliminar definitivamente">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="premium-empty-state">
                                <div class="empty-glow-icon">
                                    <i data-lucide="calendar-off"></i>
                                </div>
                                <h4>No hay eventos registrados</h4>
                                <p>Parece que aún no se han publicado castings o eventos dentro de esta categoría.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($events->hasPages())
            <div class="pagination-wrapper-premium">
                <span class="text-dim">Mostrando {{ $events->firstItem() }}-{{ $events->lastItem() }} de {{ $events->total() }} eventos</span>
                <div class="pagination-fancy">
                    {{ $events->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

    @section('head')
    <style>
        .page-header-premium {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;
            background: #ffffff; padding: 24px 32px; border-radius: 20px; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        }
        .header-info h2 { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.02em; display: flex; align-items: center; gap: 10px; }
        .header-info p { font-size: 15px; color: #64748b; margin: 0; }

        .filter-wrapper-premium { margin-bottom: 24px; }
        .filter-tabs-premium { display: flex; gap: 8px; background: #ffffff; padding: 8px; border-radius: 16px; border: 1px solid #e2e8f0; width: fit-content; }
        .filter-tab-premium {
            text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px 18px;
            border-radius: 12px; font-size: 14px; font-weight: 700; color: #64748b; transition: all 0.2s;
        }
        .filter-tab-premium:hover { background: #f8fafc; color: #1e293b; }
        .filter-tab-premium.active { background: #f1f5f9; color: #0f172a; }
        .count-badge { padding: 2px 10px; border-radius: 30px; font-size: 11.5px; font-weight: 800; }
        .count-badge.grey { background: #e2e8f0; color: #475569; }
        .count-badge.emerald { background: #dcfce7; color: #15803d; }
        .count-badge.blue { background: #e0f2fe; color: #0369a1; }
        .count-badge.red { background: #fee2e2; color: #dc2626; }

        .alert-premium { display: flex; align-items: center; gap: 12px; padding: 16px 24px; border-radius: 16px; margin-bottom: 24px; font-weight: 600; font-size: 14.5px; }
        .alert-premium.success { background: #effdf5; border: 1px solid #bbf7d0; color: #15803d; }

        .table-container-premium { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 32px; }
        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { background: #f8fafc; padding: 16px 24px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
        .premium-table td { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .premium-table tbody tr:hover { background: #fbfcfe; }

        .event-cell-fancy { display: flex; align-items: center; gap: 14px; }
        .event-icon-bg { width: 44px; height: 44px; border-radius: 12px; background: #f0f7ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .event-meta { display: flex; flex-direction: column; }
        .event-title { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
        .event-subs { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 4px; font-weight: 600; }
        .event-subs i { width: 12px; height: 12px; }

        .client-cell { display: flex; align-items: center; gap: 8px; color: #475569; font-size: 14px; font-weight: 500; }
        .client-cell i { width: 18px; height: 18px; color: #cbd5e1; }

        .date-box-premium { display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 13.5px; font-weight: 600; }
        .date-box-premium i { width: 15px; height: 15px; color: #94a3b8; }

        .badge-fancy { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 700; }
        .badge-premium-success { background: #dcfce7; color: #15803d; }
        .badge-premium-blue { background: #e0f2fe; color: #0369a1; }
        .badge-premium-danger { background: #fee2e2; color: #dc2626; }
        .badge-premium-default { background: #f1f5f9; color: #475569; }
        .badge-premium-warning { background: #fef3c7; color: #b45309; }

        .actions-group-premium { display: flex; justify-content: flex-end; gap: 8px; }
        .btn-action-premium { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border: 1.5px solid transparent; text-decoration: none; cursor: pointer; background: transparent; }
        .btn-action-premium i { width: 18px; height: 18px; }
        
        .btn-action-premium.ghost { color: #64748b; border-color: #e2e8f0; }
        .btn-action-premium.ghost:hover { background: #fff; border-color: #6366f1; color: #6366f1; }
        
        .btn-action-premium.danger-ghost { color: #ef4444; border-color: #fee2e2; }
        .btn-action-premium.danger-ghost:hover { background: #fee2e2; }
        
        .btn-action-premium.success-ghost { color: #10b981; border-color: #dcfce7; }
        .btn-action-premium.success-ghost:hover { background: #dcfce7; }
        
        .btn-action-premium.ghost-red { color: #94a3b8; }
        .btn-action-premium.ghost-red:hover { color: #ef4444; background: #fef2f2; }

        .premium-empty-state { padding: 80px 40px; text-align: center; }
        .empty-glow-icon { width: 80px; height: 80px; border-radius: 24px; background: #f8fafc; color: #cbd5e1; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; border: 1px solid #f1f5f9; }
        .empty-glow-icon i { width: 36px; height: 36px; }
        .premium-empty-state h4 { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0; }
        .premium-empty-state p { font-size: 14.5px; color: #94a3b8; margin: 0; }

        .pagination-wrapper-premium { padding: 24px 32px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .pagination-fancy .page-link { border: none; background: transparent; color: #64748b; font-weight: 600; font-size: 14px; padding: 8px 14px; border-radius: 10px; margin: 0 2px; }
        .pagination-fancy .page-item.active .page-link { background: #6366f1; color: #fff; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2); }

        .inline-form { display: inline; }

        @media (max-width: 1024px) {
            .page-header-premium { flex-direction: column; align-items: stretch; gap: 20px; }
            .filter-tabs-premium { width: 100%; overflow-x: auto; }
            .premium-table th, .premium-table td { padding: 16px; white-space: nowrap; }
        }
    </style>
    @endsection

@endsection
