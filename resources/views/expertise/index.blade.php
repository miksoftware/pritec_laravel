@extends('layouts.app')

@section('title', 'Peritajes - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0"><i class="fas fa-clipboard-check me-2" style="color: var(--accent);"></i>Peritajes Completos</h4>
            <small>Gestiona los peritajes completos del sistema</small>
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
                    <div><p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px">Total</p><h4 class="fw-bold mb-0">{{ $statistics['total'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon stat-success me-3" style="width:42px;height:42px;"><i class="fas fa-calendar-alt"></i></div>
                    <div><p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px">Este Mes</p><h4 class="fw-bold mb-0">{{ $statistics['thisMonth'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width:42px;height:42px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(243,156,18,.1);color:var(--warning);" class="me-3"><i class="fas fa-search"></i></div>
                    <div><p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px">Inspecciones</p><h4 class="fw-bold mb-0">{{ $statistics['totalInspections'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center">
                    <div style="width:42px;height:42px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(52,152,219,.1);color:var(--accent);" class="me-3"><i class="fas fa-camera"></i></div>
                    <div><p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px">Fotos</p><h4 class="fw-bold mb-0">{{ $statistics['totalPhotos'] }}</h4></div>
                </div>
            </div>
        </div>
    </div>

    <!-- In-Progress Drafts -->
    @if($inProgress->count() > 0)
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning bg-opacity-10"><h6 class="mb-0"><i class="fas fa-spinner fa-spin me-2"></i>Peritajes en Progreso ({{ $inProgress->count() }})</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Código</th><th>Cliente</th><th>Placa</th><th>Paso</th><th>Actualizado</th><th class="text-center">Acciones</th></tr></thead>
                    <tbody>
                        @foreach($inProgress as $draft)
                        <tr>
                            <td><code>{{ $draft->codigo }}</code></td>
                            <td>{{ $draft->client?->first_name }} {{ $draft->client?->last_name }}</td>
                            <td><span class="badge bg-light text-dark">{{ $draft->placa ?: 'Sin placa' }}</span></td>
                            <td><span class="badge bg-info">Paso {{ $draft->current_step }}</span></td>
                            <td><small>{{ $draft->updated_at->diffForHumans() }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('expertise.step', ['expertise' => $draft->id, 'step' => $draft->current_step < 2 ? 2 : $draft->current_step]) }}" class="btn btn-sm btn-primary"><i class="fas fa-play me-1"></i>Continuar</a>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteExpertise({{ $draft->id }}, '{{ $draft->codigo }}')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('expertise.index') }}" id="searchForm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" name="search" value="{{ $search }}" placeholder="Buscar por código, placa, marca o cliente..." id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="month" class="form-control" name="month" value="{{ $month }}" onchange="document.getElementById('searchForm').submit()">
                    </div>
                    <div class="col-md-2">
                        @if($search || $month)
                            <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-times me-1"></i>Limpiar</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Completed Expertises Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($expertises->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Código</th>
                            <th>Cliente</th>
                            <th>Placa</th>
                            <th>Marca</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Insp.</th>
                            <th>Fotos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expertises as $exp)
                        <tr>
                            <td class="ps-3"><code>{{ $exp->codigo }}</code></td>
                            <td><strong>{{ $exp->client?->first_name }} {{ $exp->client?->last_name }}</strong></td>
                            <td><span class="badge bg-light text-dark">{{ $exp->placa }}</span></td>
                            <td>{{ $exp->marca }}</td>
                            <td><small>{{ $exp->vehicleType?->name ?? 'N/A' }}</small></td>
                            <td><small>{{ $exp->service_date->format('d/m/Y') }}</small></td>
                            <td><span class="badge bg-info">{{ $exp->inspections_count }}</span></td>
                            <td><span class="badge bg-secondary">{{ $exp->photos_count }}</span></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('expertise.show', $exp) }}" class="btn btn-outline-info" title="Ver"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('expertise.pdf', $exp) }}" class="btn btn-outline-success" title="PDF" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                    <a href="{{ route('expertise.step', ['expertise' => $exp->id, 'step' => 2]) }}" class="btn btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
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
                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay peritajes completados</h5>
                <a href="{{ route('expertise.create') }}" class="btn btn-primary mt-2"><i class="fas fa-plus me-2"></i>Crear Primer Peritaje</a>
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
