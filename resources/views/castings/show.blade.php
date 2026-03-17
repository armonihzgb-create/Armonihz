@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- FLASH --}}
    @if(session('success'))
        <div id="flash-msg" style="position:fixed;top:20px;right:24px;z-index:9999;background:#22c55e;color:#fff;padding:14px 24px;border-radius:10px;font-size:14px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.15);display:flex;align-items:center;gap:10px;">
            <i data-lucide="check-circle" style="width:18px;height:18px;"></i> {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 5000);</script>
    @endif

    {{-- BREADCRUMB --}}
    <div class="cs-breadcrumb">
        <a href="{{ route('castings.index') }}" class="cs-breadcrumb-link">
            <i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Castings Activos
        </a>
    </div>

    <div class="cs-layout">

        {{-- ── LEFT PANEL ───────────────────────────── --}}
        <div class="cs-left">

            {{-- Event Card --}}
            <div class="cs-section-card">
                <div class="cs-event-topbar">
                    <span class="cs-tag">{{ $event->tipo_musica }}</span>
                    <span class="cs-event-time">{{ $event->created_at->diffForHumans() }}</span>
                </div>

                <h1 class="cs-event-title">{{ $event->titulo }}</h1>

                <div class="cs-detail-grid">
                    <div class="cs-detail-tile">
                        <span class="cs-detail-tile-label"><i data-lucide="map-pin" style="width:12px;height:12px;"></i> Ubicación</span>
                        <span class="cs-detail-tile-value">{{ $event->ubicacion }}</span>
                    </div>
                    <div class="cs-detail-tile">
                        <span class="cs-detail-tile-label"><i data-lucide="calendar" style="width:12px;height:12px;"></i> Fecha del evento</span>
                        <span class="cs-detail-tile-value">{{ $event->fecha }}</span>
                    </div>
                    <div class="cs-detail-tile">
                        <span class="cs-detail-tile-label"><i data-lucide="clock" style="width:12px;height:12px;"></i> Duración estimada</span>
                        <span class="cs-detail-tile-value">{{ $event->duracion }}</span>
                    </div>
                    <div class="cs-detail-tile cs-detail-tile--highlight">
                        <span class="cs-detail-tile-label" style="color:#15803d;">Presupuesto del cliente</span>
                        <span class="cs-detail-tile-value cs-detail-tile-budget">${{ number_format($event->presupuesto, 0) }} <small>MXN</small></span>
                    </div>
                </div>

                @if($event->descripcion)
                    <div class="cs-description">
                        <p class="cs-description-label">Descripción del cliente</p>
                        <p class="cs-description-text">{{ $event->descripcion }}</p>
                    </div>
                @endif
            </div>

            {{-- Applications count --}}
            <div class="cs-apps-count">
                <i data-lucide="users" style="width:16px;height:16px;color:#6c3fc5;"></i>
                <strong>{{ $totalApplications }}</strong> {{ $totalApplications === 1 ? 'músico se ha postulado' : 'músicos se han postulado' }} a este evento.
            </div>

        </div>

        {{-- ── RIGHT PANEL ──────────────────────────── --}}
        <div class="cs-right">

            @if($event->status !== 'open')
                {{-- Closed --}}
                <div class="cs-section-card cs-status-card cs-status-closed">
                    <i data-lucide="lock" style="width:28px;height:28px;color:#94a3b8;margin-bottom:12px;"></i>
                    <h4>Evento cerrado</h4>
                    <p>Este evento ya no acepta postulaciones.</p>
                </div>

            @elseif($myApplication)
                {{-- Already applied --}}
                <div class="cs-section-card cs-status-card cs-status-applied">
                    <div class="cs-status-applied-header">
                        <div class="cs-status-icon-wrap cs-status-icon-blue">
                            <i data-lucide="check" style="width:18px;height:18px;color:#fff;"></i>
                        </div>
                        <div>
                            <h4 style="margin:0;color:#1e40af;">Ya estás postulado</h4>
                            <span style="font-size:12px;color:#60a5fa;">Enviado {{ $myApplication->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="cs-applied-price-row">
                        <span class="cs-applied-label">Tu precio propuesto</span>
                        <span class="cs-applied-price">${{ number_format($myApplication->proposed_price, 0) }} <small>MXN</small></span>
                    </div>

                    <div class="cs-applied-message-wrap">
                        <span class="cs-applied-label">Tu mensaje</span>
                        <p class="cs-applied-message">{{ $myApplication->message }}</p>
                    </div>

                    <span class="cs-status-badge
                        {{ $myApplication->status === 'accepted' ? 'cs-status-badge--green' : ($myApplication->status === 'rejected' ? 'cs-status-badge--red' : ($myApplication->status === 'cancelled' ? 'cs-status-badge--grey' : 'cs-status-badge--yellow')) }}">
                        {{ $myApplication->status === 'accepted' ? '✓ Aceptado' : ($myApplication->status === 'rejected' ? '✗ No seleccionado' : ($myApplication->status === 'cancelled' ? '✗ Contratación cancelada' : '⏳ En revisión')) }}
                    </span>
                </div>

            @else
                {{-- Application form --}}
                <div class="cs-section-card">
                    <div class="cs-form-header">
                        <div class="cs-status-icon-wrap cs-status-icon-purple">
                            <i data-lucide="send" style="width:16px;height:16px;color:#fff;"></i>
                        </div>
                        <div>
                            <h4 style="margin:0;color:#1e293b;">Tu Propuesta</h4>
                            <p style="margin:0;font-size:13px;color:#94a3b8;">Sé específico y profesional.</p>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="cs-error-box">
                            @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
                        </div>
                    @endif

                    <form action="{{ route('castings.apply', $event->id) }}" method="POST">
                        @csrf

                        <div class="cs-field">
                            <label class="cs-label">Tu precio (MXN) *</label>
                            <div class="cs-price-input-wrap">
                                <span class="cs-price-prefix">$</span>
                                <input type="number" name="proposed_price" min="0" step="100"
                                       value="{{ old('proposed_price') }}"
                                       placeholder="3500"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       class="cs-input cs-price-input">
                            </div>
                        </div>

                        <div class="cs-field">
                            <label class="cs-label">Mensaje para el cliente *</label>
                            <textarea name="message" rows="5" maxlength="800"
                                      placeholder="Ej: Hola, somos una banda con 5 años de experiencia. Incluimos sonido y luces básicas..."
                                      class="cs-textarea"
                                      oninput="updateCharCount(this)">{{ old('message') }}</textarea>
                            <div class="cs-char-count">
                                <span id="char-count">0</span> / 800
                            </div>
                        </div>

                        <button type="submit" class="cs-submit-btn">
                            <i data-lucide="send" style="width:15px;height:15px;"></i>
                            Enviar Propuesta
                        </button>
                        <p class="cs-submit-note">Solo puedes enviar una propuesta por evento.</p>
                    </form>
                </div>
            @endif

        </div>
    </div>

    <style>
        .cs-breadcrumb { margin-bottom: 24px; }
        .cs-breadcrumb-link {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 500; color: #64748b; text-decoration: none;
            transition: color .2s;
        }
        .cs-breadcrumb-link:hover { color: #6c3fc5; }

        .cs-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            align-items: start;
        }

        /* Section Card */
        .cs-section-card {
            background: #fff;
            border: 1.5px solid #e8edf3;
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 16px;
        }

        /* Event top bar */
        .cs-event-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .cs-tag {
            font-size: 12px; font-weight: 700; padding: 5px 12px;
            border-radius: 999px; background: rgba(108,63,197,.08); color: #6c3fc5;
        }
        .cs-event-time { font-size: 12px; color: #94a3b8; }
        .cs-event-title {
            font-size: 22px; font-weight: 800; color: #0f172a;
            margin: 0 0 24px; line-height: 1.35;
        }

        /* Detail grid */
        .cs-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }
        .cs-detail-tile {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 14px 16px;
        }
        .cs-detail-tile--highlight {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }
        .cs-detail-tile-label {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 600; text-transform: uppercase;
            letter-spacing: .06em; color: #94a3b8; margin-bottom: 6px;
        }
        .cs-detail-tile-value {
            font-size: 15px; font-weight: 600; color: #1e293b;
        }
        .cs-detail-tile-budget {
            font-size: 20px; font-weight: 800; color: #15803d;
        }
        .cs-detail-tile-budget small { font-size: 12px; font-weight: 400; color: #6b7280; }

        /* Description */
        .cs-description { border-top: 1px solid #f1f5f9; padding-top: 20px; }
        .cs-description-label {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: #94a3b8; margin: 0 0 8px;
        }
        .cs-description-text {
            font-size: 14px; color: #334155; line-height: 1.75; margin: 0;
        }

        /* Apps count */
        .cs-apps-count {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #64748b;
            background: #f9fafb; border: 1px solid #f1f5f9;
            border-radius: 10px; padding: 12px 16px;
        }

        /* Status cards */
        .cs-status-card { text-align: center; }
        .cs-status-closed { color: #64748b; }
        .cs-status-closed h4 { margin: 0 0 6px; color: #475569; }
        .cs-status-closed p { margin: 0; font-size: 14px; }

        .cs-status-applied { border-color: #bfdbfe; background: linear-gradient(160deg, #f0f7ff, #fff); }
        .cs-status-applied-header {
            display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
        }
        .cs-status-icon-wrap {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .cs-status-icon-blue { background: #3b82f6; }
        .cs-status-icon-purple { background: linear-gradient(135deg, #6c3fc5, #2f93f5); }

        .cs-applied-price-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 16px; background: #eff6ff; border-radius: 10px; margin-bottom: 14px;
        }
        .cs-applied-label { font-size: 12px; font-weight: 600; color: #60a5fa; text-transform: uppercase; letter-spacing: .04em; }
        .cs-applied-price { font-size: 22px; font-weight: 800; color: #1e40af; }
        .cs-applied-price small { font-size: 12px; font-weight: 400; }
        .cs-applied-message-wrap { margin-bottom: 16px; }
        .cs-applied-message {
            font-size: 14px; color: #1e293b; line-height: 1.65;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 12px 14px; margin: 6px 0 0;
        }

        .cs-status-badge {
            display: inline-flex; align-items: center;
            font-size: 12px; font-weight: 600; padding: 5px 14px; border-radius: 999px;
        }
        .cs-status-badge--yellow { background: #fefce8; color: #ca8a04; border: 1px solid #fef08a; }
        .cs-status-badge--green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .cs-status-badge--red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .cs-status-badge--grey   { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

        /* Application form */
        .cs-form-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .cs-error-box {
            background: #fef2f2; color: #dc2626; padding: 12px 16px;
            border-radius: 8px; margin-bottom: 16px; font-size: 13px; line-height: 1.7;
        }
        .cs-field { margin-bottom: 18px; }
        .cs-label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 7px;
        }
        .cs-price-input-wrap { position: relative; display: flex; align-items: center; }
        .cs-price-prefix {
            position: absolute; left: 13px; font-size: 15px;
            font-weight: 700; color: #94a3b8;
        }
        .cs-input {
            width: 100%; padding: 11px 12px; border: 1.5px solid #e2e8f0;
            border-radius: 8px; font-size: 15px; box-sizing: border-box;
            transition: border-color .2s;
        }
        .cs-input:focus { border-color: #6c3fc5; outline: none; }
        .cs-price-input { padding-left: 36px; font-weight: 700; font-size: 17px; }
        .cs-textarea {
            width: 100%; padding: 11px 12px; border: 1.5px solid #e2e8f0;
            border-radius: 8px; font-size: 14px; line-height: 1.6; resize: vertical;
            box-sizing: border-box; font-family: inherit; transition: border-color .2s;
        }
        .cs-textarea:focus { border-color: #6c3fc5; outline: none; }
        .cs-char-count { text-align: right; font-size: 11px; color: #94a3b8; margin-top: 4px; }
        .cs-submit-btn {
            width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;
            padding: 13px; background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; border: none; border-radius: 10px; font-size: 14px; font-weight: 700;
            cursor: pointer; box-shadow: 0 4px 16px rgba(108,63,197,.3); transition: opacity .2s;
        }
        .cs-submit-btn:hover { opacity: .9; }
        .cs-submit-note { text-align: center; font-size: 12px; color: #94a3b8; margin: 10px 0 0; }

        @media (max-width: 860px) {
            .cs-layout { grid-template-columns: 1fr; }
            .cs-detail-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .cs-detail-grid { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        function updateCharCount(el) {
            document.getElementById('char-count').textContent = el.value.length;
        }
        const msgArea = document.querySelector('textarea[name="message"]');
        if (msgArea) updateCharCount(msgArea);
    </script>

@endsection
