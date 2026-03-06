@extends('layouts.dashboard')

@section('dashboard-content')

<header class="dashboard-header">
    <div>
        <h2>Mis Promociones 📈</h2>
        <p class="dashboard-subtitle">Historial de tus compañas publicitarias</p>
    </div>
    <br>
    <a href="{{ route('promotions.create') }}" class="primary-btn">
        <i data-lucide="plus"></i> Nueva campaña
    </a>
</header>

<div class="dashboard-box">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-light); text-align: left;">
                    <th style="padding: 16px; color: var(--text-dim); font-size: 12px; text-transform: uppercase;">Estado</th>
                    <th style="padding: 16px; color: var(--text-dim); font-size: 12px; text-transform: uppercase;">Contenido</th>
                    <th style="padding: 16px; color: var(--text-dim); font-size: 12px; text-transform: uppercase;">Alcance</th>
                    <th style="padding: 16px; color: var(--text-dim); font-size: 12px; text-transform: uppercase;">Vigencia</th>
                    <th style="padding: 16px; color: var(--text-dim); font-size: 12px; text-transform: uppercase;">Inversión</th>
                </tr>
            </thead>
            <tbody>
                {{-- CAMPAÑA ACTIVA --}}
                <tr style="border-bottom: 1px solid var(--border-light);">
                    <td style="padding: 16px;">
                        <span class="status-badge success" style="background: rgba(0, 195, 125, 0.1); color: var(--accent-green);">Activada</span>
                    </td>
                    <td style="padding: 16px;">
                        <strong>Perfil Principal</strong>
                    </td>
                    <td style="padding: 16px;">Regional</td>
                    <td style="padding: 16px; font-size: 14px;">
                        01 Feb - 15 Feb <br> 
                        <span style="color: var(--accent-orange); font-size: 12px;">Quedan 6 días</span>
                    </td>
                    <td style="padding: 16px;">$250 MXN</td>
                </tr>

                {{-- CAMPAÑA PASADA --}}
                <tr style="border-bottom: 1px solid var(--border-light); color: var(--text-dim);">
                    <td style="padding: 16px;">
                        <span style="background: var(--bg-secondary); padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">Finalizada</span>
                    </td>
                    <td style="padding: 16px;">
                        Video "Boda Civ..."
                    </td>
                    <td style="padding: 16px;">Local</td>
                    <td style="padding: 16px; font-size: 14px;">10 Ene - 17 Ene</td>
                    <td style="padding: 16px;">$150 MXN</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="dashboard-box" style="margin-top: 24px;">
    <div class="box-header">
        <h3>✨ Impacto total</h3>
    </div>
    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 0;">
        <div style="text-align: center;">
            <span style="display: block; font-size: 24px; font-weight: 700; color: var(--text-main);">1,240</span>
            <span style="font-size: 12px; color: var(--text-dim);">Visualizaciones extra</span>
        </div>
        <div style="text-align: center;">
             <span style="display: block; font-size: 24px; font-weight: 700; color: var(--accent-blue);">45</span>
             <span style="font-size: 12px; color: var(--text-dim);">Clics en perfil</span>
        </div>
        <div style="text-align: center;">
             <span style="display: block; font-size: 24px; font-weight: 700; color: var(--accent-green);">8</span>
             <span style="font-size: 12px; color: var(--text-dim);">Contactos directos</span>
        </div>
    </div>
</div>

@endsection
