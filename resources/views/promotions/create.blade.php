@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- PAGE HEADER --}}
    <div class="promo-page-header">
        <div>
            <div class="promo-eyebrow">
                <i data-lucide="zap" style="width:14px;height:14px;color:#6c3fc5;"></i>
                PROMOCIONAR PERFIL
            </div>
            <h1 class="promo-page-title">Impulsa tu carrera</h1>
            <p class="promo-page-subtitle">Llega a más clientes potenciales y aumenta tus contrataciones.</p>
        </div>
        <a href="{{ route('promotions.index') }}" class="promo-secondary-btn">
            <i data-lucide="bar-chart-2" style="width:15px;height:15px;"></i>
            Mis campañas
        </a>
    </div>

    <div class="promo-layout">

        {{-- ── LEFT: Form ───────────────────────────────────────── --}}
        <div class="promo-form-col">

            {{-- Step 1: Content --}}
            <div class="promo-section-card">
                <div class="promo-step-header">
                    <div class="promo-step-num">1</div>
                    <div>
                        <h2 class="promo-step-title">¿Qué quieres promocionar?</h2>
                        <p class="promo-step-desc">Selecciona el contenido que verán los clientes.</p>
                    </div>
                </div>

                <div class="promo-option-grid" id="content-grid">
                    <label class="promo-option selected" id="opt-profile">
                        <input type="radio" name="content_type" value="profile" checked hidden
                               onchange="selectOption('content-grid', this.closest('.promo-option'))">
                        <div class="promo-option-icon" style="background:#ede9fe;">
                            <i data-lucide="user" style="width:22px;height:22px;color:#6c3fc5;"></i>
                        </div>
                        <div>
                            <span class="promo-option-title">Mi Perfil Principal</span>
                            <span class="promo-option-subtitle">Muestra toda tu información al cliente</span>
                        </div>
                    </label>
                    <label class="promo-option" id="opt-video">
                        <input type="radio" name="content_type" value="video" hidden
                               onchange="selectOption('content-grid', this.closest('.promo-option'))">
                        <div class="promo-option-icon" style="background:#e0f2fe;">
                            <i data-lucide="video" style="width:22px;height:22px;color:#0ea5e9;"></i>
                        </div>
                        <div>
                            <span class="promo-option-title">Video Destacado</span>
                            <span class="promo-option-subtitle">Destaca un video de tu portafolio</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Step 2: Reach --}}
            <div class="promo-section-card">
                <div class="promo-step-header">
                    <div class="promo-step-num">2</div>
                    <div>
                        <h2 class="promo-step-title">Define tu alcance</h2>
                        <p class="promo-step-desc">¿A qué área geográfica quieres llegar?</p>
                    </div>
                </div>

                <div class="promo-reach-list">
                    <label class="promo-reach-option selected" id="reach-local">
                        <input type="radio" name="reach" value="local" checked hidden
                               onchange="selectReach(this.closest('.promo-reach-option'))">
                        <div class="promo-reach-left">
                            <span class="promo-reach-emoji">📍</span>
                            <div>
                                <span class="promo-reach-name">Local</span>
                                <span class="promo-reach-desc">Solo tu ciudad</span>
                            </div>
                        </div>
                        <span class="promo-reach-check"><i data-lucide="check" style="width:14px;height:14px;"></i></span>
                    </label>
                    <label class="promo-reach-option" id="reach-regional">
                        <input type="radio" name="reach" value="regional" hidden
                               onchange="selectReach(this.closest('.promo-reach-option'))">
                        <div class="promo-reach-left">
                            <span class="promo-reach-emoji">🗺️</span>
                            <div>
                                <span class="promo-reach-name">Regional</span>
                                <span class="promo-reach-desc">+50 km a la redonda · +40% visualizaciones</span>
                            </div>
                        </div>
                        <span class="promo-reach-check"><i data-lucide="check" style="width:14px;height:14px;"></i></span>
                    </label>
                    <label class="promo-reach-option" id="reach-state">
                        <input type="radio" name="reach" value="state" hidden
                               onchange="selectReach(this.closest('.promo-reach-option'))">
                        <div class="promo-reach-left">
                            <span class="promo-reach-emoji">🌎</span>
                            <div>
                                <span class="promo-reach-name">Todo el Estado</span>
                                <span class="promo-reach-desc">Máxima visibilidad</span>
                            </div>
                        </div>
                        <span class="promo-reach-check"><i data-lucide="check" style="width:14px;height:14px;"></i></span>
                    </label>
                </div>
            </div>

            {{-- Step 3: Duration --}}
            <div class="promo-section-card">
                <div class="promo-step-header">
                    <div class="promo-step-num">3</div>
                    <div>
                        <h2 class="promo-step-title">Duración de la campaña</h2>
                        <p class="promo-step-desc">Elige cuántos días quieres estar destacado.</p>
                    </div>
                </div>

                <div class="promo-duration-grid">
                    <label class="promo-duration-card" data-price="150" onclick="selectDuration(this, 150)">
                        <input type="radio" name="duration" value="7" hidden>
                        <span class="promo-duration-days">7</span>
                        <span class="promo-duration-label">Días</span>
                        <span class="promo-duration-price">$150 MXN</span>
                    </label>

                    <label class="promo-duration-card selected" data-price="250" onclick="selectDuration(this, 250)">
                        <input type="radio" name="duration" value="15" checked hidden>
                        <div class="promo-popular-badge">Popular</div>
                        <span class="promo-duration-days">15</span>
                        <span class="promo-duration-label">Días</span>
                        <span class="promo-duration-price">$250 MXN</span>
                    </label>

                    <label class="promo-duration-card" data-price="450" onclick="selectDuration(this, 450)">
                        <input type="radio" name="duration" value="30" hidden>
                        <span class="promo-duration-days">30</span>
                        <span class="promo-duration-label">Días</span>
                        <span class="promo-duration-price">$450 MXN</span>
                    </label>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: Summary panel ────────────────────────────── --}}
        <div class="promo-summary-col">
            <div class="promo-section-card promo-summary-card">
                <h3 class="promo-summary-title">Resumen del pedido</h3>

                <div class="promo-summary-rows">
                    <div class="promo-summary-row">
                        <span class="promo-summary-label">Contenido</span>
                        <span class="promo-summary-value">Mi Perfil</span>
                    </div>
                    <div class="promo-summary-row">
                        <span class="promo-summary-label">Alcance</span>
                        <span class="promo-summary-value">Local</span>
                    </div>
                    <div class="promo-summary-row">
                        <span class="promo-summary-label">Duración</span>
                        <span class="promo-summary-value">15 días</span>
                    </div>
                </div>

                <div class="promo-total-row">
                    <span>Total</span>
                    <span id="total-price">$250 MXN</span>
                </div>

                <button type="button" class="promo-submit-btn" onclick="Swal.fire({icon:'info', title:'Sistema de Pago', text:'La funcionalidad de pago seguro y activación inmediata estará disponible próximamente.', confirmButtonColor:'#6c3fc5'})">
                    <i data-lucide="zap" style="width:16px;height:16px;"></i>
                    Activar Promoción
                </button>
                <p class="promo-submit-note">
                    <i data-lucide="shield-check" style="width:12px;height:12px;"></i>
                    Pago seguro. Puedes cancelar antes de que inicie.
                </p>
            </div>

            {{-- What you get --}}
            <div class="promo-section-card promo-benefits-card">
                <h4 class="promo-benefits-title">¿Qué incluye?</h4>
                <ul class="promo-benefits-list">
                    <li><i data-lucide="trending-up" style="width:15px;height:15px;color:#16a34a;"></i> Posición destacada en búsquedas</li>
                    <li><i data-lucide="eye" style="width:15px;height:15px;color:#2563eb;"></i> Badge "Destacado" en tu perfil</li>
                    <li><i data-lucide="bell" style="width:15px;height:15px;color:#d97706;"></i> Notificaciones a clientes cercanos</li>
                    <li><i data-lucide="bar-chart-2" style="width:15px;height:15px;color:#6c3fc5;"></i> Estadísticas de visualizaciones</li>
                </ul>
            </div>
        </div>

    </div>

    <style>
        .promo-page-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 28px; padding-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
        }
        .promo-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .promo-page-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .promo-page-subtitle { font-size: 14px; color: #64748b; margin: 0; }
        .promo-secondary-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px; border-radius: 8px; background: #f1f5f9;
            color: #475569; font-size: 13px; font-weight: 600; text-decoration: none;
            border: 1.5px solid #e2e8f0; transition: all .2s; white-space: nowrap;
        }
        .promo-secondary-btn:hover { background: #e2e8f0; }

        /* Layout */
        .promo-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }
        .promo-section-card {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 16px; padding: 24px; margin-bottom: 16px;
        }

        /* Step header */
        .promo-step-header { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 20px; }
        .promo-step-num {
            width: 32px; height: 32px; border-radius: 10px;
            background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; font-size: 14px; font-weight: 800;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .promo-step-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 3px; }
        .promo-step-desc { font-size: 13px; color: #94a3b8; margin: 0; }

        /* Content options */
        .promo-option-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .promo-option {
            display: flex; align-items: center; gap: 14px;
            padding: 16px; border: 1.5px solid #e2e8f0;
            border-radius: 12px; cursor: pointer; transition: all .2s;
        }
        .promo-option:hover { border-color: #a78bfa; }
        .promo-option.selected { border-color: #6c3fc5; background: rgba(108,63,197,.04); box-shadow: 0 0 0 3px rgba(108,63,197,.08); }
        .promo-option-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .promo-option-title { display: block; font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
        .promo-option-subtitle { display: block; font-size: 12px; color: #94a3b8; }

        /* Reach */
        .promo-reach-list { display: flex; flex-direction: column; gap: 10px; }
        .promo-reach-option {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 16px; border: 1.5px solid #e2e8f0;
            border-radius: 12px; cursor: pointer; transition: all .2s;
        }
        .promo-reach-option:hover { border-color: #a78bfa; }
        .promo-reach-option.selected { border-color: #6c3fc5; background: rgba(108,63,197,.04); }
        .promo-reach-left { display: flex; align-items: center; gap: 12px; }
        .promo-reach-emoji { font-size: 20px; }
        .promo-reach-name { display: block; font-size: 14px; font-weight: 600; color: #0f172a; }
        .promo-reach-desc { display: block; font-size: 12px; color: #94a3b8; }
        .promo-reach-check {
            width: 22px; height: 22px; border-radius: 50%;
            background: #6c3fc5; color: #fff;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity .2s;
        }
        .promo-reach-option.selected .promo-reach-check { opacity: 1; }

        /* Duration */
        .promo-duration-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        .promo-duration-card {
            position: relative; text-align: center; padding: 20px 12px;
            border: 1.5px solid #e2e8f0; border-radius: 14px;
            cursor: pointer; transition: all .2s; display: flex;
            flex-direction: column; align-items: center; gap: 4px;
        }
        .promo-duration-card:hover { border-color: #a78bfa; }
        .promo-duration-card.selected { border-color: #6c3fc5; background: rgba(108,63,197,.04); box-shadow: 0 0 0 3px rgba(108,63,197,.08); }
        .promo-popular-badge {
            position: absolute; top: -11px; left: 50%; transform: translateX(-50%);
            background: linear-gradient(90deg, #f97316, #ef4444);
            color: #fff; font-size: 10px; font-weight: 700;
            padding: 3px 10px; border-radius: 999px; white-space: nowrap;
        }
        .promo-duration-days { font-size: 26px; font-weight: 900; color: #0f172a; }
        .promo-duration-label { font-size: 12px; color: #94a3b8; }
        .promo-duration-price { font-size: 13px; font-weight: 700; color: #6c3fc5; margin-top: 4px; }

        /* Summary panel */
        .promo-summary-card { margin-bottom: 16px; }
        .promo-summary-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 20px; }
        .promo-summary-rows { display: flex; flex-direction: column; gap: 0; margin-bottom: 16px; }
        .promo-summary-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 11px 0; border-bottom: 1px solid #f8fafc;
        }
        .promo-summary-label { font-size: 13px; color: #64748b; }
        .promo-summary-value { font-size: 13px; font-weight: 600; color: #0f172a; }
        .promo-total-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 16px; background: #f8fafc; border-radius: 10px;
            font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 16px;
        }
        #total-price { font-size: 20px; color: #6c3fc5; }
        .promo-submit-btn {
            width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;
            padding: 13px; background: linear-gradient(135deg, #6c3fc5, #2f93f5);
            color: #fff; border: none; border-radius: 10px; font-size: 14px; font-weight: 700;
            cursor: pointer; box-shadow: 0 4px 16px rgba(108,63,197,.3); transition: opacity .2s;
        }
        .promo-submit-btn:hover { opacity: .9; }
        .promo-submit-note {
            display: flex; align-items: center; justify-content: center; gap: 5px;
            text-align: center; font-size: 11.5px; color: #94a3b8; margin: 10px 0 0;
        }

        /* Benefits */
        .promo-benefits-card { padding: 20px 24px; }
        .promo-benefits-title { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 14px; }
        .promo-benefits-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
        .promo-benefits-list li { display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: #334155; }

        @media (max-width: 860px) {
            .promo-layout { grid-template-columns: 1fr; }
            .promo-option-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .promo-duration-grid { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        function selectOption(gridId, el) {
            document.querySelectorAll('.promo-option').forEach(o => o.classList.remove('selected'));
            el.classList.add('selected');
            el.querySelector('input').checked = true;
        }

        function selectReach(el) {
            document.querySelectorAll('.promo-reach-option').forEach(o => o.classList.remove('selected'));
            el.classList.add('selected');
            el.querySelector('input').checked = true;
        }

        function selectDuration(el, price) {
            document.querySelectorAll('.promo-duration-card').forEach(o => o.classList.remove('selected'));
            el.classList.add('selected');
            el.querySelector('input').checked = true;
            document.getElementById('total-price').textContent = '$' + price.toLocaleString() + ' MXN';
        }
    </script>

@endsection
