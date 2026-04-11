@extends('layouts.admin')

@section('admin-content')

<header class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
    <div>
        <h2 style="display: flex; align-items: center; gap: 12px;">
            <i data-lucide="shield-check" style="width: 28px; height: 28px; color: #6366f1;"></i>
            Gestión de Eventos
        </h2>
        <p class="dashboard-subtitle">Moderación y supervisión de castings publicados por clientes</p>
    </div>
</header>

{{-- SECCIÓN DE TABS DE FILTRADO --}}
<div style="margin-bottom: 24px;">
    <div class="filter-tabs" style="background: white; padding: 6px; border-radius: 12px; display: inline-flex; gap: 4px; border: 1px solid #e2e8f0;">
        <a href="{{ route('admin.castings.index') }}" 
           class="filter-tab {{ empty($status) ? 'active' : '' }}" 
           style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; color: {{ empty($status) ? '#fff' : '#64748b' }}; background: {{ empty($status) ? '#6366f1' : 'transparent' }}; display: flex; align-items: center; gap: 8px;">
            Todos <span style="background: {{ empty($status) ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }}; padding: 2px 8px; border-radius: 6px; font-size: 11px;">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('admin.castings.index', ['status' => 'open']) }}" 
           class="filter-tab {{ $status === 'open' ? 'active' : '' }}" 
           style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; color: {{ $status === 'open' ? '#fff' : '#64748b' }}; background: {{ $status === 'open' ? '#6366f1' : 'transparent' }}; display: flex; align-items: center; gap: 8px;">
            En curso <span style="background: {{ $status === 'open' ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }}; padding: 2px 8px; border-radius: 6px; font-size: 11px;">{{ $counts['open'] }}</span>
        </a>
        <a href="{{ route('admin.castings.index', ['status' => 'completed']) }}" 
           class="filter-tab {{ $status === 'completed' ? 'active' : '' }}" 
           style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; color: {{ $status === 'completed' ? '#fff' : '#64748b' }}; background: {{ $status === 'completed' ? '#6366f1' : 'transparent' }}; display: flex; align-items: center; gap: 8px;">
            Completados <span style="background: {{ $status === 'completed' ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }}; padding: 2px 8px; border-radius: 6px; font-size: 11px;">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('admin.castings.index', ['status' => 'canceled']) }}" 
           class="filter-tab {{ in_array($status, ['canceled', 'inactive']) ? 'active' : '' }}" 
           style="padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; color: {{ in_array($status, ['canceled', 'inactive']) ? '#fff' : '#64748b' }}; background: {{ in_array($status, ['canceled', 'inactive']) ? '#6366f1' : 'transparent' }}; display: flex; align-items: center; gap: 8px;">
            Cancelados/Inactivos <span style="background: {{ in_array($status, ['canceled', 'inactive']) ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }}; padding: 2px 8px; border-radius: 6px; font-size: 11px;">{{ $counts['other'] }}</span>
        </a>
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
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-light); color: var(--text-dim); font-size: 13px; text-transform: uppercase;">
                    <th style="padding: 12px;">Evento</th>
                    <th style="padding: 12px;">Cliente</th>
                    <th style="padding: 12px;">Fecha del Evento</th>
                    <th style="padding: 12px;">Estado</th>
                    <th style="padding: 12px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr style="border-bottom: 1px solid var(--border-light);">
                    <td style="padding: 16px;">
                        <strong>{{ $event->titulo }}</strong>
                        <div style="font-size: 12px; color: var(--text-dim); margin-top: 4px;">
                            {{ $event->applications->count() }} postulaciones
                        </div>
                    </td>
                    <td style="padding: 16px;">
                        {{ $event->client ? $event->client->nombre : 'Usuario Anónimo' }}
                    </td>
                    <td style="padding: 16px; font-size: 14px; color: var(--text-dim);">
                        @php
                            $displayDate = $event->fecha;
                            if (is_string($displayDate) && !empty($displayDate)) {
                                try {
                                    // Cambiamos / por - para que Carbon::parse no se confunda con MM/DD/YYYY
                                    $displayDate = \Carbon\Carbon::parse(str_replace('/', '-', $displayDate))->format('d M, Y');
                                } catch (\Exception $e) {
                                    // Si falla, dejamos la cadena original
                                }
                            } elseif ($displayDate instanceof \Carbon\Carbon) {
                                $displayDate = $displayDate->format('d M, Y');
                            }
                        @endphp
                        {{ $displayDate ?? 'N/A' }}
                    </td>
                    <td style="padding: 16px;">
                        @if($event->status === 'open')
                            <span class="status-pill" style="background: #e0f2fe; color: #0284c7;">Abierto</span>
                        @elseif($event->status === 'completed')
                            <span class="status-pill" style="background: #dcfce7; color: #16a34a;">Completado</span>
                        @elseif($event->status === 'canceled')
                            <span class="status-pill" style="background: #fee2e2; color: #ef4444;">Cancelado</span>
                        @elseif($event->status === 'inactive')
                            <span class="status-pill" style="background: #f1f5f9; color: #64748b;">Inactivo</span>
                        @else
                            <span class="status-pill" style="background: #fef9c3; color: #ca8a04;">{{ ucfirst($event->status) }}</span>
                        @endif
                    </td>
                    <td style="padding: 16px; text-align: right; display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                        
                        <a href="{{ route('admin.castings.show', $event->id) }}" class="secondary-btn small-btn" title="Ver detalles del evento" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 12px;">
                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Detalles
                        </a>

                        @if($event->status === 'open')
                            <form action="{{ route('admin.castings.status', $event->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas cancelar este evento? Los músicos ya no podrán postularse.');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="canceled">
                                <button type="submit" class="secondary-btn small-btn" style="color: #ef4444; border-color: #fecaca; background: #fef2f2;">Cancelar</button>
                            </form>
                        @elseif($event->status === 'canceled' || $event->status === 'inactive')
                            <form action="{{ route('admin.castings.status', $event->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="open">
                                <button type="submit" class="primary-btn small-btn" style="background: #10b981;">Reactivar</button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.castings.destroy', $event->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás SEGURO de eliminar este evento? Aplicará Soft Delete.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="secondary-btn small-btn icon-only" style="color: #ef4444; border-color: transparent;" title="Eliminar">
                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 48px; color: var(--text-dim);">
                        <i data-lucide="calendar-x" style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.3;"></i>
                        <p>No hay eventos registrados en el sistema.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($events->hasPages())
            <div style="padding: 24px; border-top: 1px solid #f1f5f9; display: flex; justify-content: center;">
                <div class="custom-pagination">
                    {{ $events->links('pagination::bootstrap-4') }}
                </div>
            </div>
            <style>
                .custom-pagination .pagination { display: flex; list-style: none; padding: 0; gap: 8px; align-items: center; margin: 0; }
                .custom-pagination .page-item .page-link { border: 1px solid #e2e8f0; padding: 8px 14px; border-radius: 8px; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; background: white; }
                .custom-pagination .page-item.active .page-link { background: #6366f1; color: white; border-color: #6366f1; }
                .custom-pagination .page-item.disabled .page-link { background: #f8fafc; color: #cbd5e1; cursor: not-allowed; }
                .custom-pagination .page-item:not(.active):not(.disabled) .page-link:hover { border-color: #6366f1; color: #6366f1; background: #f5f3ff; }
            </style>
        @endif
    </div>
</div>

@endsection
