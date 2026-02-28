<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pritec')</title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    @auth
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Content Wrapper -->
        <div class="main-wrapper" id="mainWrapper">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button class="btn btn-link mobile-toggle me-3" onclick="toggleMobileSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="d-flex align-items-center">
                        <span class="navbar-brand mb-0">
                            @yield('navbar-title', 'Panel de Administración')
                        </span>
                    </div>

                    <div class="navbar-nav ms-auto d-flex flex-row align-items-center gap-3">
                        <!-- Notifications placeholder -->
                        <a href="#" class="nav-link position-relative" style="font-size: 1rem; color: var(--text-muted);">
                            <i class="fas fa-bell"></i>
                        </a>

                        <!-- User dropdown -->
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar-sm">
                                    {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
                                </div>
                                <div class="d-none d-md-block">
                                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">{{ Auth::user()->full_name }}</span>
                                    <br>
                                    <small style="font-size: 0.7rem; color: var(--text-muted);">{{ Auth::user()->is_admin ? 'Administrador' : 'Usuario' }}</small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2" style="color: var(--accent);"></i>Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2" style="color: var(--text-muted);"></i>Configuración</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            @yield('content')
        </div>
    @else
        <!-- Auth pages: no sidebar -->
        @yield('content')
    @endauth

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.min.js"></script>

    <script>
        const APP_URL = '{{ url("/") }}/';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        function logout() {
            Swal.fire({
                title: '¿Cerrar sesión?',
                text: '¿Estás seguro de que deseas salir?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3498db',
                cancelButtonColor: '#e74c3c',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                background: '#1a2332',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json',
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) window.location.href = data.redirect;
                    });
                }
            });
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('expanded');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        function toggleMobileSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                const sidebar = document.getElementById('sidebar');
                const mainWrapper = document.getElementById('mainWrapper');
                if (sidebar) {
                    sidebar.classList.add('collapsed');
                    mainWrapper.classList.add('expanded');
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
