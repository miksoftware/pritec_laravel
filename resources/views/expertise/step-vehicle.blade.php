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
                        <span class="input-group-text"><i class="fas fa-search" aria-hidden="true"></i></span>
                        <input type="search" class="form-control" id="vtSearch" placeholder="Buscar tipo de vehículo..." autocomplete="off" autocorrect="off" spellcheck="false" enterkeyhint="search" aria-label="Buscar tipo de vehículo" value="{{ $expertise->vehicleType?->name }}">
                    </div>
                    <div id="vtResults" class="list-group mt-1" style="display:none;max-height:200px;overflow-y:auto;" aria-live="polite"></div>
                </div>

                <div id="vtSelected" class="alert alert-success mb-3" style="display:{{ $expertise->vehicle_type_id ? 'block' : 'none' }};" role="status">
                    <strong id="vtName">{{ $expertise->vehicleType?->name }}</strong>
                    <small class="text-muted" id="vtType">{{ $expertise->vehicleType?->type }}</small>
                </div>

                <hr>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="placa" class="form-label">Placa *</label>
                        <input type="text" class="form-control" id="placa" name="placa" value="{{ $expertise->placa }}" required placeholder="AAA-123" pattern="[A-Za-z0-9\- ]+" autocapitalize="characters" autocorrect="off" spellcheck="false" enterkeyhint="next">
                    </div>
                    <div class="col-md-4">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" value="{{ $expertise->marca }}" placeholder="Chevrolet, Toyota..." enterkeyhint="next">
                    </div>
                    <div class="col-md-4">
                        <label for="linea" class="form-label">Línea</label>
                        <input type="text" class="form-control" id="linea" name="linea" value="{{ $expertise->linea }}" placeholder="Corsa, Hilux..." enterkeyhint="next">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="modelo" class="form-label">Modelo (Año)</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" value="{{ $expertise->modelo }}" placeholder="2024" inputmode="numeric" enterkeyhint="next">
                    </div>
                    <div class="col-md-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="color" name="color" value="{{ $expertise->color }}" enterkeyhint="next">
                    </div>
                    <div class="col-md-3">
                        <label for="clase_vehiculo" class="form-label">Clase</label>
                        <input type="text" class="form-control" id="clase_vehiculo" name="clase_vehiculo" value="{{ $expertise->clase_vehiculo }}" enterkeyhint="next">
                    </div>
                    <div class="col-md-3">
                        <label for="kilometraje" class="form-label">Kilometraje</label>
                        <input type="text" class="form-control" id="kilometraje" name="kilometraje" value="{{ $expertise->kilometraje }}" placeholder="Km" inputmode="numeric" enterkeyhint="next">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="numero_motor" class="form-label">No. Motor</label>
                        <input type="text" class="form-control" id="numero_motor" name="numero_motor" value="{{ $expertise->numero_motor }}" autocapitalize="characters" autocorrect="off" spellcheck="false" enterkeyhint="next">
                    </div>
                    <div class="col-md-4">
                        <label for="numero_chasis" class="form-label">No. Chasis</label>
                        <input type="text" class="form-control" id="numero_chasis" name="numero_chasis" value="{{ $expertise->numero_chasis }}" autocapitalize="characters" autocorrect="off" spellcheck="false" enterkeyhint="next">
                    </div>
                    <div class="col-md-4">
                        <label for="numero_serie" class="form-label">No. Serie</label>
                        <input type="text" class="form-control" id="numero_serie" name="numero_serie" value="{{ $expertise->numero_serie }}" autocapitalize="characters" autocorrect="off" spellcheck="false" enterkeyhint="next">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="vin" class="form-label">VIN</label>
                        <input type="text" class="form-control" id="vin" name="vin" value="{{ $expertise->vin }}" autocapitalize="characters" autocorrect="off" spellcheck="false" enterkeyhint="next">
                    </div>
                    <div class="col-md-4">
                        <label for="cilindrada" class="form-label">Cilindrada</label>
                        <input type="text" class="form-control" id="cilindrada" name="cilindrada" value="{{ $expertise->cilindrada }}" inputmode="numeric" enterkeyhint="next">
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_carroceria" class="form-label">Tipo Carrocería</label>
                        <input type="text" class="form-control" id="tipo_carroceria" name="tipo_carroceria" value="{{ $expertise->tipo_carroceria }}" enterkeyhint="next">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="organismo_transito" class="form-label">Organismo de Tránsito</label>
                        <input type="text" class="form-control" id="organismo_transito" name="organismo_transito" value="{{ $expertise->organismo_transito }}" enterkeyhint="next">
                    </div>
                    <div class="col-md-4">
                        <label for="codigo_fasecolda" class="form-label">Código Fasecolda</label>
                        <input type="text" class="form-control" id="codigo_fasecolda" name="codigo_fasecolda" value="{{ $expertise->codigo_fasecolda }}" autocapitalize="characters" autocorrect="off" spellcheck="false" enterkeyhint="next">
                    </div>
                    <div class="col-md-4">
                        <label for="valor_fasecolda" class="form-label">Valor Fasecolda</label>
                        <input type="text" class="form-control" id="valor_fasecolda" name="valor_fasecolda" value="{{ $expertise->valor_fasecolda }}" inputmode="numeric" enterkeyhint="next">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="valor_sugerido" class="form-label">Valor Sugerido</label>
                        <input type="text" class="form-control" id="valor_sugerido" name="valor_sugerido" value="{{ $expertise->valor_sugerido }}" inputmode="numeric" enterkeyhint="next">
                    </div>
                    <div class="col-md-6">
                        <label for="valor_accesorios" class="form-label">Valor Accesorios</label>
                        <input type="text" class="form-control" id="valor_accesorios" name="valor_accesorios" value="{{ $expertise->valor_accesorios }}" inputmode="numeric" enterkeyhint="done">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('expertise.create') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Paso 1</a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Continuar al Paso 3 <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
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
            r.replaceChildren();
            if (data.success && data.vehicle_types.length > 0) {
                const items = data.vehicle_types.map(vt => {
                    const a = document.createElement('a');
                    a.className = 'list-group-item list-group-item-action';
                    a.href = '#';
                    a.innerHTML = `<strong>${vt.name}</strong> <span class="badge bg-${vt.type === 'moto' ? 'warning text-dark' : 'primary'} ms-2">${vt.type}</span>`;
                    a.onclick = (e) => { e.preventDefault(); selectVT(vt); };
                    return a;
                });
                r.replaceChildren(...items);
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
    btn.disabled = true;
    btn.setAttribute('aria-busy', 'true');
    btn.querySelector('.loading').classList.add('show');

    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => 2]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            btn.querySelector('.loading').classList.remove('show');
        }
    }).catch(() => {
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo procesar la solicitud.', background: '#1a2332', color: '#fff' });
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
        btn.querySelector('.loading').classList.remove('show');
    });
});
</script>
@endpush
