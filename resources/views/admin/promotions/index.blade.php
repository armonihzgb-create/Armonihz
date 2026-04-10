@extends('layouts.admin')

@section('admin-content')

<header class="dashboard-header">
    <div>
        <h2>Publicidad Interna 💲</h2>
        <p class="dashboard-subtitle">Monitoreo de ingresos por promociones destacadas</p>
    </div>
    <div style="text-align: right;">
        <span style="display: block; font-size: 12px; color: var(--text-dim);">Ingresos del mes</span>
        <span style="font-size: 24px; font-weight: 700; color: var(--accent-green);">
            ${{ number_format($ingresosMes, 2) }} MXN
        </span>
    </div>
</header>

@if(session('success'))
    <div style="background: rgba(0, 195, 125, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div class="dashboard-box">
    <div class="table-responsive">
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-light); color: var(--text-dim); font-size: 13px; text-transform: uppercase;">
                    <th style="padding: 12px;">Músico</th>
                    <th style="padding: 12px;">Plan</th>
                    <th style="padding: 12px;">Comprobante</th>
                    <th style="padding: 12px;">Vigencia</th>
                    <th style="padding: 12px;">Estado</th>
                    <th style="padding: 12px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $promo)
                <tr style="border-bottom: 1px solid var(--border-light);">
                    <td style="padding: 16px;">
                        <strong>{{ $promo->musicianProfile->stage_name ?? 'Sin nombre' }}</strong>
                    </td>
                    <td style="padding: 16px; font-size: 14px;">
                        {{ $promo->plan_type }}
                    <div style="color: var(--text-dim); font-size: 12px;">
                            @if($promo->plan_type == 'Flash') $29 MXN
                            @elseif($promo->plan_type == 'Basico') $99 MXN
                            @elseif($promo->plan_type == 'Estandar') $299 MXN
                            @else $699 MXN
                            @endif
                        </div>
                    </td>
                    <td style="padding: 16px;">
                        @if($promo->receipt_path)
                         <a href="{{ url('file/' . $promo->receipt_path) }}" target="_blank" style="color: #3b82f6; text-decoration: underline; font-size: 13px;">
                            Ver Ticket
                        </a>
                        @else
                            <span style="color: #94a3b8; font-size: 13px;">Sin ticket</span>
                        @endif
                    </td>
                    <td style="padding: 16px; font-size: 13px; color: var(--text-dim);">
                        @if($promo->valid_from && $promo->valid_until)
                            {{ $promo->valid_from->format('d M') }} - {{ $promo->valid_until->format('d M') }}
                        @else
                            Por definir
                        @endif
                    </td>
                    <td style="padding: 16px;">
                        @if($promo->status === 'pendiente')
                            <span class="status-badge" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">Pendiente</span>
                        @elseif($promo->status === 'aprobado')
                            <span class="status-badge success" style="background: rgba(0, 195, 125, 0.1); color: var(--accent-green);">Activo</span>
                        @else
                            <span class="status-badge" style="background: #f1f5f9; color: #475569;">{{ ucfirst($promo->status) }}</span>
                        @endif
                    </td>
                    <td style="padding: 16px; text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                            
                            @if($promo->status === 'pendiente')
                                <form action="{{ route('admin.promotions.status', $promo->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" style="background: var(--accent-green); color: white; border: none; padding: 6px 12px; font-size: 12px; border-radius: 4px; cursor: pointer;">
                                        Aprobar
                                    </button>
                                </form>

                                <form action="{{ route('admin.promotions.status', $promo->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 12px; font-size: 12px; border-radius: 4px; cursor: pointer;">
                                        Rechazar
                                    </button>
                                </form>
                            @endif

                            @if($promo->status === 'aprobado')
                                <form action="{{ route('admin.promotions.status', $promo->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="stop">
                                    <button type="submit" class="secondary-btn" style="padding: 6px 12px; font-size: 12px;">
                                        Detener
                                    </button>
                                </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 24px; text-align: center; color: var(--text-dim);">
                        No hay solicitudes de promoción registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="padding: 16px;">
        {{ $promotions->links() }}
    </div>
</div>

@endsection