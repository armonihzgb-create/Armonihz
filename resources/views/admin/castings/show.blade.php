@extends('layouts.admin')

@section('admin-content')

@php
    // Helper para formatear la fecha del evento
    $fechaFormateada = 'N/A';
    if (!empty($event->fecha)) {
        try {
            $fechaFormateada = \Carbon\Carbon::parse(str_replace('/', '-', $event->fecha))
                ->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        } catch (\Exception $e) {
            $fechaFormateada = $event->fecha;
        }
    }

    $statusMap = [
        'open'      => ['label' => 'Abierto',    'bg' => '#e0f2fe', 'color' => '#0284c7'],
        'completed' => ['label' => 'Completado', 'bg' => '#dcfce7', 'color' => '#16a34a'],
        'canceled'  => ['label' => 'Cancelado',  'bg' => '#fee2e2', 'color' => '#ef4444'],
        'inactive'  => ['label' => 'Inactivo',   'bg' => '#f1f5f9', 'color' => '#64748b'],
    ];
    $statusInfo = $statusMap[$event->status] ?? ['label' => ucfirst($event->status), 'bg' => '#fef9c3', 'color' => '#ca8a04'];
@endphp

<div style="margin-bottom: 24px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
    <a href="{{ route('admin.castings.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: #6366f1; text-decoration: none; font-weight: 500;">
        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Volver a Gestión de Eventos
    </a>
</div>

