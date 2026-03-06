@extends('layouts.app')

@section('content')

<div class="dashboard-wrapper">

    {{-- SIDEBAR ADMIN --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <div class="brand-text">
                <h3>Armonihz</h3>
                <span class="admin-badge">Admin</span>
            </div>
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
                            <span class="badge warning">7</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/castings" class="{{ Request::is('admin/castings*') ? 'active' : '' }}">
                            <i data-lucide="shield"></i> Gestión Eventos
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
                <span class="group-title">Accesos</span>
                <ul>
                    <li>
                        <a href="/dashboard">
                            <i data-lucide="music"></i> Vista Músico
                        </a>
                    </li>
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
                        <a href="/" class="logout-link">
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

{{-- Scripts al final --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>

@endsection
