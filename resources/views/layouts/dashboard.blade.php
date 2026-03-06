@extends('layouts.app')

@section('content')

{{-- Contenedor Principal (Flexbox) --}}
<div class="dashboard-wrapper">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz">
            <h3>Armonihz</h3>
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
                            <i data-lucide="search"></i> Castings Activos
                        </a>
                    </li>
                    <li>
                        <a href="/promote" class="{{ Request::is('promote') ? 'active' : '' }}">
                            <i data-lucide="zap"></i> Promocionar Perfil
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
                            <span class="badge">3</span>
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
                        <a href="#" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

{{-- Scripts al final --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>

@endsection
