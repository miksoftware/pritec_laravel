@extends('layouts.app')

@section('title', 'Peritaje - Paso 8: Batería - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4 class="mb-0"><i class="fas fa-battery-full me-2" style="color: var(--accent);"></i>Paso 8: Inspección de Batería</h4><small>Código: {{ $expertise->codigo }} | Placa: {{ $expertise->placa }}</small></div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>
<div class="content-body">
    @include('components.expertise-progress', ['step' => 8, 'expertise' => $expertise])

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-battery-full me-2"></i>Pruebas de Batería</h5></div>
        <div class="card-body">
            <form id="stepForm">
                @csrf
                <p class="text-muted mb-4">Indique el resultado de cada prueba de batería. Puede mover el slider o escribir el valor directamente.</p>

                <div class="row g-4 mb-4">
                    @php
                        $batteries = [
                            ['name' => 'prueba_bateria', 'label' => 'Prueba de Batería', 'icon' => 'fas fa-battery-full'],
                            ['name' => 'prueba_arranque', 'label' => 'Prueba de Arranque', 'icon' => 'fas fa-play-circle'],
                            ['name' => 'carga_bateria', 'label' => 'Carga de Batería', 'icon' => 'fas fa-bolt'],
                        ];
                    @endphp

                    @foreach($batteries as $battery)
                        @php $val = intval($expertise->{$battery['name']} ?? 50); @endphp
                        <div class="col-md-4">
                            <div class="card percentage-card">
                                <div class="card-body">
                                    <div class="text-center mb-2">
                                        <i class="{{ $battery['icon'] }} fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-0">{{ $battery['label'] }}</h6>
                                    </div>
                                    <div class="d-flex justify-content-center mb-3">
                                        <div class="input-group" style="width: 120px;">
                                            <input type="number" class="form-control text-center fw-bold percentage-input"
                                                   id="input_{{ $battery['name'] }}" min="0" max="100"
                                                   value="{{ $val }}"
                                                   data-slider="slider_{{ $battery['name'] }}"
                                                   data-bar="bar_{{ $battery['name'] }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="percentage-slider-container">
                                        <input type="range" class="percentage-slider" name="{{ $battery['name'] }}"
                                               id="slider_{{ $battery['name'] }}" min="0" max="100" step="1"
                                               value="{{ $val }}"
                                               data-input="input_{{ $battery['name'] }}"
                                               data-bar="bar_{{ $battery['name'] }}">
                                        <div class="percentage-bar" id="bar_{{ $battery['name'] }}" style="width: {{ $val }}%"></div>
                                        <div class="percentage-thumb" id="thumb_{{ $battery['name'] }}" style="left: {{ $val }}%"></div>
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
                    <label class="form-label">Observaciones de Batería</label>
                    <textarea class="form-control" name="observaciones_bateria" rows="3">{{ $expertise->observaciones_bateria }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => 7]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Paso Anterior</a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        Continuar al Paso 9 <i class="fas fa-arrow-right ms-2"></i>
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
    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => 8]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = data.redirect;
        else { Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' }); btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});
</script>
@endpush
