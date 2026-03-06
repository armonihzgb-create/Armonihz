@extends('layouts.app')

@section('head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@endsection

@section('content')

<div class="auth-wrapper">
    <div class="auth-split-layout">

        {{-- Lado Izquierdo --}}
        <div class="auth-left register-bg" style="justify-content:center;padding-bottom:20%;">
            <div class="auth-left-content">
                <h2>Tu carrera musical,<br>elevada al siguiente nivel.</h2>
                <p style="margin-bottom:28px;">Únete a la plataforma donde músicos y clientes se conectan de forma real y directa.</p>
                <ul class="benefit-list">
                    <li><i data-lucide="briefcase"></i> Recibe solicitudes de contratación</li>
                    <li><i data-lucide="badge-check"></i> Perfil verificado y visible al público</li>
                    <li><i data-lucide="calendar-check"></i> Gestiona tu disponibilidad fácilmente</li>
                    <li><i data-lucide="users"></i> Conecta con clientes de todas partes</li>
                </ul>
            </div>
        </div>

        {{-- Lado Derecho --}}
        <div class="auth-right">
            <div class="auth-container" style="max-width: 480px;">

                <div class="auth-header">
                    <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Logo" width="48" style="margin-bottom: 24px;">
                    <h2>Crear cuenta</h2>
                    <p class="auth-subtitle">Únete a Armonihz en menos de 2 minutos.</p>
                </div>

                {{-- ❌ Errores globales --}}
                @if($errors->any())
                    <div class="alert-error" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form class="auth-form" method="POST" action="{{ route('register.submit') }}" id="reg-form">
                    @csrf

                    {{-- Nombre + Ciudad --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label>Nombre Artístico *</label>
                            <input type="text" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Ej. Banda Real" required>
                            @error('name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group {{ $errors->has('city') ? 'has-error' : '' }}">
                            <label>Ciudad *</label>
                            <input type="text" name="city"
                                   value="{{ old('city') }}"
                                   placeholder="Ej. CDMX" required>
                            @error('city')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Género musical --}}
                    <div class="form-group {{ $errors->has('genre_id') ? 'has-error' : '' }}">
                        <label>Género Musical Principal</label>
                        <select name="genre_id">
                            <option value="">Selecciona tu género principal (opcional)</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Email --}}
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label>Correo Electrónico *</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="nombre@ejemplo.com"
                                   autocomplete="email" required>
                        </div>
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Contraseña --}}
                    <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                        <label>Contraseña *</label>
                        <div class="input-wrapper" style="position:relative;">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="reg-password" name="password"
                                   placeholder="Mínimo 8 caracteres"
                                   autocomplete="new-password"
                                   style="padding-right:44px;"
                                   oninput="checkStrength(this.value); checkMatch();" required>
                            <button type="button" class="pwd-toggle" id="toggle-reg"
                                    onclick="togglePwd('reg-password','toggle-reg')" tabindex="-1">
                                <i class="fa-regular fa-eye-slash" style="font-size:16px;color:#6b7280;"></i>
                            </button>
                        </div>
                        {{-- Barra de fortaleza --}}
                        <div class="strength-bar-wrap">
                            <div class="strength-bar" id="strength-bar"></div>
                        </div>
                        <span class="strength-label" id="strength-label"></span>

                        {{-- Requisitos mínimos --}}
                        <div class="pwd-requirements" id="pwd-requirements">
                            <span id="req-length"  class="req-item"><i class="fa-solid fa-circle req-dot"></i> Mínimo 8 caracteres</span>
                            <span id="req-upper"   class="req-item"><i class="fa-solid fa-circle req-dot"></i> Una letra mayúscula</span>
                            <span id="req-number"  class="req-item"><i class="fa-solid fa-circle req-dot"></i> Un número</span>
                            <span id="req-special" class="req-item"><i class="fa-solid fa-circle req-dot"></i> Un carácter especial</span>
                        </div>

                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="form-group" id="confirm-group">
                        <label>Confirmar Contraseña *</label>
                        <div class="input-wrapper" style="position:relative;">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="reg-confirm" name="password_confirmation"
                                   placeholder="Repite tu contraseña"
                                   autocomplete="new-password"
                                   style="padding-right:44px;"
                                   oninput="checkMatch()" 
                                   onpaste="return false;" required>
                            <button type="button" class="pwd-toggle" id="toggle-confirm"
                                    onclick="togglePwd('reg-confirm','toggle-confirm')" tabindex="-1">
                                <i class="fa-regular fa-eye" style="font-size:16px;color:#6b7280;"></i>
                            </button>
                        </div>
                        <span class="field-error" id="match-error" style="display:none;">Las contraseñas no coinciden.</span>
                    </div>

                    {{-- Términos --}}
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="terms" required style="width: auto; margin-top: 0; accent-color:var(--accent-blue);">
                        <label for="terms" style="margin: 0; font-weight: 400; font-size: 13px; color: var(--text-dim);">
                            Acepto los <a href="#" class="link">Términos y Condiciones</a> y la Política de Privacidad.
                        </label>
                    </div>

                    <button type="submit" class="auth-btn full-width mt-4">
                        Registrarme
                    </button>
                </form>

                <div class="auth-footer text-center mt-6">
                    <p>
                        ¿Ya tienes cuenta?
                        <a href="{{ route('login') }}" class="highlight-link">Iniciar sesión</a>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Alertas */
    .alert-error {
        display: flex; align-items: flex-start; gap: 10px;
        background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
        padding: 12px 16px; border-radius: 12px; font-size: 13px;
        font-weight: 500; margin-bottom: 20px; line-height: 1.6;
        animation: shake .35s ease;
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%  { transform: translateX(-5px); }
        75%  { transform: translateX(5px); }
    }
    .field-error { display:block; color:#dc2626; font-size:12px; margin-top:4px; font-weight:500; }
    .form-group.has-error input,
    .form-group.has-error select { border-color:#fca5a5 !important; background:#fef2f2 !important; }

    /* Input con ícono + focus */
    .input-wrapper { position: relative; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #9ca3af; font-size: 14px; pointer-events: none; z-index:1;
        transition: color .2s;
    }
    .input-wrapper:focus-within .input-icon { color: #6c3fc5; }
    .input-wrapper input { padding-left: 40px !important; }
    .input-wrapper input:focus {
        outline: none;
        border-color: #6c3fc5 !important;
        box-shadow: 0 0 0 3px rgba(108,63,197,.1);
    }

    /* Strength bar */
    .strength-bar-wrap { height:4px; background:#e5e7eb; border-radius:2px; margin-top:8px; overflow:hidden; }
    .strength-bar { height:100%; width:0%; border-radius:2px; transition:width .3s, background .3s; }
    .strength-label { font-size:11px; font-weight:600; margin-top:4px; display:block; }

    .checkbox-wrapper { display:flex; align-items:flex-start; gap:12px; margin-top:16px; }
    .link { color: var(--accent-blue); text-decoration: none; }
    .link:hover { text-decoration: underline; }

    .benefit-list { list-style:none; padding:0; margin:24px 0 0 0; }
    .benefit-list li { display:flex; align-items:center; gap:12px; font-size:16px; margin-bottom:12px; opacity:.9; }
    .benefit-list i { width:20px; height:20px; color:#4ade80; }

    .register-bg::before {
        background-image: url('https://images.unsplash.com/photo-1471478331149-c72f17e33c73?q=80&w=1469&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
    }
    .auth-right { overflow-y: auto; }

    /* Requisitos de contraseña */
    .pwd-requirements {
        display: none;
        flex-direction: column;
        gap: 4px;
        margin-top: 10px;
        padding: 10px 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }
    .pwd-requirements.visible { display: flex; }
    .req-item {
        font-size: 12px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: color .2s;
    }
    .req-item .req-dot { font-size: 7px; flex-shrink: 0; }
    .req-item.ok { color: #16a34a; }
    .req-item.ok .req-dot { color: #16a34a; }
    .req-item.fail { color: #dc2626; }
    .req-item.fail .req-dot { color: #dc2626; }

    .mt-4 { margin-top: 24px; }
    .mt-6 { margin-top: 32px; }
    .text-center { text-align: center; }
</style>

<script>
function togglePwd(inputId, btnId) {
    const input = document.getElementById(inputId);
    const btn   = document.getElementById(btnId);
    const icon  = btn.querySelector('i');
    const show  = input.type === 'password';
    input.type  = show ? 'text' : 'password';
    icon.className = show ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
}

function checkStrength(val) {
    const bar = document.getElementById('strength-bar');
    const lbl = document.getElementById('strength-label');
    const req = document.getElementById('pwd-requirements');

    // Requirements checklist
    const rules = [
        { id:'req-length',  ok: val.length >= 8 },
        { id:'req-upper',   ok: /[A-Z]/.test(val) },
        { id:'req-number',  ok: /[0-9]/.test(val) },
        { id:'req-special', ok: /[^A-Za-z0-9]/.test(val) },
    ];
    if (val.length > 0) {
        req.classList.add('visible');
        rules.forEach(r => {
            const el = document.getElementById(r.id);
            el.classList.toggle('ok',   r.ok);
            el.classList.toggle('fail', !r.ok);
        });
    } else {
        req.classList.remove('visible');
        rules.forEach(r => {
            const el = document.getElementById(r.id);
            el.classList.remove('ok','fail');
        });
    }

    // Strength bar
    let score = rules.filter(r => r.ok).length;
    const levels = [
        { w:'25%',  bg:'#ef4444', txt:'Muy débil', color:'#ef4444' },
        { w:'50%',  bg:'#f97316', txt:'Débil',     color:'#f97316' },
        { w:'75%',  bg:'#eab308', txt:'Regular',   color:'#eab308' },
        { w:'100%', bg:'#22c55e', txt:'Fuerte',    color:'#22c55e' },
    ];
    if (val.length === 0) { bar.style.width='0'; lbl.textContent=''; return; }
    const lvl = levels[Math.max(0, score - 1)];
    bar.style.width      = lvl.w;
    bar.style.background = lvl.bg;
    lbl.textContent      = lvl.txt;
    lbl.style.color      = lvl.color;

    // Also re-validate confirm field
    checkMatch();
}

function checkMatch() {
    const p1  = document.getElementById('reg-password').value;
    const p2  = document.getElementById('reg-confirm').value;
    const err = document.getElementById('match-error');
    const grp = document.getElementById('confirm-group');
    // Only evaluate if user has typed something in confirm field
    if (p2.length === 0) { err.style.display='none'; grp.classList.remove('has-error'); return; }
    const ok = p1 === p2;
    err.style.display = ok ? 'none' : 'block';
    grp.classList.toggle('has-error', !ok);
}

// Prevent submit if passwords don't match
document.getElementById('reg-form').addEventListener('submit', function(e) {
    checkMatch();
    if (document.getElementById('match-error').style.display !== 'none') {
        e.preventDefault();
    }
});
</script>

@endsection