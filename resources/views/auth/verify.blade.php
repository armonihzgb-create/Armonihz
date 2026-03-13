@extends('layouts.app')

@section('head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@endsection

@section('content')

<div class="auth-wrapper">
    <div class="auth-split-layout">

        {{-- Lado Izquierdo --}}
        <div class="auth-left verify-bg">
            <div class="auth-left-content">
                <h2>Un paso más <br>para comenzar.</h2>
                <p>La verificación de correo mantiene tu cuenta segura y conecta mejor con tus clientes.</p>
            </div>
        </div>

        {{-- Lado Derecho --}}
        <div class="auth-right">
            <div class="auth-container">

                {{-- Icono animado --}}
                <div style="text-align:center; margin-bottom:28px;">
                    <div class="email-icon-wrap">
                        <div class="email-pulse"></div>
                        <i class="fa-regular fa-envelope" style="font-size:32px; color:#2f93f5; position:relative;z-index:1;"></i>
                    </div>
                    <h2 style="margin-top:20px;">Verifica tu correo</h2>
                    <p class="auth-subtitle">
                        Hemos enviado un enlace de verificación a<br>
                        <strong style="color:#111827;">{{ Auth::user()->email }}</strong>
                    </p>
                </div>

                {{-- ✅ Flash de éxito --}}
                @if(session('status'))
                    <div class="alert-success" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Pasos --}}
                <div class="verify-steps">
                    <div class="step">
                        <div class="step-num">1</div>
                        <p>Abre tu bandeja de entrada en <strong>{{ Auth::user()->email }}</strong></p>
                    </div>
                    <div class="step">
                        <div class="step-num">2</div>
                        <p>Busca un correo de <strong>Armonihz</strong> con el asunto <em>"Verifica tu correo electrónico"</em></p>
                    </div>
                    <div class="step">
                        <div class="step-num">3</div>
                        <p>Haz clic en el botón <strong>"Verificar correo"</strong> del email</p>
                    </div>
                </div>

                {{-- Reenviar --}}
                <form method="POST" action="{{ route('verification.send') }}" style="margin-top:20px;">
                    @csrf
                    <button type="submit" class="auth-btn full-width">
                        <i class="fa-solid fa-rotate-right" style="margin-right:6px;"></i>
                        Reenviar enlace de verificación
                    </button>
                </form>

                {{-- Cerrar sesión --}}
                <div class="auth-footer text-center mt-6">
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="logout-link">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="font-size:11px;"></i>
                            Cerrar sesión y entrar con otra cuenta
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* ── Email icon con pulso ── */
    .email-icon-wrap {
        width: 88px; height: 88px;
        background: rgba(47,147,245,.1);
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        position: relative;
    }
    .email-pulse {
        position: absolute; inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(47,147,245,.4);
        animation: pulse-ring 2s cubic-bezier(.4,0,.6,1) infinite;
    }
    @keyframes pulse-ring {
        0%   { transform: scale(1);   opacity: 1; }
        100% { transform: scale(1.45); opacity: 0; }
    }

    /* ── Steps ── */
    .verify-steps { display: flex; flex-direction: column; gap: 10px; margin: 24px 0; }
    .step {
        display: flex; align-items: flex-start; gap: 14px;
        background: #f8f9fa;
        border: 1px solid #f0f0f0;
        border-left: 3px solid #2f93f5;
        border-radius: 10px; padding: 12px 16px;
        transition: background .2s;
    }
    .step:hover { background: #f0f7ff; }
    .step-num {
        width: 26px; height: 26px; border-radius: 50%;
        background: linear-gradient(135deg,#6c3fc5,#2f93f5); color: #fff;
        font-size: 12px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .step p { margin: 0; font-size: 13px; color: var(--text-main); line-height: 1.6; }

    /* ── Alerts ── */
    .alert-success {
        display: flex; align-items: flex-start; gap: 10px;
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
        padding: 12px 16px; border-radius: 12px; font-size: 13px;
        font-weight: 500; margin-bottom: 20px; line-height: 1.5;
    }
    .alert-success i { margin-top: 2px; flex-shrink: 0; color: #16a34a; }

    /* ── Logout link ── */
    .logout-link {
        background: none; border: none;
        color: var(--text-dim); font-size: 13px;
        cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px;
        transition: color .2s;
    }
    .logout-link:hover { color: #dc2626; }

    /* Left bg */
    .verify-bg::before {
        background-image: url('https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=2070&auto=format&fit=crop');
    }

    .mt-6 { margin-top: 32px; }
    .text-center { text-align: center; }
    .full-width { width: 100%; }
</style>

<script>
(function() {
    const INTERVAL    = 4000;
    const CHECK_URL   = '{{ route("verification.check") }}';
    const DASHBOARD   = '{{ route("dashboard") }}';
    let attempts      = 0;

    function checkVerification() {
        fetch(CHECK_URL, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.verified) {
                // Update UI to show success
                const icon = document.querySelector('.email-icon-wrap i');
                if (icon) { icon.className = 'fa-solid fa-circle-check'; icon.style.color = '#16a34a'; }
                const h2 = document.querySelector('.auth-container h2');
                if (h2) h2.textContent = '¡Correo verificado!';
                const sub = document.querySelector('.auth-subtitle');
                if (sub) sub.innerHTML = 'Redirigiendo al Dashboard&hellip;';
                // Redirect this (original) tab — no new tab needed
                setTimeout(() => { window.location.href = DASHBOARD; }, 1200);
            } else {
                attempts++;
                if (attempts < 225) setTimeout(checkVerification, INTERVAL);
            }
        })
        .catch(() => { if (attempts < 225) setTimeout(checkVerification, INTERVAL * 2); });
    }

    setTimeout(checkVerification, 5000); // Start after 5s
})();
</script>

@endsection
