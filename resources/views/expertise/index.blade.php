@extends('layouts.app')

@section('title', 'Peritajes - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0"><i class="fas fa-clipboard-check me-2" style="color: var(--accent);"></i>Peritajes</h4>
            <small>Gestiona y consulta todos los peritajes del sistema</small>
        </div>
        <a href="{{ route('expertise.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Peritaje
        </a>
    </div>
</div>

<div class="content-body">
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-info me-3" style="width:42px;height:42px;"><i class="fas fa-clipboard-list"></i></div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px">Total</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width:42px;height:42px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(243,156,18,.15);color:var(--warning);" class="me-3"><i class="fas fa-spinner"></i></div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px">En Progreso</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['inProgress'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-success me-3" style="width:42px;height:42px;"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px">Completados</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['completed'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width:42px;height:42px;background:rgba(99,102,241,.15);color:#6366f1;"><i class="fas fa-calendar-alt"></i></div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px">Este Mes</p>
                        <h4 class="fw-bold mb-0">{{ $statistics['thisMonth'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <ul class="nav nav-pills mb-3 gap-2" id="statusTabs">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'all' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('expertise.index', array_merge(request()->except(['page', 'status']), ['status' => 'all'])) }}">
                <i class="fas fa-list me-1"></i>Todos <span class="badge {{ $status === 'all' ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $statusCounts['all'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'in_progress' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('expertise.index', array_merge(request()->except(['page', 'status']), ['status' => 'in_progress'])) }}">
                <i class="fas fa-spinner me-1"></i>En Progreso <span class="badge {{ $status === 'in_progress' ? 'bg-light text-dark' : 'bg-warning text-dark' }} ms-1">{{ $statusCounts['in_progress'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'completed' ? 'active' : 'bg-white border text-dark' }}" href="{{ route('expertise.index', array_merge(request()->except(['page', 'status']), ['status' => 'completed'])) }}">
                <i class="fas fa-check-circle me-1"></i>Completados <span class="badge {{ $status === 'completed' ? 'bg-light text-dark' : 'bg-success' }} ms-1">{{ $statusCounts['completed'] }}</span>
            </a>
        </li>
    </ul>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('expertise.index') }}" id="searchForm">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" name="search" value="{{ $search }}" placeholder="Buscar por código, placa, marca o cliente..." id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="month" class="form-control" name="month" value="{{ $month }}" onchange="document.getElementById('searchForm').submit()" title="Filtrar por mes de servicio">
                    </div>
                    <div class="col-md-2">
                        @if($search || $month || $status !== 'all')
                            <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-times me-1"></i>Limpiar</a>
                        @else
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrar</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Expertises Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($expertises->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Código</th>
                            <th>Cliente</th>
                            <th>Placa</th>
                            <th>Marca / Línea</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-center">Insp.</th>
                            <th class="text-center">Fotos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expertises as $exp)
                        <tr>
                            <td class="ps-3"><code>{{ $exp->codigo }}</code></td>
                            <td>
                                <strong>{{ $exp->client?->first_name }} {{ $exp->client?->last_name }}</strong>
                                @if($exp->client?->identification_number)
                                    <br><small class="text-muted">CC: {{ $exp->client->identification_number }}</small>
                                @endif
                            </td>
                            <td>
                                @if($exp->placa)
                                    <span class="badge bg-light text-dark border">{{ $exp->placa }}</span>
                                @else
                                    <span class="badge bg-light text-muted">Sin placa</span>
                                @endif
                            </td>
                            <td>
                                <span>{{ $exp->marca ?: '-' }}</span>
                                @if($exp->linea)
                                    <small class="text-muted d-block">{{ $exp->linea }}</small>
                                @endif
                            </td>
                            <td><small>{{ $exp->vehicleType?->name ?? 'N/A' }}</small></td>
                            <td>
                                <small>
                                    {{ $exp->service_date ? $exp->service_date->format('d/m/Y') : $exp->created_at->format('d/m/Y') }}
                                </small>
                            </td>
                            <td>
                                @if($exp->status === 'completed')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completado</span>
                                @elseif($exp->status === 'in_progress')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-spinner fa-spin me-1"></i>En Progreso (Paso {{ $exp->current_step }})</span>
                                @elseif($exp->status === 'draft')
                                    <span class="badge bg-secondary">Borrador</span>
                                @else
                                    <span class="badge bg-info">{{ $exp->status_label }}</span>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge bg-info">{{ $exp->inspections_count }}</span></td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $exp->photos_count }}</span></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    @if($exp->status === 'completed')
                                        <a href="{{ route('expertise.show', $exp) }}" class="btn btn-outline-info" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('expertise.pdf', $exp) }}" class="btn btn-outline-success" title="PDF" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                        <a href="{{ route('expertise.step', ['expertise' => $exp->id, 'step' => 2]) }}" class="btn btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                    @else
                                        <a href="{{ route('expertise.step', ['expertise' => $exp->id, 'step' => $exp->current_step < 2 ? 2 : $exp->current_step]) }}" class="btn btn-primary" title="Continuar peritaje">
                                            <i class="fas fa-play me-1"></i>Continuar
                                        </a>
                                    @endif
                                    <button class="btn btn-outline-danger" title="Eliminar" onclick="deleteExpertise({{ $exp->id }}, '{{ $exp->codigo }}')"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($expertises->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small>Mostrando {{ $expertises->firstItem() }} - {{ $expertises->lastItem() }} de {{ $expertises->total() }}</small>
                {{ $expertises->links('pagination::bootstrap-5') }}
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron peritajes</h5>
                <p class="text-muted small">No hay peritajes registrados que coincidan con los criterios de búsqueda o filtros seleccionados.</p>
                <a href="{{ route('expertise.create') }}" class="btn btn-primary mt-2"><i class="fas fa-plus me-2"></i>Crear Nuevo Peritaje</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchInput')?.addEventListener('keypress', e => { if (e.key === 'Enter') document.getElementById('searchForm').submit(); });

function deleteExpertise(id, codigo) {
    Swal.fire({
        title: '¿Eliminar peritaje?', text: `¿Eliminar "${codigo}"?`, icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#e74c3c', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', background: '#1a2332', color: '#fff'
    }).then(r => {
        if (r.isConfirmed) {
            fetch(`/expertise/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
            .then(r => r.json()).then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Eliminado!', text: data.message, timer: 2000, showConfirmButton: false, background: '#1a2332', color: '#fff' }).then(() => location.reload());
                }
            });
        }
    });
}
</script>
@endpush
