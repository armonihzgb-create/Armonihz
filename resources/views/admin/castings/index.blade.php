@extends('layouts.admin')

@section('admin-content')

@php
    $adminCastings = [
         [
            'id' => 101,
            'title' => 'Boda en Jardín Las Fuentes',
            'organizer' => 'Carlos H.',
            'date_posted' => 'Hoy, 10:30 AM',
            'status' => 'Pendiente',
            'report_count' => 0
        ],
        [
            'id' => 102,
            'title' => 'Fiesta de Fin de Año - Empresa X',
            'organizer' => 'Ana M.',
            'date_posted' => 'Ayer',
            'status' => 'Aprobado',
            'report_count' => 0
        ],
        [
            'id' => 103,
            'title' => 'Busco banda barato',
            'organizer' => 'Usuario Desconocido',
            'date_posted' => 'Hace 2 días',
            'status' => 'Reportado',
            'report_count' => 5
        ]
    ];
@endphp

<header class="dashboard-header">
    <div>
        <h2>Gestión de Eventos 🛡️</h2>
        <p class="dashboard-subtitle">Moderación de castings publicados por clientes</p>
    </div>
</header>

<div class="dashboard-box">
    <table style="width: 100%; text-align: left; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-light); color: var(--text-dim); font-size: 13px; text-transform: uppercase;">
                <th style="padding: 12px;">Evento</th>
                <th style="padding: 12px;">Publicado por</th>
                <th style="padding: 12px;">Fecha</th>
                <th style="padding: 12px;">Estado</th>
                <th style="padding: 12px; text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($adminCastings as $casting)
            <tr style="border-bottom: 1px solid var(--border-light);">
                <td style="padding: 16px;">
                    <strong>{{ $casting['title'] }}</strong>
                    @if($casting['report_count'] > 0)
                        <span style="background: var(--accent-orange); color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 8px;">{{ $casting['report_count'] }} reportes</span>
                    @endif
                </td>
                <td style="padding: 16px;">
                    {{ $casting['organizer'] }}
                </td>
                <td style="padding: 16px; font-size: 14px; color: var(--text-dim);">
                    {{ $casting['date_posted'] }}
                </td>
                <td style="padding: 16px;">
                    @if($casting['status'] === 'Pendiente')
                        <span class="status-badge" style="background: rgba(254, 105, 13, 0.1); color: var(--accent-orange);">Pendiente</span>
                    @elseif($casting['status'] === 'Aprobado')
                        <span class="status-badge success" style="background: rgba(0, 195, 125, 0.1); color: var(--accent-green);">Aprobado</span>
                    @else
                        <span class="status-badge error" style="background: #fee2e2; color: #ef4444;">Reportado</span>
                    @endif
                </td>
                <td style="padding: 16px; text-align: right;">
                    <button class="secondary-btn" style="padding: 6px 12px; font-size: 12px;">Ver</button>
                    @if($casting['status'] === 'Pendiente')
                         <button class="primary-btn" style="padding: 6px 12px; font-size: 12px; background: var(--accent-green) !important;">Aprobar</button>
                    @endif
                    <button class="secondary-btn" style="padding: 6px 12px; font-size: 12px; color: #ef4444; border-color: #ef4444;">Borrar</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
