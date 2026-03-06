@extends('layouts.dashboard')

@section('dashboard-content')

    <div class="page-header">
        <div>
            <h2>Disponibilidad 📅</h2>
            <p class="dashboard-subtitle">Bloquea fechas y gestiona tu agenda</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button class="secondary-btn">
                <i data-lucide="settings"></i> Configurar Horario
            </button>
            <button class="primary-btn">
                <i data-lucide="plus"></i> Bloquear Fecha
            </button>
        </div>
    </div>

    <div class="calendar-wrapper dashboard-box" style="padding: 0; overflow: hidden;">
        
        {{-- Calendar Toolbar --}}
        <div class="calendar-toolbar">
            <div class="month-selector">
                <button class="icon-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                </button>
                <h3>Octubre 2026</h3>
                <button class="icon-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
            <div class="view-selector">
                <button class="view-btn active">Mes</button>
                <button class="view-btn">Semana</button>
                <button class="view-btn">Día</button>
            </div>
        </div>

        {{-- Calendar Grid (Mockup Visual) --}}
        <div class="calendar-grid">
            {{-- Header Days --}}
            <div class="cal-day-header">Dom</div>
            <div class="cal-day-header">Lun</div>
            <div class="cal-day-header">Mar</div>
            <div class="cal-day-header">Mié</div>
            <div class="cal-day-header">Jue</div>
            <div class="cal-day-header">Vie</div>
            <div class="cal-day-header">Sáb</div>

            {{-- Week 1 --}}
            <div class="cal-day dim">28</div>
            <div class="cal-day dim">29</div>
            <div class="cal-day dim">30</div>
            <div class="cal-day">1</div>
            <div class="cal-day">2</div>
            <div class="cal-day">3</div>
            <div class="cal-day">4</div>

            {{-- Week 2 --}}
            <div class="cal-day">5</div>
            <div class="cal-day">6</div>
            <div class="cal-day">
                <span class="day-number">7</span>
                <div class="event-chip event-private">Boda - Fam. Rz...</div>
            </div>
            <div class="cal-day">8</div>
            <div class="cal-day">9</div>
            <div class="cal-day">10</div>
            <div class="cal-day">11</div>

            {{-- Week 3 --}}
            <div class="cal-day">12</div>
            <div class="cal-day">13</div>
            <div class="cal-day">14</div>
            <div class="cal-day current-day">15</div>
            <div class="cal-day">16</div>
            <div class="cal-day">17</div>
            <div class="cal-day">
                <span class="day-number">18</span>
                <div class="event-chip event-casting">Casting: Rest...</div>
            </div>

            {{-- Week 4 --}}
            <div class="cal-day">19</div>
            <div class="cal-day">20</div>
            <div class="cal-day">21</div>
            <div class="cal-day">22</div>
            <div class="cal-day">23</div>
            <div class="cal-day">
                <span class="day-number">24</span>
                <div class="event-chip event-blocked">No disponible</div>
            </div>
            <div class="cal-day">25</div>
        </div>
    </div>

    <style>
        .calendar-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-light);
            background: #fafafa;
        }
        .month-selector {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .month-selector h3 { margin: 0; font-size: 18px; }
        .view-selector {
            display: flex;
            background: #e5e7eb;
            border-radius: 8px;
            padding: 4px;
        }
        .view-btn {
            background: none; border: none;
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }
        .view-btn.active { background: white; shadow: var(--shadow-soft); color: var(--accent-blue); }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-left: 1px solid var(--border-light);
        }
        .cal-day-header {
            text-align: center;
            padding: 12px;
            font-weight: 600;
            color: var(--text-dim);
            border-bottom: 1px solid var(--border-light);
            border-right: 1px solid var(--border-light);
            background: #fafafa;
        }
        .cal-day {
            min-height: 120px;
            padding: 12px;
            border-bottom: 1px solid var(--border-light);
            border-right: 1px solid var(--border-light);
            position: relative;
        }
        .cal-day.dim { background: #f9f9f9; color: #d1d5db; }
        .cal-day.current-day { background: rgba(47, 147, 245, 0.05); }
        .cal-day.current-day .day-number {
            background: var(--accent-blue);
            color: white;
            width: 24px; height: 24px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
        }
        
        .event-chip {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-top: 4px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        .event-private { background: rgba(139, 92, 246, 0.15); color: #7c3aed; border-left: 2px solid #7c3aed; }
        .event-casting { background: rgba(0, 195, 125, 0.15); color: var(--accent-green); border-left: 2px solid var(--accent-green); }
        .event-blocked { background: #fee2e2; color: #ef4444; border-left: 2px solid #ef4444; }

        .icon-btn { background: none; border: 1px solid var(--border-light); border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-dim); }
        .icon-btn:hover { background: #f3f4f6; }
    </style>

@endsection