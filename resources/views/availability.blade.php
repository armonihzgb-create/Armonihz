@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- PAGE HEADER --}}
    <div class="av-header">
        <div>
            <div class="av-eyebrow">
                <i data-lucide="calendar" style="width:14px;height:14px;color:#6c3fc5;"></i>
                DISPONIBILIDAD
            </div>
            <h1 class="av-title">Mi Agenda</h1>
            <p class="av-subtitle">Bloquea fechas y mantén tu disponibilidad actualizada para los clientes.</p>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;">
            <button class="av-secondary-btn" onclick="alert('Próximamente')">
                <i data-lucide="settings" style="width:15px;height:15px;"></i>
                Configurar horario
            </button>
            <button class="av-primary-btn" onclick="alert('Próximamente')">
                <i data-lucide="plus" style="width:15px;height:15px;"></i>
                Bloquear fecha
            </button>
        </div>
    </div>

    {{-- LEGEND + MONTH CONTROLS --}}
    <div class="av-toolbar">
        {{-- Month navigation --}}
        <div class="av-month-nav">
            <button class="av-nav-btn">
                <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
            </button>
            <h2 class="av-month-title">Octubre 2026</h2>
            <button class="av-nav-btn">
                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
            </button>
        </div>

        {{-- View tabs --}}
        <div class="av-view-tabs">
            <button class="av-view-tab active">Mes</button>
            <button class="av-view-tab">Semana</button>
            <button class="av-view-tab">Día</button>
        </div>

        {{-- Legend --}}
        <div class="av-legend">
            <span class="av-legend-item">
                <span class="av-legend-dot av-dot-event"></span> Evento
            </span>
            <span class="av-legend-item">
                <span class="av-legend-dot av-dot-casting"></span> Casting
            </span>
            <span class="av-legend-item">
                <span class="av-legend-dot av-dot-blocked"></span> Bloqueado
            </span>
        </div>
    </div>

    {{-- CALENDAR --}}
    <div class="av-calendar-wrap">

        {{-- Day headers --}}
        <div class="av-day-headers">
            @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $d)
                <div class="av-day-header">{{ $d }}</div>
            @endforeach
        </div>

        {{-- Calendar grid --}}
        <div class="av-grid">

            {{-- Week 1 — previous month overflow --}}
            <div class="av-cell av-cell--dim"><span class="av-day-num">28</span></div>
            <div class="av-cell av-cell--dim"><span class="av-day-num">29</span></div>
            <div class="av-cell av-cell--dim"><span class="av-day-num">30</span></div>
            <div class="av-cell"><span class="av-day-num">1</span></div>
            <div class="av-cell"><span class="av-day-num">2</span></div>
            <div class="av-cell"><span class="av-day-num">3</span></div>
            <div class="av-cell"><span class="av-day-num">4</span></div>

            {{-- Week 2 --}}
            <div class="av-cell"><span class="av-day-num">5</span></div>
            <div class="av-cell"><span class="av-day-num">6</span></div>
            <div class="av-cell">
                <span class="av-day-num">7</span>
                <div class="av-chip av-chip--event">🎵 Boda - Fam. Rz...</div>
            </div>
            <div class="av-cell"><span class="av-day-num">8</span></div>
            <div class="av-cell"><span class="av-day-num">9</span></div>
            <div class="av-cell"><span class="av-day-num">10</span></div>
            <div class="av-cell"><span class="av-day-num">11</span></div>

            {{-- Week 3 --}}
            <div class="av-cell"><span class="av-day-num">12</span></div>
            <div class="av-cell"><span class="av-day-num">13</span></div>
            <div class="av-cell"><span class="av-day-num">14</span></div>
            <div class="av-cell av-cell--today">
                <span class="av-day-num av-today-num">15</span>
            </div>
            <div class="av-cell"><span class="av-day-num">16</span></div>
            <div class="av-cell"><span class="av-day-num">17</span></div>
            <div class="av-cell">
                <span class="av-day-num">18</span>
                <div class="av-chip av-chip--casting">📋 Casting: Rest...</div>
            </div>

            {{-- Week 4 --}}
            <div class="av-cell"><span class="av-day-num">19</span></div>
            <div class="av-cell"><span class="av-day-num">20</span></div>
            <div class="av-cell"><span class="av-day-num">21</span></div>
            <div class="av-cell"><span class="av-day-num">22</span></div>
            <div class="av-cell"><span class="av-day-num">23</span></div>
            <div class="av-cell av-cell--blocked">
                <span class="av-day-num">24</span>
                <div class="av-chip av-chip--blocked">🚫 No disponible</div>
            </div>
            <div class="av-cell"><span class="av-day-num">25</span></div>

            {{-- Week 5 --}}
            <div class="av-cell"><span class="av-day-num">26</span></div>
            <div class="av-cell"><span class="av-day-num">27</span></div>
            <div class="av-cell"><span class="av-day-num">28</span></div>
            <div class="av-cell"><span class="av-day-num">29</span></div>
            <div class="av-cell"><span class="av-day-num">30</span></div>
            <div class="av-cell"><span class="av-day-num">31</span></div>
            <div class="av-cell av-cell--dim"><span class="av-day-num">1</span></div>

        </div>
    </div>

    {{-- UPCOMING EVENTS SIDEBAR ROW --}}
    <div class="av-upcoming">
        <div class="av-upcoming-header">
            <h2 class="av-upcoming-title">Próximos eventos</h2>
            <span class="av-upcoming-count">2 confirmados</span>
        </div>

        <div class="av-upcoming-list">
            <div class="av-event-row av-event-row--event">
                <div class="av-event-date-box">
                    <span class="av-event-month">OCT</span>
                    <span class="av-event-day">7</span>
                </div>
                <div class="av-event-info">
                    <span class="av-event-name">Boda - Fam. Rodríguez</span>
                    <div class="av-event-meta">
                        <span><i data-lucide="map-pin" style="width:12px;height:12px;"></i> Puebla, Pue.</span>
                        <span><i data-lucide="clock" style="width:12px;height:12px;"></i> 18:00 – 22:00</span>
                    </div>
                </div>
                <span class="av-event-badge av-badge--event">Evento</span>
            </div>

            <div class="av-event-row av-event-row--casting">
                <div class="av-event-date-box av-date-box--casting">
                    <span class="av-event-month">OCT</span>
                    <span class="av-event-day">18</span>
                </div>
                <div class="av-event-info">
                    <span class="av-event-name">Casting: Restaurante El Cielo</span>
                    <div class="av-event-meta">
                        <span><i data-lucide="map-pin" style="width:12px;height:12px;"></i> Tehuacán, Pue.</span>
                        <span><i data-lucide="clock" style="width:12px;height:12px;"></i> 20:00 – 23:00</span>
                    </div>
                </div>
                <span class="av-event-badge av-badge--casting">Casting</span>
            </div>

            <div class="av-event-row av-event-row--blocked">
                <div class="av-event-date-box av-date-box--blocked">
                    <span class="av-event-month">OCT</span>
                    <span class="av-event-day">24</span>
                </div>
                <div class="av-event-info">
                    <span class="av-event-name">No disponible</span>
                    <div class="av-event-meta">
                        <span><i data-lucide="info" style="width:12px;height:12px;"></i> Fecha bloqueada manualmente</span>
                    </div>
                </div>
                <span class="av-event-badge av-badge--blocked">Bloqueado</span>
            </div>
        </div>
    </div>

    <style>
        /* ── Header ─────────────────────────────── */
        .av-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 22px; padding-bottom: 22px;
            border-bottom: 1px solid #f1f5f9;
        }
        .av-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .av-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .av-subtitle { font-size: 14px; color: #64748b; margin: 0; }
        .av-primary-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px; border-radius: 8px;
            background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; font-size: 13px; font-weight: 700; border: none;
            cursor: pointer; white-space: nowrap; transition: opacity .2s;
            box-shadow: 0 4px 14px rgba(108,63,197,.25);
        }
        .av-primary-btn:hover { opacity: .9; }
        .av-secondary-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 16px; border-radius: 8px; border: 1.5px solid #e2e8f0;
            background: #f8fafc; color: #475569; font-size: 13px; font-weight: 600;
            cursor: pointer; white-space: nowrap; transition: all .2s;
        }
        .av-secondary-btn:hover { border-color: #6c3fc5; color: #6c3fc5; }

        /* ── Toolbar ─────────────────────────────── */
        .av-toolbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; flex-wrap: wrap;
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 14px; padding: 14px 20px; margin-bottom: 16px;
        }
        .av-month-nav { display: flex; align-items: center; gap: 12px; }
        .av-nav-btn {
            width: 32px; height: 32px; border-radius: 8px; border: 1.5px solid #e2e8f0;
            background: #f8fafc; color: #475569; display: flex; align-items: center;
            justify-content: center; cursor: pointer; transition: all .2s;
        }
        .av-nav-btn:hover { border-color: #6c3fc5; color: #6c3fc5; }
        .av-month-title { font-size: 17px; font-weight: 800; color: #0f172a; margin: 0; min-width: 140px; text-align: center; }
        .av-view-tabs { display: flex; background: #f1f5f9; border-radius: 8px; padding: 3px; gap: 2px; }
        .av-view-tab {
            padding: 6px 14px; border: none; border-radius: 6px;
            font-size: 13px; font-weight: 500; color: #64748b;
            background: transparent; cursor: pointer; transition: all .2s;
        }
        .av-view-tab.active { background: #fff; color: #6c3fc5; font-weight: 700; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .av-legend { display: flex; align-items: center; gap: 14px; }
        .av-legend-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #64748b; }
        .av-legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .av-dot-event   { background: #7c3aed; }
        .av-dot-casting { background: #16a34a; }
        .av-dot-blocked { background: #ef4444; }

        /* ── Calendar ────────────────────────────── */
        .av-calendar-wrap {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 16px; overflow: hidden; margin-bottom: 20px;
        }
        .av-day-headers {
            display: grid; grid-template-columns: repeat(7,1fr);
            border-bottom: 1.5px solid #f1f5f9;
        }
        .av-day-header {
            text-align: center; padding: 12px 8px;
            font-size: 12px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .04em;
            background: #f9fafb;
        }
        .av-grid { display: grid; grid-template-columns: repeat(7,1fr); }
        .av-cell {
            min-height: 110px; padding: 10px;
            border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;
            position: relative; transition: background .15s; cursor: pointer;
        }
        .av-cell:hover { background: #faf5ff; }
        .av-cell--dim { background: #f9fafb; }
        .av-cell--dim .av-day-num { color: #d1d5db; }
        .av-cell--today { background: rgba(108,63,197,.04); }
        .av-cell--blocked { background: rgba(239,68,68,.03); }
        .av-day-num { font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 5px; }
        .av-today-num {
            display: inline-flex; align-items: center; justify-content: center;
            width: 26px; height: 26px; border-radius: 50%;
            background: #6c3fc5; color: #fff; font-size: 13px; font-weight: 700;
        }
        .av-chip {
            font-size: 11px; font-weight: 600; padding: 3px 7px;
            border-radius: 5px; margin-top: 3px; cursor: pointer;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            border-left: 2.5px solid transparent;
        }
        .av-chip--event   { background: rgba(124,58,237,.1); color: #7c3aed; border-color: #7c3aed; }
        .av-chip--casting { background: rgba(22,163,74,.1);  color: #16a34a; border-color: #16a34a; }
        .av-chip--blocked { background: #fee2e2; color: #ef4444; border-color: #ef4444; }

        /* ── Upcoming events ─────────────────────── */
        .av-upcoming {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 16px; padding: 22px 24px;
        }
        .av-upcoming-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .av-upcoming-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
        .av-upcoming-count { font-size: 13px; color: #94a3b8; }
        .av-upcoming-list { display: flex; flex-direction: column; gap: 10px; }

        .av-event-row {
            display: flex; align-items: center; gap: 16px;
            padding: 14px 16px; border-radius: 12px; border: 1.5px solid #f1f5f9;
            transition: box-shadow .2s;
        }
        .av-event-row:hover { box-shadow: 0 3px 12px rgba(0,0,0,.05); }
        .av-event-row--event   { border-color: rgba(124,58,237,.15); background: rgba(124,58,237,.02); }
        .av-event-row--casting { border-color: rgba(22,163,74,.15); background: rgba(22,163,74,.02); }
        .av-event-row--blocked { border-color: rgba(239,68,68,.15); background: #fff8f8; }

        .av-event-date-box {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            width: 48px; height: 52px; border-radius: 10px; flex-shrink: 0;
            background: rgba(124,58,237,.08); border: 1px solid rgba(124,58,237,.15);
        }
        .av-date-box--casting { background: rgba(22,163,74,.08); border-color: rgba(22,163,74,.15); }
        .av-date-box--blocked { background: #fee2e2; border-color: rgba(239,68,68,.2); }
        .av-event-month { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; }
        .av-event-day { font-size: 20px; font-weight: 900; color: #0f172a; line-height: 1; }

        .av-event-info { flex: 1; min-width: 0; }
        .av-event-name { display: block; font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .av-event-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: #64748b; }
        .av-event-meta span { display: flex; align-items: center; gap: 4px; }

        .av-event-badge { font-size: 11px; font-weight: 600; padding: 4px 11px; border-radius: 999px; flex-shrink: 0; }
        .av-badge--event   { background: rgba(124,58,237,.1); color: #7c3aed; }
        .av-badge--casting { background: rgba(22,163,74,.1);  color: #16a34a; }
        .av-badge--blocked { background: #fee2e2; color: #ef4444; }

        @media (max-width: 768px) {
            .av-header, .av-toolbar { flex-direction: column; align-items: flex-start; }
            .av-cell { min-height: 70px; }
            .av-chip { display: none; }
        }
    </style>

@endsection