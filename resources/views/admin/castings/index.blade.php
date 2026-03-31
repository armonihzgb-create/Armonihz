@extends('layouts.admin')

@section('admin-content')

<header class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
    <div>
        <h2>Gestión de Eventos 🛡️</h2>
        <p class="dashboard-subtitle">Moderación de castings publicados por clientes</p>
    </div>
</header>

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
                        {{ \Carbon\Carbon::parse($event->fecha)->format('d M, Y') }}
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
                        
                        <a href="{{ route('castings.show', $event->id) }}" target="_blank" class="secondary-btn small-btn icon-only" title="Ver detalles públicos" style="text-decoration: none;">
                            <i data-lucide="external-link" style="width: 14px; height: 14px;"></i>
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
            <div style="padding: 16px; border-top: 1px solid var(--border-light);">
                {{ $events->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

@endsection
