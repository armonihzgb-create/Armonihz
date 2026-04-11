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

    <div class="grid-settings">
        
        {{-- COLUMNA IZQUIERDA --}}
        <div class="settings-column">
            
            {{-- Tarjeta: Comisiones (ELIMINADA por solicitud) --}}

            {{-- Tarjeta: Mantenimiento --}}
            <div class="dashboard-box">
                <div class="box-header">
                    <h3>🛠️ Estado del Sistema</h3>
                </div>
                
                <div class="toggle-row">
                    <div>
                        <strong>Modo Mantenimiento</strong>
                        <p class="text-dim text-small">Desactiva el acceso a usuarios no administradores.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="maintenance_mode" {{ \App\Models\Setting::get('maintenance_mode', '0') == '1' ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>

                <div class="toggle-row mt-4">
                    <div>
                        <strong>Registro de nuevos usuarios</strong>
                        <p class="text-dim text-small">Permitir que nuevos músicos o clientes se registren.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="registration_enabled" {{ \App\Models\Setting::get('registration_enabled', '1') == '1' ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="settings-column">
            
            {{-- Tarjeta: Notificaciones --}}
            <div class="dashboard-box">
                <div class="box-header">
                    <h3>🔔 Notificaciones Administrativas</h3>
                </div>
                
                <div class="checkbox-group">
                    <label class="checkbox-item">
                        <input type="checkbox" name="notify_new_musician" {{ \App\Models\Setting::get('notify_new_musician', '1') == '1' ? 'checked' : '' }}>
                        <span>Notificar nuevo registro de músico</span>
                    </label>
                    <label class="checkbox-item">
                        <input type="checkbox" name="notify_report" {{ \App\Models\Setting::get('notify_report', '1') == '1' ? 'checked' : '' }}>
                        <span>Notificar reporte de usuario</span>
                    </label>
                    <label class="checkbox-item">
                        <input type="checkbox" name="notify_casting" {{ \App\Models\Setting::get('notify_casting', '0') == '1' ? 'checked' : '' }}>
                        <span>Notificar nuevo casting publicado</span>
                    </label>
                </div>
            </div>

            {{-- Tarjeta: Soporte --}}
            <div class="dashboard-box">
                <div class="box-header">
                    <h3>📞 Contacto de Soporte</h3>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email de soporte</label>
                    <input type="email" name="support_email" value="{{ \App\Models\Setting::get('support_email', 'soporte@armonihz.com') }}" required>
                </div>
                
                <div class="form-group mt-3">
                    <label class="form-label">Teléfono de contacto</label>
                    <input type="text" name="support_phone" value="{{ \App\Models\Setting::get('support_phone', '+52 55 1234 5678') }}">
                </div>
            </div>

        </div>

    </div>
    </form>

    <style>
        .grid-settings {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .toggle-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .text-small { font-size: 12px; margin: 2px 0 0 0; }
        .mb-4 { margin-bottom: 24px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mt-4 { margin-top: 16px; }

        /* Switch Toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: var(--accent-blue); }
        input:checked + .slider:before { transform: translateX(20px); }

        /* Checkbox list */
        .checkbox-group { display: flex; flex-direction: column; gap: 12px; }
        .checkbox-item { display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; }
        .checkbox-item input { width: 16px; height: 16px; margin: 0; }
    </style>

@endsection
