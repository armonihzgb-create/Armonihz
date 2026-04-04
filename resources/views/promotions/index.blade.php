@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="pm-empty-container">
        <div class="pm-empty-card">
            <div class="pm-empty-icon-wrap">
                <i data-lucide="zap" class="pm-empty-icon"></i>
            </div>
            
            <div class="pm-coming-soon-badge">PRÓXIMAMENTE</div>
            
            <h1 class="pm-empty-title">Multiplica tus contrataciones</h1>
            <p class="pm-empty-subtitle">
                Nuestro sistema de <strong>Promoción de Perfil</strong> te permitirá destacar en la cima de los resultados de búsqueda, atrayendo hasta 4x más clientes.
            </p>

            <div class="pm-benefits-grid">
                <div class="pm-benefit-item">
                    <i data-lucide="trending-up"></i>
                    <span>Aparece primero en tu ciudad</span>
                </div>
                <div class="pm-benefit-item">
                    <i data-lucide="star"></i>
                    <span>Insignia "Músico Destacado"</span>
                </div>
                 <div class="pm-benefit-item">
                    <i data-lucide="mouse-pointer-click"></i>
                    <span>Gana más clicks y vistas</span>
                </div>
            </div>

            <div class="pm-notice-box">
                <i data-lucide="shield-check"></i>
                <div>
                    <strong>Sistema de Pago</strong>
                    <p>La funcionalidad de pago seguro y activación inmediata estará disponible próximamente en una futura actualización.</p>
                </div>
            </div>

            <a href="{{ route('promotions.create') }}" class="pm-preview-btn">
                Ver los Planes Disponibles
                <i data-lucide="arrow-right"></i>
            </a>
        </div>
    </div>

    <style>
        .pm-empty-container { display: flex; align-items: center; justify-content: center; min-height: 75vh; padding: 20px; }
        .pm-empty-card { 
            background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 24px;
            max-width: 580px; width: 100%; padding: 48px; text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            position: relative; overflow: hidden;
        }
        .pm-empty-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px;
            background: linear-gradient(90deg, #6c3fc5, #3b82f6, #06b6d4);
        }
        .pm-empty-icon-wrap {
            width: 84px; height: 84px; background: linear-gradient(135deg, rgba(108,63,197,0.1), rgba(59,130,246,0.1));
            border-radius: 24px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px; box-shadow: inset 0 0 0 1px rgba(108,63,197,0.15);
        }
        .pm-empty-icon { width: 40px; height: 40px; color: #6c3fc5; }
        .pm-coming-soon-badge {
            display: inline-block; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;
            font-size: 11px; font-weight: 800; padding: 6px 14px; border-radius: 999px;
            letter-spacing: 0.5px; margin-bottom: 20px;
        }
        .pm-empty-title { font-size: 28px; font-weight: 900; color: #0f172a; margin: 0 0 12px; line-height: 1.2; letter-spacing: -0.5px; }
        .pm-empty-subtitle { font-size: 15px; color: #64748b; margin: 0 0 36px; line-height: 1.5; }
        
        .pm-benefits-grid { display: flex; flex-direction: column; gap: 14px; text-align: left; background: #f8fafc; padding: 24px; border-radius: 16px; margin-bottom: 30px; border: 1px solid #f1f5f9; }
        .pm-benefit-item { display: flex; align-items: center; gap: 12px; font-size: 14.5px; font-weight: 600; color: #334155; }
        .pm-benefit-item i { width: 18px; height: 18px; color: #3b82f6; }

        .pm-notice-box {
            display: flex; gap: 12px; align-items: flex-start; text-align: left;
            background: #fefce8; border: 1px dashed #fef08a; padding: 18px; border-radius: 12px; margin-bottom: 30px;
        }
        .pm-notice-box i { width: 22px; height: 22px; color: #ca8a04; flex-shrink: 0; margin-top: 2px; }
        .pm-notice-box strong { display: block; font-size: 14px; font-weight: 700; color: #854d0e; margin-bottom: 4px; }
        .pm-notice-box p { margin: 0; font-size: 13px; color: #a16207; line-height: 1.4; }

        .pm-preview-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; background: #0f172a; color: #fff; padding: 16px;
            border-radius: 14px; font-size: 15px; font-weight: 700; text-decoration: none;
            transition: all 0.2s; box-shadow: 0 10px 20px rgba(15,23,42,0.15);
        }
        .pm-preview-btn:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 12px 24px rgba(15,23,42,0.2); }
        .pm-preview-btn i { width: 16px; height: 16px; }

        @media (max-width: 600px) {
            .pm-empty-card { padding: 30px 20px; }
            .pm-empty-title { font-size: 24px; }
        }
    </style>
@endsection
