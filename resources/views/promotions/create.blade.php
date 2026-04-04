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

    {{-- Coming Soon Notice --}}
    <div class="promo-notice-banner">
        <div class="promo-notice-icon">
            <i data-lucide="shopping-bag" style="width:24px;height:24px;"></i>
        </div>
        <div class="promo-notice-content">
            <h3>Sistema de Pago Próximamente</h3>
            <p>La funcionalidad de pago seguro y activación inmediata de campañas estará disponible muy pronto. Actualmente los planes de suscripción están en fase de previsualización.</p>
        </div>
    </div>

    <div class="promo-pricing-grid">
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
                <button class="promo-select-btn disabled-btn" onclick="showComingSoon()">Pagar Plan</button>
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
                <button class="promo-select-btn popular-btn disabled-btn" onclick="showComingSoon()">Pagar Plan</button>
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
                <button class="promo-select-btn disabled-btn" onclick="showComingSoon()">Pagar Plan</button>
            </div>
        </div>
    </div>

    <style>
        .promo-page-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9;
        }
        .promo-eyebrow {
            display: flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 800;
            letter-spacing: .08em; color: #d97706; text-transform: uppercase; margin-bottom: 6px;
        }
        .promo-page-title { font-size: 26px; font-weight: 900; color: #0f172a; margin: 0 0 6px; letter-spacing: -0.5px; }
        .promo-page-subtitle { font-size: 15px; color: #64748b; margin: 0; }
        .promo-secondary-btn {
            display: inline-flex; align-items: center; gap: 7px; padding: 10px 18px; border-radius: 10px;
            background: #fff; color: #475569; font-size: 13.5px; font-weight: 700; text-decoration: none;
            border: 1.5px solid #e2e8f0; transition: all .2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .promo-secondary-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }

        .promo-notice-banner {
            display: flex; gap: 18px; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
            border: 1px solid #bfdbfe; border-radius: 20px; padding: 22px 28px; margin-bottom: 40px;
            align-items: center; box-shadow: 0 4px 15px rgba(37,99,235,0.05);
        }
        .promo-notice-icon { width: 52px; height: 52px; background: #2563eb; color: #fff; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 6px 16px rgba(37,99,235,0.25); }
        .promo-notice-content h3 { font-size: 16px; font-weight: 800; color: #1e3a8a; margin: 0 0 4px; }
        .promo-notice-content p { font-size: 14px; color: #3b82f6; margin: 0; line-height: 1.5; font-weight: 500; }

        .promo-pricing-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; max-width: 1000px; margin: 0 auto 40px;
        }
        .promo-pricing-card {
            background: #fff; border: 1.5px solid #e2e8f0; border-radius: 24px;
            overflow: hidden; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s;
            display: flex; flex-direction: column;
        }
        .promo-pricing-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.06); }
        
        .promo-pricing-card.popular {
            border: 2.5px solid #6c3fc5; position: relative; box-shadow: 0 12px 32px rgba(108,63,197,0.12);
            transform: scale(1.03); z-index: 10;
        }
        .promo-pricing-card.popular:hover { transform: scale(1.03) translateY(-6px); box-shadow: 0 24px 48px rgba(108,63,197,0.18); }
        
        .promo-popular-badge {
            background: linear-gradient(90deg, #6c3fc5, #3b82f6); color: #fff; text-align: center; font-size: 11.5px;
            font-weight: 800; letter-spacing: 1px; padding: 8px; text-transform: uppercase;
        }

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

        .promo-select-btn {
            width: 100%; padding: 16px; border-radius: 14px; font-size: 15px; font-weight: 800;
            text-align: center; cursor: pointer; transition: all 0.2s; border: none; letter-spacing: 0.5px;
        }
        
        .promo-select-btn:not(.popular-btn) { background: #f1f5f9; color: #0f172a; }
        .promo-select-btn.popular-btn { background: #6c3fc5; color: #fff; box-shadow: 0 6px 16px rgba(108,63,197,0.3); }
        
        .disabled-btn { opacity: 0.8; cursor: pointer; }
        .disabled-btn:not(.popular-btn):hover { background: #e2e8f0; }

        @media (max-width: 950px) {
            .promo-pricing-grid { grid-template-columns: 1fr; max-width: 400px; gap: 32px; margin-bottom: 30px; }
            .promo-pricing-card.popular { transform: none; }
            .promo-pricing-card.popular:hover { transform: translateY(-4px); }
            .promo-notice-banner { flex-direction: column; text-align: center; padding: 20px; }
            .promo-notice-icon { margin-bottom: 8px; }
        }
    </style>

    <script>
        function showComingSoon() {
            Swal.fire({
                icon: 'info',
                title: 'Sistema de Pago',
                text: 'La funcionalidad de pago seguro y activación inmediata estará disponible próximamente.',
                confirmButtonColor: '#6c3fc5',
                confirmButtonText: 'Entendido',
                customClass: {
                    container: 'promo-swal'
                }
            });
        }
    </script>
@endsection
