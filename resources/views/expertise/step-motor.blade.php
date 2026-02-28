@extends('layouts.app')

@section('title', 'Peritaje - Paso 9: Motor y Sistemas - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4 class="mb-0"><i class="fas fa-cog me-2" style="color: var(--accent);"></i>Paso 9: Motor y Sistemas</h4><small>Código: {{ $expertise->codigo }} | Placa: {{ $expertise->placa }}</small></div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>
<div class="content-body">
    @include('components.expertise-progress', ['step' => 9, 'expertise' => $expertise])

    @php
        $motorData = $expertise->motor_sistemas_data ?? [];
        $estadoOptions = ['Bueno' => 'Bueno', 'Regular' => 'Regular', 'Malo' => 'Malo', 'No Aplica' => 'No Aplica'];

        $motorSections = [
            'Motor' => [
                'estado_arranque' => 'Arranque',
                'estado_radiador' => 'Radiador',
                'estado_carter_motor' => 'Carter Motor',
                'estado_carter_caja' => 'Carter Caja',
                'estado_caja_velocidades' => 'Caja de Velocidades',
                'estado_soporte_caja' => 'Soporte Caja',
                'estado_soporte_motor' => 'Soporte Motor',
                'estado_mangueras_radiador' => 'Mangueras Radiador',
                'estado_correas' => 'Correas',
                'tension_correas' => 'Tensión Correas',
                'estado_filtro_aire' => 'Filtro de Aire',
                'estado_externo_bateria' => 'Externo Batería',
            ],
            'Frenos y Suspensión' => [
                'estado_pastilla_freno' => 'Pastillas de Freno',
                'estado_discos_freno' => 'Discos de Freno',
                'estado_punta_eje' => 'Punta de Eje',
                'estado_axiales' => 'Axiales',
                'estado_terminales' => 'Terminales',
                'estado_rotulas' => 'Rótulas',
                'estado_tijeras' => 'Tijeras',
                'estado_caja_direccion' => 'Caja de Dirección',
                'estado_rodamientos' => 'Rodamientos',
                'estado_cardan' => 'Cardán',
                'estado_crucetas' => 'Crucetas',
            ],
            'Interior' => [
                'estado_calefaccion' => 'Calefacción',
                'estado_aire_acondicionado' => 'Aire Acondicionado',
                'estado_cinturones' => 'Cinturones',
                'estado_tapiceria_asientos' => 'Tapicería Asientos',
                'estado_tapiceria_techo' => 'Tapicería Techo',
                'estado_millaret' => 'Millaret',
                'estado_alfombra' => 'Alfombra',
                'estado_chapas' => 'Chapas',
            ],
        ];
    @endphp

    <form id="stepForm">
        @csrf
        @foreach($motorSections as $sectionName => $items)
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-wrench me-2"></i>{{ $sectionName }}</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Sistema</th><th style="width:200px">Estado</th><th>Observación</th></tr></thead>
                        <tbody>
                            @foreach($items as $field => $label)
                            @php $respField = str_replace('estado_', 'respuesta_', $field); if (str_starts_with($field, 'tension_')) $respField = 'respuesta_' . $field; @endphp
                            <tr>
                                <td class="align-middle">{{ $label }}</td>
                                <td>
                                    <select class="form-select form-select-sm" name="{{ $field }}">
                                        <option value="">--</option>
                                        @foreach($estadoOptions as $val => $text)
                                            <option value="{{ $val }}" {{ ($motorData[$field] ?? '') === $val ? 'selected' : '' }}>{{ $text }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="{{ $respField }}" value="{{ $motorData[$respField] ?? '' }}" placeholder="Observación...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Observaciones Motor</label>
                        <textarea class="form-control" name="observaciones_motor" rows="3">{{ $motorData['observaciones_motor'] ?? $expertise->observaciones_motor }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Observaciones Interior</label>
                        <textarea class="form-control" name="observaciones_interior" rows="3">{{ $motorData['observaciones_interior'] ?? $expertise->observaciones_interior }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => 8]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Paso Anterior</a>
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                Continuar al Paso 10 <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('stepForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.querySelector('.loading').classList.add('show');
    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => 9]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = data.redirect;
        else { Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' }); btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});
</script>
@endpush
