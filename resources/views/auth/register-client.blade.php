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



                        {{-- Términos --}}
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="terms" required style="width: auto; margin-top: 0; accent-color:var(--accent-blue);">
                            <label for="terms" style="margin: 0; font-weight: 400; font-size: 13px; color: var(--text-dim);">
                                Acepto los <a href="{{ route('legal.terminos') }}" class="link">Términos y Condiciones</a> y la <a href="{{ route('legal.privacidad') }}" class="link">Política de Privacidad de Clientes</a>.
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



    .mt-4 { margin-top: 24px; }
    .mt-6 { margin-top: 32px; }
    .text-center { text-align: center; }
</style>



@endsection
