@extends('layouts.app')

@section('title', 'Clientes - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0"><i class="fas fa-user-friends me-2" style="color: var(--accent);"></i>Clientes</h4>
            <small>Gestiona la información de clientes del sistema</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('clients.export', ['search' => $search, 'status' => $status]) }}" class="btn btn-outline-success">
                <i class="fas fa-file-csv me-2"></i>Exportar CSV
            </a>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Cliente
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-info me-3" style="width: 42px; height: 42px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Total</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-success me-3" style="width: 42px; height: 42px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Activos</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['active'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(231, 76, 60, 0.1); color: var(--danger);" class="me-3">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Inactivos</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['inactive'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(52, 152, 219, 0.1); color: var(--accent);" class="me-3">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Hoy</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['today'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(243, 156, 18, 0.1); color: var(--warning);" class="me-3">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Semana</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['thisWeek'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(46, 204, 113, 0.1); color: var(--success);" class="me-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Mes</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['thisMonth'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('clients.index') }}" id="searchForm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" name="search" value="{{ $search }}"
                                   placeholder="Buscar por nombre, identificación, email o teléfono..." id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status" onchange="document.getElementById('searchForm').submit()">
                            <option value="">Todos los estados</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        @if($search || $status)
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="clientsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Nombre Completo</th>
                                <th>Identificación</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark">{{ $client->id }}</span></td>
                                <td>
                                    <strong>{{ $client->first_name }} {{ $client->last_name }}</strong>
                                </td>
                                <td><code>{{ $client->identification }}</code></td>
                                <td><small>{{ $client->phone }}</small></td>
                                <td><small class="text-muted">{{ $client->email }}</small></td>
                                <td>
                                    @if($client->status === 'active')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td><small>{{ $client->created_at->format('d/m/Y') }}</small></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-info" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-warning" title="Cambiar Estado"
                                                onclick="toggleStatus({{ $client->id }}, '{{ $client->status }}')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Eliminar"
                                                onclick="deleteClient({{ $client->id }}, '{{ $client->full_name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($clients->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">Mostrando {{ $clients->firstItem() }} - {{ $clients->lastItem() }} de {{ $clients->total() }}</small>
                    {{ $clients->links('pagination::bootstrap-5') }}
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay clientes registrados</h5>
                    <p class="text-muted">Comienza creando tu primer cliente</p>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Crear Primer Cliente</a>
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
            title: '¿Confirmar acción?', text: `¿Deseas ${action} este cliente?`, icon: 'question',
            showCancelButton: true, confirmButtonColor: currentStatus === 'active' ? '#e74c3c' : '#2ecc71',
            cancelButtonColor: '#6c757d', confirmButtonText: `Sí, ${action}`, cancelButtonText: 'Cancelar',
            background: '#1a2332', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/clients/${id}/toggle-status`, {
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

    function deleteClient(id, name) {
        Swal.fire({
            title: '¿Eliminar cliente?', text: `¿Deseas eliminar a "${name}"?`, icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#e74c3c', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
            background: '#1a2332', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/clients/${id}`, {
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
        document.querySelectorAll('#clientsTable tbody tr').forEach((row, i) => {
            row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; row.style.transition = 'all 0.3s ease';
            setTimeout(() => { row.style.opacity = '1'; row.style.transform = 'translateX(0)'; }, i * 50 + 100);
        });
    });
</script>
@endpush
