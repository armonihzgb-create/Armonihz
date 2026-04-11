@extends('layouts.dashboard')

@section('dashboard-content')

    @php
        // 1. Datos del cliente
        $client = $hiringRequest->client;
        $clientName = $client ? trim($client->nombre . ' ' . $client->apellido) : 'Cliente Anónimo';
        $initials = $client ? strtoupper(substr($client->nombre, 0, 1) . substr($client->apellido, 0, 1)) : 'CA';
        $memberSince = $client && $client->created_at ? $client->created_at->translatedFormat('F Y') : 'Reciente';

        // 2. Duración del evento
        $durationText = 'No especificada';
        if($hiringRequest->end_time && $hiringRequest->event_date) {
            $hours = $hiringRequest->event_date->diffInHours($hiringRequest->end_time);
            if($hours > 0) {
                $durationText = $hours . ' hora' . ($hours > 1 ? 's' : '');
            }
        }

        // 3. Configuración visual de estados
        $statusConfig = match($hiringRequest->status) {
            'pending'  => ['color' => '#ca8a04', 'bg' => '#fefce8', 'border' => '#fef08a', 'text' => '⏳ En espera de respuesta'],
            'accepted' => ['color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '✅ Solicitud Confirmada'],
            'rejected' => ['color' => '#dc2626', 'bg' => '#fef2f2', 'border' => '#fecaca', 'text' => '❌ Solicitud Rechazada'],
            'completed' => ['color' => '#6c3fc5', 'bg' => '#f5f3ff', 'border' => '#ddd6fe', 'text' => '⭐ Evento Finalizado'],
            'counter_offer' => ['color' => '#6366f1', 'bg' => '#eef2ff', 'border' => '#e0e7ff', 'text' => '🔄 Revisión de contraoferta'],
            default    => ['color' => '#64748b', 'bg' => '#f1f5f9', 'border' => '#e2e8f0', 'text' => '❔ Estado Desconocido']
        };
    @endphp

    {{-- BREADCRUMB --}}
    <div class="rqs-breadcrumb">
        <a href="{{ url('/requests') }}" class="rqs-back-link">
            <i data-lucide="arrow-left" style="width:15px;height:15px;"></i>
            Solicitudes
        </a>
        <span class="rqs-breadcrumb-sep">/</span>
        <span>Solicitud #{{ $hiringRequest->id }}</span>
    </div>

    {{-- TOP BAR --}}
    <div class="rqs-topbar">
        <div>
            <div class="rqs-eyebrow">
                <i data-lucide="inbox" style="width:14px;height:14px;color:#6c3fc5;"></i>
                SOLICITUD #{{ $hiringRequest->id }}
            </div>
            <h1 class="rqs-title">Evento — {{ $clientName }}</h1>
            <p class="rqs-subtitle">Recibida el {{ $hiringRequest->created_at->translatedFormat('d M, Y \· h:i A') }}</p>
        </div>
        
        {{-- Solo mostramos los botones de acción si está pendiente --}}
        @if($hiringRequest->status === 'pending')
        <div class="rqs-action-btns">
            <button class="rqs-reject-btn" onclick="cambiarEstado('rejected')">
                <i data-lucide="x" style="width:15px;height:15px;"></i>
                Rechazar
            </button>
            <button class="rqs-accept-btn" onclick="cambiarEstado('accepted')">
                <i data-lucide="check" style="width:15px;height:15px;"></i>
                Aceptar solicitud
            </button>
        </div>
        @elseif($hiringRequest->status === 'accepted')
        <div class="rqs-action-btns">
            <button class="rqs-accept-btn" onclick="cambiarEstado('completed')" style="background: linear-gradient(135deg, #6c3fc5, #a855f7); box-shadow: 0 4px 14px rgba(168,85,247,.25);">
                <i data-lucide="check-circle" style="width:15px;height:15px;"></i>
                Finalizar Evento
            </button>
        </div>
        @endif
    </div>

    <div class="rqs-layout">

        {{-- ── LEFT COLUMN ─────────────────────────── --}}
        <div class="rqs-left">

            {{-- Client card --}}
            <div class="rqs-section-card">
                <h3 class="rqs-card-title">
                    <i data-lucide="user" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Información del Cliente
                </h3>

  <div class="rqs-client-profile">
    <div class="rqs-avatar" style="overflow: hidden;">
        @php
            $imagenFinal = null;
            
            // 1. Priorizamos la foto subida por el usuario
            if (!empty($client->fotoPerfil)) {
                // Removemos cualquier barra extra al inicio por si acaso
                $cleanPath = ltrim($client->fotoPerfil, '/');
                // Usamos tu ruta especial /file/
                $imagenFinal = url('/file/' . $cleanPath);
            } 
            // 2. Si no hay foto propia, usamos la de Google
            elseif (!empty($client->google_picture)) {
                $imagenFinal = $client->google_picture;
            }
        @endphp

        @if($imagenFinal)
            <img src="{{ $imagenFinal }}" alt="Foto de {{ $clientName }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @else
            {{-- Si ambas están vacías (NULL), mostramos las iniciales --}}
            {{ $initials }}
        @endif
    </div>
    <div>
        <span class="rqs-client-name">{{ $clientName }}</span>
    </div>
</div>

               <div class="rqs-client-stats" style="grid-template-columns: 1fr 1fr; gap: 14px; text-align: left;">
                    
                    {{-- Ocupa las dos columnas para que el correo largo no se corte --}}
                    <div class="rqs-client-stat" style="grid-column: span 2;">
                        <span class="rqs-stat-label">Correo Electrónico</span>
                        <span class="rqs-stat-value" style="font-size: 13px; word-break: break-all;">
                            {{ $client->email ?? 'No registrado' }}
                        </span>
                    </div>

                    <div class="rqs-client-stat">
                        <span class="rqs-stat-label">Teléfono</span>
                        <span class="rqs-stat-value">
                            {{ $client->telefono ?? 'Sin número' }}
                        </span>
                    </div>

                    <div class="rqs-client-stat">
                        <span class="rqs-stat-label">Miembro desde</span>
                        <span class="rqs-stat-value">{{ ucfirst($memberSince) }}</span>
                    </div>

                </div>
            </div>

            {{-- Event details --}}
            <div class="rqs-section-card">
                <h3 class="rqs-card-title">
                    <i data-lucide="calendar" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Datos del Evento
                </h3>

                <div class="rqs-detail-rows">
                    <div class="rqs-detail-row">
                        <div class="rqs-detail-icon" style="background:#ede9fe;">
                            <i data-lucide="calendar" style="width:16px;height:16px;color:#6c3fc5;"></i>
                        </div>
                        <div class="rqs-detail-info">
                            <span class="rqs-detail-label">Fecha y hora</span>
                            <span class="rqs-detail-value">{{ $hiringRequest->event_date->translatedFormat('d F, Y — H:i \h\r\s') }}</span>
                            <span class="rqs-detail-sub">Duración estimada: {{ $durationText }}</span>
                        </div>
                    </div>
                    <div class="rqs-detail-row">
                        <div class="rqs-detail-icon" style="background:#eff6ff;">
                            <i data-lucide="map-pin" style="width:16px;height:16px;color:#2563eb;"></i>
                        </div>
                        <div class="rqs-detail-info">
                            <span class="rqs-detail-label">Ubicación</span>
                            <span class="rqs-detail-value">{{ $hiringRequest->event_location }}</span>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($hiringRequest->event_location) }}" target="_blank" class="rqs-map-link">Ver en Google Maps →</a>
                        </div>
                    </div>
                    <div class="rqs-detail-row rqs-budget-row">
                        <div class="rqs-detail-icon" style="background:#f0fdf4;">
                            <i data-lucide="banknote" style="width:16px;height:16px;color:#16a34a;"></i>
                        </div>
                        <div class="rqs-detail-info">
                            <span class="rqs-detail-label">Presupuesto ofrecido</span>
                            <span class="rqs-price">${{ number_format($hiringRequest->budget, 2) }} <small>MXN</small></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT COLUMN ────────────────────────── --}}
        <div class="rqs-right">

            {{-- Status badge dinámico --}}
            <div class="rqs-status-banner" style="background: {{ $statusConfig['bg'] }}; border-color: {{ $statusConfig['border'] }};">
                <span class="rqs-badge-lg" style="color: {{ $statusConfig['color'] }};">
                    {{ $statusConfig['text'] }}
                </span>
                <span class="rqs-status-time" style="color: {{ $statusConfig['color'] }}; opacity: 0.8;">
                    Actualizado {{ $hiringRequest->updated_at->diffForHumans() }}
                </span>
            </div>

            {{-- Client message --}}
            <div class="rqs-section-card">
                <h3 class="rqs-card-title">
                    <i data-lucide="message-square" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Mensaje del Cliente
                </h3>
                <div class="rqs-msg-bubble">
                    {{-- Usamos nl2br para respetar los saltos de línea enviados desde Android --}}
                    {!! nl2br(e($hiringRequest->description)) !!}
                </div>
            </div>

           {{-- Reply form (Oculto si ya fue aceptada/rechazada/contraoferta) --}}
            @if($hiringRequest->status === 'pending')
            <div class="rqs-section-card">
                <h3 class="rqs-card-title">
                    <i data-lucide="send" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Tu Respuesta (Contraoferta)
                </h3>

                <form id="counterOfferForm">
                    <textarea id="musicianMessage" class="rqs-textarea" rows="4"
                        placeholder="Ej: Hola {{ $client->nombre ?? 'cliente' }}, sí tengo disponibilidad, pero por las horas extra mi tarifa sería de..."></textarea>
                    
                    <div style="margin-top: 15px; margin-bottom: 15px;">
                        <label style="font-size: 13px; font-weight: bold; color: #6c3fc5;">💰 Nueva Tarifa Propuesta (MXN):</label>
                        <input type="number" id="counterPrice" placeholder="Ej: 8000" style="width: 100%; padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; margin-top: 6px; font-family: inherit; font-size: 14px; color: #334155; box-sizing: border-box;">
                    </div>

                    <div class="rqs-form-footer">
                        <p class="rqs-form-hint">
                            <i data-lucide="info" style="width:12px;height:12px;"></i>
                            Al enviar, el estado cambiará a <strong>Contraoferta</strong>.
                        </p>
                        <button type="button" class="rqs-send-btn" onclick="enviarContraoferta()">
                            <i data-lucide="send" style="width:14px;height:14px;"></i>
                            Enviar propuesta
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>

    <style>
        /* ── Breadcrumb ──────────────────────────── */
        .rqs-breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #94a3b8; margin-bottom: 20px;
        }
        .rqs-back-link {
            display: flex; align-items: center; gap: 5px;
            color: #64748b; text-decoration: none; font-weight: 500; transition: color .2s;
        }
        .rqs-back-link:hover { color: #6c3fc5; }
        .rqs-breadcrumb-sep { color: #e2e8f0; }

        /* ── Top bar ─────────────────────────────── */
        .rqs-topbar {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 24px; padding-bottom: 22px;
            border-bottom: 1px solid #f1f5f9;
        }
        .rqs-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .rqs-title { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .rqs-subtitle { font-size: 13px; color: #94a3b8; margin: 0; }
        .rqs-action-btns { display: flex; gap: 10px; flex-shrink: 0; }
        .rqs-reject-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px; border-radius: 8px; border: 1.5px solid #fecaca;
            background: #fef2f2; color: #dc2626; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .2s;
        }
        .rqs-reject-btn:hover { background: #fee2e2; }
        .rqs-accept-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px; border-radius: 8px;
            background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; font-size: 13px; font-weight: 700; border: none;
            cursor: pointer; box-shadow: 0 4px 14px rgba(108,63,197,.25); transition: opacity .2s;
        }
        .rqs-accept-btn:hover { opacity: .9; }

        /* ── Layout ──────────────────────────────── */
        .rqs-layout { display: grid; grid-template-columns: 340px 1fr; gap: 22px; align-items: start; }
        .rqs-section-card {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 16px; padding: 22px; margin-bottom: 16px;
        }
        .rqs-card-title {
            display: flex; align-items: center; gap: 8px;
            font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 18px;
        }

        /* ── Client ──────────────────────────────── */
        .rqs-client-profile {
            display: flex; align-items: center; gap: 14px; margin-bottom: 18px;
        }
        .rqs-avatar {
            width: 52px; height: 52px; border-radius: 14px;
            background: #ede9fe; color: #6c3fc5;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800; flex-shrink: 0;
        }
        .rqs-client-name { display: block; font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 3px; }
        .rqs-client-verified { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #2563eb; font-weight: 500; margin-bottom: 3px; }
        .rqs-stars { font-size: 13px; color: #f59e0b; }
        .rqs-stars small { color: #64748b; font-weight: 400; }

        .rqs-client-stats {
            display: grid; grid-template-columns: repeat(3,1fr); gap: 10px;
            border-top: 1px solid #f8fafc; padding-top: 14px;
        }
        .rqs-client-stat { display: flex; flex-direction: column; gap: 2px; text-align: center; }
        .rqs-stat-label { font-size: 11px; color: #94a3b8; font-weight: 500; }
        .rqs-stat-value { font-size: 13px; font-weight: 700; color: #0f172a; }

        /* ── Event Details ───────────────────────── */
        .rqs-detail-rows { display: flex; flex-direction: column; gap: 0; }
        .rqs-detail-row {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 13px 0; border-bottom: 1px solid #f8fafc;
        }
        .rqs-detail-row:last-child { border-bottom: none; }
        .rqs-budget-row { background: #f0fdf4; border-radius: 10px; padding: 13px 12px; border: 1px solid #bbf7d0; margin-top: 4px; }
        .rqs-detail-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .rqs-detail-info { display: flex; flex-direction: column; gap: 2px; }
        .rqs-detail-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; }
        .rqs-detail-value { font-size: 14px; font-weight: 600; color: #0f172a; }
        .rqs-detail-sub { font-size: 12px; color: #94a3b8; }
        .rqs-map-link { font-size: 12px; color: #2563eb; text-decoration: none; }
        .rqs-map-link:hover { text-decoration: underline; }
        .rqs-price { font-size: 22px; font-weight: 900; color: #15803d; }
        .rqs-price small { font-size: 12px; font-weight: 400; color: #94a3b8; }

        /* ── Status banner ───────────────────────── */
        .rqs-status-banner {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 18px; border: 1.5px solid transparent;
            border-radius: 12px; margin-bottom: 16px;
        }
        .rqs-badge-lg { font-size: 13px; font-weight: 600; }
        .rqs-status-time { font-size: 12px; font-weight: 500; }

        /* ── Message ─────────────────────────────── */
        .rqs-msg-bubble {
            background: #f8fafc; border: 1px solid #f1f5f9;
            border-radius: 12px; padding: 16px 18px;
            font-size: 14.5px; color: #334155; line-height: 1.75;
        }
        .rqs-msg-bubble p { margin: 0 0 10px; }
        .rqs-msg-bubble p:last-child { margin: 0; }

        /* ── Reply ───────────────────────────────── */
        .rqs-textarea {
            width: 100%; padding: 13px 14px; border: 1.5px solid #e2e8f0;
            border-radius: 10px; font-family: inherit; font-size: 14px;
            line-height: 1.6; resize: vertical; box-sizing: border-box;
            transition: border-color .2s; color: #334155;
        }
        .rqs-textarea:focus { border-color: #6c3fc5; outline: none; }
        .rqs-form-footer {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 12px; gap: 12px;
        }
        .rqs-form-hint { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 5px; margin: 0; }
        .rqs-send-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; border-radius: 8px;
            background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; font-size: 13px; font-weight: 700; border: none;
            cursor: pointer; box-shadow: 0 4px 14px rgba(108,63,197,.2); transition: opacity .2s;
            white-space: nowrap;
        }
        .rqs-send-btn:hover { opacity: .9; }

        @media (max-width: 900px) {
            .rqs-layout { grid-template-columns: 1fr; }
            .rqs-topbar, .rqs-action-btns { flex-wrap: wrap; }
            .rqs-client-stats { grid-template-columns: 1fr 1fr; }
        }
    </style>

 {{-- SCRIPT PARA CAMBIAR EL ESTADO SIN RECARGAR LA PÁGINA --}}
    <script>
        function cambiarEstado(nuevoEstado) {
            // 1. Configuramos los textos y colores dinámicamente según el estado
            let accion = '';
            let confirmColor = '';
            let textoAlerta = '';
            let icono = 'warning';

            if (nuevoEstado === 'accepted') {
                accion = 'aceptar';
                confirmColor = '#16a34a'; // Verde
                textoAlerta = 'Este evento se bloqueará automáticamente en tu calendario de disponibilidad.';
            } else if (nuevoEstado === 'rejected') {
                accion = 'rechazar';
                confirmColor = '#dc2626'; // Rojo
                textoAlerta = 'El cliente será notificado y esta acción no se puede deshacer.';
            } else if (nuevoEstado === 'completed') {
                accion = 'finalizar';
                confirmColor = '#6c3fc5'; // Morado
                textoAlerta = 'El evento se marcará como completado y el cliente podrá dejarte una reseña desde la app móvil.';
                icono = 'info'; // Cambiamos el icono para que sea más amigable
            }

            // 2. Lanzamos la alerta de confirmación
            Swal.fire({
                title: `¿Estás seguro de ${accion} este evento?`,
                text: textoAlerta,
                icon: icono,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Mostrar loading
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Actualizando solicitud',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    // Hacer la petición al servidor
                    fetch(`/requests/{{ $hiringRequest->id }}/status`, {
                        method: 'POST', // Usamos POST con _method PATCH (Truco de Laravel)
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json', 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            _method: 'PATCH',
                            status: nuevoEstado
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                title: '¡Listo!',
                                text: 'La solicitud ha sido actualizada exitosamente.',
                                icon: 'success',
                                confirmButtonColor: '#6c3fc5'
                            }).then(() => {
                                // Recargar la página para ver los nuevos colores y ocultar los botones
                                window.location.reload();
                            });
                        } else {
                            // Mostrar el mensaje de error que viene del backend (ej. si intentan saltar estados)
                            Swal.fire('Error', data.message || 'No se pudo actualizar.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Problema de conexión.', 'error');
                    });
                }
            });
        }

        function enviarContraoferta() {
            const mensaje = document.getElementById('musicianMessage').value;
            const precio = document.getElementById('counterPrice').value;

            // Validar que no envíen el formulario vacío
            if(!mensaje || !precio) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan datos',
                    text: 'Debes escribir un mensaje y proponer un nuevo precio.',
                    confirmButtonColor: '#6c3fc5'
                });
                return;
            }

            Swal.fire({
                title: '¿Enviar Contraoferta?',
                text: "El cliente será notificado con tu nuevo precio y mensaje. Tendrá que aceptar para confirmar el evento.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6c3fc5',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    Swal.fire({
                        title: 'Enviando...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch(`/requests/{{ $hiringRequest->id }}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            _method: 'PATCH',
                            status: 'counter_offer',
                            musician_message: mensaje,
                            counter_offer: precio
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                title: '¡Enviada!',
                                text: 'Tu propuesta ha sido enviada al cliente.',
                                icon: 'success',
                                confirmButtonColor: '#6c3fc5'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', 'No se pudo enviar la contraoferta.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Problema de conexión.', 'error');
                    });
                }
            });
        }
    </script>

    

@endsection