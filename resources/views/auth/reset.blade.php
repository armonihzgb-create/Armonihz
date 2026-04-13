@extends('layouts.app')

@section('head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@endsection

@section('content')

<div class="auth-wrapper">
    <div class="auth-split-layout">

        {{-- Lado Izquierdo --}}
        <div class="auth-left reset-bg">
            <div class="auth-left-content">
                <h2>Crea una nueva <br>contraseña segura.</h2>
                <p>Usa al menos 8 caracteres combinando letras, números y símbolos.</p>
            </div>
        </div>

        {{-- Lado Derecho --}}
        <div class="auth-right">
            <div class="auth-container">

                {{-- Icono hero --}}
                <div class="auth-icon-wrap" style="background:rgba(34,197,94,.1);">
                    <i class="fa-solid fa-shield-halved" style="font-size:28px;color:#16a34a;"></i>
                </div>

                <div class="auth-header" style="margin-top:20px;">
                    <h2>Nueva contraseña</h2>
                    <p class="auth-subtitle">Ingresa y confirma tu nueva contraseña.</p>
                </div>

                {{-- ❌ Errores --}}
                @if($errors->any())
                    <div class="alert-error" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form class="auth-form" method="POST" action="{{ route('password.update') }}" id="reset-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email (pre-poblado) --}}
                    @php
                        // Fix for edge cases where the URL hash appends the query string inside the email variable itself
                        $cleanEmail = explode('?', $email)[0];
                    @endphp
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="email">Correo electrónico</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email', $cleanEmail) }}"
                                   placeholder="ejemplo@correo.com"
                                   autocomplete="email"
                                   style="background:#f9fafb;color:#6b7280;" readonly>
                        </div>
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nueva contraseña --}}
                    <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                        <label for="password">Nueva contraseña</label>
                        <div class="input-wrapper" style="position:relative;">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password" name="password"
                                   placeholder="Mínimo 8 caracteres"
                                   autocomplete="new-password"
                                   style="padding-right:44px;"
                                   oninput="checkStrength(this.value); checkMatch();">
                            <button type="button" class="pwd-toggle" id="toggle-pwd"
                                    onclick="togglePwd('password','toggle-pwd')" tabindex="-1">
                                <i class="fa-regular fa-eye-slash" style="font-size:16px;color:#6b7280;"></i>
                            </button>
                        </div>
                        {{-- Barra fortaleza --}}
                        <div class="strength-bar-wrap">
                            <div class="strength-bar" id="strength-bar"></div>
                        </div>
                        <span class="strength-label" id="strength-label"></span>

                     {{-- Requisitos --}}
                        <div class="pwd-requirements" id="pwd-requirements">
                            <span id="req-length"  class="req-item"><i class="fa-solid fa-circle req-dot"></i> Mínimo 8 caracteres</span>
                            <span id="req-upper"   class="req-item"><i class="fa-solid fa-circle req-dot"></i> Una letra mayúscula</span>
                            <span id="req-lower"   class="req-item"><i class="fa-solid fa-circle req-dot"></i> Una letra minúscula</span>
                            <span id="req-number"  class="req-item"><i class="fa-solid fa-circle req-dot"></i> Un número</span>
                            <span id="req-special" class="req-item"><i class="fa-solid fa-circle req-dot"></i> Un carácter especial</span>
                        </div>

                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirmar contraseña --}}
                    <div class="form-group" id="confirm-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <div class="input-wrapper" style="position:relative;">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   placeholder="Repite tu contraseña"
                                   autocomplete="new-password"
                                   style="padding-right:44px;"
                                   oninput="checkMatch()">
                            <button type="button" class="pwd-toggle" id="toggle-confirm"
                                    onclick="togglePwd('password_confirmation','toggle-confirm')" tabindex="-1">
                                <i class="fa-regular fa-eye-slash" style="font-size:16px;color:#6b7280;"></i>
                            </button>
                        </div>
                        <span class="field-error" id="match-error" style="display:none;">Las contraseñas no coinciden.</span>
                    </div>

                    <button type="submit" class="auth-btn full-width mt-4">
                        <i class="fa-solid fa-shield-halved" style="margin-right:6px;"></i>
                        Cambiar contraseña
                    </button>
                </form>

                <div class="auth-footer text-center mt-6">
                    <a href="{{ route('login') }}" class="back-link">
                        <i class="fa-solid fa-arrow-left" style="font-size:11px;"></i>
                        Volver al inicio de sesión
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .auth-icon-wrap {
        width: 72px; height: 72px;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 8px;
    }
    .alert-error {
        display: flex; align-items: center; gap: 10px;
        background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
        padding: 12px 16px; border-radius: 12px; font-size: 13px;
        font-weight: 500; margin-bottom: 20px;
    }
    .field-error { display:block; color:#dc2626; font-size:12px; margin-top:4px; font-weight:500; }
    .form-group.has-error input { border-color:#fca5a5 !important; background:#fef2f2 !important; }

    .input-wrapper { position: relative; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #9ca3af; font-size: 14px; pointer-events: none; z-index:1;
    }
    .input-wrapper input { padding-left: 40px !important; }

    /* Strength */
    .strength-bar-wrap { height:4px; background:#e5e7eb; border-radius:2px; margin-top:8px; overflow:hidden; }
    .strength-bar { height:100%; width:0%; border-radius:2px; transition:width .3s, background .3s; }
    .strength-label { font-size:11px; font-weight:600; margin-top:4px; display:block; }

    /* Requirements */
    .pwd-requirements {
        display: none; flex-direction: column; gap: 4px;
        margin-top: 10px; padding: 10px 12px;
        background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;
    }
    .pwd-requirements.visible { display: flex; }
    .req-item { font-size:12px; color:#6b7280; display:flex; align-items:center; gap:7px; transition:color .2s; }
    .req-item .req-dot { font-size:7px; flex-shrink:0; }
    .req-item.ok { color:#16a34a; } .req-item.ok .req-dot { color:#16a34a; }
    .req-item.fail { color:#dc2626; } .req-item.fail .req-dot { color:#dc2626; }

    .back-link {
        display: inline-flex; align-items: center; gap: 6px;
        color: var(--text-dim); font-size: 13px; text-decoration: none; transition: color .2s;
    }
    .back-link:hover { color: #6c3fc5; }

    .reset-bg::before {
        background-image: url('https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=2070&auto=format&fit=crop');
    }
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
    const rules = [
        { id:'req-length',  ok: val.length >= 8 },
        { id:'req-upper',   ok: /[A-Z]/.test(val) },
        { id:'req-lower',   ok: /[a-z]/.test(val) }, // NUEVA REGLA MINÚSCULA
        { id:'req-number',  ok: /[0-9]/.test(val) },
        { id:'req-special', ok: /[^A-Za-z0-9]/.test(val) },
    ];
    if (val.length > 0) {
        req.classList.add('visible');
        rules.forEach(r => { const el=document.getElementById(r.id); if(el) { el.classList.toggle('ok',r.ok); el.classList.toggle('fail',!r.ok); }});
    } else {
        req.classList.remove('visible');
        rules.forEach(r => { const el=document.getElementById(r.id); if(el) el.classList.remove('ok','fail'); });
    }
    let score = rules.filter(r => r.ok).length;
    const levels = [
        { w:'20%', bg:'#ef4444', txt:'Muy débil', color:'#ef4444' },
        { w:'40%', bg:'#f97316', txt:'Débil',     color:'#f97316' },
        { w:'60%', bg:'#eab308', txt:'Regular',   color:'#eab308' },
        { w:'80%', bg:'#3b82f6', txt:'Buena',     color:'#3b82f6' },
        { w:'100%', bg:'#22c55e', txt:'Fuerte',   color:'#22c55e' },
    ];
    if (val.length === 0) { bar.style.width='0'; lbl.textContent=''; return; }
    const lvl = levels[Math.max(0, score - 1)];
    bar.style.width = lvl.w; bar.style.background = lvl.bg;
    lbl.textContent = lvl.txt; lbl.style.color = lvl.color;
}

function checkMatch() {
    const p1  = document.getElementById('password').value;
    const p2  = document.getElementById('password_confirmation').value;
    const err = document.getElementById('match-error');
    const grp = document.getElementById('confirm-group');
    if (p2.length === 0) { err.style.display='none'; grp.classList.remove('has-error'); return; }
    const ok = p1 === p2;
    err.style.display = ok ? 'none' : 'block';
    grp.classList.toggle('has-error', !ok);
}

document.getElementById('reset-form').addEventListener('submit', function(e) {
    checkMatch();
    if (document.getElementById('match-error').style.display !== 'none') e.preventDefault();
});
</script>

@endsection
