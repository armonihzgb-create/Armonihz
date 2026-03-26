@extends('layouts.admin')

@section('admin-content')

@php
    $promotions = [
        [
            'user' => 'Mariachi Sol de México',
            'plan' => 'Regional (15 días)',
            'start_date' => '01 Feb',
            'end_date' => '15 Feb',
            'amount' => '$250',
            'status' => 'Activo'
        ],
        [
            'user' => 'DJ Cosmic',
            'plan' => 'Local (7 días)',
            'start_date' => '05 Feb',
            'end_date' => '12 Feb',
            'amount' => '$150',
            'status' => 'Activo'
        ],
         [
            'user' => 'Grupo Norteño "Los Jefes"',
            'plan' => 'Estatal (30 días)',
            'start_date' => '10 Ene',
            'end_date' => '09 Feb',
            'amount' => '$450',
            'status' => 'Finalizando'
        ]
    ];
@endphp

<header class="dashboard-header">
    <div>
        <h2>Publicidad Interna 💲</h2>
        <p class="dashboard-subtitle">Monitoreo de ingresos por promociones destacadas</p>
    </div>
    <div style="text-align: right;">
        <span style="display: block; font-size: 12px; color: var(--text-dim);">Ingresos del mes</span>
        <span style="font-size: 24px; font-weight: 700; color: var(--accent-green);">$3,450 MXN</span>
    </div>
</header>

<div class="dashboard-box">
    <div class="table-responsive">
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-light); color: var(--text-dim); font-size: 13px; text-transform: uppercase;">
                    <th style="padding: 12px;">Usuario</th>
                    <th style="padding: 12px;">Plan</th>
                    <th style="padding: 12px;">Vigencia</th>
                    <th style="padding: 12px;">Monto</th>
                    <th style="padding: 12px;">Estado</th>
                    <th style="padding: 12px; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($promotions as $promo)
                <tr style="border-bottom: 1px solid var(--border-light);">
                    <td style="padding: 16px;">
                        <strong>{{ $promo['user'] }}</strong>
                    </td>
                    <td style="padding: 16px; font-size: 14px;">
                        {{ $promo['plan'] }}
                    </td>
                    <td style="padding: 16px; font-size: 14px; color: var(--text-dim);">
                        {{ $promo['start_date'] }} - {{ $promo['end_date'] }}
                    </td>
                    <td style="padding: 16px; font-weight: 600;">
                        {{ $promo['amount'] }}
                    </td>
                    <td style="padding: 16px;">
                        <span class="status-badge success" style="background: rgba(0, 195, 125, 0.1); color: var(--accent-green);">
                            {{ $promo['status'] }}
                        </span>
                    </td>
                    <td style="padding: 16px; text-align: right;">
                        <button class="secondary-btn" style="padding: 6px 12px; font-size: 12px;">Detener</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
