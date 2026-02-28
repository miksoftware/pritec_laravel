@extends('layouts.app')

@section('title', 'Dashboard - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-th-large me-2" style="color: var(--accent);"></i>
                Dashboard
            </h4>
            <small>Bienvenido, {{ Auth::user()->full_name }}</small>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-primary px-3 py-2">
                <i class="fas fa-clock me-1"></i>
                {{ now()->format('d M, Y') }}
            </span>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-primary me-3">
                        <i class="fas fa-clipboard-check fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Peritajes</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-success me-3">
                        <i class="fas fa-user-friends fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Clientes</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-info me-3">
                        <i class="fas fa-car fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Vehículos</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información del Sistema
                    </h6>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon stat-primary me-3" style="width: 38px; height: 38px;">
                                    <i class="fas fa-code" style="font-size: 0.85rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Versión</small>
                                    <strong style="font-size: 0.9rem;">3.0.0 (Laravel)</strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="stat-icon stat-success me-3" style="width: 38px; height: 38px;">
                                    <i class="fas fa-user" style="font-size: 0.85rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Usuario</small>
                                    <strong style="font-size: 0.9rem;">{{ Auth::user()->full_name }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon stat-info me-3" style="width: 38px; height: 38px;">
                                    <i class="fas fa-clock" style="font-size: 0.85rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Último acceso</small>
                                    <strong style="font-size: 0.9rem;">
                                        {{ Auth::user()->last_login ? Auth::user()->last_login->format('d/m/Y H:i') : 'Primer ingreso' }}
                                    </strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(243, 156, 18, 0.1); color: var(--warning);" class="me-3">
                                    <i class="fas fa-shield-alt" style="font-size: 0.85rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Rol</small>
                                    @if(Auth::user()->is_admin)
                                        <span class="badge bg-warning text-dark">Administrador</span>
                                    @else
                                        <span class="badge bg-primary">Usuario</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Accesos Rápidos
                    </h6>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary text-start w-100">
                        <i class="fas fa-users me-2"></i>
                        Gestión de Usuarios
                    </a>
                    <button class="btn btn-outline-primary text-start w-100" onclick="showComingSoon('Nuevo Peritaje')">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Nuevo Peritaje
                    </button>
                    <button class="btn btn-outline-primary text-start w-100" onclick="showComingSoon('Clientes')">
                        <i class="fas fa-user-friends me-2"></i>
                        Gestión de Clientes
                    </button>
                    <button class="btn btn-outline-primary text-start w-100" onclick="showComingSoon('Reportes')">
                        <i class="fas fa-chart-bar me-2"></i>
                        Ver Reportes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
</script>
@endpush
