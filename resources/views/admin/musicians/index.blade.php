@extends('layouts.admin')

@section('admin-content')

    <div class="page-header-premium">
        <div class="header-info">
            <h2>Validación de Músicos <i data-lucide="shield-check" style="color: #6366f1; width: 26px; height: 26px; opacity: 0.2;"></i></h2>
            <p>Gestiona y revisa las identidades de los músicos registrados.</p>
        </div>
        <div class="header-actions">
            {{-- Buscador: navega a /admin/musicians/{status}/{term} sin query strings --}}
            <div class="search-group-premium">
                <input
                    type="text"
                    id="musician-search"
                    value="{{ $search ?? '' }}"
                    placeholder="Nombre o correo..."
                    class="fancy-search"
                    data-status="{{ $status }}"
                >
                <button onclick="performSearch()" class="search-trigger">
                    <i data-lucide="search"></i>
                </button>
                @if(!empty($search))
                    <a href="{{ route('admin.musicians.index', $status) }}" class="clear-search" title="Limpiar búsqueda">&times;</a>
                @endif
            </div>
        </div>
    </div>

    <div class="filter-wrapper-premium">
        <div class="filter-tabs-premium">
            @php
                $searchSuffix = !empty($search) ? '/' . rawurlencode($search) : '';
            @endphp
            <a href="{{ route('admin.musicians.index', 'pending') }}{{ $searchSuffix }}" class="filter-tab-premium {{ $status === 'pending' ? 'active' : '' }}">
                <span>Pendientes</span>
                <span class="count-badge amber">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('admin.musicians.index', 'unverified') }}{{ $searchSuffix }}" class="filter-tab-premium {{ $status === 'unverified' ? 'active' : '' }}">
                <span>Sin Docs</span>
                <span class="count-badge grey">{{ $counts['unverified'] }}</span>
            </a>
            <a href="{{ route('admin.musicians.index', 'rejected') }}{{ $searchSuffix }}" class="filter-tab-premium {{ $status === 'rejected' ? 'active' : '' }}">
                <span>Rechazados</span>
                <span class="count-badge red">{{ $counts['rejected'] }}</span>
            </a>
            <a href="{{ route('admin.musicians.index', 'approved') }}{{ $searchSuffix }}" class="filter-tab-premium {{ $status === 'approved' ? 'active' : '' }}">
                <span>Aprobados</span>
                <span class="count-badge emerald">{{ $counts['approved'] }}</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-premium success animate-fade-in">
            <i data-lucide="check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-premium danger animate-fade-in">
            <i data-lucide="x-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="table-container-premium shadow-sm">
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="padding-left: 32px;">Músico / Banda</th>
                        <th>Género</th>
                        <th>Ubicación</th>
                        <th>Documentos</th>
                        <th>Estado de Validación</th>
                        <th style="padding-right: 32px; text-align: right;">Operación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($musicians as $m)
                        @php 
                            $initials = strtoupper(substr($m->stage_name, 0, 2));
                            $avatarClass = $m->verification_status === 'approved' ? 'bg-emerald' : ($m->verification_status === 'pending' ? 'bg-amber' : ($m->verification_status === 'rejected' ? 'bg-red' : 'bg-grey'));
                        @endphp
                        <tr>
                            <td style="padding-left: 32px;">
                                <div class="user-cell-fancy">
                                    <div class="avatar-box {{ $avatarClass }}">{{ $initials }}</div>
                                    <div class="user-meta">
                                        <span class="user-name">{{ $m->stage_name }}</span>
                                        <span class="user-date">ID #{{ $m->id }} • {{ $m->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="genre-list-premium">
                                    @forelse($m->genres as $g)
                                        <span class="genre-chip">{{ $g->name }}</span>
                                    @empty
                                        <span class="text-dim text-small italic">Sin género</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <div class="location-item">
                                    <i data-lucide="map-pin"></i>
                                    <span>{{ $m->location ?? 'No especificada' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($m->id_document_path)
                                    <span class="doc-badge has-docs"><i data-lucide="file-check"></i> Presentado</span>
                                @else
                                    <span class="doc-badge no-docs"><i data-lucide="file-warning"></i> Ausente</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'badge-premium-warning', 'label' => 'Pendiente', 'icon' => 'clock'],
                                        'approved' => ['class' => 'badge-premium-success', 'label' => 'Aprobado', 'icon' => 'check-circle'],
                                        'rejected' => ['class' => 'badge-premium-danger', 'label' => 'Rechazado', 'icon' => 'x-circle'],
                                        'unverified' => ['class' => 'badge-premium-default', 'label' => 'Sin iniciar', 'icon' => 'user-minus']
                                    ];
                                    $cfg = $statusConfig[$m->verification_status] ?? $statusConfig['unverified'];
                                @endphp
                                <span class="badge-fancy {{ $cfg['class'] }}">
                                    <i data-lucide="{{ $cfg['icon'] }}"></i>
                                    {{ $cfg['label'] }}
                                </span>
                            </td>
                            <td style="padding-right: 32px; text-align: right;">
                                @if($m->verification_status === 'pending')
                                    <a href="{{ route('admin.musicians.verify', $m->id) }}" class="btn-action-premium primary">
                                        Validar <i data-lucide="chevron-right"></i>
                                    </a>
                                @elseif($m->verification_status === 'approved' || $m->verification_status === 'rejected')
                                    <a href="{{ route('admin.musicians.verify', $m->id) }}" class="btn-action-premium ghost">
                                        Detalles
                                    </a>
                                @else
                                    <span class="btn-disabled"><i data-lucide="lock" style="width: 14px;"></i> Bloqueado</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="premium-empty-state">
                                    <div class="empty-glow-icon">
                                        <i data-lucide="{{ request('search') ? 'search-x' : 'users-2' }}"></i>
                                    </div>
                                    <h4>{{ request('search') ? 'Búsqueda sin éxito' : 'Bandeja despejada' }}</h4>
                                    <p>{{ $emptyMsg }}</p>
                                    @if(request('search'))
                                        <a href="{{ route('admin.musicians.index', ['status' => $status]) }}" class="btn-action-premium ghost mt-3">Ver todos los registros</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($musicians->hasPages())
            <div class="pagination-wrapper-premium">
                <span class="text-dim">Mostrando {{ $musicians->firstItem() }}-{{ $musicians->lastItem() }} de {{ $musicians->total() }} músicos</span>
                <div class="pagination-fancy">
                    {{ $musicians->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

    @section('head')
    <style>
        .page-header-premium {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
            background: #ffffff; padding: 24px 32px; border-radius: 20px; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        }
        .header-info h2 { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.02em; display: flex; align-items: center; gap: 10px; }
        .header-info p { font-size: 15px; color: #64748b; margin: 0; }
        .search-group-premium { position: relative; display: flex; align-items: center; }
        .fancy-search {
            padding: 12px 48px 12px 16px; border-radius: 14px; border: 1.5px solid #e2e8f0; font-size: 14px;
            width: 300px; outline: none; transition: all 0.2s; background: #f8fafc;
        }
        .fancy-search:focus { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); width: 340px; }
        .search-trigger { position: absolute; right: 14px; background: none; border: none; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .clear-search { position: absolute; right: 44px; font-size: 20px; color: #cbd5e1; text-decoration: none; display: flex; align-items: center; line-height: 1; }
        .clear-search:hover { color: #94a3b8; }

        .filter-wrapper-premium { margin-bottom: 24px; }
        .filter-tabs-premium { display: flex; gap: 8px; background: #ffffff; padding: 8px; border-radius: 16px; border: 1px solid #e2e8f0; width: fit-content; }
        .filter-tab-premium {
            text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px 18px;
            border-radius: 12px; font-size: 14px; font-weight: 700; color: #64748b; transition: all 0.2s;
        }
        .filter-tab-premium:hover { background: #f8fafc; color: #1e293b; }
        .filter-tab-premium.active { background: #f1f5f9; color: #0f172a; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); }
        .count-badge { padding: 2px 10px; border-radius: 30px; font-size: 11.5px; font-weight: 800; }
        .count-badge.amber { background: #fef3c7; color: #b45309; }
        .count-badge.grey { background: #e2e8f0; color: #475569; }
        .count-badge.red { background: #fee2e2; color: #dc2626; }
        .count-badge.emerald { background: #dcfce7; color: #15803d; }

        .alert-premium { display: flex; align-items: center; gap: 12px; padding: 16px 24px; border-radius: 16px; margin-bottom: 24px; font-weight: 600; font-size: 14.5px; }
        .alert-premium.success { background: #effdf5; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-premium.danger { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .alert-premium i { width: 20px; height: 20px; }

        .table-container-premium { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 32px; }
        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { background: #f8fafc; padding: 16px 24px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
        .premium-table td { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .premium-table tbody tr:hover { background: #fbfcfe; }

        .user-cell-fancy { display: flex; align-items: center; gap: 14px; }
        .avatar-box { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; color: #fff; flex-shrink: 0; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .bg-emerald { background: linear-gradient(135deg, #10b981, #059669); }
        .bg-amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .bg-red { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .bg-grey { background: linear-gradient(135deg, #94a3b8, #64748b); }

        .user-meta { display: flex; flex-direction: column; }
        .user-name { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
        .user-date { font-size: 12px; color: #94a3b8; font-weight: 500; }

        .genre-list-premium { display: flex; flex-wrap: wrap; gap: 6px; }
        .genre-chip { background: #eff6ff; color: #3b82f6; padding: 3px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 700; border: 1px solid #dbeafe; }
        
        .location-item { display: flex; align-items: center; gap: 6px; color: #475569; font-size: 13.5px; }
        .location-item i { width: 14px; height: 14px; color: #94a3b8; }

        .doc-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 8px; }
        .doc-badge.has-docs { background: #f0fdf4; color: #16a34a; }
        .doc-badge.no-docs { background: #fff1f2; color: #e11d48; }

        .badge-fancy { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 700; }
        .badge-premium-success { background: #dcfce7; color: #15803d; }
        .badge-premium-warning { background: #fef3c7; color: #b45309; }
        .badge-premium-danger { background: #fee2e2; color: #dc2626; }
        .badge-premium-default { background: #f1f5f9; color: #475569; }

        .btn-action-premium { padding: 9px 18px; border-radius: 12px; font-size: 13px; font-weight: 700; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-action-premium.primary { background: #6366f1; color: #fff; border: none; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }
        .btn-action-premium.primary:hover { background: #4f46e5; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3); }
        .btn-action-premium.ghost { background: #ffffff; color: #475569; border: 1.5px solid #e2e8f0; }
        .btn-action-premium.ghost:hover { background: #f8fafc; border-color: #6366f1; color: #6366f1; }
        .btn-disabled { font-size: 12.5px; font-weight: 600; color: #94a3b8; background: #f8fafc; padding: 8px 16px; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px; }

        .premium-empty-state { padding: 80px 40px; text-align: center; }
        .empty-glow-icon { width: 80px; height: 80px; border-radius: 24px; background: #f8fafc; color: #cbd5e1; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; border: 1px solid #f1f5f9; box-shadow: inset 0 2px 10px rgba(0,0,0,0.02); }
        .empty-glow-icon i { width: 36px; height: 36px; }
        .premium-empty-state h4 { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0; }
        .premium-empty-state p { font-size: 14.5px; color: #94a3b8; margin: 0; line-height: 1.6; }

        .pagination-wrapper-premium { padding: 24px 32px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .pagination-fancy .page-link { border: none; background: transparent; color: #64748b; font-weight: 600; font-size: 14px; padding: 8px 14px; border-radius: 10px; margin: 0 2px; }
        .pagination-fancy .page-item.active .page-link { background: #6366f1; color: #fff; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2); }

        @media (max-width: 1024px) {
            .page-header-premium { flex-direction: column; align-items: stretch; gap: 20px; }
            .fancy-search { width: 100%; }
            .filter-tabs-premium { width: 100%; overflow-x: auto; padding-bottom: 2px; }
            .premium-table th, .premium-table td { padding: 16px; white-space: nowrap; }
        }
    </style>
    @endsection

    <script>
        function performSearch() {
            var rawTerm = document.getElementById('musician-search').value.trim();
            var status  = document.getElementById('musician-search').dataset.status || 'pending';
            var base    = window.location.pathname.split('/').slice(0, 3).join('/'); // /admin/musicians

            if (rawTerm.length > 0) {
                // Navegar a /admin/musicians/{status}/{term} — SIN query strings
                window.location.href = window.location.origin + '/admin/musicians/' + encodeURIComponent(status) + '/' + encodeURIComponent(rawTerm);
            } else {
                window.location.href = window.location.origin + '/admin/musicians/' + encodeURIComponent(status);
            }
        }

        // Buscar con la tecla Enter
        document.getElementById('musician-search').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') performSearch();
        });
    </script>

@endsection
