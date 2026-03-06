@extends('layouts.admin')

@section('admin-content')

    <div class="page-header">
        <div>
            <h2>Validación de Músicos</h2>
            <p class="dashboard-subtitle">Revisa y aprueba los nuevos perfiles registrados</p>
        </div>
        
        <div class="filter-tabs">
            <button class="filter-tab active">Pendientes <span class="counter">7</span></button>
            <button class="filter-tab">Rechazados</button>
            <button class="filter-tab">Aprobados</button>
        </div>
    </div>

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
                    
                    {{-- Fila 1 --}}
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar-circle">MS</div>
                                <div>
                                    <strong>Mariachi Sol de México</strong>
                                    <span class="sub-text">Registrado: Hoy, 10:30 AM</span>
                                </div>
                            </div>
                        </td>
                        <td>Mariachi</td>
                        <td>Tehuacán, Puebla</td>
                        <td>
                            <span class="file-tag"><i data-lucide="file-check"></i> INE</span>
                        </td>
                        <td><span class="status-pill warning">Pendiente</span></td>
                        <td class="text-right">
                            <div class="btn-group">
                                <button class="secondary-btn small-btn danger-hover" title="Rechazar">
                                    <i data-lucide="x"></i>
                                </button>
                                <button class="primary-btn small-btn" title="Aprobar">
                                    <i data-lucide="check"></i> Aprobar
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Fila 2 --}}
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar-circle">SV</div>
                                <div>
                                    <strong>Sonido Versátil</strong>
                                    <span class="sub-text">Registrado: Ayer</span>
                                </div>
                            </div>
                        </td>
                        <td>Versátil / DJ</td>
                        <td>Guadalajara, Jalisco</td>
                        <td>
                            <span class="file-tag"><i data-lucide="file-check"></i> INE</span>
                            <span class="file-tag"><i data-lucide="music"></i> Demo</span>
                        </td>
                        <td><span class="status-pill warning">Pendiente</span></td>
                        <td class="text-right">
                            <div class="btn-group">
                                <button class="secondary-btn small-btn danger-hover" title="Rechazar">
                                    <i data-lucide="x"></i>
                                </button>
                                <button class="primary-btn small-btn" title="Aprobar">
                                    <i data-lucide="check"></i> Aprobar
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Fila 3 --}}
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar-circle blue">GR</div>
                                <div>
                                    <strong>Grupo Rock & Pop</strong>
                                    <span class="sub-text">Registrado: 08 Feb</span>
                                </div>
                            </div>
                        </td>
                        <td>Rock / Pop</td>
                        <td>CDMX</td>
                        <td>
                            <span class="file-tag"><i data-lucide="alert-circle" class="text-orange"></i> Incompleto</span>
                        </td>
                        <td><span class="status-pill warning">Revisión</span></td>
                        <td class="text-right">
                            <button class="secondary-btn small-btn">Ver Detalle</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        
        {{-- Paginación Mock --}}
        <div class="table-footer">
            <span class="text-dim text-small">Mostrando 3 de 7 pendientes</span>
            <div class="pagination">
                <button disabled><i data-lucide="chevron-left"></i></button>
                <button class="active">1</button>
                <button>2</button>
                <button><i data-lucide="chevron-right"></i></button>
            </div>
        </div>
    </div>

    <style>
        .file-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            background: var(--bg-secondary);
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid var(--border-light);
            margin-right: 4px;
            color: var(--text-main);
        }
        .file-tag i { width: 12px; height: 12px; }
        .text-orange { color: var(--accent-orange); }
        
        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid var(--border-light);
            margin-top: 16px;
        }
        .text-small { font-size: 12px; }
        .pagination { display: flex; gap: 4px; }
        .pagination button {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border-light);
            background: white;
            border-radius: 8px;
            cursor: pointer;
            color: var(--text-dim);
        }
        .pagination button.active {
            background: var(--accent-blue);
            color: white;
            border-color: var(--accent-blue);
        }
        .pagination button:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>

@endsection
