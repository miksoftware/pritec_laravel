@extends('layouts.app')

@php
    $sectionNames = ['carroceria' => 'Carrocería', 'estructura' => 'Estructura', 'chasis' => 'Chasis'];
    $sectionIcons = ['carroceria' => 'fas fa-car-side', 'estructura' => 'fas fa-building', 'chasis' => 'fas fa-cogs'];
    $sectionTitle = $sectionNames[$section] ?? $section;
    $sectionIcon = $sectionIcons[$section] ?? 'fas fa-search';
    $hasExisting = isset($existingInspections) && $existingInspections->count() > 0;
    $currentObs = $expertise->{'observaciones_' . $section} ?? '';
    $initiallySinAfectaciones = !$hasExisting && (!empty($currentObs) && str_contains(mb_strtolower($currentObs, 'UTF-8'), 'sin afectaci'));
@endphp

@section('title', "Peritaje - Paso {$step}: {$sectionTitle} - Pritec")

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4 class="mb-0"><i class="{{ $sectionIcon }} me-2" style="color: var(--accent);"></i>Paso {{ $step }}: Inspección {{ $sectionTitle }}</h4><small>Código: {{ $expertise->codigo }} | Placa: {{ $expertise->placa }}</small></div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>
<div class="content-body">
    @include('components.expertise-progress', ['step' => $step, 'expertise' => $expertise])

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="{{ $sectionIcon }} me-2"></i>Inspección de {{ $sectionTitle }}</h5></div>
        <div class="card-body">
            <form id="stepForm">
                @csrf
                <p class="text-muted mb-3">Seleccione una pieza y asigne el concepto de inspección correspondiente, o active "Sin afectaciones" si el vehículo está en óptimas condiciones.</p>

                <!-- Opción destacada: Sin afectaciones -->
                <div class="card mb-3 border-success-subtle shadow-sm" style="background-color: rgba(25, 135, 84, 0.05);">
                    <div class="card-body p-3">
                        <div class="form-check form-switch d-flex align-items-center gap-2 mb-0">
                            <input class="form-check-input fs-5 mt-0 cursor-pointer" type="checkbox" role="switch" 
                                   id="sinAfectacionesSwitch" name="sin_afectaciones" value="1"
                                   {{ $initiallySinAfectaciones ? 'checked' : '' }}>
                            <div class="flex-grow-1">
                                <label class="form-check-label fw-bold cursor-pointer text-dark d-flex align-items-center gap-1" for="sinAfectacionesSwitch">
                                    <i class="fas fa-shield-alt text-success fs-5"></i>
                                    <span>Sin afectaciones</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-2">100% Bueno</span>
                                </label>
                                <div class="text-muted small">
                                    Active esta opción si la {{ strtolower($sectionTitle) }} del vehículo está en estado nuevo u óptimo, sin golpes, rayones, deformidades ni reparaciones.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banner informativo cuando está activo Sin afectaciones -->
                <div id="sinAfectacionesBanner" class="alert alert-success {{ $initiallySinAfectaciones ? 'd-flex' : 'd-none' }} align-items-center mb-3">
                    <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                    <div>
                        <strong>Sección sin afectaciones:</strong> Esta sección se registrará con calificación óptima (100% Bueno). Puede continuar directamente al siguiente paso.
                    </div>
                </div>

                <!-- Contenedor dinámico de piezas a inspeccionar -->
                <div id="inspectionsContainer" class="{{ $initiallySinAfectaciones ? 'd-none' : '' }}">
                    <div id="inspectionRows">
                        @if($hasExisting)
                            @foreach($existingInspections as $i => $insp)
                            <div class="row mb-2 inspection-row align-items-center">
                                <div class="col-md-1 text-center"><span class="badge bg-secondary">{{ $i + 1 }}</span></div>
                                <div class="col-md-5">
                                    <select class="form-select" name="pieza_id[]" aria-label="Seleccionar Pieza"></select>
                                </div>
                                <div class="col-md-5">
                                    <select class="form-select" name="concepto_id[]" aria-label="Seleccionar Concepto"></select>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.inspection-row').remove();updateNumbers()" aria-label="Eliminar fila de inspección"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary" id="addRowBtn"><i class="fas fa-plus me-2"></i>Agregar Pieza</button>
                        <button type="button" class="btn btn-outline-success" id="quickSinAfectacionesBtn"><i class="fas fa-check-circle me-2"></i>Marcar Sin Afectaciones</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="observaciones_{{ $section }}" class="form-label">Observaciones de {{ $sectionTitle }}</label>
                    <textarea class="form-control" id="observaciones_{{ $section }}" name="observaciones_{{ $section }}" rows="3" placeholder="Observaciones generales...">{{ $currentObs }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => $expertise->getPreviousStep($step)]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Paso Anterior</a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Continuar al Paso {{ $expertise->getNextStep($step) }} <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let piecesData = [];
