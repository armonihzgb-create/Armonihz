@extends('layouts.dashboard')

@section('dashboard-content')

<div style="max-width: 600px; margin: 0 auto;">
    <header class="dashboard-header" style="text-align: center; margin-bottom: 32px;">
        <h2 style="font-size: 24px; margin-bottom: 8px;">🚀 Impulsa tu carrera</h2>
        <p class="dashboard-subtitle">Llega a más clientes potenciales destacando tu perfil.</p>
    </header>

    <div class="dashboard-box">
        <form>
            @csrf
            
            {{-- PASO 1: SELECCIONAR CONTENIDO --}}
            <div class="form-section" style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px dashed var(--border-light);">
                <h3 style="font-size: 16px; margin-bottom: 16px;">1. ¿Qué quieres promocionar?</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <label style="cursor: pointer; border: 2px solid var(--accent-blue); padding: 12px; border-radius: 12px; background: rgba(47, 147, 245, 0.05); text-align: center;">
                        <input type="radio" name="content_type" value="profile" checked style="accent-color: var(--accent-blue);">
                        <div style="margin-top: 8px;">
                            <i data-lucide="user" style="color: var(--accent-blue); width: 24px; height: 24px; margin-bottom: 8px;"></i>
                            <span style="display: block; font-weight: 600; font-size: 14px;">Mi Perfil Principal</span>
                        </div>
                    </label>
                    <label style="cursor: pointer; border: 1px solid var(--border-light); padding: 12px; border-radius: 12px; text-align: center;">
                        <input type="radio" name="content_type" value="video" style="accent-color: var(--accent-blue);">
                        <div style="margin-top: 8px;">
                            <i data-lucide="video" style="color: var(--text-dim); width: 24px; height: 24px; margin-bottom: 8px;"></i>
                            <span style="display: block; color: var(--text-dim); font-size: 14px;">Video Destacado</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- PASO 2: ALCANCE --}}
            <div class="form-section" style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px dashed var(--border-light);">
                <h3 style="font-size: 16px; margin-bottom: 16px;">2. Define tu alcance</h3>
                
                <select style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-light); margin-bottom: 16px;">
                    <option value="local">📍 Local (Tu ciudad)</option>
                    <option value="regional">🗺️ Regional (+50km a la redonda)</option>
                    <option value="state">🌎 Todo el Estado</option>
                </select>

                <div style="background: var(--bg-secondary); padding: 12px; border-radius: 8px; font-size: 13px; color: var(--text-dim); display: flex; gap: 8px;">
                    <i data-lucide="info" style="width: 16px; flex-shrink: 0;"></i>
                    El alcance regional aumenta tus visualizaciones en un 40% aproximadamente.
                </div>
            </div>

            {{-- PASO 3: DURACIÓN --}}
            <div class="form-section" style="margin-bottom: 32px;">
                <h3 style="font-size: 16px; margin-bottom: 16px;">3. Duración de la campaña</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                    <label style="text-align: center; padding: 16px; border: 1px solid var(--border-light); border-radius: 8px; cursor: pointer;">
                        <input type="radio" name="duration" value="7">
                        <span style="display: block; font-weight: 700; font-size: 18px; margin-top: 8px;">7</span>
                        <span style="font-size: 12px; color: var(--text-dim);">Días</span>
                         <span style="display: block; font-size: 10px; color: var(--text-main); font-weight: 600; margin-top: 4px;">$150 MXN</span>
                    </label>
                    <label style="text-align: center; padding: 16px; border: 2px solid var(--accent-blue); border-radius: 8px; cursor: pointer; background: rgba(47, 147, 245, 0.05);">
                        <input type="radio" name="duration" value="15" checked>
                        <span style="display: block; font-weight: 700; font-size: 18px; margin-top: 8px;">15</span>
                        <span style="font-size: 12px; color: var(--text-dim);">Días</span>
                        <span style="display: block; font-size: 10px; color: var(--accent-blue); font-weight: 600; margin-top: 4px;">$250 MXN</span>
                        <span style="position: absolute; top: -10px; right: 10px; background: var(--accent-orange); color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px;">Popular</span>
                    </label>
                    <label style="text-align: center; padding: 16px; border: 1px solid var(--border-light); border-radius: 8px; cursor: pointer;">
                        <input type="radio" name="duration" value="30">
                        <span style="display: block; font-weight: 700; font-size: 18px; margin-top: 8px;">30</span>
                        <span style="font-size: 12px; color: var(--text-dim);">Días</span>
                         <span style="display: block; font-size: 10px; color: var(--text-main); font-weight: 600; margin-top: 4px;">$450 MXN</span>
                    </label>
                </div>
            </div>

            <div style="text-align: center;">
                <p style="margin-bottom: 16px; font-size: 14px;">
                    Total a pagar: <strong style="font-size: 18px;">$250 MXN</strong>
                </p>
                <button type="button" class="auth-btn full-width" onclick="alert('Funcionalidad de pago simulada')">
                    Activar Promoción
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
