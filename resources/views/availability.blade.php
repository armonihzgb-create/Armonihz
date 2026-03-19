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

    <div class="av-calendar-wrap" style="padding: 20px;">
        <div id="calendar"></div>
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
        
        /* ── Toolbar ─────────────────────────────── */
        .av-toolbar {
            display: flex; align-items: center; justify-content: flex-end;
            gap: 16px; flex-wrap: wrap; background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 14px; padding: 14px 20px; margin-bottom: 16px;
        }
        .av-legend { display: flex; align-items: center; gap: 14px; }
        .av-legend-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #64748b; }
        .av-legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .av-dot-available { background: #22c55e; }
        .av-dot-busy      { background: #dc2626; }
        .av-dot-system    { background: #9333ea; }

        /* FullCalendar Customization */
        .fc { font-family: inherit; }
        .fc .fc-toolbar-title { font-size: 1.25em; font-weight: 800; color: #0f172a; }
        .fc .fc-button-primary { background: #f8fafc; border: 1.5px solid #e2e8f0; color: #475569; font-weight: 600; text-transform: capitalize; border-radius: 8px;}
        .fc .fc-button-primary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #0f172a; }
        .fc .fc-button-primary:not(:disabled).fc-button-active, .fc .fc-button-primary:not(:disabled):active { background: #6c3fc5; border-color: #6c3fc5; color: #fff;}
        .fc-theme-standard td, .fc-theme-standard th, .fc-theme-standard .fc-scrollgrid { border-color: #f1f5f9; }
        .fc-col-header-cell { padding: 12px 0; background: #f9fafb; font-size: 12px; color: #94a3b8; text-transform: uppercase; }
        .fc-daygrid-day-number { font-size: 13px; font-weight: 600; color: #475569; padding: 8px !important; }
        .fc-day-today { background-color: rgba(108,63,197,.02) !important; }
        .fc-event { border-radius: 6px; padding: 2px 4px; font-size: 11px; font-weight: 600; cursor: pointer; border: none !important; margin-bottom: 2px; }
        
        #calendar { min-height: 600px; }
        
        @media (max-width: 768px) {
            .av-header, .av-toolbar { flex-direction: column; align-items: flex-start; }
            .fc .fc-toolbar { flex-direction: column; gap: 10px; }
        }
    </style>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día'
            },
            editable: true,       // Permite arrastrar
            selectable: true,     // Permite seleccionar días/horas
            selectMirror: true,
            dayMaxEvents: true,
            events: '{{ route("availability.events") }}',

            // Click on an empty day/time to add an event
            select: async function(arg) {
                const { value: formValues } = await Swal.fire({
                    title: 'Agregar Disponibilidad',
                    html: `
                        <select id="swal-type" class="swal2-select" style="display:flex; margin: 10px auto; width: 80%;">
                            <option value="available">Disponible</option>
                            <option value="busy">Ocupado / Bloqueado</option>
                        </select>
                        <input id="swal-title" class="swal2-input" placeholder="Título (ej: Disponible para eventos)">
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Guardar',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        return {
                            title: document.getElementById('swal-title').value || (document.getElementById('swal-type').value === 'available' ? 'Disponible' : 'Ocupado'),
                            type: document.getElementById('swal-type').value
                        }
                    }
                });

                if (formValues) {
                    // Send to backend via AJAX
                    fetch('{{ route("availability.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: formValues.title,
                            start: arg.startStr,
                            end: arg.endStr,
                            type: formValues.type
                        })
                    }).then(res => res.json()).then(data => {
                        if(data.success) {
                            calendar.refetchEvents();
                        }
                    });
                }
                calendar.unselect();
            },

            // Click on an event
            eventClick: async function(arg) {
                const isManual = arg.event.extendedProps.source === 'manual';
                
                if(!isManual) {
                    Swal.fire({
                        title: arg.event.title,
                        text: arg.event.extendedProps.description || 'Generado automáticamente por el sistema.',
                        icon: 'info'
                    });
                    return;
                }

                // If manual, prompt to delete
                const result = await Swal.fire({
                    title: '¿Eliminar bloque?',
                    text: `¿Deseas eliminar "${arg.event.title}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    fetch(`{{ url('/profile/availability') }}/${arg.event.extendedProps.real_id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => res.json()).then(data => {
                        if(data.success) {
                            arg.event.remove();
                        }
                    });
                }
            },

            // Drag / Resize event
            eventChange: function(arg) {
                const isManual = arg.event.extendedProps.source === 'manual';
                if(!isManual) {
                    arg.revert();
                    Swal.fire('No permitido', 'Solo puedes mover los bloques de disponibilidad que creaste manualmente.', 'error');
                    return;
                }

                fetch(`{{ url('/profile/availability') }}/${arg.event.extendedProps.real_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        start: arg.event.startStr,
                        end: arg.event.endStr || arg.event.startStr
                    })
                });
            }
        });

        calendar.render();
    });
    </script>
@endsection