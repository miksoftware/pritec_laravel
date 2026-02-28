@extends('layouts.app')

@php
    $sectionNames = ['carroceria' => 'Carrocería', 'estructura' => 'Estructura', 'chasis' => 'Chasis'];
    $sectionIcons = ['carroceria' => 'fas fa-car-side', 'estructura' => 'fas fa-building', 'chasis' => 'fas fa-cogs'];
    $sectionTitle = $sectionNames[$section] ?? $section;
    $sectionIcon = $sectionIcons[$section] ?? 'fas fa-search';
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
                <p class="text-muted mb-3">Seleccione una pieza y asigne el concepto de inspección correspondiente.</p>

                <div id="inspectionRows">
                    @if(isset($existingInspections) && $existingInspections->count() > 0)
                        @foreach($existingInspections as $i => $insp)
                        <div class="row mb-2 inspection-row align-items-center">
                            <div class="col-md-1 text-center"><span class="badge bg-secondary">{{ $i + 1 }}</span></div>
                            <div class="col-md-5">
                                <select class="form-select" name="pieza_id[]"></select>
                            </div>
                            <div class="col-md-5">
                                <select class="form-select" name="concepto_id[]"></select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.inspection-row').remove();updateNumbers()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <button type="button" class="btn btn-outline-primary mb-3" id="addRowBtn"><i class="fas fa-plus me-2"></i>Agregar Pieza</button>

                <div class="mb-3">
                    <label class="form-label">Observaciones de {{ $sectionTitle }}</label>
                    <textarea class="form-control" name="observaciones_{{ $section }}" rows="3" placeholder="Observaciones generales...">{{ $expertise->{'observaciones_' . $section} }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => $expertise->getPreviousStep($step)]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Paso Anterior</a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        Continuar al Paso {{ $expertise->getNextStep($step) }} <i class="fas fa-arrow-right ms-2"></i>
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

    if (existingInspections.length === 0) addRow();
}

function populateSelects(row, selPieza, selConcepto) {
    const pSel = row.querySelector('select[name="pieza_id[]"]');
    const cSel = row.querySelector('select[name="concepto_id[]"]');
    pSel.innerHTML = '<option value="">-- Seleccionar Pieza --</option>';
    cSel.innerHTML = '<option value="">-- Seleccionar Concepto --</option>';
    piecesData.forEach(p => {
        pSel.innerHTML += `<option value="${p.id}" ${p.id == selPieza ? 'selected' : ''}>${p.piece_number}. ${p.piece_name}</option>`;
    });
    conceptsData.forEach(c => {
        cSel.innerHTML += `<option value="${c.id}" ${c.id == selConcepto ? 'selected' : ''}>${c.name}</option>`;
    });
}

function addRow() {
    const rows = document.getElementById('inspectionRows');
    const num = rows.children.length + 1;
    const div = document.createElement('div');
    div.className = 'row mb-2 inspection-row align-items-center';
    div.innerHTML = `
        <div class="col-md-1 text-center"><span class="badge bg-secondary">${num}</span></div>
        <div class="col-md-5"><select class="form-select" name="pieza_id[]"></select></div>
        <div class="col-md-5"><select class="form-select" name="concepto_id[]"></select></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.inspection-row').remove();updateNumbers()"><i class="fas fa-times"></i></button></div>
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
    btn.disabled = true; btn.querySelector('.loading').classList.add('show');

    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => $step]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = data.redirect;
        else { Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' }); btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});

loadData();
</script>
@endpush
