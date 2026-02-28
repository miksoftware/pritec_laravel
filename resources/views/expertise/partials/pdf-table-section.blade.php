{{-- Partial: Table Section (Motor/Líquidos/Fugas/Componentes) --}}
<section class="p-2 rounded my-2 simple-border">
    <div class="d-flex gap-2">
        <div class="yellow-background sub-title-vertical">{{ $title }}</div>
        <div class="d-flex flex-column w-100">
            <div style="width: 100%;">
                <!-- Header -->
                <div class="d-flex" style="background-color: var(--main-color); border: 1px solid var(--main-color);">
                    <div style="width: {{ $hasEstado ? '40%' : '50%' }}; padding: 0.3rem 0.5rem; font-size: 10px; font-weight: bold; text-align: center; border-right: 1px solid var(--main-color);">SISTEMA</div>
                    @if($hasEstado)
                        <div style="width: 30%; padding: 0.3rem 0.5rem; font-size: 10px; font-weight: bold; text-align: center; border-right: 1px solid var(--main-color);">ESTADO</div>
                    @endif
                    <div style="width: {{ $hasEstado ? '30%' : '50%' }}; padding: 0.3rem 0.5rem; font-size: 10px; font-weight: bold; text-align: center;">RESPUESTA</div>
                </div>
                <!-- Data rows -->
                @foreach($fields as $campo => $etiqueta)
                    <div class="d-flex" style="border: 1px solid var(--main-color); border-top: none;">
                        <div style="width: {{ $hasEstado ? '40%' : '50%' }}; padding: 0.3rem 0.5rem; font-size: 9px; border-right: 1px solid var(--main-color);">{{ $etiqueta }}</div>
                        @if($hasEstado)
                            <div style="width: 30%; padding: 0.3rem 0.5rem; font-size: 9px; text-align: center; border-right: 1px solid var(--main-color);">{{ $data[$campo] ?? 'N/A' }}</div>
                            @php
                                $respKey = str_replace('estado_', 'respuesta_', $campo);
                                if (str_starts_with($campo, 'estado_comp_')) {
                                    $respKey = str_replace('estado_comp_', 'respuesta_estado_', $campo);
                                }
                            @endphp
                            <div style="width: 30%; padding: 0.3rem 0.5rem; font-size: 9px; text-align: center;">{{ $data[$respKey] ?? '' }}</div>
                        @else
                            <div style="width: 50%; padding: 0.3rem 0.5rem; font-size: 9px; text-align: center;">{{ $data[$campo] ?? 'N/A' }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="remarks" style="margin-top: 0.5rem; height: fit-content;">
                OBSERVACIONES: <br> {{ $observations ?? 'Sin observaciones' }}
            </div>
        </div>
    </div>
</section>
