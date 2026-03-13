@extends('layouts.app')

@section('head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@endsection

@section('content')

<div class="auth-wrapper">
    <div class="auth-split-layout">

        {{-- Lado Izquierdo --}}
        <div class="auth-left forgot-bg">
            <div class="auth-left-content">
                <h2>Recupera tu acceso <br>de forma segura.</h2>
                <p>Te enviaremos un enlace para crear una nueva contraseña en segundos.</p>
            </div>
        </div>

        {{-- Lado Derecho (Formulario) --}}
        <div class="auth-right">
            <div class="auth-container">

                {{-- Icono central --}}
                <div class="auth-icon-wrap" style="background:rgba(108,63,197,.1);">
                    <i class="fa-solid fa-lock" style="font-size:28px;color:#6c3fc5;"></i>
                </div>

                <div class="auth-header" style="margin-top:20px;">
                    <h2>¿Olvidaste tu contraseña?</h2>
                    <p class="auth-subtitle">Ingresa tu correo y te enviaremos el enlace de recuperación.</p>
                </div>

                {{-- ✅ Mensaje de éxito --}}
                @if(session('status'))
                    <div class="alert-success" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- ❌ Errores --}}
                @if($errors->any())
                    <div class="alert-error" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                    @csrf

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

                    <button type="submit" class="auth-btn full-width mt-4">
                        <i class="fa-regular fa-paper-plane" style="margin-right:6px;"></i>
                        Enviar enlace de recuperación
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
    /* ── Icon hero ── */
    .auth-icon-wrap {
        width: 72px; height: 72px;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 8px;
    }

    /* ── Alerts ── */
    .alert-success {
        display: flex; align-items: flex-start; gap: 10px;
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
        padding: 12px 16px; border-radius: 12px; font-size: 13px;
        font-weight: 500; margin-bottom: 20px; line-height: 1.5;
    }
    .alert-success i { margin-top:2px; flex-shrink:0; color:#16a34a; }

    .alert-error {
        display: flex; align-items: center; gap: 10px;
        background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
        padding: 12px 16px; border-radius: 12px; font-size: 13px;
        font-weight: 500; margin-bottom: 20px;
    }
    .field-error { display:block; color:#dc2626; font-size:12px; margin-top:4px; font-weight:500; }
    .form-group.has-error input { border-color:#fca5a5 !important; background:#fef2f2 !important; }

    /* ── Input ── */
    .input-wrapper { position: relative; }
    .input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #9ca3af; font-size: 14px; pointer-events: none; z-index:1;
    }
    .input-wrapper input { padding-left: 40px !important; }
    .mt-4 { margin-top: 24px; }
    .mt-6 { margin-top: 32px; }
    .text-center { text-align: center; }
</style>

@endsection

@section('scripts')
<script>
// Only activate the listener after user submitted the email (success message visible)
@if(session('status'))
(function() {
    if (!('BroadcastChannel' in window)) return;

    const ch = new BroadcastChannel('armonihz_reset');

    ch.onmessage = function(e) {
        if (e.data && e.data.type === 'reset_url' && e.data.url) {
            ch.close();
            // Navigate THIS (original) tab to the reset form
            window.location.href = e.data.url;
        }
    };

    // Stop listening after 30 min
    setTimeout(() => ch.close(), 30 * 60 * 1000);
})();
@endif
</script>
@endsection
