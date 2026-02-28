@extends('layouts.app')

@section('title', 'Peritaje - Paso 10: Fugas y Niveles - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4 class="mb-0"><i class="fas fa-tint me-2" style="color: var(--accent);"></i>Paso 10: Fugas y Niveles</h4><small>Código: {{ $expertise->codigo }} | Placa: {{ $expertise->placa }}</small></div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>
<div class="content-body">
    @include('components.expertise-progress', ['step' => 10, 'expertise' => $expertise])

    @php
        $fugasData = $expertise->fugas_niveles_data ?? [];
        $estadoOptions = ['Bueno' => 'Bueno', 'Regular' => 'Regular', 'Malo' => 'Malo', 'No Aplica' => 'No Aplica'];
        $respOptions = ['Sin Fuga' => 'Sin Fuga', 'Fuga Leve' => 'Fuga Leve', 'Fuga Moderada' => 'Fuga Moderada', 'Fuga Severa' => 'Fuga Severa', 'No Aplica' => 'No Aplica'];
        $nivelOptions = ['Lleno' => 'Lleno', 'Adecuado' => 'Adecuado', 'Bajo' => 'Bajo', 'Vacío' => 'Vacío', 'No Aplica' => 'No Aplica'];
        $estadoCompOptions = ['Bueno' => 'Bueno', 'Regular' => 'Regular', 'Malo' => 'Malo', 'No Aplica' => 'No Aplica'];

        $fugasItems = [
            'respuesta_fuga_aceite_motor' => 'Aceite de Motor',
            'respuesta_fuga_aceite_caja_velocidades' => 'Aceite Caja de Velocidades',
            'respuesta_fuga_aceite_caja_transmision' => 'Aceite Caja de Transmisión',
            'respuesta_fuga_liquido_frenos' => 'Líquido de Frenos',
            'respuesta_fuga_aceite_direccion_hidraulica' => 'Aceite Dirección Hidráulica',
            'respuesta_fuga_liquido_bomba_embrague' => 'Líquido Bomba de Embrague',
            'respuesta_fuga_tanque_combustible' => 'Tanque de Combustible',
        ];

        $estadoItems = [
            'respuesta_estado_tanque_silenciador' => 'Tanque Silenciador',
            'respuesta_estado_tubo_exhosto' => 'Tubo de Exhosto',
            'respuesta_estado_tanque_catalizador_gases' => 'Catalizador de Gases',
            'respuesta_estado_guardapolvo_caja_direccion' => 'Guardapolvo Caja Dirección',
            'respuesta_estado_tuberia_frenos' => 'Tubería de Frenos',
        ];

        $nivelItems = [
            'respuesta_viscosidad_aceite_motor' => 'Viscosidad Aceite Motor',
            'respuesta_nivel_refrigerante_motor' => 'Nivel Refrigerante Motor',
            'respuesta_nivel_liquido_frenos' => 'Nivel Líquido de Frenos',
            'respuesta_nivel_agua_limpiavidrios' => 'Nivel Agua Limpiavidrios',
            'respuesta_nivel_aceite_direccion_hidraulica' => 'Nivel Aceite Dir. Hidráulica',
            'respuesta_nivel_liquido_embrague' => 'Nivel Líquido Embrague',
            'respuesta_nivel_aceite_motor' => 'Nivel Aceite Motor',
        ];
    @endphp

    <form id="stepForm">
        @csrf

        <!-- Fugas -->
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-tint me-2"></i>Fugas</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Componente</th><th style="width:200px">Estado</th><th style="width:220px">Respuesta</th></tr></thead>
                        <tbody>
                            @foreach($fugasItems as $field => $label)
                            @php $estadoField = str_replace('respuesta_', 'estado_', $field); @endphp
                            <tr><td>{{ $label }}</td>
                            <td>
                                <select class="form-select form-select-sm" name="{{ $estadoField }}">
                                    <option value="">--</option>
                                    @foreach($estadoOptions as $val => $text)
                                        <option value="{{ $val }}" {{ ($fugasData[$estadoField] ?? '') === $val ? 'selected' : '' }}>{{ $text }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm" name="{{ $field }}">
                                    <option value="">--</option>
                                    @foreach($respOptions as $val => $text)
                                        <option value="{{ $val }}" {{ ($fugasData[$field] ?? '') === $val ? 'selected' : '' }}>{{ $text }}</option>
                                    @endforeach
                                </select>
                            </td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Estado Componentes -->
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-search me-2"></i>Estado de Componentes</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Componente</th><th style="width:200px">Estado</th><th style="width:220px">Respuesta</th></tr></thead>
                        <tbody>
                            @foreach($estadoItems as $field => $label)
                            @php $estadoField = str_replace('respuesta_estado_', 'estado_comp_', $field); @endphp
                            <tr><td>{{ $label }}</td>
                            <td>
                                <select class="form-select form-select-sm" name="{{ $estadoField }}">
                                    <option value="">--</option>
                                    @foreach($estadoOptions as $val => $text)
                                        <option value="{{ $val }}" {{ ($fugasData[$estadoField] ?? '') === $val ? 'selected' : '' }}>{{ $text }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm" name="{{ $field }}">
                                    <option value="">--</option>
                                    @foreach($estadoCompOptions as $val => $text)
                                        <option value="{{ $val }}" {{ ($fugasData[$field] ?? '') === $val ? 'selected' : '' }}>{{ $text }}</option>
                                    @endforeach
                                </select>
                            </td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Niveles -->
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-thermometer-half me-2"></i>Niveles</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Componente</th><th style="width:200px">Estado</th><th style="width:220px">Respuesta</th></tr></thead>
                        <tbody>
                            @foreach($nivelItems as $field => $label)
                            @php $estadoField = str_replace('respuesta_', 'estado_', $field); @endphp
                            <tr><td>{{ $label }}</td>
                            <td>
                                <select class="form-select form-select-sm" name="{{ $estadoField }}">
                                    <option value="">--</option>
                                    @foreach($estadoOptions as $val => $text)
                                        <option value="{{ $val }}" {{ ($fugasData[$estadoField] ?? '') === $val ? 'selected' : '' }}>{{ $text }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm" name="{{ $field }}">
                                    <option value="">--</option>
                                    @foreach($nivelOptions as $val => $text)
                                        <option value="{{ $val }}" {{ ($fugasData[$field] ?? '') === $val ? 'selected' : '' }}>{{ $text }}</option>
                                    @endforeach
                                </select>
                            </td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Prueba de Ruta -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-road me-1"></i>Prueba de Ruta</label>
                    <textarea class="form-control" name="prueba_ruta" rows="3">{{ $fugasData['prueba_ruta'] ?? $expertise->prueba_ruta }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Observaciones Generales</label>
                    <textarea class="form-control" name="observaciones_fugas" rows="3">{{ $fugasData['observaciones_fugas'] ?? $expertise->observaciones_fugas }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => 9]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Paso Anterior</a>
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                Continuar al Paso 11 <i class="fas fa-arrow-right ms-2"></i>
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
    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => 10]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = data.redirect;
        else { Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' }); btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});
</script>
@endpush
