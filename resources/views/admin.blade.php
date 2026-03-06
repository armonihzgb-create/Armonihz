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
                <span class="stat-number">128</span>
                <p class="stat-meta">Total en plataforma</p>
            </div>
        </div>

        <div class="card stat-card warning-border">
            <div class="stat-icon orange">
                <i data-lucide="alert-circle"></i>
            </div>
            <div>
                <h4>Perfiles Pendientes</h4>
                <span class="stat-number text-orange">7</span>
                <p class="stat-meta">Requieren validación</p>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon green">
                <i data-lucide="check-circle"></i>
            </div>
            <div>
                <h4>Eventos Completados</h4>
                <span class="stat-number">342</span>
                <p class="stat-meta">Este mes</p>
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
                    {{-- Fila 1: Pendiente --}}
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar-circle">MS</div>
                                <div>
                                    <strong>Mariachi Sol de México</strong>
                                    <span class="sub-text">Reg: Hoy</span>
                                </div>
                            </div>
                        </td>
                        <td>Tehuacán, Pue</td>
                        <td>Mariachi</td>
                        <td><span class="status-pill warning">Pendiente</span></td>
                        <td class="text-right">
                            <button class="primary-btn small-btn">Validar</button>
                        </td>
                    </tr>

                    {{-- Fila 2: Activo --}}
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar-circle blue">BN</div>
                                <div>
                                    <strong>Banda Norteña Real</strong>
                                    <span class="sub-text">Reg: Ayer</span>
                                </div>
                            </div>
                        </td>
                        <td>Monterrey, NL</td>
                        <td>Norteño</td>
                        <td><span class="status-pill success">Activo</span></td>
                        <td class="text-right">
                            <button class="secondary-btn small-btn icon-only">
                                <i data-lucide="more-horizontal"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Fila 3: Pendiente --}}
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar-circle">SV</div>
                                <div>
                                    <strong>Sonido Versátil</strong>
                                    <span class="sub-text">Reg: 2 Feb</span>
                                </div>
                            </div>
                        </td>
                        <td>Guadalajara, Jal</td>
                        <td>Versátil</td>
                        <td><span class="status-pill warning">Pendiente</span></td>
                        <td class="text-right">
                            <button class="primary-btn small-btn">Validar</button>
                        </td>
                    </tr>
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