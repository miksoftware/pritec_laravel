@extends('layouts.app')

@section('title', 'Peritaje - Paso 7: Amortiguadores - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4 class="mb-0"><i class="fas fa-compress-arrows-alt me-2" style="color: var(--accent);"></i>Paso 7: Amortiguadores</h4><small>Código: {{ $expertise->codigo }} | Placa: {{ $expertise->placa }}</small></div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>
<div class="content-body">
    @include('components.expertise-progress', ['step' => 7, 'expertise' => $expertise])

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-compress-arrows-alt me-2"></i>Porcentaje de Vida Útil de Amortiguadores</h5></div>
        <div class="card-body">
            <form id="stepForm">
                @csrf
                <p class="text-muted mb-4">Indique el porcentaje de vida útil restante de cada amortiguador. Puede mover el slider o escribir el valor directamente.</p>

                @if($expertise->is_moto)
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Cant. Amortiguadores Delanteros</label>
                        <select class="form-select" name="cant_amortiguadores_delanteros" id="cantDel">
                            <option value="1" {{ ($expertise->cant_amortiguadores_delanteros ?? 1) == 1 ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($expertise->cant_amortiguadores_delanteros ?? 1) == 2 ? 'selected' : '' }}>2</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cant. Amortiguadores Traseros</label>
                        <select class="form-select" name="cant_amortiguadores_traseros" id="cantTras">
                            <option value="1" {{ ($expertise->cant_amortiguadores_traseros ?? 1) == 1 ? 'selected' : '' }}>1</option>
                            <option value="2" {{ ($expertise->cant_amortiguadores_traseros ?? 1) == 2 ? 'selected' : '' }}>2</option>
                        </select>
                    </div>
                </div>
                @endif

                <div class="row g-4 mb-4">
                    @php
                        $shocks = [
                            ['name' => 'amortiguador_anterior_izquierdo', 'label' => 'Anterior Izquierdo', 'icon' => 'fas fa-compress-arrows-alt', 'moto_class' => 'moto-del-izq'],
                            ['name' => 'amortiguador_anterior_derecho', 'label' => 'Anterior Derecho', 'icon' => 'fas fa-compress-arrows-alt'],
                            ['name' => 'amortiguador_posterior_izquierdo', 'label' => 'Posterior Izquierdo', 'icon' => 'fas fa-compress-arrows-alt', 'moto_class' => 'moto-tras-izq'],
                            ['name' => 'amortiguador_posterior_derecho', 'label' => 'Posterior Derecho', 'icon' => 'fas fa-compress-arrows-alt'],
                        ];
                    @endphp

                    @foreach($shocks as $shock)
                        @php $val = intval($expertise->{$shock['name']} ?? 50); @endphp
                        <div class="col-md-6 {{ $shock['moto_class'] ?? '' }}"
                             @if($expertise->is_moto && isset($shock['moto_class'])) style="display:none" @endif>
                            <div class="card percentage-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0"><i class="{{ $shock['icon'] }} me-2 text-primary"></i>{{ $shock['label'] }}</h6>
                                        <div class="input-group" style="width: 120px;">
                                            <input type="number" class="form-control text-center fw-bold percentage-input"
                                                   id="input_{{ $shock['name'] }}" min="0" max="100"
                                                   value="{{ $val }}"
                                                   data-slider="slider_{{ $shock['name'] }}"
                                                   data-bar="bar_{{ $shock['name'] }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="percentage-slider-container">
                                        <input type="range" class="percentage-slider" name="{{ $shock['name'] }}"
                                               id="slider_{{ $shock['name'] }}" min="0" max="100" step="1"
                                               value="{{ $val }}"
                                               data-input="input_{{ $shock['name'] }}"
                                               data-bar="bar_{{ $shock['name'] }}">
                                        <div class="percentage-bar" id="bar_{{ $shock['name'] }}" style="width: {{ $val }}%"></div>
                                        <div class="percentage-thumb" id="thumb_{{ $shock['name'] }}" style="left: {{ $val }}%"></div>
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
                    <label class="form-label">Observaciones de Amortiguadores</label>
                    <textarea class="form-control" name="observaciones_amortiguadores" rows="3">{{ $expertise->observaciones_amortiguadores }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => 6]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Paso Anterior</a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        Continuar al Paso 8 <i class="fas fa-arrow-right ms-2"></i>
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
@if($expertise->is_moto)
document.getElementById('cantDel')?.addEventListener('change', function() {
    document.querySelector('.moto-del-izq').style.display = this.value == 2 ? '' : 'none';
});
document.getElementById('cantTras')?.addEventListener('change', function() {
    document.querySelector('.moto-tras-izq').style.display = this.value == 2 ? '' : 'none';
});
@endif

document.getElementById('stepForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.querySelector('.loading').classList.add('show');
    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => 7]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = data.redirect;
        else { Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' }); btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});
</script>
@endpush
