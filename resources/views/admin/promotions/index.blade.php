@extends('layouts.admin')

@section('admin-content')

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
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        padding: 16px 24px;
        border-radius: 16px;
        border: 1px solid #bbf7d0;
        box-shadow: 0 4px 10px rgba(22, 163, 74, 0.1);
    }

    .revenue-label {
        display: block;
        font-size: 12px;
        color: #166534;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .revenue-amount {
        font-size: 28px;
        font-weight: 900;
        color: #15803d;
        letter-spacing: -0.02em;
    }

    .table-container {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .premium-table th {
        background: #f8fafc;
        padding: 16px 24px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
    }

    .premium-table td {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .premium-table tr:last-child td {
        border-bottom: none;
    }

    .premium-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .premium-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .musician-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 15px;
    }

    .plan-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 14.5px;
        margin-bottom: 4px;
    }

    .plan-price {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }

    .ticket-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #2563eb;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        padding: 8px 14px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .ticket-link:hover {
        background: #dbeafe;
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .no-ticket {
        color: #94a3b8;
        font-size: 13.5px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .badge-pending { background: #fef3c7; color: #b45309; }
    .badge-active { background: #dcfce7; color: #15803d; }
    .badge-default { background: #f1f5f9; color: #475569; }

    .vigencia-text {
        font-size: 14px;
        color: #475569;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .btn-approve, .btn-reject, .btn-stop {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-approve { background: #16a34a; color: white; }
    .btn-approve:hover { background: #15803d; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2);}

    .btn-reject { background: #ef4444; color: white; }
    .btn-reject:hover { background: #dc2626; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);}

    .btn-stop { background: #ffffff; color: #475569; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.05);}
    .btn-stop:hover { background: #f1f5f9; color: #334155; }

    .empty-state {
        text-align: center;
        padding: 80px 24px;
    }
    
    .empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px auto;
    }
    
    .empty-text {
        font-size: 16px;
        font-weight: 600;
        color: #475569;
        margin: 0;
    }

    .pagination-wrapper {
        padding: 20px 24px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    @media (max-width: 768px) {
        .page-header-premium { flex-direction: column; align-items: stretch; gap: 20px; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .premium-table th, .premium-table td { padding: 16px; white-space: nowrap; }
        .revenue-card { text-align: left; }
    }
</style>
@endsection

<header class="page-header-premium">
    <div class="header-info">
        <h2>Publicidad Interna <i data-lucide="zap" style="color: #eab308; width: 26px; height: 26px; fill: #eab308; opacity: 0.2;"></i></h2>
        <p>Monitoreo de validación e ingresos por promociones destacadas</p>
    </div>
    <div class="revenue-card">
        <span class="revenue-label">Ingresos del mes</span>
        <span class="revenue-amount">
            ${{ number_format($ingresosMes, 2) }} <span style="font-size: 16px; font-weight: 700; color: #166534;">MXN</span>
        </span>
    </div>
</header>

@if(session('success'))
    <div style="background: rgba(22, 163, 74, 0.1); border: 1px solid rgba(22, 163, 74, 0.2); color: #16a34a; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
        <i data-lucide="check-circle-2" style="width: 20px; height: 20px;"></i>
        {{ session('success') }}
    </div>
@endif

<div class="table-container">
    <div class="table-responsive">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Músico</th>
                    <th>Plan Adquirido</th>
                    <th>Comprobante</th>
                    <th>Vigencia</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $promo)
                <tr>
                    <td>
                        <div class="musician-name">{{ $promo->musicianProfile->stage_name ?? 'Sin nombre' }}</div>
                    </td>
                    <td>
                        <div class="plan-name">{{ $promo->plan_type }}</div>
                        <div class="plan-price">
                            @if($promo->plan_type == 'Flash') $29 MXN
                            @elseif($promo->plan_type == 'Basico') $99 MXN
                            @elseif($promo->plan_type == 'Estandar') $299 MXN
                            @else $699 MXN
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($promo->receipt_path)
                         <a href="{{ url('file/' . $promo->receipt_path) }}" target="_blank" class="ticket-link">
                            <i data-lucide="receipt" style="width: 16px; height: 16px;"></i> Ver Ticket
                        </a>
                        @else
                            <span class="no-ticket"><i data-lucide="file-x-2" style="width: 16px; height: 16px;"></i> Sin ticket</span>
                        @endif
                    </td>
                    <td>
                        <div class="vigencia-text">
                            @if($promo->valid_from && $promo->valid_until)
                                {{ $promo->valid_from->format('d M') }} - {{ $promo->valid_until->format('d M') }}
                            @else
                                <span style="color: #94a3b8;">Por transcurrir</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($promo->status === 'pendiente')
                            <span class="badge badge-pending">Pendiente de revisión</span>
                        @elseif($promo->status === 'aprobado')
                            <span class="badge badge-active">Operando en App</span>
                        @else
                            <span class="badge badge-default">{{ ucfirst($promo->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if($promo->status === 'pendiente')
                                <form action="{{ route('admin.promotions.status', $promo->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-approve">
                                        <i data-lucide="check" style="width: 16px; height: 16px;"></i> Aprobar
                                    </button>
                                </form>

                                <form action="{{ route('admin.promotions.status', $promo->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn-reject">
                                        <i data-lucide="x" style="width: 16px; height: 16px;"></i> Rechazar
                                    </button>
                                </form>
                            @endif

                            @if($promo->status === 'aprobado')
                                <form action="{{ route('admin.promotions.status', $promo->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="stop">
                                    <button type="submit" class="btn-stop">
                                        <i data-lucide="power-off" style="width: 16px; height: 16px;"></i> Detener
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i data-lucide="inbox" style="width: 32px; height: 32px;"></i>
                            </div>
                            <p class="empty-text">No hay solicitudes de promoción registradas en esta temporada.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination-wrapper">
        {{ $promotions->links() }}
    </div>
</div>

@endsection