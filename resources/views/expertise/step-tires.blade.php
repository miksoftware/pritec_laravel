@extends('layouts.app')

@section('title', 'Peritaje - Paso 6: Llantas - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4 class="mb-0"><i class="fas fa-circle me-2" style="color: var(--accent);"></i>Paso 6: Inspección de Llantas</h4><small>Código: {{ $expertise->codigo }} | Placa: {{ $expertise->placa }}</small></div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>
<div class="content-body">
    @include('components.expertise-progress', ['step' => 6, 'expertise' => $expertise])

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-circle me-2"></i>Porcentaje de Vida Útil de Llantas</h5></div>
        <div class="card-body">
            <form id="stepForm">
                @csrf
                <p class="text-muted mb-4">Indique el porcentaje de vida útil restante de cada llanta (0% = desgastada, 100% = nueva). Puede mover el slider o escribir el valor directamente.</p>

                <div class="row g-4 mb-4">
                    @php
                        $tires = [
                            ['name' => 'llanta_anterior_izquierda', 'label' => 'Anterior Izquierda', 'icon' => 'fas fa-circle', 'hide_moto' => true],
                            ['name' => 'llanta_anterior_derecha', 'label' => 'Anterior Derecha (Delantera)', 'icon' => 'fas fa-circle', 'hide_moto' => false],
                            ['name' => 'llanta_posterior_izquierda', 'label' => 'Posterior Izquierda', 'icon' => 'fas fa-circle', 'hide_moto' => true],
                            ['name' => 'llanta_posterior_derecha', 'label' => 'Posterior Derecha (Trasera)', 'icon' => 'fas fa-circle', 'hide_moto' => false],
                        ];
                    @endphp

                    @foreach($tires as $tire)
                        @if($tire['hide_moto'] && $expertise->is_moto) @continue @endif
                        @php $val = intval($expertise->{$tire['name']} ?? 50); @endphp
                        <div class="col-md-6">
                            <div class="card percentage-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0"><i class="{{ $tire['icon'] }} me-2 text-primary"></i>{{ $tire['label'] }}</h6>
                                        <div class="input-group" style="width: 120px;">
                                            <input type="number" class="form-control text-center fw-bold percentage-input"
                                                   id="input_{{ $tire['name'] }}" min="0" max="100"
                                                   value="{{ $val }}"
                                                   data-slider="slider_{{ $tire['name'] }}"
                                                   data-bar="bar_{{ $tire['name'] }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="percentage-slider-container">
                                        <input type="range" class="percentage-slider" name="{{ $tire['name'] }}"
                                               id="slider_{{ $tire['name'] }}" min="0" max="100" step="1"
                                               value="{{ $val }}"
                                               data-input="input_{{ $tire['name'] }}"
                                               data-bar="bar_{{ $tire['name'] }}">
                                        <div class="percentage-bar" id="bar_{{ $tire['name'] }}" style="width: {{ $val }}%"></div>
                                        <div class="percentage-thumb" id="thumb_{{ $tire['name'] }}" style="left: {{ $val }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-danger">0%</small>
                                        <small class="text-muted">50%</small>
                                        <small class="text-success">100%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones de Llantas</label>
                    <textarea class="form-control" name="observaciones_llantas" rows="3">{{ $expertise->observaciones_llantas }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => $expertise->getPreviousStep(6)]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Paso Anterior</a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        Continuar al Paso 7 <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('components.percentage-slider-styles')
@endsection

@push('scripts')
@include('components.percentage-slider-scripts')
<script>
document.getElementById('stepForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.querySelector('.loading').classList.add('show');
    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => 6]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = data.redirect;
        else { Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' }); btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});
</script>
@endpush
