@extends('layouts.admin')

@section('admin-content')

    <div class="page-header-premium">
        <div class="header-info">
            <h2>Panel Administrativo <i data-lucide="layout-dashboard" style="color: #6366f1; width: 26px; height: 26px; opacity: 0.2;"></i></h2>
            <p>Gestión centralizada y supervisión de la red Armonihz</p>
        </div>
        <div class="header-date">
            <span class="date-badge">
                <i data-lucide="calendar"></i>
                {{ \Carbon\Carbon::now()->locale('es')->isoFormat('ddd, D MMM YYYY') }}
            </span>
        </div>
    </div>

    {{-- TARJETAS DE ESTADÍSTICAS --}}
    <div class="stats-grid-premium">
        <div class="stat-card-fancy indigo">
            <div class="stat-content">
                <div class="stat-icon-bg">
                    <i data-lucide="users"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value">{{ $totalMusicians }}</span>
                    <span class="stat-label">Músicos Registrados</span>
                </div>
            </div>
            <div class="stat-footer">
                <p>Crecimiento total en plataforma</p>
            </div>
        </div>

        <div class="stat-card-fancy amber clickable" onclick="window.location='{{ route('admin.musicians.index', ['status' => 'pending']) }}'">
            <div class="stat-content">
                <div class="stat-icon-bg">
                    <i data-lucide="clock-4"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value">{{ $pendingMusiciansCount }}</span>
                    <span class="stat-label">Perfiles Pendientes</span>
                </div>
            </div>
            <div class="stat-footer">
                <p>Requieren validación manual &rarr;</p>
            </div>
        </div>

        <div class="stat-card-fancy emerald">
            <div class="stat-content">
                <div class="stat-icon-bg">
                    <i data-lucide="party-popper"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value">{{ $totalCompletedEvents }}</span>
                    <span class="stat-label">Eventos Cerrados</span>
                </div>
            </div>
            <div class="stat-footer">
                <p>Métricas de éxito acumuladas</p>
            </div>
        </div>

        <div class="stat-card-fancy violet">
            <div class="stat-content">
                <div class="stat-icon-bg">
                    <i data-lucide="smartphone"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-value">{{ $totalClients }}</span>
                    <span class="stat-label">Clientes App</span>
                </div>
            </div>
            <div class="stat-footer">
                <p>Usuarios registrados vía móvil</p>
            </div>
        </div>
    </div>

    {{-- TABLA DE GESTIÓN --}}
    <div class="table-container-premium shadow-sm">
        <div class="box-header-premium">
            <div class="header-main">
                <h3>Músicos Recientes</h3>
                <p>Últimos perfiles sincronizados en la plataforma</p>
            </div>
            <div class="header-actions">
                <div class="search-group-premium">
                    <input
                        type="text"
                        id="dashboard-search"
                        value="{{ $search ?? '' }}"
                        placeholder="Buscar músico..."
                        class="fancy-search"
                    >
                    <button onclick="goToSearch()" class="search-trigger">
                        <i data-lucide="search"></i>
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.dashboard') }}" class="clear-search" title="Limpiar búsqueda">&times;</a>
                    @endif
                </div>
                <a href="{{ route('admin.musicians.index') }}" class="view-all-link">Ver todo <i data-lucide="arrow-right"></i></a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Identidad Artística</th>
                        <th>Ubicación</th>
                        <th>Género</th>
                        <th>Nivel de Acceso</th>
                        <th style="text-align: right;">Operación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentMusicians as $m)
                    <tr>
                        <td>
                            <div class="user-cell-fancy">
                                @php
                                    $initials = strtoupper(substr($m->stage_name, 0, 2));
                                    $avatarClass = $m->verification_status === 'approved' ? 'bg-success' : ($m->verification_status === 'pending' ? 'bg-warning' : 'bg-secondary');
                                @endphp
                                <div class="avatar-box {{ $avatarClass }}">{{ $initials }}</div>
                                <div class="user-meta">
                                    <span class="user-name">{{ $m->stage_name }}</span>
                                    <span class="user-date">Registrado el {{ $m->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="location-box">
                                <i data-lucide="map-pin"></i>
                                <span>{{ $m->location ?? 'No especificada' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="genre-tag">{{ $m->genres->first()->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @php
                                $vstatus = $m->verification_status ?? 'unverified';
                                $badgeMap = [
                                    'approved'   => ['class' => 'badge-success',   'label' => 'Aprobado', 'icon' => 'check-circle'],
                                    'pending'    => ['class' => 'badge-warning',   'label' => 'Validación', 'icon' => 'clock'],
                                    'rejected'   => ['class' => 'badge-danger',    'label' => 'Rechazado', 'icon' => 'x-circle'],
                                    'unverified' => ['class' => 'badge-default', 'label' => 'Sin docs', 'icon' => 'file-warning'],
                                ];
                                $badge = $badgeMap[$vstatus] ?? ['class' => 'badge-default', 'label' => ucfirst($vstatus), 'icon' => 'user'];
                            @endphp
                            <span class="badge-fancy {{ $badge['class'] }}">
                                <i data-lucide="{{ $badge['icon'] }}"></i>
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            @if($m->verification_status === 'pending')
                                <a href="{{ route('admin.musicians.verify', $m->id) }}" class="btn-action-premium primary">
                                    Validar <i data-lucide="external-link"></i>
                                </a>
                            @else
                                <a href="{{ route('admin.musicians.verify', $m->id) }}" class="btn-action-premium ghost">
                                    Detalles
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="premium-empty-state">
                                <div class="empty-glow-icon">
                                    <i data-lucide="{{ !empty($search) ? 'search-x' : 'users-2' }}"></i>
                                </div>
                                <h4>{{ !empty($search) ? 'Sin coincidencias' : 'Aún no hay músicos' }}</h4>
                                <p>{{ !empty($search) ? 'No encontramos resultados para "' . $search . '" en esta sección.' : 'Los perfiles de músicos que se registren aparecerán aquí.' }}</p>
                                @if(!empty($search))
                                    <a href="{{ route('admin.dashboard') }}" class="btn-action-premium ghost mt-3">Limpiar búsqueda</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- BOTÓN FLOTANTE O INFO BOX --}}
    <div class="info-alert-premium mt-5">
        <div class="alert-icon">
            <i data-lucide="shield-check"></i>
        </div>
        <div class="alert-content">
            <h4>Seguridad y Validación</h4>
            <p>Recuerda que cada aprobación de perfil habilita al músico para empezar a recibir contrataciones reales. Verifica la autenticidad de los documentos antes de proceder.</p>
        </div>
    </div>

    @section('head')
    <style>
        .page-header-premium {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            background: #ffffff;
            padding: 24px 32px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        }
        .header-info h2 { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.02em; display: flex; align-items: center; gap: 10px; }
        .header-info p { font-size: 15px; color: #64748b; margin: 0; }
        .date-badge {
            background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 12px;
            font-size: 14px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 8px;
        }
        .date-badge i { width: 16px; height: 16px; color: #6366f1; }

        /* STATS GRID PREMIUM */
        .stats-grid-premium { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px; }
        .stat-card-fancy {
            background: #ffffff; padding: 24px; border-radius: 20px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); transition: all 0.3s ease; display: flex; flex-direction: column; gap: 16px; position: relative; overflow: hidden;
        }
        .stat-card-fancy::after { content: ""; position: absolute; top: -50%; right: -50%; width: 100px; height: 100px; background: rgba(0,0,0,0.03); border-radius: 50%; z-index: 0; pointer-events: none; }
        .stat-card-fancy:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.05); }
        .stat-card-fancy.clickable { cursor: pointer; }
        .stat-content { display: flex; align-items: center; gap: 18px; position: relative; z-index: 1; }
        .stat-icon-bg {
            width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .stat-icon-bg i { width: 24px; height: 24px; }
        .stat-data { display: flex; flex-direction: column; }
        .stat-value { font-size: 28px; font-weight: 900; color: #0f172a; line-height: 1; margin-bottom: 2px; }
        .stat-label { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.02em; }
        .stat-footer { padding-top: 12px; border-top: 1px solid #f1f5f9; position: relative; z-index: 1; }
        .stat-footer p { font-size: 12px; color: #94a3b8; font-weight: 500; margin: 0; }

        .indigo .stat-icon-bg { background: #e0e7ff; color: #4338ca; }
        .amber .stat-icon-bg { background: #fef3c7; color: #b45309; }
        .emerald .stat-icon-bg { background: #dcfce7; color: #059669; }
        .violet .stat-icon-bg { background: #f3e8ff; color: #7c3aed; }

        /* TABLE CONTAINER PREMIUM */
        .table-container-premium { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
        .box-header-premium {
            padding: 24px 32px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 20px;
        }
        .header-main h3 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; }
        .header-main p { font-size: 14px; color: #64748b; margin: 0; }
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .search-group-premium { position: relative; display: flex; align-items: center; }
        .fancy-search {
            padding: 10px 40px 10px 16px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-size: 14px;
            width: 260px; outline: none; transition: all 0.2s; background: #f8fafc;
        }
        .fancy-search:focus { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); width: 300px; }
        .search-trigger { position: absolute; right: 12px; background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .search-trigger i { width: 18px; height: 18px; }
        .clear-search { position: absolute; right: 40px; font-size: 20px; color: #cbd5e1; text-decoration: none; display: flex; align-items: center; }
        .clear-search:hover { color: #94a3b8; }
        .view-all-link { font-size: 13.5px; font-weight: 700; color: #6366f1; text-decoration: none; display: flex; align-items: center; gap: 6px; }

        /* PREMIUM TABLE */
        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { background: #f8fafc; padding: 16px 32px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
        .premium-table td { padding: 20px 32px; border-bottom: 1px solid #f1f5f9; }
        .premium-table tbody tr:hover { background: #fbfcfe; }

        .user-cell-fancy { display: flex; align-items: center; gap: 14px; }
        .avatar-box { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; color: #fff; flex-shrink: 0; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .bg-success { background: linear-gradient(135deg, #10b981, #059669); }
        .bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .bg-secondary { background: linear-gradient(135deg, #94a3b8, #64748b); }

        .user-meta { display: flex; flex-direction: column; }
        .user-name { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
        .user-date { font-size: 12px; color: #94a3b8; font-weight: 500; }

        .location-box { display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 13.5px; }
        .location-box i { width: 14px; height: 14px; color: #94a3b8; }
        .genre-tag { background: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; }

        .badge-fancy { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-default { background: #f1f5f9; color: #475569; }
        .badge-fancy i { width: 14px; height: 14px; }

        .btn-action-premium { padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-action-premium.primary { background: #6366f1; color: #fff; border: none; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }
        .btn-action-premium.primary:hover { background: #4f46e5; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3); }
        .btn-action-premium.ghost { background: #f8fafc; color: #475569; border: 1.5px solid #e2e8f0; }
        .btn-action-premium.ghost:hover { background: #fff; border-color: #6366f1; color: #6366f1; }

        /* EMPTY STATE PREMIUM */
        .premium-empty-state { padding: 60px 40px; text-align: center; }
        .empty-glow-icon { width: 80px; height: 80px; border-radius: 24px; background: #f8fafc; color: #cbd5e1; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; border: 1px solid #f1f5f9; box-shadow: inset 0 2px 10px rgba(0,0,0,0.02); }
        .empty-glow-icon i { width: 40px; height: 40px; }
        .premium-empty-state h4 { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0; }
        .premium-empty-state p { font-size: 14.5px; color: #94a3b8; margin: 0 auto; max-width: 320px; line-height: 1.6; }

        /* INFO ALERT PREMIUM */
        .info-alert-premium { background: #ffffff; padding: 24px 32px; border-radius: 20px; border-left: 5px solid #6366f1; display: flex; align-items: center; gap: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03); }
        .alert-icon { width: 48px; height: 48px; border-radius: 12px; background: #e0e7ff; color: #6366f1; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .alert-icon i { width: 22px; height: 22px; }
        .alert-content h4 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0; }
        .alert-content p { font-size: 14px; color: #64748b; margin: 0; line-height: 1.6; }

        @media (max-width: 1024px) {
            .page-header-premium { flex-direction: column; align-items: flex-start; gap: 16px; padding: 20px; }
            .box-header-premium { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; flex-direction: column; align-items: stretch; }
            .fancy-search { width: 100%; }
            .fancy-search:focus { width: 100%; }
        }
    </style>
    @endsection

    <script>
        function goToSearch() {
            var term = document.getElementById('dashboard-search').value.trim();
            if (term.length > 0) {
                // Navegar a /admin/search/{termino} — EVITA el query string (?search=) que corrompe el proxy
                window.location.href = window.location.origin + '/admin/search/' + encodeURIComponent(term);
            } else {
                window.location.href = window.location.origin + '/admin';
            }
        }

        document.getElementById('dashboard-search').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') goToSearch();
        });
    </script>

@endsection