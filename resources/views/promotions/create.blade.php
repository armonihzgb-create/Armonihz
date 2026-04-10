@extends('layouts.dashboard')

@section('dashboard-content')

    <div class="promo-page-header">
        <div>
            <div class="promo-eyebrow">
                <i data-lucide="star" style="width:14px;height:14px;color:#d97706;"></i>
                VENTAJAS PREMIUM
            </div>
            <h1 class="promo-page-title">Elige tu Plan de Promoción</h1>
            <p class="promo-page-subtitle">Destaca por encima de otros músicos en tu área y recibe más solicitudes.</p>
        </div>
        <a href="{{ route('promotions.index') }}" class="promo-secondary-btn">
            <i data-lucide="arrow-left" style="width:15px;height:15px;"></i>
            Volver
        </a>
    </div>

    {{-- Errores de validación --}}
    @if($errors->any())
        <div class="promo-error-banner">
            <i data-lucide="alert-circle" style="width:20px;height:20px;"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="promo-pricing-grid">
        {{-- Plan Flash (1 Día) --}}
        <div class="promo-pricing-card">
            <div class="promo-pricing-header">
                <h3>Flash</h3>
                <div class="promo-price"><span>$</span>29<small>MXN</small></div>
                <p>Ideal para eventos de último minuto.</p>
            </div>
            <div class="promo-pricing-body">
                <ul>
                    <li><i data-lucide="check"></i> <strong>1 día</strong> de promoción</li>
                    <li><i data-lucide="check"></i> Posicionamiento rápido</li>
                    <li><i data-lucide="check"></i> Insignia de "Destacado"</li>
                </ul>
                <button class="promo-select-btn" onclick="openPaymentModal('Flash')">Pagar Plan</button>
            </div>
        </div>
        {{-- Plan Basic --}}
        <div class="promo-pricing-card">
            <div class="promo-pricing-header">
                <h3>Básico</h3>
                <div class="promo-price"><span>$</span>99<small>MXN</small></div>
                <p>Ideal para probar el impacto.</p>
            </div>
            <div class="promo-pricing-body">
                <ul>
                    <li><i data-lucide="check"></i> <strong>7 días</strong> de promoción</li>
                    <li><i data-lucide="check"></i> Posicionamiento superior</li>
                    <li><i data-lucide="check"></i> Insignia de "Destacado"</li>
                    <li><i data-lucide="check"></i> Estadísticas básicas</li>
                </ul>
                <button class="promo-select-btn" onclick="openPaymentModal('Basico')">Pagar Plan</button>
            </div>
        </div>

        {{-- Plan Standard --}}
        <div class="promo-pricing-card popular">
            <div class="promo-popular-badge">MÁS ELEGIDO</div>
            <div class="promo-pricing-header">
                <h3>Estándar</h3>
                <div class="promo-price"><span>$</span>299<small>MXN</small></div>
                <p>Mantén un flujo constante de clientes.</p>
            </div>
            <div class="promo-pricing-body">
                <ul>
                    <li><i data-lucide="check"></i> <strong>30 días</strong> de promoción</li>
                    <li><i data-lucide="check"></i> Posicionamiento prioritario</li>
                    <li><i data-lucide="check"></i> Insignia de "Destacado"</li>
                    <li><i data-lucide="check"></i> Ahorras $97 MXN</li>
                </ul>
                <button class="promo-select-btn popular-btn" onclick="openPaymentModal('Estandar')">Pagar Plan</button>
            </div>
        </div>

        {{-- Plan Premium --}}
        <div class="promo-pricing-card">
            <div class="promo-pricing-header">
                <h3>Premium</h3>
                <div class="promo-price"><span>$</span>699<small>MXN</small></div>
                <p>Dedicación completa para profesionales.</p>
            </div>
            <div class="promo-pricing-body">
                <ul>
                    <li><i data-lucide="check"></i> <strong>90 días</strong> de promoción</li>
                    <li><i data-lucide="check"></i> Posicionamiento máximo absoluto</li>
                    <li><i data-lucide="check"></i> Insignia de "Destacado" dorada</li>
                    <li><i data-lucide="check"></i> Ahorras $488 MXN</li>
                </ul>
                <button class="promo-select-btn" onclick="openPaymentModal('Premium')">Pagar Plan</button>
            </div>
        </div>
    </div>

    {{-- Modal de Pago (Oculto por defecto) --}}
    <div id="paymentModal" class="promo-modal-overlay" style="display: none;">
        <div class="promo-modal-content">
            <div class="promo-modal-header">
                <h3>Instrucciones de Pago</h3>
                <button onclick="closePaymentModal()" class="promo-modal-close"><i data-lucide="x"></i></button>
            </div>
            
            <form action="{{ route('promotions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="plan_type" id="selected_plan_input" value="">

                <div class="promo-modal-body">
                    <p class="promo-modal-text">
                        Para activar tu plan <strong><span id="plan_name_display"></span></strong>, realiza una transferencia interbancaria con los siguientes datos:
                    </p>
                    
                    <div class="promo-bank-details">
                        <div class="bank-row"><span>Banco:</span> <strong>BBVA</strong></div>
                        <div class="bank-row"><span>CLABE:</span> <strong>123456789012345678</strong></div>
                        <div class="bank-row"><span>Beneficiario:</span> <strong>Armonihz App</strong></div>
                        <div class="bank-row"><span>Concepto:</span> <strong id="concepto_pago">Pago Promocion</strong></div>
                    </div>

                    <div class="promo-file-upload">
                        <label for="receipt">Sube tu comprobante de pago (Foto o PDF)</label>
                        <input type="file" name="receipt" id="receipt" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                </div>

                <div class="promo-modal-footer">
                    <button type="button" onclick="closePaymentModal()" class="promo-btn-cancel">Cancelar</button>
                    <button type="submit" class="promo-btn-submit">Enviar Comprobante</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* (Conserva aquí tus estilos anteriores para la cabecera y tarjetas) */
        .promo-page-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9; }
        .promo-eyebrow { display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 800; letter-spacing: .08em; color: #d97706; text-transform: uppercase; margin-bottom: 6px; }
        .promo-page-title { font-size: 26px; font-weight: 900; color: #0f172a; margin: 0 0 6px; letter-spacing: -0.5px; }
        .promo-page-subtitle { font-size: 15px; color: #64748b; margin: 0; }
        .promo-secondary-btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 18px; border-radius: 10px; background: #fff; color: #475569; font-size: 13.5px; font-weight: 700; text-decoration: none; border: 1.5px solid #e2e8f0; transition: all .2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .promo-secondary-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
        
        .promo-error-banner { display: flex; align-items: center; gap: 12px; background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 600; }

       .promo-pricing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; max-width: 1200px; margin: 0 auto 40px; }
        .promo-pricing-card { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 24px; overflow: hidden; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s; display: flex; flex-direction: column; }
        .promo-pricing-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.06); }
        .promo-pricing-card.popular { border: 2.5px solid #6c3fc5; position: relative; box-shadow: 0 12px 32px rgba(108,63,197,0.12); transform: scale(1.03); z-index: 10; }
        .promo-pricing-card.popular:hover { transform: scale(1.03) translateY(-6px); box-shadow: 0 24px 48px rgba(108,63,197,0.18); }
        .promo-popular-badge { background: linear-gradient(90deg, #6c3fc5, #3b82f6); color: #fff; text-align: center; font-size: 11.5px; font-weight: 800; letter-spacing: 1px; padding: 8px; text-transform: uppercase; }
        .promo-pricing-header { padding: 36px 28px 24px; border-bottom: 1px solid #f1f5f9; text-align: center; }
        .promo-pricing-header h3 { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 16px; }
        .promo-price { font-size: 48px; font-weight: 900; color: #0f172a; line-height: 1; display: flex; justify-content: center; align-items: flex-start; gap: 4px; margin-bottom: 16px; letter-spacing: -1px; }
        .promo-price span { font-size: 22px; font-weight: 700; color: #64748b; margin-top: 6px; }
        .promo-price small { font-size: 15px; font-weight: 700; color: #64748b; align-self: flex-end; margin-bottom: 8px; }
        .promo-pricing-header p { font-size: 14px; color: #64748b; margin: 0; font-weight: 500; }
        .promo-pricing-body { padding: 28px; flex-grow: 1; display: flex; flex-direction: column; }
        .promo-pricing-body ul { list-style: none; padding: 0; margin: 0 0 32px; flex-grow: 1; display: flex; flex-direction: column; gap: 16px; }
        .promo-pricing-body li { display: flex; align-items: flex-start; gap: 12px; font-size: 14.5px; color: #334155; line-height: 1.4; font-weight: 500;}
        .promo-pricing-body li i { width: 18px; height: 18px; color: #10b981; flex-shrink: 0; stroke-width: 3px; }
        .promo-pricing-body li strong { font-weight: 800; color: #0f172a; }
        .promo-select-btn { width: 100%; padding: 16px; border-radius: 14px; font-size: 15px; font-weight: 800; text-align: center; cursor: pointer; transition: all 0.2s; border: none; letter-spacing: 0.5px; background: #f1f5f9; color: #0f172a; }
        .promo-select-btn:hover { background: #e2e8f0; }
        .promo-select-btn.popular-btn { background: #6c3fc5; color: #fff; box-shadow: 0 6px 16px rgba(108,63,197,0.3); }
        .promo-select-btn.popular-btn:hover { background: #5b33a8; }

        /* Estilos del Modal */
        .promo-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; }
        .promo-modal-content { background: #fff; width: 100%; max-width: 500px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; }
        .promo-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f1f5f9; }
        .promo-modal-header h3 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; }
        .promo-modal-close { background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; border-radius: 6px; transition: background 0.2s; }
        .promo-modal-close:hover { background: #f1f5f9; color: #0f172a; }
        .promo-modal-body { padding: 24px; }
        .promo-modal-text { font-size: 14.5px; color: #475569; margin: 0 0 20px; line-height: 1.5; }
        .promo-bank-details { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
        .bank-row { display: flex; justify-content: space-between; font-size: 14px; padding: 6px 0; border-bottom: 1px dashed #cbd5e1; }
        .bank-row:last-child { border-bottom: none; }
        .bank-row span { color: #64748b; }
        .bank-row strong { color: #0f172a; font-weight: 700; }
        .promo-file-upload { display: flex; flex-direction: column; gap: 8px; }
        .promo-file-upload label { font-size: 14px; font-weight: 600; color: #1e293b; }
        .promo-file-upload input[type="file"] { border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; font-size: 14px; color: #475569; background: #fff; }
        .promo-modal-footer { padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 12px; }
        .promo-btn-cancel { padding: 10px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .promo-btn-cancel:hover { background: #f1f5f9; }
        .promo-btn-submit { padding: 10px 18px; border-radius: 8px; border: none; background: #2563eb; color: #fff; font-weight: 600; cursor: pointer; transition: background 0.2s; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); }
        .promo-btn-submit:hover { background: #1d4ed8; }

      @media (max-width: 1100px) {
            .promo-pricing-grid { grid-template-columns: repeat(2, 1fr); max-width: 800px; gap: 32px; }
            .promo-pricing-card.popular { transform: none; }
            .promo-pricing-card.popular:hover { transform: translateY(-4px); }
        }
        @media (max-width: 650px) {
            .promo-pricing-grid { grid-template-columns: 1fr; max-width: 400px; }
        }
    </style>

    <script>
        function openPaymentModal(planType) {
            // Actualizar input oculto y textos
            document.getElementById('selected_plan_input').value = planType;
            document.getElementById('plan_name_display').innerText = planType;
            document.getElementById('concepto_pago').innerText = 'Promo ' + planType + ' {{ Auth::user()->id ?? "" }}';
            
            // Mostrar modal
            document.getElementById('paymentModal').style.display = 'flex';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }
    </script>
@endsection