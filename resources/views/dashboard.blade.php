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
                {{ now()->translatedFormat('d M, Y') }}
            </span>
        </div>
    </div>
</div>

<div class="content-body">
    {{-- KPIs principales --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('expertise.index') }}" class="text-decoration-none">
                <div class="stat-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon stat-primary me-3">
                            <i class="fas fa-clipboard-check fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Peritajes</p>
                            <h3 class="fw-bold mb-0">{{ $totalExpertises }}</h3>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $completedExpertises }} completados</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('expertise.index') }}" class="text-decoration-none">
                <div class="stat-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                            <i class="fas fa-spinner fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">En Progreso</p>
                            <h3 class="fw-bold mb-0">{{ $inProgressExpertises }}</h3>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $expertisesToday }} hoy</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('clients.index') }}" class="text-decoration-none">
                <div class="stat-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon stat-success me-3">
                            <i class="fas fa-user-friends fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Clientes</p>
                            <h3 class="fw-bold mb-0">{{ $totalClients }}</h3>
                            <small class="text-muted" style="font-size: 0.7rem;">+{{ $clientsThisMonth }} este mes</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('vehicle-types.index') }}" class="text-decoration-none">
                <div class="stat-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon stat-info me-3">
                            <i class="fas fa-car fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Vehículos</p>
                            <h3 class="fw-bold mb-0">{{ $totalVehicleTypes }}</h3>
                            <small class="text-muted" style="font-size: 0.7rem;">tipos activos</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Resumen del mes --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card p-3 text-center">
                <p class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase;">Peritajes este mes</p>
                <h2 class="fw-bold mb-0" style="color: var(--accent);">{{ $expertisesThisMonth }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-3 text-center">
                <p class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase;">Clientes este mes</p>
                <h2 class="fw-bold mb-0" style="color: #2ecc71;">{{ $clientsThisMonth }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-3 text-center">
                <p class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase;">Usuarios activos</p>
                <h2 class="fw-bold mb-0" style="color: #f39c12;">{{ $totalUsers }}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Últimos peritajes --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Últimos Peritajes</h6>
                    <a href="{{ route('expertise.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
                <div class="card-body p-0">
                    @if($recentExpertises->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Cliente</th>
                                        <th>Placa</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentExpertises as $exp)
                                        <tr>
                                            <td><span class="fw-bold" style="color: var(--accent);">{{ $exp->codigo }}</span></td>
                                            <td>{{ $exp->client->full_name ?? 'N/A' }}</td>
                                            <td>{{ $exp->placa ?: '-' }}</td>
                                            <td>
                                                @if($exp->status === 'completed')
                                                    <span class="badge bg-success">Completado</span>
                                                @elseif($exp->status === 'in_progress')
                                                    <span class="badge bg-warning text-dark">En Progreso</span>
                                                @else
                                                    <span class="badge bg-secondary">Borrador</span>
                                                @endif
                                            </td>
                                            <td>{{ $exp->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @if($exp->status === 'completed')
                                                    <a href="{{ route('expertise.pdf', $exp) }}" target="_blank" class="btn btn-sm btn-outline-success" title="PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('expertise.step', [$exp, $exp->current_step]) }}" class="btn btn-sm btn-outline-primary" title="Continuar">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No hay peritajes registrados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Acciones rápidas + últimos clientes --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h6>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('expertise.create') }}" class="btn btn-primary text-start w-100">
                        <i class="fas fa-plus-circle me-2"></i>Nuevo Peritaje
                    </a>
                    <a href="{{ route('clients.create') }}" class="btn btn-outline-success text-start w-100">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Cliente
                    </a>
                    <a href="{{ route('vehicle-types.index') }}" class="btn btn-outline-info text-start w-100">
                        <i class="fas fa-car me-2"></i>Tipos de Vehículos
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary text-start w-100">
                        <i class="fas fa-users me-2"></i>Gestión de Usuarios
                    </a>
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary text-start w-100">
                        <i class="fas fa-user-friends me-2"></i>Ver Clientes
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0"><i class="fas fa-user-clock me-2"></i>Últimos Clientes</h6>
                    <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
                <div class="card-body p-0">
                    @if($recentClients->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentClients as $client)
                                <a href="{{ route('clients.show', $client) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" style="font-size: 0.82rem;">
                                    <div>
                                        <div class="fw-bold">{{ $client->full_name }}</div>
                                        <small class="text-muted">CC: {{ $client->identification }}</small>
                                    </div>
                                    <small class="text-muted">{{ $client->created_at->format('d/m') }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">No hay clientes registrados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
