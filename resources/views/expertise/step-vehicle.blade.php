@extends('layouts.app')

@section('title', 'Peritaje - Paso 2: Vehículo - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4 class="mb-0"><i class="fas fa-car me-2" style="color: var(--accent);"></i>Paso 2: Datos del Vehículo</h4><small>Código: {{ $expertise->codigo }}</small></div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>
<div class="content-body">
    @include('components.expertise-progress', ['step' => 2, 'expertise' => $expertise])

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-car me-2"></i>Información del Vehículo</h5></div>
        <div class="card-body">
            <form id="stepForm">
                @csrf

                <h6 class="text-muted mb-3"><i class="fas fa-search me-1"></i>Tipo de Vehículo *</h6>
                <input type="hidden" name="vehicle_type_id" id="vehicle_type_id" value="{{ $expertise->vehicle_type_id }}" required>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="vtSearch" placeholder="Buscar tipo de vehículo..." autocomplete="off" value="{{ $expertise->vehicleType?->name }}">
                    </div>
                    <div id="vtResults" class="list-group mt-1" style="display:none;max-height:200px;overflow-y:auto;"></div>
                </div>

                <div id="vtSelected" class="alert alert-success mb-3" style="display:{{ $expertise->vehicle_type_id ? 'block' : 'none' }};">
                    <strong id="vtName">{{ $expertise->vehicleType?->name }}</strong>
                    <small class="text-muted" id="vtType">{{ $expertise->vehicleType?->type }}</small>
                </div>

                <hr>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Placa *</label>
                        <input type="text" class="form-control" name="placa" value="{{ $expertise->placa }}" required placeholder="AAA-123">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Marca</label>
                        <input type="text" class="form-control" name="marca" value="{{ $expertise->marca }}" placeholder="Chevrolet, Toyota...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Línea</label>
                        <input type="text" class="form-control" name="linea" value="{{ $expertise->linea }}" placeholder="Corsa, Hilux...">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Modelo (Año)</label>
                        <input type="text" class="form-control" name="modelo" value="{{ $expertise->modelo }}" placeholder="2024">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Color</label>
                        <input type="text" class="form-control" name="color" value="{{ $expertise->color }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Clase</label>
                        <input type="text" class="form-control" name="clase_vehiculo" value="{{ $expertise->clase_vehiculo }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kilometraje</label>
                        <input type="text" class="form-control" name="kilometraje" value="{{ $expertise->kilometraje }}" placeholder="Km">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">No. Motor</label>
                        <input type="text" class="form-control" name="numero_motor" value="{{ $expertise->numero_motor }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No. Chasis</label>
                        <input type="text" class="form-control" name="numero_chasis" value="{{ $expertise->numero_chasis }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No. Serie</label>
                        <input type="text" class="form-control" name="numero_serie" value="{{ $expertise->numero_serie }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">VIN</label>
                        <input type="text" class="form-control" name="vin" value="{{ $expertise->vin }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cilindrada</label>
                        <input type="text" class="form-control" name="cilindrada" value="{{ $expertise->cilindrada }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo Carrocería</label>
                        <input type="text" class="form-control" name="tipo_carroceria" value="{{ $expertise->tipo_carroceria }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Organismo de Tránsito</label>
                        <input type="text" class="form-control" name="organismo_transito" value="{{ $expertise->organismo_transito }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Código Fasecolda</label>
                        <input type="text" class="form-control" name="codigo_fasecolda" value="{{ $expertise->codigo_fasecolda }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Valor Fasecolda</label>
                        <input type="text" class="form-control" name="valor_fasecolda" value="{{ $expertise->valor_fasecolda }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Valor Sugerido</label>
                        <input type="text" class="form-control" name="valor_sugerido" value="{{ $expertise->valor_sugerido }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Valor Accesorios</label>
                        <input type="text" class="form-control" name="valor_accesorios" value="{{ $expertise->valor_accesorios }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('expertise.create') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Paso 1</a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        Continuar al Paso 3 <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let vtTimeout;
document.getElementById('vtSearch').addEventListener('input', function() {
    clearTimeout(vtTimeout);
    const q = this.value.trim();
    vtTimeout = setTimeout(() => {
        fetch(`{{ route('expertise.search-vehicle-types') }}?search=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json()).then(data => {
            const r = document.getElementById('vtResults');
            r.innerHTML = '';
            if (data.success && data.vehicle_types.length > 0) {
                data.vehicle_types.forEach(vt => {
                    const a = document.createElement('a');
                    a.className = 'list-group-item list-group-item-action';
                    a.href = '#';
                    a.innerHTML = `<strong>${vt.name}</strong> <span class="badge bg-${vt.type === 'moto' ? 'warning' : 'primary'} ms-2">${vt.type}</span>`;
                    a.onclick = (e) => { e.preventDefault(); selectVT(vt); };
                    r.appendChild(a);
                });
                r.style.display = 'block';
            } else r.style.display = 'none';
        });
    }, 300);
});

function selectVT(vt) {
    document.getElementById('vehicle_type_id').value = vt.id;
    document.getElementById('vtSearch').value = vt.name;
    document.getElementById('vtName').textContent = vt.name;
    document.getElementById('vtType').textContent = vt.type;
    document.getElementById('vtSelected').style.display = 'block';
    document.getElementById('vtResults').style.display = 'none';
}

document.getElementById('stepForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!document.getElementById('vehicle_type_id').value) {
        Swal.fire({ icon: 'warning', title: 'Falta tipo de vehículo', text: 'Seleccione un tipo', background: '#1a2332', color: '#fff' });
        return;
    }
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.querySelector('.loading').classList.add('show');

    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => 2]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = data.redirect;
        else { Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' }); btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});
</script>
@endpush
