<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Armonihz</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Armonihz_logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @yield('head')
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
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
</body>
</html>
