<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Armonihz</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Armonihz_logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @yield('head')
    <!-- Script para Modo Oscuro y FOUC -->
    <script>
        (function() {
            var storedTheme = localStorage.getItem('theme');
            if (storedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
        
        window.toggleDarkMode = function() {
            var currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            var newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Actualizar iconos moon/sun dinámicamente
            document.querySelectorAll('.theme-toggle-icon').forEach(function(icon) {
                icon.setAttribute('data-lucide', newTheme === 'dark' ? 'sun' : 'moon');
            });
            if (window.lucide) {
                window.lucide.createIcons();
            }
        };
    </script>
</head>
<body>


<main>
    @yield('content')
</main>

<footer class="app-footer">
            <div class="footer-content">
                <div class="footer-left">
                    <p>© 2026 <strong>Armonihz</strong>. Todos los derechos reservados.</p>
                </div>
                <div class="footer-right">
                    <a href="#">Privacidad</a>
                    <a href="#">Términos</a>
                    <a href="#">Ayuda</a>
                </div>
            </div>
        </footer>

    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();

            // 1. Monkey-patch window.alert
            window.alert = function(message) {
                Swal.fire({
                    text: message,
                    confirmButtonColor: '#6c3fc5',
                    confirmButtonText: 'Entendido'
                });
            };

            // 2. Handle Laravel Session Flash Messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#6c3fc5',
                    timer: 4000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#6c3fc5'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    text: "{{ session('info') }}",
                    confirmButtonColor: '#6c3fc5'
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    text: "{{ session('warning') }}",
                    confirmButtonColor: '#6c3fc5'
                });
            @endif
        });
    </script>
</body>
</html>
