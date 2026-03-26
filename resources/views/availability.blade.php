@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- ── FOUC FIX + STYLES ───────────────────────────────────────── --}}
    <style>
        /* ── Header ── */
        .av-header { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; margin-bottom:22px; padding-bottom:22px; border-bottom:1px solid #f1f5f9; }
        .av-eyebrow { display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700; letter-spacing:.08em; color:#6c3fc5; text-transform:uppercase; margin-bottom:6px; }
        .av-title { font-size:24px; font-weight:800; color:#0f172a; margin:0 0 4px; }
        .av-subtitle { font-size:14px; color:#64748b; margin:0; }

        /* ── Legend ── */
        .av-toolbar { display:flex; align-items:center; justify-content:flex-end; gap:16px; flex-wrap:wrap; background:#fff; border:1.5px solid #e8edf3; border-radius:14px; padding:14px 20px; margin-bottom:16px; }
        .av-legend { display:flex; align-items:center; gap:14px; }
        .av-legend-item { display:flex; align-items:center; gap:5px; font-size:12px; color:#64748b; }
        .av-legend-dot { width:10px; height:10px; border-radius:50%; display:inline-block; }
        .av-dot-busy   { background:#dc2626; }
        .av-dot-system { background:#9333ea; }

        /* ── FullCalendar customization ── */
        .fc { font-family: inherit; }
        .fc .fc-toolbar-title { font-size:1.3em; font-weight:800; color:#0f172a; text-transform:capitalize; }
        .fc .fc-toolbar-chunk { display:flex; align-items:center; gap:8px; }
        .fc .fc-button { padding:6px 14px !important; font-size:13px !important; line-height:1.5 !important; height:auto !important; box-shadow:none !important; }
        .fc .fc-button-primary { background:#f8fafc !important; border:1.5px solid #e2e8f0 !important; color:#475569 !important; font-weight:600 !important; text-transform:capitalize; border-radius:8px !important; }
        .fc .fc-button-primary:hover { background:#f1f5f9 !important; border-color:#cbd5e1 !important; color:#0f172a !important; }
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active { background:#6c3fc5 !important; border-color:#6c3fc5 !important; color:#fff !important; }
        .fc .fc-button-primary:focus { box-shadow:0 0 0 3px rgba(108,63,197,.15) !important; outline:none; }
        .fc .fc-prev-button, .fc .fc-next-button { padding:6px 10px !important; display:flex; align-items:center; justify-content:center; }
        .fc-theme-standard td, .fc-theme-standard th, .fc-theme-standard .fc-scrollgrid { border-color:#f1f5f9; border-width:1.5px; }
        .fc-col-header-cell { padding:12px 0; background:#f9fafb; font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; }
        .fc-col-header-cell-cushion { color:inherit; text-decoration:none; }
        .fc-daygrid-day-number { font-size:13px; font-weight:600; color:#475569; padding:10px !important; text-decoration:none; }
        .fc-daygrid-day-number:hover { color:#6c3fc5; }
        .fc-day-today { background-color:rgba(108,63,197,.03) !important; }
        .fc-day-today .fc-daygrid-day-number { background:#6c3fc5; color:#fff; border-radius:50%; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; margin:4px; padding:0 !important; }
        .fc-event { border-radius:6px; padding:3px 8px; font-size:11px; font-weight:600; cursor:pointer; border:none !important; margin-bottom:3px; }
        .fc-daygrid-day:hover:not(.fc-day-past) { background:rgba(108,63,197,.03) !important; cursor:pointer; }
        .fc-day-past { background-color: #f8fafc !important; opacity: 0.6; cursor: not-allowed !important; }
        .fc-day-past .fc-daygrid-day-number { color: #94a3b8 !important; }
        #calendar { min-height:600px; }

        /* ── Custom Modal ── */
        .av-modal-overlay { display:none; position:fixed; inset:0; z-index:1000; background:rgba(15,23,42,.55); backdrop-filter:blur(4px); align-items:center; justify-content:center; }
        .av-modal-overlay.open { display:flex; }
        .av-modal { background:#fff; border-radius:20px; width:100%; max-width:460px; box-shadow:0 24px 60px rgba(0,0,0,.18); animation:avSlideUp .24s ease; overflow:hidden; }
        @keyframes avSlideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .av-modal-head { padding:20px 24px 16px; border-bottom:1px solid #f1f5f9; }
        .av-modal-date { font-size:13px; font-weight:700; color:#6c3fc5; background:#faf5ff; border:1px solid #ede9fe; border-radius:8px; padding:8px 14px; margin-bottom:0; display:inline-flex; align-items:center; gap:7px; }
        .av-modal-title { font-size:17px; font-weight:800; color:#0f172a; margin:10px 0 0; }
        .av-modal-body { padding:20px 24px; }

        /* Mode tabs */
        .av-mode-tabs { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:20px; }
        .av-mode-tab { padding:12px 14px; border-radius:12px; border:1.5px solid #e2e8f0; background:#f8fafc; text-align:center; cursor:pointer; transition:all .18s; }
        .av-mode-tab.active { border-color:#6c3fc5; background:#faf5ff; }
        .av-mode-tab .av-tab-icon { font-size:22px; display:block; margin-bottom:4px; }
        .av-mode-tab .av-tab-label { font-size:12px; font-weight:700; color:#374151; display:block; }
        .av-mode-tab .av-tab-sub { font-size:10px; color:#94a3b8; display:block; margin-top:1px; }
        .av-mode-tab.active .av-tab-label { color:#6c3fc5; }

        /* Time form */
        .av-time-form { display:none; }
        .av-time-form.visible { display:block; animation:avSlideUp .18s ease; }
        .av-form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px; }
        .av-form-group { display:flex; flex-direction:column; gap:5px; }
        .av-form-group label { font-size:12px; font-weight:700; color:#475569; }
        .av-form-group input[type="time"] { padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; color:#0f172a; background:#fafafa; outline:none; transition:border .2s; }
        .av-form-group input[type="time"]:focus { border-color:#6c3fc5; background:#fff; }

        /* Optional note */
        .av-note-input { width:100%; padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; color:#0f172a; background:#fafafa; outline:none; transition:border .2s; box-sizing:border-box; }
        .av-note-input:focus { border-color:#6c3fc5; background:#fff; }

        /* Footer buttons */
        .av-modal-foot { display:flex; gap:10px; padding:16px 24px 20px; border-top:1px solid #f1f5f9; }
        .av-btn-cancel { flex:0 0 auto; padding:11px 20px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafc; color:#475569; font-size:14px; font-weight:600; cursor:pointer; transition:all .18s; }
        .av-btn-cancel:hover { background:#e2e8f0; }
        .av-btn-save { flex:1; padding:11px 20px; border-radius:10px; border:none; background:#6c3fc5; color:#fff; font-size:14px; font-weight:700; cursor:pointer; transition:all .18s; }
        .av-btn-save:hover { background:#5b32a8; }
        .av-btn-save:disabled { background:#94a3b8; cursor:not-allowed; }

        /* Delete modal */
        .av-delete-modal { background:#fff; border-radius:16px; width:100%; max-width:400px; box-shadow:0 24px 60px rgba(0,0,0,.18); animation:avSlideUp .24s ease; padding:24px; text-align:center; }
        .av-delete-icon { width:52px; height:52px; background:#fef2f2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
        .av-delete-title { font-size:17px; font-weight:800; color:#0f172a; margin:0 0 10px; }
        .av-delete-info { background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:10px; padding:12px 14px; text-align:left; font-size:13px; line-height:1.8; color:#374151; margin-bottom:16px; }
        .av-delete-info strong { color:#0f172a; }
        .av-delete-warn { font-size:12px; color:#94a3b8; margin-bottom:20px; }
        .av-delete-foot { display:flex; gap:10px; }

        @media(max-width:768px) {
            .av-header, .av-toolbar { flex-direction:column; align-items:flex-start; }
            .fc .fc-toolbar { flex-direction:column; gap:10px; }
            .av-modal { max-width:95vw; margin:0 12px; }
        }
    </style>
    <style>
        /* Responsive adjustments for FullCalendar Toolbar */
        @media (max-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 12px;
            }
            .fc .fc-toolbar-title {
                font-size: 1.2em !important;
            }
            .legend-box {
                flex-wrap: wrap;
                justify-content: center;
            }
            .calendar-layout {
                grid-template-columns: 1fr;
            }
            .calendar-container {
                padding: 16px; /* Less padding on mobile */
            }
        }
    </style>

    {{-- PAGE HEADER --}}
    <div class="av-header">
        <div>
            <div class="av-eyebrow">
                <i data-lucide="calendar" style="width:14px;height:14px;color:#6c3fc5;"></i>
                DISPONIBILIDAD
            </div>
            <h1 class="av-title">Mi Agenda</h1>
            <p class="av-subtitle">Toca cualquier día para bloquear tiempo. Los clientes verán tu disponibilidad en la app.</p>
        </div>
    </div>

    {{-- LEGEND --}}
    <div class="av-toolbar">
        <div class="av-legend">
            <span class="av-legend-item"><span class="av-legend-dot av-dot-busy"></span> Día bloqueado</span>
            <span class="av-legend-item"><span class="av-legend-dot" style="background:#ef4444;opacity:.6;"></span> Horario específico</span>
            <span class="av-legend-item"><span class="av-legend-dot av-dot-system"></span> Reservas / Castings</span>
        </div>
    </div>

    {{-- CALENDAR --}}
    <div class="av-calendar-wrap" style="padding: 20px;">
        <div id="calendar"></div>
    </div>

    {{-- ══ MODAL: AGREGAR BLOQUE ══ --}}
    <div class="av-modal-overlay" id="addModal">
        <div class="av-modal">
            <div class="av-modal-head">
                <span class="av-modal-date" id="modalDateLabel">
                    <i class="fa-regular fa-calendar" style="color:#6c3fc5;font-size:13px;"></i>
                    <span id="modalDateText"></span>
                </span>
                <h3 class="av-modal-title">Marcar tiempo ocupado</h3>
            </div>
            <div class="av-modal-body">

                {{-- Tabs --}}
                <div class="av-mode-tabs">
                    <div class="av-mode-tab active" id="tabFullDay" onclick="selectMode('full')">
                        <span class="av-tab-icon">📅</span>
                        <span class="av-tab-label">Día completo</span>
                        <span class="av-tab-sub">Bloquea todo el día</span>
                    </div>
                    <div class="av-mode-tab" id="tabTimeSlot" onclick="selectMode('time')">
                        <span class="av-tab-icon">⏰</span>
                        <span class="av-tab-label">Horario específico</span>
                        <span class="av-tab-sub">Define hora inicio / fin</span>
                    </div>
                </div>

                {{-- Time slot form (only shown when mode=time) --}}
                <div class="av-time-form" id="timeForm">
                    <div class="av-form-row">
                        <div class="av-form-group">
                            <label>Hora inicio</label>
                            <input type="time" id="timeStart" value="09:00">
                        </div>
                        <div class="av-form-group">
                            <label>Hora fin</label>
                            <input type="time" id="timeEnd" value="18:00">
                        </div>
                    </div>
                </div>

                {{-- Optional note --}}
                <div style="margin-top:4px;">
                    <label style="font-size:12px;font-weight:700;color:#475569;display:block;margin-bottom:6px;">Nota (opcional)</label>
                    <input type="text" id="noteInput" class="av-note-input" placeholder="Ej: Boda en Puebla, Ensayo, etc.">
                </div>

            </div>
            <div class="av-modal-foot">
                <button class="av-btn-cancel" onclick="closeAddModal()">Cancelar</button>
                <button class="av-btn-save" id="btnSave" onclick="saveBlock()">
                    <i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i> Guardar bloque
                </button>
            </div>
        </div>
    </div>

    {{-- ══ MODAL: ELIMINAR BLOQUE ══ --}}
    <div class="av-modal-overlay" id="deleteModal">
        <div class="av-delete-modal">
            <div class="av-delete-icon">
                <i class="fa-solid fa-triangle-exclamation" style="color:#dc2626;font-size:20px;"></i>
            </div>
            <h3 class="av-delete-title">¿Eliminar este bloque?</h3>
            <div class="av-delete-info" id="deleteInfo"></div>
            <p class="av-delete-warn">Esta acción no se puede deshacer.</p>
            <div class="av-delete-foot">
                <button class="av-btn-cancel" style="flex:1;" onclick="closeDeleteModal()">Cancelar</button>
                <button class="av-btn-save" style="background:#dc2626;flex:1;" id="btnDelete" onclick="confirmDelete()">
                    <i class="fa-solid fa-trash" style="margin-right:6px;"></i> Eliminar
                </button>
            </div>
        </div>
    </div>

    {{-- FullCalendar + SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // ── State ──────────────────────────────────────────────────
    let currentMode    = 'full';   // 'full' | 'time'
    let selectedDate   = null;     // YYYY-MM-DD string
    let deleteEventId  = null;
    let deleteEventObj = null;
    let calendar;

    // ── Helpers ────────────────────────────────────────────────
    function fmtDate(str) {
        const [y, m, d] = str.split('-');
        const date = new Date(y, m - 1, d);
        return date.toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    // ── Mode selector ──────────────────────────────────────────
    function selectMode(mode) {
        currentMode = mode;
        document.getElementById('tabFullDay').classList.toggle('active', mode === 'full');
        document.getElementById('tabTimeSlot').classList.toggle('active', mode === 'time');
        document.getElementById('timeForm').classList.toggle('visible', mode === 'time');
    }

    // ── Add modal ──────────────────────────────────────────────
    function openAddModal(dateStr) {
        selectedDate = dateStr;
        // Reset form
        selectMode('full');
        document.getElementById('noteInput').value = '';
        document.getElementById('timeStart').value = '09:00';
        document.getElementById('timeEnd').value   = '18:00';
        // Set date label
        document.getElementById('modalDateText').textContent = fmtDate(dateStr).replace(/^\w/, c => c.toUpperCase());
        // Open
        document.getElementById('addModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.remove('open');
        document.body.style.overflow = '';
        if (calendar) calendar.unselect();
    }

    // ── Save block ─────────────────────────────────────────────
    function saveBlock() {
        const btn = document.getElementById('btnSave');
        btn.disabled = true;
        btn.textContent = 'Guardando…';

        const note = document.getElementById('noteInput').value.trim();
        let startStr, endStr, title;

        if (currentMode === 'time') {
            const tStart = document.getElementById('timeStart').value;
            const tEnd   = document.getElementById('timeEnd').value;
            startStr = selectedDate + 'T' + tStart + ':00';
            endStr   = selectedDate + 'T' + tEnd + ':00';
            title    = note || `🔴 Ocupado ${tStart}–${tEnd}`;
        } else {
            // All-day block: send YYYY-MM-DD
            startStr = selectedDate;
            endStr   = selectedDate;
            title    = note || '🔴 Día ocupado';
        }

        fetch('{{ route("availability.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ title, start: startStr, end: endStr, type: 'busy' })
        })
        .then(async r => {
            const data = await r.json();
            if (r.ok && data.success) {
                calendar.refetchEvents();
                closeAddModal();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Bloque guardado',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo guardar el bloque.',
                    confirmButtonColor: '#6c3fc5'
                });
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i> Guardar bloque';
        });
    }

    // ── Delete modal ───────────────────────────────────────────
    function openDeleteModal(event) {
        deleteEventId  = event.extendedProps.real_id;
        deleteEventObj = event;

        const evStart = event.start
            ? event.start.toLocaleDateString('es-MX', { weekday:'long', day:'numeric', month:'long', year:'numeric' })
            : '—';
        const evEnd = event.end
            ? event.end.toLocaleDateString('es-MX', { weekday:'long', day:'numeric', month:'long', year:'numeric' })
            : evStart;

        // Detect if it's a timed event
        const hasTimes = !event.allDay && event.start;
        const timeInfo = hasTimes
            ? `<div><strong>Hora:</strong> ${event.start.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'})}–${event.end ? event.end.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'}) : ''}</div>`
            : '';

        document.getElementById('deleteInfo').innerHTML = `
            <div><strong>Nombre:</strong> ${event.title}</div>
            <div><strong>Inicio:</strong> <span style="text-transform:capitalize">${evStart}</span></div>
            ${event.end && evEnd !== evStart ? `<div><strong>Fin:</strong> <span style="text-transform:capitalize">${evEnd}</span></div>` : ''}
            ${timeInfo}
        `;

        document.getElementById('deleteModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
        document.body.style.overflow = '';
        deleteEventId  = null;
        deleteEventObj = null;
    }

    function confirmDelete() {
        const btn = document.getElementById('btnDelete');
        btn.disabled = true;
        btn.textContent = 'Eliminando…';

        fetch(`{{ url('/availability') }}/${deleteEventId}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (deleteEventObj) deleteEventObj.remove();
                closeDeleteModal();
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-trash" style="margin-right:6px;"></i> Eliminar';
        });
    }

    // ── Calendar init ──────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  ''           // No view switcher — month only
            },
            buttonText: { today: 'Hoy' },
            editable: true,
            selectable: false,       // We handle clicks per-day ourselves
            dayMaxEvents: true,
            events: '{{ route("availability.events") }}',

            // Click on empty day cell
            dateClick: function(info) {
                const now = new Date();
                now.setHours(0,0,0,0);
                if (info.date < now) {
                    return; // Ignorar fechas pasadas
                }
                openAddModal(info.dateStr);
            },

            // Click on an event
            eventClick: function(info) {
                const isManual = info.event.extendedProps.source === 'manual';
                if (!isManual) {
                    // System event: show info only
                    const desc = info.event.extendedProps.description || 'Generado automáticamente por el sistema.';
                    const start = info.event.start
                        ? info.event.start.toLocaleDateString('es-MX', { weekday:'long', day:'numeric', month:'long', year:'numeric' })
                        : '';
                    // Simple styled alert using our delete modal repurposed as info
                    document.getElementById('deleteInfo').innerHTML = `
                        <div><strong>Evento:</strong> ${info.event.title}</div>
                        ${start ? `<div><strong>Fecha:</strong> <span style="text-transform:capitalize">${start}</span></div>` : ''}
                        <div style="margin-top:6px;color:#6c3fc5;">${desc}</div>
                    `;
                    document.querySelector('.av-delete-title').textContent = 'Información del evento';
                    document.querySelector('.av-delete-warn').style.display = 'none';
                    document.getElementById('btnDelete').style.display = 'none';
                    document.getElementById('deleteModal').classList.add('open');
                    document.body.style.overflow = 'hidden';
                    return;
                }
                // Reset info modal to delete mode
                document.querySelector('.av-delete-title').textContent = '¿Eliminar este bloque?';
                document.querySelector('.av-delete-warn').style.display = '';
                document.getElementById('btnDelete').style.display = '';
                openDeleteModal(info.event);
            },

            // Drag / resize (manual events only)
            eventChange: function(info) {
                const isManual = info.event.extendedProps.source === 'manual';
                const now = new Date();
                now.setHours(0,0,0,0);
                
                if (!isManual || info.event.start < now) { 
                    info.revert(); 
                    return; 
                }

                fetch(`{{ url('/availability') }}/${info.event.extendedProps.real_id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ start: info.event.startStr, end: info.event.endStr || info.event.startStr })
                }).then(async r => {
                    if (!r.ok) {
                        const data = await r.json();
                        info.revert();
                        Swal.fire({
                            icon: 'error',
                            title: 'No permitido',
                            text: data.message || 'Error al actualizar el bloque.',
                            confirmButtonColor: '#6c3fc5'
                        });
                    }
                });
            }
        });

        calendar.render();
    });

    // Close modals on overlay click
    document.getElementById('addModal').addEventListener('click', function(e) {
        if (e.target === this) closeAddModal();
    });
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeAddModal(); closeDeleteModal(); }
    });
    </script>

@endsection