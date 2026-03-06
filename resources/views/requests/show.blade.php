@extends('layouts.dashboard')

@section('dashboard-content')

    <div class="page-header">
        <div>
            <a href="{{ url('/requests') }}" class="back-link">
                <i data-lucide="arrow-left"></i> Volver a solicitudes
            </a>
            <h2 style="margin-top: 8px;">Detalle de Solicitud #2045</h2>
            <p class="dashboard-subtitle">Recibida el 14 Oct, 2026 - 10:30 AM</p>
        </div>
        <div class="header-actions">
            {{-- Los botones de acción cambian según el estado --}}
            <button class="secondary-btn danger">
                <i data-lucide="x-circle"></i> Rechazar
            </button>
            <button class="primary-btn">
                <i data-lucide="check-circle"></i> Aceptar Solicitud
            </button>
        </div>
    </div>

    <div class="request-detail-grid">
        
        {{-- COLUMNA IZQUIERDA: INFORMACIÓN --}}
        <div class="detail-left">
            
            {{-- Tarjeta del Cliente --}}
            <div class="dashboard-box client-card">
                <div class="box-header">
                    <h3>Información del Cliente</h3>
                </div>
                <div class="client-profile">
                    <div class="avatar-circle-lg">JP</div>
                    <div>
                        <h4>Juan Pérez</h4>
                        <p>Cliente Verificado</p>
                        <div class="rating-stars">⭐⭐⭐⭐⭐ (5.0)</div>
                    </div>
                </div>
                <div class="client-stats">
                    <div class="stat">
                        <span class="label">Eventos</span>
                        <span class="value">3</span>
                    </div>
                    <div class="stat">
                        <span class="label">Ubicación</span>
                        <span class="value">Guadalajara</span>
                    </div>
                </div>
            </div>

            {{-- Tarjeta del Evento --}}
            <div class="dashboard-box event-card">
                <div class="box-header">
                    <h3>Datos del Evento</h3>
                </div>
                <ul class="event-details-list">
                    <li>
                        <i data-lucide="calendar"></i>
                        <div>
                            <span class="label">Fecha y Hora</span>
                            <strong>15 Octubre, 2026 - 19:00 hrs</strong>
                            <span class="sub-text">Duración: 5 horas</span>
                        </div>
                    </li>
                    <li>
                        <i data-lucide="map-pin"></i>
                        <div>
                            <span class="label">Ubicación</span>
                            <strong>Salón "Los Arcos", GDL</strong>
                            <a href="#" class="map-link">Ver en mapa</a>
                        </div>
                    </li>
                    <li>
                        <i data-lucide="music"></i>
                        <div>
                            <span class="label">Tipo de Evento</span>
                            <strong>Boda Civil</strong>
                        </div>
                    </li>
                    <li>
                        <i data-lucide="dollar-sign"></i>
                        <div>
                            <span class="label">Presupuesto Ofrecido</span>
                            <strong class="price-tag">$18,000 MXN</strong>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        {{-- COLUMNA DERECHA: MENSAJE Y RESPUESTA --}}
        <div class="detail-right">
            
            {{-- Mensaje Original --}}
            <div class="dashboard-box message-box">
                <div class="box-header">
                    <h3>Mensaje del Cliente</h3>
                </div>
                <div class="message-content">
                    <p>Hola, me gustaría contratar sus servicios para mi boda civil. Sería en el salón Los Arcos. Nos interesa mucho su repertorio de música clásica y también algo de mariachi para el final. ¿Tienen disponibilidad? Quedo atento a su respuesta.</p>
                </div>
            </div>

            {{-- Área de Respuesta --}}
            <div class="dashboard-box reply-box">
                <div class="box-header">
                    <h3>Tu Respuesta</h3>
                </div>
                
                {{-- Formulario para responder --}}
                <form action="#" class="reply-form">
                    <textarea placeholder="Escribe tu respuesta aquí... Por ejemplo: 'Hola Juan, ¡claro que sí! Tenemos disponibilidad...'" rows="6"></textarea>
                    
                    <div class="form-actions">
                        <p class="hint-text"><i data-lucide="info"></i> Al responder, el estado cambiará a <strong>Respondida</strong>.</p>
                        <button type="button" class="primary-btn">
                            <i data-lucide="send"></i> Enviar Respuesta
                        </button>
                    </div>
                </form>

                {{-- EJEMPLO DE RESPUESTA ENVIADA (Oculto por defecto/lógica de backend) --}}
                {{-- 
                <div class="response-sent">
                    <div class="response-header">
                        <span class="badge success">Respuesta Enviada</span>
                        <span class="date">Hoy, 10:45 AM</span>
                    </div>
                    <p>Hola Juan, ¡muchas gracias por contactarnos! Sí tenemos disponibilidad para esa fecha...</p>
                </div> 
                --}}
            </div>

        </div>
    </div>

    <style>
        .page-header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start; }
        .back-link { display: flex; align-items: center; gap: 6px; color: var(--text-dim); text-decoration: none; font-size: 14px; font-weight: 500; }
        .back-link:hover { color: var(--accent-blue); }
        .header-actions { display: flex; gap: 12px; }

        .request-detail-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 24px; }
        
        /* Client Profile */
        .client-profile { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
        .avatar-circle-lg { width: 64px; height: 64px; background: #e0e7ff; color: #4338ca; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; }
        .client-profile h4 { margin: 0; font-size: 18px; }
        .client-profile p { margin: 0; color: var(--text-dim); font-size: 14px; }
        .rating-stars { font-size: 12px; color: #f59e0b; margin-top: 4px; }
        
        .client-stats { display: flex; justify-content: space-between; border-top: 1px solid var(--border-light); padding-top: 16px; }
        .client-stats .stat { display: flex; flex-direction: column; font-size: 13px; }
        .client-stats .label { color: var(--text-dim); margin-bottom: 4px; }
        .client-stats .value { font-weight: 600; }

        /* Event Details */
        .event-details-list { list-style: none; padding: 0; margin: 0; }
        .event-details-list li { display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px dashed var(--border-light); }
        .event-details-list li:last-child { border-bottom: none; }
        .event-details-list i { color: var(--text-dim); width: 20px; height: 20px; margin-top: 2px; }
        .event-details-list div { display: flex; flex-direction: column; }
        .event-details-list .label { font-size: 12px; color: var(--text-dim); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .event-details-list strong { font-size: 15px; }
        .sub-text { font-size: 13px; color: var(--text-dim); margin-top: 2px; }
        .map-link { font-size: 13px; color: var(--accent-blue); text-decoration: none; margin-top: 2px; }
        .price-tag { color: var(--accent-green); font-size: 18px; }

        /* Messages */
        .message-content { background: #f9fafb; padding: 20px; border-radius: 12px; font-size: 15px; line-height: 1.6; color: var(--text-main); }
        
        /* Reply Box */
        .reply-form textarea { width: 100%; padding: 16px; border: 1px solid var(--border-light); border-radius: 12px; font-family: inherit; font-size: 15px; resize: vertical; margin-bottom: 16px; min-height: 150px; }
        .reply-form textarea:focus { border-color: var(--accent-blue); outline: none; ring: 2px rgba(47, 147, 245, 0.1); }
        
        .form-actions { display: flex; justify-content: space-between; align-items: center; }
        .hint-text { font-size: 13px; color: var(--text-dim); display: flex; align-items: center; gap: 6px; margin: 0; }
        .hint-text i { width: 16px; height: 16px; }

        .secondary-btn.danger { color: #ef4444; border-color: #fecaca; background: #fef2f2; }
        .secondary-btn.danger:hover { background: #fee2e2; }

        @media (max-width: 900px) {
            .request-detail-grid { grid-template-columns: 1fr; }
            .page-header { flex-direction: column; gap: 16px; }
            .header-actions { width: 100%; justify-content: space-between; }
            .header-actions button { flex: 1; }
        }
    </style>

@endsection
