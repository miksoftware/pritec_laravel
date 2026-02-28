@extends('layouts.app')

@section('title', 'Tipos de Vehículos - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-car me-2" style="color: var(--accent);"></i>
                Tipos de Vehículos
            </h4>
            <small>Gestiona los diferentes tipos de vehículos del sistema</small>
        </div>
        <a href="{{ route('vehicle-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nuevo Tipo de Vehículo
        </a>
    </div>
</div>

<div class="content-body">
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-info me-3" style="width: 42px; height: 42px;">
                        <i class="fas fa-car"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Carros</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['carros'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(243, 156, 18, 0.1); color: var(--warning);" class="me-3">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Motos</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['motos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-success me-3" style="width: 42px; height: 42px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Activos</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['activos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(231, 76, 60, 0.1); color: var(--danger);" class="me-3">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Inactivos</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['inactivos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('vehicle-types.index') }}" id="searchForm">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" name="search" value="{{ $search }}"
                           placeholder="Buscar por nombre o descripción..." id="searchInput">
                    @if($search)
                        <a href="{{ route('vehicle-types.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($vehicleTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="vehicleTypesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicleTypes as $vt)
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark">{{ $vt->id }}</span></td>
                                <td>
                                    @if($vt->type === 'carro')
                                        <span class="badge" style="background: rgba(0, 206, 201, 0.12); color: #00cec9;">
                                            <i class="fas fa-car me-1"></i> Carro
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(243, 156, 18, 0.12); color: #f39c12;">
                                            <i class="fas fa-motorcycle me-1"></i> Moto
                                        </span>
                                    @endif
                                </td>
                                <td><strong>{{ $vt->name }}</strong></td>
                                <td><small class="text-muted">{{ Str::limit($vt->description, 50) ?: '—' }}</small></td>
                                <td>
                                    @if($vt->status === 'active')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td><small>{{ $vt->created_at->format('d/m/Y') }}</small></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('vehicle-types.sections', $vt) }}" class="btn btn-outline-info" title="Configurar Secciones">
                                            <i class="fas fa-cogs"></i>
                                        </a>
                                        <a href="{{ route('vehicle-types.edit', $vt) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-warning" title="Cambiar Estado"
                                                onclick="toggleStatus({{ $vt->id }}, '{{ $vt->status }}')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Eliminar"
                                                onclick="deleteVehicleType({{ $vt->id }}, '{{ $vt->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($vehicleTypes->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">Mostrando {{ $vehicleTypes->firstItem() }} - {{ $vehicleTypes->lastItem() }} de {{ $vehicleTypes->total() }}</small>
                    {{ $vehicleTypes->links('pagination::bootstrap-5') }}
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-car fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay tipos de vehículos registrados</h5>
                    <p class="text-muted">Comienza creando tu primer tipo de vehículo</p>
                    <a href="{{ route('vehicle-types.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear Primer Tipo
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') document.getElementById('searchForm').submit();
    });

    function toggleStatus(id, currentStatus) {
        const action = currentStatus === 'active' ? 'desactivar' : 'activar';
        Swal.fire({
            title: '¿Confirmar acción?',
            text: `¿Deseas ${action} este tipo de vehículo?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: currentStatus === 'active' ? '#e74c3c' : '#2ecc71',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Sí, ${action}`,
            cancelButtonText: 'Cancelar',
            background: '#1a2332', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/vehicle-types/${id}/toggle-status`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '¡Éxito!', text: data.message, timer: 2000, showConfirmButton: false, background: '#1a2332', color: '#fff' })
                        .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
                    }
                });
            }
        });
    }

    function deleteVehicleType(id, name) {
        Swal.fire({
            title: '¿Eliminar tipo?',
            text: `¿Deseas eliminar "${name}"? Se eliminarán también sus secciones y piezas.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#1a2332', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/vehicle-types/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '¡Eliminado!', text: data.message, timer: 2000, showConfirmButton: false, background: '#1a2332', color: '#fff' })
                        .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
                    }
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#vehicleTypesTable tbody tr').forEach((row, i) => {
            row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; row.style.transition = 'all 0.3s ease';
            setTimeout(() => { row.style.opacity = '1'; row.style.transform = 'translateX(0)'; }, i * 50 + 100);
        });
    });
</script>
@endpush
