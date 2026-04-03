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
                <h2>Lleva la música en vivo<br>en la palma de tu mano.</h2>
                <p style="margin-bottom:28px;">Regístrate ahora y sé el primero en conectarte directamente con los mejores artistas locales a través de la nueva app móvil de Armonihz.</p>
                <ul class="benefit-list">
                    <li><i data-lucide="smartphone"></i> Acceso a la App Móvil 100% gratuita</li>
                    <li><i data-lucide="music-4"></i> Catálogo verificados de músicos profesionales</li>
                    <li><i data-lucide="shield-check"></i> Contrataciones fáciles y seguras en segundos</li>
                    <li><i data-lucide="ticket"></i> Ofertas y promociones exclusivas en tu ciudad</li>
                </ul>
            </div>
        </div>

        {{-- Lado Derecho --}}
        <div class="auth-right">
            <div class="auth-container" style="max-width: 480px;">

                <div class="auth-header">
                    <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Logo" width="48" style="margin-bottom: 24px;">
                    <h2>Registro de Clientes</h2>
                    <p class="auth-subtitle">Crea tu cuenta de cliente de forma anticipada. Te avisaremos el día del lanzamiento de la App Móvil.</p>
                </div>

                {{-- ✅ Éxito --}}
                @if(session('status'))
                    <div class="alert-success" role="alert" style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; background: rgba(22, 163, 74, 0.1); border: 1.5px solid rgba(22, 163, 74, 0.3); border-radius: 16px; margin-bottom: 30px;">
                        <i class="fa-solid fa-circle-check" style="font-size: 48px; color: #16a34a; margin-bottom: 16px;"></i>
                        <h3 style="margin: 0 0 10px; color: #1e293b; font-size: 18px;">{{ session('status') }}</h3>
                        <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">Puedes regresar a la <a href="/" class="highlight-link">Página Principal</a> por ahora.</p>
                    </div>
                @else
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

                    <form class="auth-form" method="POST" action="{{ route('register.client.submit') }}" id="reg-form">
                        @csrf

                        {{-- Nombre + Apellido --}}
                        <div class="form-row-auth">
                            <div class="form-group {{ $errors->has('nombre') ? 'has-error' : '' }}">
                                <label>Nombre *</label>
                                <input type="text" name="nombre"
                                       value="{{ old('nombre') }}"
                                       placeholder="Tu nombre" required>
                                @error('nombre')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group {{ $errors->has('apellido') ? 'has-error' : '' }}">
                                <label>Apellido *</label>
                                <input type="text" name="apellido"
                                       value="{{ old('apellido') }}"
                                       placeholder="Tu apellido" required>
                                @error('apellido')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
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

                        {{-- Teléfono --}}
                        <div class="form-group {{ $errors->has('telefono') ? 'has-error' : '' }}">
                            <label>Teléfono (Móvil) *</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-phone input-icon"></i>
                                <input type="tel" name="telefono"
                                       value="{{ old('telefono') }}"
                                       placeholder="10 dígitos"
                                       autocomplete="tel" required>
                            </div>
                            @error('telefono')<span class="field-error">{{ $message }}</span>@enderror
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
                                Acepto los <a href="#" class="link">Términos y Condiciones</a> y la Política de Privacidad de Clientes.
                            </label>
                        </div>

                        <button type="submit" class="auth-btn full-width mt-4">
                            Preparar mi Cuenta Móvil
                        </button>
                    </form>
                @endif
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
        background-image: url('https://images.unsplash.com/photo-1540039155733-d76e6e488312?q=80&w=1470&auto=format&fit=crop');
        filter: brightness(0.7);
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
    if(!input || !btn) return;
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
            if(el) {
                el.classList.toggle('ok',   r.ok);
                el.classList.toggle('fail', !r.ok);
            }
        });
    } else {
        req.classList.remove('visible');
        rules.forEach(r => {
            const el = document.getElementById(r.id);
            if(el) el.classList.remove('ok','fail');
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
    const p1el  = document.getElementById('reg-password');
    const p2el  = document.getElementById('reg-confirm');
    if(!p1el || !p2el) return;
    
    const p1 = p1el.value;
    const p2 = p2el.value;
    const err = document.getElementById('match-error');
    const grp = document.getElementById('confirm-group');
    // Only evaluate if user has typed something in confirm field
    if (p2.length === 0) { err.style.display='none'; grp.classList.remove('has-error'); return; }
    const ok = p1 === p2;
    err.style.display = ok ? 'none' : 'block';
    grp.classList.toggle('has-error', !ok);
}

// Prevent submit if passwords don't match
const frm = document.getElementById('reg-form');
if(frm) {
    frm.addEventListener('submit', function(e) {
        checkMatch();
        const err = document.getElementById('match-error');
        if (err && err.style.display !== 'none') {
            e.preventDefault();
        }
    });
}
</script>

@endsection