let conceptsData = [];
const existingInspections = @json($existingInspections ?? []);
const switchSinAfectaciones = document.getElementById('sinAfectacionesSwitch');
const bannerSinAfectaciones = document.getElementById('sinAfectacionesBanner');
const inspectionsContainer = document.getElementById('inspectionsContainer');
const quickBtn = document.getElementById('quickSinAfectacionesBtn');
const observacionesField = document.getElementById('observaciones_{{ $section }}');

function setSinAfectacionesState(active) {
    switchSinAfectaciones.checked = active;
    if (active) {
        inspectionsContainer.classList.add('d-none');
        bannerSinAfectaciones.classList.remove('d-none');
        bannerSinAfectaciones.classList.add('d-flex');
        if (!observacionesField.value.trim()) {
            observacionesField.value = 'Sin afectaciones';
        }
    } else {
        inspectionsContainer.classList.remove('d-none');
        bannerSinAfectaciones.classList.remove('d-flex');
        bannerSinAfectaciones.classList.add('d-none');
        if (observacionesField.value.trim() === 'Sin afectaciones') {
            observacionesField.value = '';
        }
        const rows = document.getElementById('inspectionRows');
        if (rows.children.length === 0) {
            addRow();
        }
    }
}

switchSinAfectaciones.addEventListener('change', function() {
    setSinAfectacionesState(this.checked);
});

if (quickBtn) {
    quickBtn.addEventListener('click', function() {
        setSinAfectacionesState(true);
    });
}

async function loadData() {
    const [pRes, cRes] = await Promise.all([
        fetch(`{{ route('expertise.get-pieces') }}?vehicle_type_id={{ $expertise->vehicle_type_id }}&section={{ $section }}`).then(r => r.json()),
        fetch(`{{ route('expertise.get-concepts') }}?category={{ $section }}`).then(r => r.json())
    ]);
    piecesData = pRes.success ? pRes.pieces : [];
    conceptsData = cRes.success ? cRes.concepts : [];

    // Populate existing rows
    document.querySelectorAll('.inspection-row').forEach((row, i) => {
        populateSelects(row, existingInspections[i]?.pieza_id, existingInspections[i]?.concepto_id);
    });

    if (existingInspections.length === 0 && !switchSinAfectaciones.checked) {
        addRow();
    }
}

function populateSelects(row, selPieza, selConcepto) {
    const pSel = row.querySelector('select[name="pieza_id[]"]');
    const cSel = row.querySelector('select[name="concepto_id[]"]');

    const pFragment = document.createDocumentFragment();
    const pDefault = document.createElement('option');
    pDefault.value = '';
    pDefault.textContent = '-- Seleccionar Pieza --';
    pFragment.appendChild(pDefault);

    piecesData.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = `${p.piece_number}. ${p.piece_name}`;
        if (p.id == selPieza) opt.selected = true;
        pFragment.appendChild(opt);
    });
    pSel.replaceChildren(pFragment);

    const cFragment = document.createDocumentFragment();
    const cDefault = document.createElement('option');
    cDefault.value = '';
    cDefault.textContent = '-- Seleccionar Concepto --';
    cFragment.appendChild(cDefault);

    conceptsData.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        if (c.id == selConcepto) opt.selected = true;
        cFragment.appendChild(opt);
    });
    cSel.replaceChildren(cFragment);
}

function addRow() {
    if (switchSinAfectaciones.checked) {
        setSinAfectacionesState(false);
    }
    const rows = document.getElementById('inspectionRows');
    const num = rows.children.length + 1;
    const div = document.createElement('div');
    div.className = 'row mb-2 inspection-row align-items-center';
    div.innerHTML = `
        <div class="col-md-1 text-center"><span class="badge bg-secondary">${num}</span></div>
        <div class="col-md-5"><select class="form-select" name="pieza_id[]" aria-label="Seleccionar Pieza"></select></div>
        <div class="col-md-5"><select class="form-select" name="concepto_id[]" aria-label="Seleccionar Concepto"></select></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.inspection-row').remove();updateNumbers()" aria-label="Eliminar fila de inspección"><i class="fas fa-times"></i></button></div>
    `;
    rows.appendChild(div);
    populateSelects(div, null, null);
}

function updateNumbers() {
    document.querySelectorAll('.inspection-row').forEach((row, i) => {
        row.querySelector('.badge').textContent = i + 1;
    });
}

document.getElementById('addRowBtn').addEventListener('click', addRow);

document.getElementById('stepForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.setAttribute('aria-busy', 'true');
    btn.querySelector('.loading').classList.add('show');

    // Si está marcado Sin afectaciones, deshabilitamos los selects vacíos para evitar datos sucios
    if (switchSinAfectaciones.checked) {
        document.querySelectorAll('#inspectionRows select').forEach(sel => sel.disabled = true);
    }

    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => $step]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            btn.querySelector('.loading').classList.remove('show');
            document.querySelectorAll('#inspectionRows select').forEach(sel => sel.disabled = false);
        }
    }).catch(() => {
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo procesar la solicitud.', background: '#1a2332', color: '#fff' });
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
        btn.querySelector('.loading').classList.remove('show');
        document.querySelectorAll('#inspectionRows select').forEach(sel => sel.disabled = false);
    });
});

loadData();
</script>
@endpush
