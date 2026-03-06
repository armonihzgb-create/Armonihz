@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <a href="{{ route('castings.index') }}" class="back-link">
            <i data-lucide="arrow-left"></i> Volver a Castings
        </a>
    </div>

    <div class="dashboard-box casting-detail">
        <div class="detail-header">
            <span class="badge purple">Boda</span>
            <h1>Banda versátil para Boda Civil</h1>
            <p class="posting-meta">Publicado por <strong>Ana G.</strong> hace 2 horas • Guadalajara, Jal.</p>
        </div>

        <div class="detail-grid">
            <div class="detail-main">
                <h3>Descripción del evento</h3>
                <p>Buscamos una banda versátil con experiencia en bodas para amenizar nuestra recepción civil. El repertorio debe incluir baladas, pop actual y algo de cumbia para bailar al final. Indispensable equipo de sonido propio.</p>
                
                <h3>Requisitos</h3>
                <ul class="req-list">
                    <li><i data-lucide="check"></i> Equipo de sonido propio para 100 personas</li>
                    <li><i data-lucide="check"></i> Repertorio variado (3 horas)</li>
                    <li><i data-lucide="check"></i> Vestimenta formal</li>
                </ul>
            </div>
            
            <div class="detail-sidebar">
                <div class="info-card">
                    <div class="info-item">
                        <label>Fecha</label>
                        <span>15 Oct, 2026</span>
                    </div>
                    <div class="info-item">
                        <label>Horario</label>
                        <span>19:00 - 22:00 hrs</span>
                    </div>
                    <div class="info-item">
                        <label>Presupuesto</label>
                        <span class="price">$15,000 - $20,000</span>
                    </div>
                    
                    <button class="primary-btn full-width mt-4">
                        Aplicar ahora
                    </button>
                    <button class="secondary-btn full-width mt-2">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .back-link { display: flex; align-items: center; gap: 8px; color: var(--text-dim); text-decoration: none; font-weight: 500; margin-bottom: 16px; }
        .back-link:hover { color: var(--accent-blue); }
        
        .casting-detail { padding: 40px; }
        .detail-header { margin-bottom: 32px; border-bottom: 1px solid var(--border-light); padding-bottom: 24px; }
        .detail-header h1 { margin: 12px 0 8px 0; font-size: 28px; }
        .posting-meta { color: var(--text-dim); font-size: 14px; }
        
        .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }
        
        .detail-main h3 { font-size: 18px; margin-bottom: 12px; margin-top: 24px; }
        .detail-main p { line-height: 1.6; color: var(--text-main); }
        
        .req-list { list-style: none; padding: 0; }
        .req-list li { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .req-list i { color: var(--accent-green); width: 18px; height: 18px; }
        
        .info-card { background: #f9fafb; padding: 24px; border-radius: 12px; }
        .info-item { margin-bottom: 16px; }
        .info-item label { display: block; font-size: 12px; color: var(--text-dim); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .info-item span { font-size: 16px; font-weight: 500; }
        .info-item span.price { font-size: 20px; font-weight: 700; color: var(--accent-green); }
        
        .full-width { width: 100%; justify-content: center; }
        .mt-4 { margin-top: 16px; }
        .mt-2 { margin-top: 8px; }
        
        @media (max-width: 768px) {
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection
