@extends('layouts.app')

@section('content')

{{-- Contenedor Principal (Flexbox) --}}
<div class="dashboard-wrapper">

    {{-- MOBILE HEADER (Visible only on < 768px) --}}
    <div class="mobile-header">
        <div class="mobile-brand">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <span>Armonihz</span>
        </div>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i data-lucide="menu"></i>
        </button>
    </div>

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="dashboard-sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <h3>Armonihz</h3>
            <button class="mobile-close-btn" onclick="toggleMobileMenu()">
                <i data-lucide="x"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            {{-- Grupo 1: General --}}
            <div class="nav-group">
                <span class="group-title">General</span>
                <ul>
                    <li>
                        <a href="/dashboard" class="{{ Request::is('dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/profile" class="{{ Request::is('profile') ? 'active' : '' }}">
                            <i data-lucide="user"></i> Perfil
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Grupo 2: Oportunidades (NUEVO) --}}
            <div class="nav-group">
                <span class="group-title">Oportunidades</span>
                <ul>
                    <li>
                        <a href="/castings" class="{{ Request::is('castings*') ? 'active' : '' }}">
                            <i data-lucide="mic-2"></i> Castings Activos
                        </a>
                    </li>
                    <li>
                        <a href="/promote" class="{{ Request::is('promote') ? 'active' : '' }}">
                            <i data-lucide="eye"></i> Promocionar Perfil
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Grupo 3: Gestión --}}
            <div class="nav-group">
                <span class="group-title">Gestión</span>
                <ul>
                    <li>
                        <a href="/multimedia" class="{{ Request::is('multimedia') ? 'active' : '' }}">
                            <i data-lucide="image"></i> Multimedia
                        </a>
                    </li>
                    <li>
                        <a href="/availability" class="{{ Request::is('availability') ? 'active' : '' }}">
                            <i data-lucide="calendar"></i> Disponibilidad
                        </a>
                    </li>
                    <li>
                        <a href="/my-promotions" class="{{ Request::is('my-promotions') ? 'active' : '' }}">
                            <i data-lucide="bar-chart-2"></i> Mis Promociones
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Grupo 4: Interacción --}}
            <div class="nav-group">
                <span class="group-title">Interacción</span>
                <ul>
                    <li>
                        <a href="/requests" class="{{ Request::is('requests') ? 'active' : '' }}">
                            <i data-lucide="clipboard-list"></i> Solicitudes
                            @if(isset($pendingRequestsCount) && $pendingRequestsCount > 0)
                                <span class="badge">{{ $pendingRequestsCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="/reviews" class="{{ Request::is('reviews') ? 'active' : '' }}">
                            <i data-lucide="star"></i> Reseñas
                        </a>
                    </li>
                </ul>
            </div>



            {{-- Logout al final --}}
            <div class="nav-group mt-auto">
                <ul>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="logout-link" onclick="event.preventDefault(); showLogoutModal();">
                            <i data-lucide="log-out"></i> Cerrar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="dashboard-content">
        @yield('dashboard-content')
    </main>

</div>

{{-- Logout Confirmation Modal --}}
<div id="logout-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.45); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:20px; padding:32px 28px; max-width:380px; width:90%; box-shadow:0 24px 60px rgba(0,0,0,.2); text-align:center; animation:logoutFadeIn .2s ease;">
        <div style="width:56px;height:56px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i data-lucide="log-out" style="width:24px;height:24px;color:#dc2626;"></i>
        </div>
        <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 8px;">¿Cerrar sesión?</h3>
        <p style="font-size:14px;color:#64748b;margin:0 0 24px;line-height:1.6;">Tu sesión se cerrará y tendrás que iniciar sesión de nuevo para acceder.</p>
        <div style="display:flex;gap:10px;">
            <button onclick="hideLogoutModal()" style="flex:1;padding:11px;border-radius:9px;border:1.5px solid #e2e8f0;background:#f8fafc;color:#475569;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s;">
                Cancelar
            </button>
            <button onclick="document.getElementById('logout-form').submit()" style="flex:1;padding:11px;border-radius:9px;border:none;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(220,38,38,.3);transition:opacity .2s;">
                Sí, cerrar sesión
            </button>
        </div>
    </div>
</div>

{{-- Scripts al final --}}
<script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
    lucide.createIcons();

    function showLogoutModal() {
        const m = document.getElementById('logout-modal');
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function hideLogoutModal() {
        const m = document.getElementById('logout-modal');
        m.style.display = 'none';
        document.body.style.overflow = '';
    }
    // Mobile Menu Toggle
    function toggleMobileMenu() {
        const sidebar = document.getElementById('dashboard-sidebar');
        sidebar.classList.toggle('active');
        // Prevent body scroll when menu is open
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('dashboard-sidebar');
        const menuBtn = document.querySelector('.mobile-menu-btn');
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && (!menuBtn || !menuBtn.contains(e.target))) {
                toggleMobileMenu();
            }
        }
    });

    // Close on backdrop click (Logout)
    document.getElementById('logout-modal').addEventListener('click', function(e) {
        if (e.target === this) hideLogoutModal();
    });
</script>
<style>
    @keyframes logoutFadeIn {
        from { opacity:0; transform:scale(.95) translateY(8px); }
        to   { opacity:1; transform:scale(1)  translateY(0); }
    }
</style>

@endsection