{{-- CABECERA DEL EVENTO --}}
<div class="dashboard-box" style="padding: 28px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <span style="font-size: 22px; font-weight: 700; color: #0f172a;">{{ $event->titulo }}</span>
                <span style="background: {{ $statusInfo['bg'] }}; color: {{ $statusInfo['color'] }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    {{ $statusInfo['label'] }}
                </span>
            </div>
            <div style="font-size: 13px; color: #94a3b8;">
                Publicado el {{ \Carbon\Carbon::parse($event->created_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
            </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @if($event->status === 'open')
                <form action="{{ route('admin.castings.status', $event->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('¿Cancelar este evento?');">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="canceled">
                    <button type="submit" class="secondary-btn" style="color: #ef4444; border-color: #fecaca; background: #fef2f2;">
                        <i data-lucide="x-circle" style="width: 14px; height: 14px;"></i> Cancelar
                    </button>
                </form>
            @elseif(in_array($event->status, ['canceled', 'inactive']))
                <form action="{{ route('admin.castings.status', $event->id) }}" method="POST" style="margin:0;">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="open">
                    <button type="submit" class="primary-btn" style="background: #10b981;">
                        <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i> Reactivar
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.castings.destroy', $event->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('¿Eliminar este evento permanentemente?');">
                @csrf @method('DELETE')
                <button type="submit" class="secondary-btn" style="color: #ef4444; border-color: #fecaca; background: #fef2f2;">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Eliminar
                </button>
            </form>
        </div>
    </div>
</div>

{{-- GRID DE INFO --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">

    {{-- DETALLES DEL EVENTO --}}
    <div class="dashboard-box" style="padding: 24px;">
        <h3 style="font-size: 15px; font-weight: 600; color: #475569; margin: 0 0 20px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="calendar-check" style="width: 16px; height: 16px; color: #6366f1;"></i> Detalles del Evento
        </h3>
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                <i data-lucide="map-pin" style="width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Ubicación</div>
                    <div style="font-size: 14px; font-weight: 500; color: #1e293b;">{{ $event->ubicacion ?? 'No especificada' }}</div>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                <i data-lucide="calendar" style="width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Fecha</div>
                    <div style="font-size: 14px; font-weight: 500; color: #1e293b; text-transform: capitalize;">{{ $fechaFormateada }}</div>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                <i data-lucide="clock" style="width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Horario</div>
                    <div style="font-size: 14px; font-weight: 500; color: #1e293b;">{{ $event->duracion ?? 'No especificado' }}</div>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                <i data-lucide="music" style="width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Género Musical</div>
                    <div style="font-size: 14px; font-weight: 500; color: #1e293b;">{{ $event->genre ? $event->genre->name : 'No especificado' }}</div>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                <i data-lucide="dollar-sign" style="width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; margin-top: 2px;"></i>
                <div>
                    <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Presupuesto</div>
                    <div style="font-size: 14px; font-weight: 500; color: #1e293b;">
                        @if($event->presupuesto)
                            ${{ number_format($event->presupuesto, 0, '.', ',') }} MXN
                        @else
                            A negociar
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($event->descripcion)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Descripción</div>
                <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0;">{{ $event->descripcion }}</p>
            </div>
        @endif
    </div>

    {{-- INFO DEL CLIENTE --}}
    <div class="dashboard-box" style="padding: 24px;">
        <h3 style="font-size: 15px; font-weight: 600; color: #475569; margin: 0 0 20px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="user-circle" style="width: 16px; height: 16px; color: #6366f1;"></i> Cliente Organizador
        </h3>
        @if($event->client)
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 20px; flex-shrink: 0;">
                    {{ strtoupper(substr($event->client->nombre ?? 'C', 0, 1)) }}
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 600; color: #1e293b;">{{ $event->client->nombre }}</div>
                    <div style="font-size: 13px; color: #94a3b8;">ID Firebase: {{ substr($event->client->firebase_uid, 0, 12) }}...</div>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @if($event->client->email)
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <i data-lucide="mail" style="width: 14px; height: 14px; color: #94a3b8; flex-shrink: 0;"></i>
                        <span style="font-size: 13px; color: #475569;">{{ $event->client->email }}</span>
                    </div>
                @endif
                @if($event->client->telefono)
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <i data-lucide="phone" style="width: 14px; height: 14px; color: #94a3b8; flex-shrink: 0;"></i>
                        <span style="font-size: 13px; color: #475569;">{{ $event->client->telefono }}</span>
                    </div>
                @endif
                @if($event->client->ciudad)
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <i data-lucide="map-pin" style="width: 14px; height: 14px; color: #94a3b8; flex-shrink: 0;"></i>
                        <span style="font-size: 13px; color: #475569;">{{ $event->client->ciudad }}</span>
                    </div>
                @endif
            </div>
        @else
            <div style="text-align: center; padding: 32px 0; color: #94a3b8;">
                <i data-lucide="user-x" style="width: 40px; height: 40px; opacity: 0.3; margin-bottom: 8px;"></i>
                <p style="margin: 0; font-size: 14px;">Cliente anónimo o cuenta eliminada</p>
            </div>
        @endif

        {{-- Estadísticas rápidas --}}
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div style="text-align: center; padding: 12px; background: #f8fafc; border-radius: 10px;">
                <div style="font-size: 22px; font-weight: 700; color: #6366f1;">{{ $event->applications->count() }}</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Postulaciones</div>
            </div>
            <div style="text-align: center; padding: 12px; background: #f8fafc; border-radius: 10px;">
                <div style="font-size: 22px; font-weight: 700; color: #10b981;">{{ $event->applications->where('status', 'accepted')->count() }}</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Aceptadas</div>
            </div>
        </div>
    </div>

</div>

{{-- TABLA DE POSTULADOS --}}
<div class="dashboard-box">
    <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="users" style="width: 18px; height: 18px; color: #6366f1;"></i>
        <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #1e293b;">Músicos Postulados</h3>
        <span style="margin-left: auto; background: #f1f5f9; color: #64748b; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">
            {{ $event->applications->count() }} total
        </span>
    </div>
    <div class="table-responsive">
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #f1f5f9; color: #94a3b8; font-size: 12px; text-transform: uppercase;">
                    <th style="padding: 14px 20px;">Músico</th>
                    <th style="padding: 14px 20px;">Géneros</th>
                    <th style="padding: 14px 20px;">Precio Propuesto</th>
                    <th style="padding: 14px 20px;">Mensaje</th>
                    <th style="padding: 14px 20px;">Estado</th>
                    <th style="padding: 14px 20px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($event->applications as $app)
                @php
                    $appStatusMap = [
                        'pending'  => ['label' => 'Pendiente', 'bg' => '#fef9c3', 'color' => '#ca8a04'],
                        'accepted' => ['label' => 'Aceptado',  'bg' => '#dcfce7', 'color' => '#16a34a'],
                        'rejected' => ['label' => 'Rechazado', 'bg' => '#fee2e2', 'color' => '#ef4444'],
                    ];
                    $appStatus = $appStatusMap[$app->status] ?? ['label' => ucfirst($app->status), 'bg' => '#f1f5f9', 'color' => '#64748b'];
                @endphp
                <tr style="border-bottom: 1px solid #f8fafc;">
                    <td style="padding: 16px 20px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px; flex-shrink: 0;">
                                {{ strtoupper(substr($app->musician->stage_name ?? $app->musician->user->name ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 14px; color: #1e293b;">
                                    {{ $app->musician->stage_name ?? $app->musician->user->name ?? 'Músico' }}
                                </div>
                                <div style="font-size: 12px; color: #94a3b8;">{{ $app->musician->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                            @forelse($app->musician->genres as $genre)
                                <span style="background: #ede9fe; color: #7c3aed; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 500;">{{ $genre->name }}</span>
                            @empty
                                <span style="color: #94a3b8; font-size: 12px;">—</span>
                            @endforelse
                        </div>
                    </td>
                    <td style="padding: 16px 20px; font-size: 14px; font-weight: 600; color: #1e293b;">
                        @if($app->proposed_price)
                            ${{ number_format($app->proposed_price, 0, '.', ',') }}
                        @else
                            <span style="color: #94a3b8;">No indicado</span>
                        @endif
                    </td>
                    <td style="padding: 16px 20px; font-size: 13px; color: #475569; max-width: 220px;">
                        @if($app->message)
                            <span title="{{ $app->message }}" style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                {{ $app->message }}
                            </span>
                        @else
                            <span style="color: #94a3b8;">Sin mensaje</span>
                        @endif
                    </td>
                    <td style="padding: 16px 20px;">
                        <span style="background: {{ $appStatus['bg'] }}; color: {{ $appStatus['color'] }}; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                            {{ $appStatus['label'] }}
                        </span>
                    </td>
                    <td style="padding: 16px 20px; text-align: right;">
                        <a href="{{ route('admin.musicians.verify', $app->musician->id) }}" class="secondary-btn small-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 12px;">
                            <i data-lucide="user" style="width: 12px; height: 12px;"></i> Perfil
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 48px 0; color: #94a3b8;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                            <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.2;"></i>
                            <p style="margin: 0; font-size: 14px;">Aún no hay músicos postulados a este evento.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
