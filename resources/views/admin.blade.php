@extends('layouts.admin')

@section('admin-content')

    <div class="page-header">
        <div>
            <h2>Panel Administrativo</h2>
            <p class="dashboard-subtitle">Gestión y validación de músicos en Armonihz</p>
        </div>
        <span class="date-badge">
            <i data-lucide="calendar"></i> {{ date('d M, Y') }}
        </span>
    </div>

    {{-- TARJETAS DE ESTADÍSTICAS --}}
    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-icon blue">
                <i data-lucide="users"></i>
            </div>
            <div>
                <h4>Músicos Registrados</h4>
                <span class="stat-number">{{ $totalMusicians }}</span>
                <p class="stat-meta">Total en plataforma</p>
            </div>
        </div>

        <div class="card stat-card warning-border" style="cursor:pointer;" onclick="window.location='{{ route('admin.musicians.index', ['status' => 'pending']) }}'">
            <div class="stat-icon orange">
                <i data-lucide="alert-circle"></i>
            </div>
            <div>
                <h4>Perfiles Pendientes</h4>
                <span class="stat-number text-orange">{{ $pendingMusiciansCount }}</span>
                <p class="stat-meta">Requieren validación &rarr;</p>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon green">
                <i data-lucide="check-circle"></i>
            </div>
            <div>
                <h4>Eventos Completados</h4>
                <span class="stat-number">{{ $totalCompletedEvents }}</span>
                <p class="stat-meta">Métricas globales</p>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon cyan">
                <i data-lucide="user-check"></i>
            </div>
            <div>
                <h4>Clientes Totales</h4>
                <span class="stat-number">{{ $totalClients }}</span>
                <p class="stat-meta">Registrados vía App</p>
            </div>
        </div>
    </div>

    {{-- TABLA DE GESTIÓN --}}
    <div class="dashboard-box">
        <div class="box-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h3>Gestión de Músicos Recientes</h3>
            <div class="box-actions" style="display: flex; gap: 12px; align-items: center;">
                <a href="{{ route('admin.musicians.index') }}" style="font-size: 13px; font-weight: 500; color: #6366f1; text-decoration: none;">Ver todos &rarr;</a>
                <div style="display: flex; gap: 6px;">
                    <input
                        type="text"
                        id="dashboard-search"
                        placeholder="Buscar en toda la BD..."
                        class="search-small"
                        style="padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 14px; min-width: 220px;"
                    >
                    <button onclick="goToSearch()" style="padding: 8px 12px; border-radius: 6px; border: none; background: #6366f1; color: white; cursor: pointer; font-size: 13px;">
                        <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nombre Artístico</th>
                        <th>Ubicación</th>
                        <th>Género</th>
                        <th>Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentMusicians as $m)
                    <tr>
                        <td>
                            <div class="user-cell">
                                @php
                                    $initials = strtoupper(substr($m->stage_name, 0, 2));
                                @endphp
                                <div class="avatar-circle {{ $m->is_verified ? 'blue' : '' }}">{{ $initials }}</div>
                                <div>
                                    <strong>{{ $m->stage_name }}</strong>
                                    <span class="sub-text">Reg: {{ $m->created_at->format('d M') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $m->location ?? 'No especificada' }}</td>
                        <td>{{ $m->genres->first()->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $vstatus = $m->verification_status ?? 'unverified';
                                $pillMap = [
                                    'approved'   => ['class' => 'success',   'label' => 'Aprobado'],
                                    'pending'    => ['class' => 'warning',   'label' => 'Pendiente'],
                                    'rejected'   => ['class' => 'danger',    'label' => 'Rechazado'],
                                    'unverified' => ['class' => 'secondary', 'label' => 'Sin docs'],
                                ];
                                $pill = $pillMap[$vstatus] ?? ['class' => 'secondary', 'label' => ucfirst($vstatus)];
                            @endphp
                            <span class="status-pill {{ $pill['class'] }}">{{ $pill['label'] }}</span>
                        </td>
                        <td class="text-right">
                            @if($m->verification_status === 'pending')
                                <a href="{{ route('admin.musicians.verify', $m->id) }}" class="primary-btn small-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Validar
                                </a>
                            @else
                                <a href="{{ route('admin.musicians.verify', $m->id) }}" class="secondary-btn small-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;" title="Ver perfil de usuario">
                                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Detalles
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px; color: #64748b;">
                            No hay músicos registrados todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TIP FOOTER --}}
    <div class="info-box blue-box mt-large">
        <div class="icon-wrapper">
            <i data-lucide="shield-alert"></i>
        </div>
        <div>
            <strong>Consejo Admin:</strong> Revisa que las fotos de perfil cumplan con los términos de uso antes de validar la cuenta.
        </div>
    </div>

    <script>
        function goToSearch() {
            var term = document.getElementById('dashboard-search').value.trim();
            if (term.length > 0) {
                // Navegar sin query strings — path: /admin/musicians/unverified/{term}
                window.location.href = window.location.origin + '/admin/musicians/unverified/' + encodeURIComponent(term);
            }
        }

        document.getElementById('dashboard-search').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') goToSearch();
        });
    </script>

@endsection