@extends('layouts.admin')

@section('admin-content')

    <div class="page-header">
        <div>
            <h2>Validación de Músicos</h2>
            <p class="dashboard-subtitle">Gestiona y revisa las identidades de los músicos registrados.</p>
        </div>
        
        <div class="filter-tabs">
            <a href="{{ route('admin.musicians.index', ['status' => 'pending']) }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
                Pendientes <span class="counter">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('admin.musicians.index', ['status' => 'unverified']) }}" class="filter-tab {{ $status === 'unverified' ? 'active' : '' }}">
                Sin Documentos <span class="counter">{{ $counts['unverified'] }}</span>
            </a>
            <a href="{{ route('admin.musicians.index', ['status' => 'rejected']) }}" class="filter-tab {{ $status === 'rejected' ? 'active' : '' }}">
                Rechazados <span class="counter">{{ $counts['rejected'] }}</span>
            </a>
            <a href="{{ route('admin.musicians.index', ['status' => 'approved']) }}" class="filter-tab {{ $status === 'approved' ? 'active' : '' }}">
                Aprobados <span class="counter">{{ $counts['approved'] }}</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #10b981; color: #047857; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
            <span style="font-size: 14px; font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- TABLA DE VALIDACIÓN --}}
    <div class="dashboard-box">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Músico / Banda</th>
                        <th>Género</th>
                        <th>Ubicación</th>
                        <th>Documentos</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($musicians as $m)
                        @php $initials = strtoupper(substr($m->stage_name, 0, 2)); @endphp
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar-circle {{ $m->verification_status === 'approved' ? 'blue' : '' }}">{{ $initials }}</div>
                                    <div>
                                        <strong>{{ $m->stage_name }}</strong>
                                        <span class="sub-text">ID: #{{ $m->id }} • {{ $m->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                    @forelse($m->genres as $g)
                                        <span class="genre-tag-mini">{{ $g->name }}</span>
                                    @empty
                                        <span class="text-dim text-small">N/A</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>{{ $m->location ?? 'No especificada' }}</td>
                            <td>
                                @if($m->id_document_path)
                                    <span class="file-tag success"><i data-lucide="file-check"></i> Documento</span>
                                @else
                                    <span class="file-tag danger"><i data-lucide="file-x"></i> Sin archivo</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'unverified' => 'secondary'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'approved' => 'Aprobado',
                                        'rejected' => 'Rechazado',
                                        'unverified' => 'No iniciado'
                                    ];
                                @endphp
                                <span class="status-pill {{ $statusClasses[$m->verification_status] ?? 'secondary' }}">
                                    {{ $statusLabels[$m->verification_status] ?? $m->verification_status }}
                                </span>
                            </td>
                            <td class="text-right">
                                @if($m->verification_status === 'pending' || $m->verification_status === 'rejected' || $m->verification_status === 'approved')
                                    <a href="{{ route('admin.musicians.verify', $m->id) }}" class="primary-btn small-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Revisar
                                    </a>
                                @else
                                    <button class="secondary-btn small-btn icon-only" disabled title="No hay documentos para revisar">
                                        <i data-lucide="slash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px 0; color: #94a3b8;">
                                <i data-lucide="users" style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.2;"></i>
                                <p style="margin: 0;">No se encontraron músicos con este estado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($musicians->hasPages())
            <div class="table-footer">
                <span class="text-dim text-small">Mostrando {{ $musicians->firstItem() }}-{{ $musicians->lastItem() }} de {{ $musicians->total() }}</span>
                <div class="pagination">
                    {{ $musicians->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

    <style>
        .filter-tab {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .filter-tab.active {
            background: white;
            color: #1e293b;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #e2e8f0;
        }
        .filter-tab .counter {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
        }
        .filter-tab.active .counter {
            background: #e0e7ff;
            color: #4338ca;
        }
        
        .genre-tag-mini {
            font-size: 10px;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        .file-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .file-tag.success { background: #ecfdf5; color: #059669; }
        .file-tag.danger { background: #fef2f2; color: #dc2626; }
        .file-tag i { width: 12px; height: 12px; }
        
        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0 0;
            border-top: 1px solid #f1f5f9;
            margin-top: 16px;
        }
        .text-small { font-size: 12px; }
        
        /* Ajustes de paginación de Laravel */
        .pagination { margin: 0; }
        .page-link { border-radius: 8px !important; margin: 0 2px; border: none; font-size: 14px; }
        .page-item.active .page-link { background-color: #6366f1; }
    </style>

@endsection
