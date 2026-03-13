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
                <h2>Revisa tu <br>bandeja de entrada.</h2>
                <p>Ingresa el código de 6 dígitos que te enviamos para continuar de forma segura.</p>
            </div>
        </div>

        {{-- Lado Derecho --}}
        <div class="auth-right">
            <div class="auth-container">

                {{-- Icono animado --}}
                <div style="text-align:center; margin-bottom:28px;">
                    <div class="email-icon-wrap">
                        <div class="email-pulse"></div>
                        <i class="fa-solid fa-key" style="font-size:30px; color:#6c3fc5; position:relative;z-index:1;"></i>
                    </div>
                    <h2 style="margin-top:20px;">Ingresa el código</h2>
                    <p class="auth-subtitle">
                        Enviamos un código de 6 dígitos a<br>
                        <strong style="color:#111827;">{{ $email }}</strong>
                    </p>
                </div>

                {{-- ✅ Flash de éxito --}}
                @if(session('status'))
                    <div class="alert-success" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{!! session('status') !!}</span>
                    </div>
                @endif

                {{-- ❌ Errores --}}
                @if($errors->any())
                    <div class="alert-error" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- Formulario del código --}}
                <form method="POST" action="{{ route('password.verify.code') }}" class="auth-form">
                    @csrf
                    <input type="hidden" name="email" value="{{ old('email', $email) }}">

                    <div class="form-group" style="margin-bottom: 24px;">
                        <label for="code" style="display:block; margin-bottom:10px; text-align:center; font-weight:600;">Código de verificación</label>
                        <div id="code-inputs" style="display:flex; gap:10px; justify-content:center;">
                            @for($i = 0; $i < 6; $i++)
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                       class="otp-digit"
                                       style="width:48px;height:56px;text-align:center;font-size:22px;font-weight:700;
                                              border:1.5px solid #e5e7eb;border-radius:10px;background:#fafafa;
                                              color:#1a202c;transition:border .2s;outline:none;"
                                       oninput="moveNext(this, {{ $i }})"
                                       onkeydown="movePrev(event, {{ $i }}, this)">
                            @endfor
                        </div>
                        {{-- Hidden real input --}}
                        <input type="hidden" name="code" id="code-value">
                        @error('code')
                            <span style="display:block;color:#dc2626;font-size:12px;margin-top:8px;text-align:center;font-weight:500;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="auth-btn full-width" id="verify-btn">
                        <i class="fa-solid fa-shield-halved" style="margin-right:6px;"></i>
                        Verificar código
                    </button>
                </form>

                {{-- Reenviar --}}
                <div class="auth-footer text-center mt-6">
                    <span style="font-size:13px;color:#64748b;">¿No recibiste el código?</span>
                    <a href="{{ route('password.request') }}" style="font-size:13px;color:#6c3fc5;font-weight:600;margin-left:4px;text-decoration:none;">
                        Solicitar nuevo código
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .email-icon-wrap {
        width: 88px; height: 88px;
        background: rgba(108,63,197,.1);
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        position: relative;
    }
    .email-pulse {
        position: absolute; inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(108,63,197,.4);
        animation: pulse-ring 2s cubic-bezier(.4,0,.6,1) infinite;
    }
    @keyframes pulse-ring {
        0%   { transform: scale(1);    opacity: 1; }
        100% { transform: scale(1.45); opacity: 0; }
    }
    .otp-digit:focus { border-color: #6c3fc5 !important; background: #fff !important; box-shadow: 0 0 0 3px rgba(108,63,197,.12); }
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
    }
    .mt-6 { margin-top: 32px; }
    .text-center { text-align: center; }
    .full-width { width: 100%; }
    .verify-bg::before {
        background-image: url('https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=2070&auto=format&fit=crop');
    }
</style>

<script>
const digits = document.querySelectorAll('.otp-digit');
const codeInput = document.getElementById('code-value');
const form = digits[0].closest('form');

function updateHiddenCode() {
    codeInput.value = Array.from(digits).map(d => d.value).join('');
}

function moveNext(input, idx) {
    // Only allow digits
    input.value = input.value.replace(/[^0-9]/g, '');
    updateHiddenCode();
    if (input.value && idx < 5) {
        digits[idx + 1].focus();
    }
}

function movePrev(e, idx, input) {
    if (e.key === 'Backspace' && !input.value && idx > 0) {
        digits[idx - 1].focus();
        digits[idx - 1].value = '';
        updateHiddenCode();
    }
}

// Handle paste — spread digits
document.getElementById('code-inputs').addEventListener('paste', function(e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
    text.split('').slice(0, 6).forEach((ch, i) => { digits[i].value = ch; });
    updateHiddenCode();
    const next = Math.min(text.length, 5);
    digits[next].focus();
});

form.addEventListener('submit', updateHiddenCode);
</script>

@endsection
