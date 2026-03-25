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
        <!-- Botones de cabecera removidos -->
    </div>

    {{-- LEGEND --}}
    <div class="av-toolbar" style="justify-content: flex-end;">
        <div class="av-legend">
            <span class="av-legend-item"><span class="av-legend-dot av-dot-available"></span> Disponible (Resaltado)</span>
            <span class="av-legend-item"><span class="av-legend-dot av-dot-busy"></span> Ocupado (No disponible)</span>
            <span class="av-legend-item"><span class="av-legend-dot av-dot-system"></span> Reservas / Castings</span>
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
        .fc .fc-toolbar-title { font-size: 1.3em; font-weight: 800; color: #0f172a; text-transform: capitalize; }
        .fc .fc-toolbar-chunk { display: flex; align-items: center; gap: 8px; }
        .fc .fc-button { padding: 6px 14px !important; font-size: 13px !important; line-height: 1.5 !important; height: auto !important; box-shadow: none !important; }
        .fc .fc-button-primary { 
            background: #f8fafc !important; 
            border: 1.5px solid #e2e8f0 !important; 
            color: #475569 !important; 
            font-weight: 600 !important; 
            text-transform: capitalize; 
            border-radius: 8px !important;
        }
        .fc .fc-button-primary:hover { background: #f1f5f9 !important; border-color: #cbd5e1 !important; color: #0f172a !important; }
        .fc .fc-button-primary:not(:disabled).fc-button-active, .fc .fc-button-primary:not(:disabled):active { 
            background: #6c3fc5 !important; border-color: #6c3fc5 !important; color: #fff !important;
        }
        .fc .fc-button-primary:focus { box-shadow: 0 0 0 3px rgba(108,63,197,.15) !important; outline: none; }
        .fc .fc-prev-button, .fc .fc-next-button { padding: 6px 10px !important; display: flex; align-items: center; justify-content: center; }
        .fc-theme-standard td, .fc-theme-standard th, .fc-theme-standard .fc-scrollgrid { border-color: #f1f5f9; border-width: 1.5px; }
        .fc-col-header-cell { padding: 12px 0; background: #f9fafb; font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
        .fc-col-header-cell-cushion { color: inherit; text-decoration: none; }
        .fc-daygrid-day-number { font-size: 13px; font-weight: 600; color: #475569; padding: 10px !important; text-decoration: none; }
        .fc-daygrid-day-number:hover { text-decoration: underline; color: #6c3fc5; }
        .fc-day-today { background-color: rgba(108,63,197,.02) !important; }
        .fc-day-today .fc-daygrid-day-number { background: #6c3fc5; color: #fff; border-radius: 50%; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; margin: 4px; padding: 0 !important; text-decoration: none; }
        .fc-event { border-radius: 6px; padding: 3px 6px; font-size: 11px; font-weight: 600; cursor: pointer; border: none !important; margin-bottom: 3px; }
        .fc-icon { vertical-align: middle; }
        
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
                // Formatting the selected date/range
                let dateText = '';
                const start = new Date(arg.startStr + 'T12:00:00'); // Force midday to avoid timezone shift on all-day events
                const end = arg.allDay ? new Date(new Date(arg.endStr + 'T12:00:00').getTime() - 86400000) : new Date(arg.endStr);
                
                if (arg.allDay && start.getTime() === end.getTime()) {
                    dateText = start.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                } else if (arg.allDay) {
                    dateText = `Del ${start.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })} al ${end.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })}`;
                } else {
                     dateText = `${start.toLocaleString('es-ES', { dateStyle: 'medium', timeStyle: 'short' })} - ${end.toLocaleString('es-ES', { timeStyle: 'short' })}`;
                }

                const { value: formValues } = await Swal.fire({
                    title: 'Agregar Disponibilidad',
                    html: `
                        <div style="margin-bottom: 20px; font-size: 14px; color: #6c3fc5; background: #faf5ff; padding: 12px; border-radius: 8px; font-weight: 600; border: 1.5px solid #f3e8ff;">
                            <i data-lucide="calendar" style="width:16px; height:16px; vertical-align: -3px; margin-right: 6px;"></i>
                            <span style="text-transform: capitalize;">${dateText}</span>
                        </div>
                        <div style="text-align: left; margin: 0 auto; width: 90%;">
                            <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Estado de esta fecha/hora <span style="color:#ef4444">*</span></label>
                            <select id="swal-type" class="swal2-select" style="display:flex; margin: 0 0 20px 0; width: 100%; font-size: 14px; padding: 12px; border-radius: 8px; border: 1.5px solid #e2e8f0; background-color: #f8fafc; color: #0f172a; outline: none;">
                                <option value="busy">🔴 Estaré Ocupado (Bloquear disponibilidad)</option>
                                <option value="available">🟢 Estaré Disponible (Resaltar como fecha ideal)</option>
                            </select>
                            
                            <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">Nota / Título (Opcional)</label>
                            <input id="swal-title" class="swal2-input" placeholder="Ej: Disponible para bodas" style="margin: 0; width: 100%; font-size: 14px; padding: 12px 14px; height: auto; border-radius: 8px; border: 1.5px solid #e2e8f0; background-color: #f8fafc; outline: none; box-sizing: border-box;">
                        </div>
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonColor: '#6c3fc5',
                    cancelButtonColor: '#f1f5f9',
                    confirmButtonText: 'Guardar horario',
                    cancelButtonText: '<span style="color:#475569; font-weight:600;">Cancelar</span>',
                    didOpen: () => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    },
                    preConfirm: () => {
                        return {
                            title: document.getElementById('swal-title').value || (document.getElementById('swal-type').value === 'available' ? 'Disponible especial' : '🔴 Ocupado'),
                            type: document.getElementById('swal-type').value
                        }
                    }
                });

                if (formValues) {
                    // FullCalendar all-day endStr is EXCLUSIVE (next day).
                    // We subtract 1 day so the backend stores the correct inclusive end.
                    let endStr = arg.endStr;
                    if (arg.allDay) {
                        const endDate = new Date(arg.endStr + 'T12:00:00');
                        endDate.setDate(endDate.getDate() - 1);
                        endStr = endDate.toISOString().split('T')[0]; // YYYY-MM-DD
                    }

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
                            end: endStr,
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
                    fetch(`{{ url('/availability') }}/${arg.event.extendedProps.real_id}`, {
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

                fetch(`{{ url('/availability') }}/${arg.event.extendedProps.real_id}`, {
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