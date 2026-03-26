@extends('layouts.app')

@section('head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Google Identity Services (GSI) --}}
    <script src="https://accounts.google.com/gsi/client" async defer></script>
@endsection

@section('content')

<div class="auth-wrapper">
    <div class="auth-split-layout">

        {{-- Lado Izquierdo (Imagen) --}}
        <div class="auth-left">
            <div class="auth-left-content">
                <h2>Descubre Armonihz, <br>tu conexión.</h2>
                <p>Únete a la plataforma de músicos y conecta con clientes.</p>
            </div>
        </div>

        {{-- Lado Derecho (Formulario) --}}
        <div class="auth-right">
            <div class="auth-container">

                <div class="auth-header">
                    <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Logo" width="48" style="margin-bottom: 24px;">
                    <h2>Bienvenido de nuevo</h2>
                    <p class="auth-subtitle">Ingresa tus datos para acceder a tu panel.</p>
                </div>

                {{-- Mensaje de éxito (ej. contraseña restablecida) --}}
                @if(session('status'))
                    <div class="alert-success" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('status') }}
                    </div>
                @endif

                {{-- ❌ Errores de autenticación --}}
                @if($errors->any())
                    <div class="alert-error" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form class="auth-form" method="POST" action="{{ route('login') }}" novalidate id="login-form">
                    @csrf

                    {{-- Email --}}
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="email">Correo electrónico</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="ejemplo@correo.com"
                                   autocomplete="email">
                        </div>
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Contraseña --}}
                    <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <label for="password" style="margin: 0;">Contraseña</label>
                            <a href="{{ route('password.request') }}" class="forgot-link-small">¿Olvidaste tu contraseña?</a>
                        </div>
                        <div class="input-wrapper" style="position:relative;">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password" name="password"
                                   placeholder="••••••••"
                                   autocomplete="current-password"
                                   style="padding-right:44px;">
                            <button type="button" class="pwd-toggle" id="toggle-login"
                                    onclick="togglePwd('password','toggle-login')" tabindex="-1">
                                <i class="fa-regular fa-eye-slash" style="font-size:16px;color:#6b7280;"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Recordarme --}}
                    <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                        <input type="checkbox" id="remember" name="remember"
                               style="width:auto;margin:0;accent-color:var(--accent-blue);"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" style="margin:0;font-weight:400;font-size:13px;color:var(--text-dim);cursor:pointer;">
                            Mantener sesión iniciada
                        </label>
                    </div>

                    <button type="submit" class="auth-btn full-width mt-4" id="submit-btn">
                        Iniciar Sesión
                    </button>
                </form>

                <div class="auth-footer text-center mt-6">
                    <p>
                        ¿Aún no tienes cuenta?
                        <a href="{{ route('register') }}" class="highlight-link">Regístrate gratis</a>
                    </p>
                </div>

                {{-- ── Google Sign-In (Native GSI Button) ── --}}
                <div class="auth-divider"><span>o continúa con</span></div>

                <div id="g_id_onload"
                     data-client_id="{{ env('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE') }}"
                     data-context="signin"
                     data-ux_mode="popup"
                     data-callback="handleGoogleCallback"
                     data-auto_prompt="false">
                </div>

                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="outline"
                     data-text="signin_with"
                     data-size="large"
                     data-logo_alignment="left"
                     data-width="300">
                </div>
                {{-- Hidden form to POST Firebase credential to Laravel --}}
                <form id="google-form" method="POST" action="{{ route('auth.google.callback') }}" style="display:none">
                    @csrf
                    <input type="hidden" name="credential" id="google-credential">
                </form>

            </div>
        </div>

    </div>
</div>

<style>
    /* ── Alertas ── */
    .alert-success {
        display: flex; align-items: flex-start; gap: 10px;
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
        padding: 12px 16px; border-radius: 12px; font-size: 13px;
        font-weight: 500; margin-bottom: 20px; line-height: 1.5;
    }
    .alert-success i { margin-top: 2px; flex-shrink: 0; color: #16a34a; }

    .alert-error {
        display: flex; align-items: center; gap: 10px;
        background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
        padding: 12px 16px; border-radius: 12px; font-size: 13px;
        font-weight: 500; margin-bottom: 20px;
        animation: shake 0.35s ease;
    }
    .alert-error i { flex-shrink: 0; }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%  { transform: translateX(-5px); }
        75%  { transform: translateX(5px); }
    }

    /* ── Field errors ── */
    .field-error { display: block; color: #dc2626; font-size: 12px; margin-top: 4px; font-weight: 500; }
    .form-group.has-error input,
    .form-group.has-error select { border-color: #fca5a5 !important; background: #fef2f2 !important; }

    /* ── Input con ícono + focus ── */
    .input-wrapper { position: relative; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #9ca3af; font-size: 14px; pointer-events: none; z-index: 1;
        transition: color .2s;
    }
    .input-wrapper:focus-within .input-icon { color: #6c3fc5; }
    .input-wrapper input { padding-left: 40px !important; }
    .input-wrapper input:focus {
        outline: none;
        border-color: #6c3fc5 !important;
        box-shadow: 0 0 0 3px rgba(108,63,197,.1);
    }

    /* ── Forgot link ── */
    .forgot-link-small {
        font-size: 12px; color: var(--accent-blue);
        text-decoration: none; font-weight: 500;
        transition: color .2s;
        text-underline-offset: 3px;
    }
    .forgot-link-small:hover { color: #6c3fc5; text-decoration: underline; }

    .mt-4 { margin-top: 24px; }
    .mt-6 { margin-top: 32px; }
    .text-center { text-align: center; }

    /* ── Divider ── */
    .auth-divider {
        display: flex; align-items: center; gap: 12px;
        margin: 24px 0 16px;
        color: #9ca3af; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: .05em;
    }
    .auth-divider::before,
    .auth-divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

    /* ── GSI Wrapper ── */
    .g_id_signin {
        display: flex; justify-content: center; width: 100%;
    }

    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
function togglePwd(inputId, btnId) {
    const input = document.getElementById(inputId);
    const btn   = document.getElementById(btnId);
    const icon  = btn.querySelector('i');
    const show  = input.type === 'password';
    input.type  = show ? 'text' : 'password';
    icon.className = show ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
    icon.style.fontSize = '16px';
    icon.style.color = '#6b7280';
}

// Client-side validation feedback
document.getElementById('login-form').addEventListener('submit', function(e) {
    let hasError = false;
    const fields = [
        { id: 'email', msg: 'El correo electrónico es obligatorio.' },
        { id: 'password', msg: 'La contraseña es obligatoria.' },
    ];
    // Clear previous JS errors
    document.querySelectorAll('.js-error').forEach(el => el.remove());
    document.querySelectorAll('.js-field-error').forEach(el => {
        el.classList.remove('has-error');
    });

    fields.forEach(({ id, msg }) => {
        const input = document.getElementById(id);
        if (!input.value.trim()) {
            const wrapper = input.closest('.form-group');
            wrapper.classList.add('has-error');
            const err = document.createElement('span');
            err.className = 'field-error js-error';
            err.textContent = msg;
            input.closest('.input-wrapper, div').parentElement.appendChild(err);
            hasError = true;
        }
    });

    if (hasError) e.preventDefault();
});

// ── Google Sign-In Callback (GSI) ──
function handleGoogleCallback(response) {
    if (response.credential) {
        document.getElementById('google-credential').value = response.credential;
        document.getElementById('google-form').submit();
    }
}

</script>

@endsection