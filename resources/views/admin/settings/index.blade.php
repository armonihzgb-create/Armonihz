@extends('layouts.admin')

@section('admin-content')

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <div class="page-header">
            <div>
            <h2>Configuración</h2>
            <p class="dashboard-subtitle">Ajustes generales de la plataforma</p>
        </div>
            <button type="submit" class="primary-btn">
                <i data-lucide="save"></i> Guardar Cambios
            </button>
        </div>

    <div class="settings-wrapper">
        
        <div class="settings-column">
            <div class="dashboard-box premium-box">
                <div class="box-header-fancy">
                    <div class="icon-wrapper">
                        <i data-lucide="shield-alert"></i>
                    </div>
                    <div class="header-text">
                        <h3>Gestión de Acceso Global</h3>
                        <p>Controla el flujo de registro y el entorno público del sistema en tiempo real.</p>
                    </div>
                </div>
                
                <div class="toggle-card">
                    <div class="toggle-info">
                        <strong>Modo Mantenimiento</strong>
                        <p>Desactiva el acceso a usuarios no administradores y muestra una pantalla de "Vuelve pronto" en verde. Ideal para realizar optimizaciones o mantenimientos sin afectar a usuarios activos.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="maintenance_mode" {{ \App\Models\Setting::get('maintenance_mode', '0') == '1' ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>

                <div class="toggle-card">
                    <div class="toggle-info">
                        <strong>Registro de nuevos usuarios</strong>
                        <p>Permite que nuevos músicos o clientes abran cuentas en Armonihz. Apágalo para pausar el enrolamiento sin desconectar a los perfiles ya existentes.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="registration_enabled" {{ \App\Models\Setting::get('registration_enabled', '1') == '1' ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

    </div>
    </form>

    @section('head')
    <style>
        .settings-wrapper {
            max-width: 800px; /* Centrado y estrecho */
            margin: 0 auto;
            margin-top: 16px;
        }

        .premium-box {
            padding: 36px 40px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        }

        .box-header-fancy {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 28px;
        }

        .icon-wrapper {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.15);
        }

        .icon-wrapper i { width: 26px; height: 26px; }

        .header-text h3 {
            font-size: 19px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px 0;
            letter-spacing: -0.01em;
        }

        .header-text p {
            font-size: 14.5px;
            color: #64748b;
            margin: 0;
        }

        .toggle-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            margin-bottom: 16px;
            transition: all 0.2s ease;
        }

        .toggle-card:last-child {
            margin-bottom: 0;
        }

        .toggle-card:hover {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 6px 14px rgba(0,0,0,0.03);
            transform: translateY(-2px);
        }

        .toggle-info strong {
            display: block;
            font-size: 15.5px;
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .toggle-info p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
            line-height: 1.6;
            max-width: 540px;
        }

        /* Switch Toggle Premium */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
            flex-shrink: 0;
            margin-left: 24px;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s cubic-bezier(0.4, 0.0, 0.2, 1);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        input:checked + .slider { background-color: var(--accent-blue); }
        input:checked + .slider:before { transform: translateX(22px); }

        @media (max-width: 640px) {
            .toggle-card { flex-direction: column; align-items: flex-start; gap: 16px; padding: 20px; }
            .switch { margin-left: 0; }
            .premium-box { padding: 24px 20px; }
            .box-header-fancy { flex-direction: column; align-items: flex-start; gap: 16px; }
        }
    </style>
    @endsection

@endsection
