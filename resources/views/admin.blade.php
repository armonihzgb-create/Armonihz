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
        <div class="box-header">
            <h3>Gestión de Músicos Recientes</h3>
            <div class="box-actions">
                <input type="text" placeholder="Buscar músico..." class="search-small">
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
                                <a href="{{ route('admin.musicians.verify', $m->id) }}" class="primary-btn small-btn" style="text-decoration: none; display: inline-block;">Validar</a>
                            @elseif($m->verification_status === 'unverified')
                                <button class="primary-btn small-btn" disabled title="Falta que el músico suba su documento" style="opacity: 0.5;">Sin Docs</button>
                            @else
                                <button class="secondary-btn small-btn icon-only" disabled>
                                    <i data-lucide="more-horizontal"></i>
                                </button>
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

@endsection