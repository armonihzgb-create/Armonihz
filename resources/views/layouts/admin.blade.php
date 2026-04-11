@extends('layouts.app')

@section('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-bg: #f8fafc;
            --sidebar-bg: #ffffff;
            --primary-accent: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1, #4f46e5);
            --sidebar-width: 280px;
            --transition-speed: 0.25s;
        }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background-color: var(--admin-bg) !important;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR MODERN */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform var(--transition-speed);
        }

        .sidebar-brand {
            padding: 32px 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .brand-text h3 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .admin-badge {
            font-size: 11px;
            font-weight: 800;
            color: var(--primary-accent);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #f5f3ff;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .sidebar-nav {
            padding: 24px 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 24px;
            overflow-y: auto;
        }

        .nav-group .group-title {
            display: block;
            margin-bottom: 8px;
            padding-left: 12px;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav li {
            margin-bottom: 4px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            color: #64748b;
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .sidebar-nav a i {
            width: 20px;
            height: 20px;
            transition: transform 0.2s;
        }

        .sidebar-nav a:hover {
            color: var(--primary-accent);
            background: #f8fafc;
        }

        .sidebar-nav a:hover i {
            transform: translateX(2px);
        }

        .sidebar-nav a.active {
            background: #f1f5f9;
            color: #0f172a;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }

        .sidebar-nav a.active i {
            color: var(--primary-accent);
        }

        .badge {
            margin-left: auto;
            padding: 2px 8px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 800;
        }

        .badge.warning { background: #fef3c7; color: #b45309; }
        .badge.danger { background: #fee2e2; color: #dc2626; }

        .dashboard-content {
            flex: 1;
            padding: 40px;
            margin-left: var(--sidebar-width);
            max-width: calc(100% - var(--sidebar-width));
        }

        /* MOBILE HEADER */
        .mobile-header {
            display: none;
            padding: 16px 20px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .mobile-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mobile-brand img { height: 32px; }
        .mobile-brand span { font-weight: 800; font-size: 16px; }

        .mobile-menu-btn {
            background: none;
            border: none;
            color: #0f172a;
            cursor: pointer;
        }

        .mobile-close-btn { display: none; }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .mobile-header { display: flex; justify-content: space-between; align-items: center; }
            .dashboard-content { margin-left: 0; max-width: 100%; padding: 24px 20px; }
            .mobile-close-btn { 
                display: flex; margin-left: auto; background: none; border: none; color: #94a3b8; 
                padding: 8px; border-radius: 50%;
            }
        }

        /* REUTILIZABLES PREMIUM QUE YA USAMOS EN VISTAS */
        .page-header-premium {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;
            background: #ffffff; padding: 24px 32px; border-radius: 20px; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        }
    </style>
    @yield('admin-head')
@endsection

@section('content')

<div class="dashboard-wrapper">

    {{-- MOBILE HEADER --}}
    <div class="mobile-header">
        <div class="mobile-brand">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <span>Admin</span>
        </div>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i data-lucide="menu"></i>
        </button>
    </div>

    {{-- SIDEBAR ADMIN --}}
    <aside class="sidebar" id="dashboard-sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <div class="brand-text">
                <h3>Armonihz</h3>
                <span class="admin-badge">Admin</span>
            </div>
            <button class="mobile-close-btn" onclick="toggleMobileMenu()">
                <i data-lucide="x"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-group">
                <span class="group-title">Supervisión</span>
                <ul>
                    <li>
                        <a href="/admin" class="{{ Request::is('admin') ? 'active' : '' }}">
                            <i data-lucide="bar-chart-3"></i> Panel General
                        </a>
                    </li>
                    <li>
                        <a href="/admin/musicians" class="{{ Request::is('admin/musicians*') ? 'active' : '' }}">
                            <i data-lucide="users"></i> Validar Músicos
                            @if(isset($pendingMusiciansCountSidebar) && $pendingMusiciansCountSidebar > 0)
                                <span class="badge warning">{{ $pendingMusiciansCountSidebar }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="/admin/castings" class="{{ Request::is('admin/castings*') ? 'active' : '' }}">
                            <i data-lucide="shield"></i> Gestión Eventos
                        </a>
                    </li>
                    <li>
                        <a href="/admin/reports" class="{{ Request::is('admin/reports*') ? 'active' : '' }}">
                            <i data-lucide="flag"></i> Reportes
                            @if(isset($pendingReportsCount) && $pendingReportsCount > 0)
                                <span class="badge danger">{{ $pendingReportsCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="/admin/promotions" class="{{ Request::is('admin/promotions*') ? 'active' : '' }}">
                            <i data-lucide="monitor"></i> Publicidad Interna
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-group">
                <span class="group-title">Accesos Interiores</span>
                <ul>
                    <li>
                        <a href="/admin/settings" class="{{ Request::is('admin/settings*') ? 'active' : '' }}">
                            <i data-lucide="settings"></i> Configuración
                        </a>
                    </li>
                </ul>
            </div>

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
        @yield('admin-content')
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
            <button onclick="hideLogoutModal()" style="flex:1;padding:11px;border-radius:9px;border:1.5px solid #e2e8f0;background:#f8fafc;color:#475569;font-size:14px;font-weight:600;cursor:pointer;">
                Cancelar
            </button>
            <button onclick="document.getElementById('logout-form').submit()" style="flex:1;padding:11px;border-radius:9px;border:none;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(220,38,38,.3);">
                Sí, cerrar sesión
            </button>
        </div>
    </div>
</div>

    @yield('admin-scripts')
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
            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Close on outside click for sidebar
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('dashboard-sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            if (sidebar && sidebar.classList.contains('active')) {
                if (!sidebar.contains(e.target) && (!menuBtn || !menuBtn.contains(e.target))) {
                    toggleMobileMenu();
                }
            }
        });

        document.getElementById('logout-modal').addEventListener('click', function(e) {
            if (e.target === this) hideLogoutModal();
        });
    </script>
    <style>
        @keyframes logoutFadeIn {
            from { opacity:0; transform:scale(.95) translateY(12px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        .animate-slide-up { animation: slideUp 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>

@endsection
