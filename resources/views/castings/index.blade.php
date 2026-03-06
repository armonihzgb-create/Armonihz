@extends('layouts.dashboard')

@section('dashboard-content')
    <header class="dashboard-header">
        <div>
            <h2>Castings Activos 🎭</h2>
            <p class="dashboard-subtitle">Oportunidades disponibles para tu perfil</p>
            <br>
            
        </div>
        <div class="filter-tabs">
            <button class="filter-tab active">Todos</button>
            <button class="filter-tab">Bodas</button>
            <button class="filter-tab">Eventos Corp.</button>
            <button class="filter-tab">Restaurantes</button>
        </div>
    </header>

    <div class="grid-castings">
        {{-- Card 1 --}}
        <div class="card casting-card">
            <div class="casting-header">
                <span class="badge purple">Boda</span>
                <span class="posting-time">Hace 2 horas</span>
            </div>
            <h3>Banda versátil para Boda Civil</h3>
            <p class="casting-details">
                <i data-lucide="map-pin"></i> Guadalajara, Jal.<br>
                <i data-lucide="calendar"></i> 15 Oct, 2026<br>
                <i data-lucide="dollar-sign"></i> presupuesto: $15,000 - $20,000
            </p>
            <div class="casting-footer">
                <a href="{{ route('castings.show', 1) }}" class="primary-btn sm">Ver detalles</a>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="card casting-card">
            <div class="casting-header">
                <span class="badge blue">Corporativo</span>
                <span class="posting-time">Hace 5 horas</span>
            </div>
            <h3>Saxofonista para Cena de Gala</h3>
            <p class="casting-details">
                <i data-lucide="map-pin"></i> Zapopan, Jal.<br>
                <i data-lucide="calendar"></i> 20 Nov, 2026<br>
                <i data-lucide="dollar-sign"></i> presupuesto: $3,000 - $5,000
            </p>
            <div class="casting-footer">
                <a href="{{ route('castings.show', 2) }}" class="primary-btn sm">Ver detalles</a>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="card casting-card">
            <div class="casting-header">
                <span class="badge orange">Restaurante</span>
                <span class="posting-time">Ayer</span>
            </div>
            <h3>Grupo Norteño para Inauguración</h3>
            <p class="casting-details">
                <i data-lucide="map-pin"></i> Tlaquepaque, Jal.<br>
                <i data-lucide="calendar"></i> 01 Dic, 2026<br>
                <i data-lucide="dollar-sign"></i> presupuesto: A tratar
            </p>
            <div class="casting-footer">
                <a href="{{ route('castings.show', 3) }}" class="primary-btn sm">Ver detalles</a>
            </div>
        </div>
    </div>

    <style>
        .grid-castings {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }
        .casting-card {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.2s;
        }
        .casting-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .casting-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
        }
        .badge.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
        .badge.blue { background: rgba(47, 147, 245, 0.1); color: var(--accent-blue); }
        .badge.orange { background: rgba(254, 105, 13, 0.1); color: var(--accent-orange); }
        
        .posting-time { font-size: 12px; color: var(--text-dim); }
        
        .casting-card h3 { margin: 0; font-size: 18px; }
        
        .casting-details {
            font-size: 14px;
            color: var(--text-dim);
            line-height: 1.6;
        }
        .casting-details i { width: 14px; height: 14px; margin-right: 6px; position: relative; top: 2px; }
        
        .casting-footer { margin-top: auto; padding-top: 16px; border-top: 1px dashed var(--border-light); }
        .primary-btn.sm { padding: 8px 16px; font-size: 13px; width: 100%; text-align: center; display: block; box-sizing: border-box; text-decoration: none; }
    </style>
@endsection
