<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="sidebar-text">
            <h3>Pritec</h3>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-nav-section sidebar-text">General</div>

        <div class="sidebar-nav-item">
            <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </div>

        <div class="sidebar-nav-section sidebar-text">Administración</div>

        <div class="sidebar-nav-item">
            <a href="{{ route('users.index') }}" class="sidebar-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span class="sidebar-text">Usuarios</span>
            </a>
        </div>

        <div class="sidebar-nav-item">
            <a href="{{ route('vehicle-types.index') }}" class="sidebar-nav-link {{ request()->routeIs('vehicle-types.*') || request()->routeIs('vehicle-sections.*') ? 'active' : '' }}">
                <i class="fas fa-car"></i>
                <span class="sidebar-text">Tipos de Vehículos</span>
            </a>
        </div>

        <div class="sidebar-nav-item">
            <a href="{{ route('clients.index') }}" class="sidebar-nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="fas fa-user-friends"></i>
                <span class="sidebar-text">Clientes</span>
            </a>
        </div>

        <div class="sidebar-nav-section sidebar-text">Peritajes</div>

        <div class="sidebar-nav-item sidebar-dropdown {{ request()->routeIs('expertise.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="sidebar-nav-link sidebar-dropdown-toggle {{ request()->routeIs('expertise.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i>
                <span class="sidebar-text">Peritajes</span>
                <i class="fas fa-chevron-down dropdown-icon"></i>
            </a>
            <div class="sidebar-dropdown-content">
                <div class="sidebar-nav-item">
                    <a href="{{ route('expertise.index') }}" class="sidebar-nav-link {{ request()->routeIs('expertise.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span class="sidebar-text">Peritaje Completo</span>
                    </a>
                </div>
                <div class="sidebar-nav-item">
                    <a href="#" class="sidebar-nav-link" onclick="showComingSoon('Peritaje Básico')">
                        <i class="fas fa-clipboard"></i>
                        <span class="sidebar-text">Peritaje Básico</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="sidebar-nav-section sidebar-text">Reportes</div>

        <div class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link" onclick="showComingSoon('Reportes')">
                <i class="fas fa-chart-bar"></i>
                <span class="sidebar-text">Estadísticas</span>
            </a>
        </div>

        <div class="sidebar-nav-item">
            <a href="{{ route('appointments.index') }}" class="sidebar-nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span class="sidebar-text">Citas</span>
            </a>
        </div>

        <div class="sidebar-nav-section sidebar-text">Sistema</div>

        <div class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link" onclick="showComingSoon('Configuración')">
                <i class="fas fa-cog"></i>
                <span class="sidebar-text">Configuración</span>
            </a>
        </div>

        @if(Auth::user()->email === 'admin@pritec.com')
        <div class="sidebar-nav-item">
            <a href="{{ route('migration.index') }}" class="sidebar-nav-link {{ request()->routeIs('migration.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i>
                <span class="sidebar-text">Migración v2</span>
            </a>
        </div>
        @endif
    </nav>

    <div class="sidebar-user">
        <div class="sidebar-user-info">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
            </div>
            <div class="sidebar-user-details sidebar-text">
                <h6>{{ Auth::user()->full_name }}</h6>
                <small>{{ Auth::user()->email }}</small>
            </div>
        </div>
    </div>
</aside>

<script>
    function showComingSoon(name) {
        Swal.fire({
            title: 'Próximamente',
            text: `El módulo "${name}" estará disponible pronto.`,
            icon: 'info',
            confirmButtonColor: '#3498db',
            background: '#1a2332',
            color: '#fff'
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.sidebar-dropdown-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                this.closest('.sidebar-dropdown').classList.toggle('open');
            });
        });
    });
</script>
