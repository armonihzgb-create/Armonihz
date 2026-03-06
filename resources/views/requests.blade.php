@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <div>
            <h2>Bandeja de Solicitudes 📥</h2>
            <p class="dashboard-subtitle">Gestiona tus propuestas de contratación.</p>
        </div>
        {{-- Filtros de estado --}}
        <div class="filter-tabs">
            <button class="filter-tab active">
                Todas <span class="counter">5</span>
            </button>
            <button class="filter-tab">
                Pendientes <span class="counter" style="background: #f59e0b;">2</span>
            </button>
            <button class="filter-tab">
                Respondidas <span class="counter" style="background: var(--accent-blue);">1</span>
            </button>
            <button class="filter-tab">
                Confirmadas <span class="counter" style="background: var(--accent-green);">1</span>
            </button>
        </div>
    </div>

    <div class="dashboard-box" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Solicitante</th>
                        <th>Detalles del Evento</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>
                            <div class="client-info">
                                <div class="avatar-circle {{ $req->status === 'accepted' ? 'green' : ($req->status === 'pending' ? 'blue' : 'gray') }}">
                                    {{ substr($req->client->name ?? 'Usuario', 0, 2) }}
                                </div>
                                <div>
                                    <strong>{{ $req->client->name ?? 'Cliente Anónimo' }}</strong>
                                    <span class="client-type">Solicitante</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="event-summary">
                                <strong>{{ Str::limit($req->description, 30) }}</strong>
                                <span><i data-lucide="map-pin" style="width:12px;"></i> {{ $req->event_location }}</span>
                                <span class="budget">${{ number_format($req->budget, 2) }} MXN</span>
                            </div>
                        </td>
                        <td>
                            <div class="date-box">
                                <span class="day">{{ \Carbon\Carbon::parse($req->event_date)->format('d') }}</span>
                                <span class="month">{{ \Carbon\Carbon::parse($req->event_date)->format('M') }}</span>
                            </div>
                        </td>
                        <td>
                            @if($req->status === 'pending')
                                <span class="status-badge warning">Pendiente</span>
                            @elseif($req->status === 'accepted')
                                <span class="status-badge success">Confirmada</span>
                            @else
                                <span class="status-badge error">Rechazada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('requests.show', ['id' => $req->id]) }}" class="secondary-btn sm">
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-dim);">
                            No tienes solicitudes de contratación aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .table-responsive { overflow-x: auto; }
        .requests-table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .requests-table th { text-align: left; padding: 16px 24px; color: var(--text-dim); font-size: 12px; text-transform: uppercase; background: #f9fafb; border-bottom: 1px solid var(--border-light); font-weight: 700; }
        .requests-table td { padding: 20px 24px; border-bottom: 1px solid var(--border-light); vertical-align: middle; }
        .requests-table tr:last-child td { border-bottom: none; }
        .requests-table tr:hover td { background: #fafafa; }
        
        .client-info { display: flex; align-items: center; gap: 12px; }
        .avatar-circle { width: 40px; height: 40px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-main); font-size: 14px; }
        .avatar-circle.blue { background: rgba(47, 147, 245, 0.1); color: var(--accent-blue); }
        .avatar-circle.green { background: rgba(0, 195, 125, 0.1); color: var(--accent-green); }
        .avatar-circle.gray { background: #f3f4f6; color: #9ca3af; }
        
        .client-type { display: block; font-size: 11px; color: var(--text-dim); margin-top: 2px; }
        
        .event-summary { display: flex; flex-direction: column; gap: 4px; }
        .event-summary strong { font-size: 14px; color: var(--text-main); }
        .event-summary span { font-size: 12px; color: var(--text-dim); display: flex; align-items: center; gap: 4px; }
        .budget { color: var(--text-main) !important; font-weight: 600; }

        .date-box { border: 1px solid var(--border-light); border-radius: 8px; padding: 4px 8px; text-align: center; display: inline-block; background: white; min-width: 50px; }
        .date-box .day { display: block; font-size: 16px; font-weight: 700; line-height: 1; }
        .date-box .month { display: block; font-size: 10px; text-transform: uppercase; color: var(--text-dim); margin-top: 2px; }

        .status-badge.info { background: rgba(47, 147, 245, 0.1); color: var(--accent-blue); }
        
        .primary-btn.sm, .secondary-btn.sm { padding: 6px 16px; font-size: 13px; height: 32px; min-width: 100px; }
    </style>
@endsection
