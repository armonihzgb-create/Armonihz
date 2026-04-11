@extends('layouts.dashboard')

@section('dashboard-content')
    
    @if(session('success'))
        <div class="pm-alert pm-alert-success">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="pm-alert pm-alert-error">
            <i data-lucide="alert-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    @forelse($promotions as $promo)
        @if($loop->first)
            {{-- Renderizar la tabla solo si hay elementos --}}
            <div class="pm-table-header">
                <h1 class="pm-title">Mis Promociones</h1>
                <a href="{{ route('promotions.create') }}" class="pm-new-btn">
                    <i data-lucide="plus"></i> Nueva Promoción
                </a>
            </div>

            <div class="pm-table-container">
                <table class="pm-table">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Estado</th>
                            <th>Subido el</th>
                            <th>Vigencia</th>
                        </tr>
                    </thead>
                    <tbody>
        @endif

                        <tr>
                            <td>
                                <strong>Plan {{ $promo->plan_type }}</strong>
                            </td>
                            <td>
                                @if($promo->status === 'pendiente')
                                    <span class="pm-badge pm-badge-warning">En revisión</span>
                                @elseif($promo->status === 'aprobado' && $promo->is_active)
                                    <span class="pm-badge pm-badge-success">Activo</span>
                                @else
                                    <span class="pm-badge pm-badge-neutral">Finalizado</span>
                                @endif
                            </td>
                            <td>{{ $promo->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($promo->valid_until)
                                    Hasta el {{ $promo->valid_until->format('d/m/Y') }}
                                @else
                                    <span style="color:#94a3b8; font-size:13px;">Por definir</span>
                                @endif
                            </td>
                        </tr>

        @if($loop->last)
                    </tbody>
                </table>
            </div>
        @endif

    @empty
        {{-- Estado Vacío (Tu diseño actualizado sin el próximamente) --}}
        <div class="pm-empty-container">
            <div class="pm-empty-card">
                <div class="pm-empty-icon-wrap">
                    <i data-lucide="zap" class="pm-empty-icon"></i>
                </div>
                
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

                <a href="{{ route('promotions.create') }}" class="pm-preview-btn">
                    Ver los Planes Disponibles
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>
        </div>
    @endforelse

    @section('head')
<style>
        /* Alertas */
        .pm-alert { display: flex; align-items: center; gap: 12px; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; font-size: 14.5px; }
        .pm-alert i { width: 20px; height: 20px; flex-shrink: 0; }
        .pm-alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .pm-alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        /* Estilos de la Tabla */
        .pm-table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .pm-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .pm-new-btn { display: inline-flex; align-items: center; gap: 8px; background: #0f172a; color: #fff; padding: 10px 16px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: background 0.2s; }
        .pm-new-btn:hover { background: #1e293b; }
        .pm-new-btn i { width: 16px; height: 16px; }

        .pm-table-container { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; overflow-x: auto; -webkit-overflow-scrolling: touch; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .pm-table { width: 100%; min-width: 650px; border-collapse: collapse; text-align: left; }
        .pm-table th { background: #f8fafc; padding: 16px 24px; font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
        .pm-table td { padding: 16px 24px; font-size: 14.5px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .pm-table tr:last-child td { border-bottom: none; }
        .pm-table td strong { color: #0f172a; }

        /* Badges de Estado */
        .pm-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; letter-spacing: 0.3px; }
        .pm-badge-warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .pm-badge-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .pm-badge-neutral { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* Estilos del Estado Vacío */
        .pm-empty-container { display: flex; align-items: center; justify-content: center; min-height: 60vh; padding: 20px; }
        .pm-empty-card { background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 24px; max-width: 580px; width: 100%; padding: 48px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.03); position: relative; overflow: hidden; }
        .pm-empty-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #6c3fc5, #3b82f6, #06b6d4); }
        .pm-empty-icon-wrap { width: 84px; height: 84px; background: linear-gradient(135deg, rgba(108,63,197,0.1), rgba(59,130,246,0.1)); border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; box-shadow: inset 0 0 0 1px rgba(108,63,197,0.15); }
        .pm-empty-icon { width: 40px; height: 40px; color: #6c3fc5; }
        .pm-empty-title { font-size: 28px; font-weight: 900; color: #0f172a; margin: 0 0 12px; line-height: 1.2; letter-spacing: -0.5px; }
        .pm-empty-subtitle { font-size: 15px; color: #64748b; margin: 0 0 36px; line-height: 1.5; }
        
        .pm-benefits-grid { display: flex; flex-direction: column; gap: 14px; text-align: left; background: #f8fafc; padding: 24px; border-radius: 16px; margin-bottom: 30px; border: 1px solid #f1f5f9; }
        .pm-benefit-item { display: flex; align-items: center; gap: 12px; font-size: 14.5px; font-weight: 600; color: #334155; }
        .pm-benefit-item i { width: 18px; height: 18px; color: #3b82f6; }

        .pm-preview-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #0f172a; color: #fff; padding: 16px; border-radius: 14px; font-size: 15px; font-weight: 700; text-decoration: none; transition: all 0.2s; box-shadow: 0 10px 20px rgba(15,23,42,0.15); }
        .pm-preview-btn:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 12px 24px rgba(15,23,42,0.2); }
        .pm-preview-btn i { width: 16px; height: 16px; }

        @media (max-width: 600px) {
            .pm-empty-card { padding: 30px 20px; }
            .pm-empty-title { font-size: 24px; }
            .pm-table-header { flex-direction: column; align-items: flex-start; gap: 16px; }
            .pm-table-container { overflow-x: auto; }
        }
</style>
@endsection

@endsection