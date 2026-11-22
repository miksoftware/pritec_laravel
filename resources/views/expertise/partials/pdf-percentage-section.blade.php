{{-- Partial: Percentage Section (Llantas/Amortiguadores/Batería) with color bar + table --}}
@php
    $sum = 0;
    $count = 0;
    foreach ($items as $item) {
        $val = floatval($item[1] ?? 0);
        $sum += $val;
        $count++;
    }
    $promedio = $count > 0 ? round($sum / $count, 2) : 0;
    $promedioClamped = max(0, min(100, $promedio));
    $estadoPromedio = $fnEstado($promedio);
    $clasePromedio = obtenerClasePorPorcentaje($promedio);
    $badgeLeft = max(8, min(92, $promedioClamped));
@endphp
<section class="p-2 rounded my-2 simple-border">
    <div class="d-flex gap-2">
        <div class="yellow-background sub-title-vertical">{{ $title }}</div>
        <div class="d-flex flex-column w-100">
            <div class="yellow-background sub-title w-100">{{ $subtitle }}</div>

            <!-- Barra de colores con indicador de promedio -->
            <div class="mx-auto" style="width: 95%; margin-top: 1.5rem; margin-bottom: 0.5rem;">
                
                <!-- Barra relativa con marcador de posición -->
                <div style="position: relative; width: 100%; margin-bottom: 5px;">
                    
                    <!-- Pin / Marcador de Promedio flotante encima de la barra -->
                    <div style="position: absolute; left: {{ $badgeLeft }}%; bottom: calc(100% + 2px); transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; z-index: 10; pointer-events: none;">
                        <div style="background: #111827; color: #ffffff; font-size: 0.72rem; font-weight: bold; padding: 2px 8px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.3); border: 1px solid #374151; white-space: nowrap; display: flex; align-items: center; gap: 5px;">
                            <span>Promedio: <strong>{{ number_format($promedio, 1) }}%</strong></span>
                            <span class="{{ $clasePromedio }}" style="padding: 1px 5px; border-radius: 3px; font-size: 0.65rem; font-weight: 700;">{{ $estadoPromedio }}</span>
                        </div>
                        <!-- Flecha indicadora hacia la barra -->
                        <div style="width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #111827; margin-top: -1px;"></div>
                    </div>

                    <!-- Aguja / línea vertical sobre la barra en la posición exacta -->
                    <div style="position: absolute; left: {{ $promedioClamped }}%; top: -2px; height: calc(100% + 4px); width: 3px; background: #000000; transform: translateX(-50%); z-index: 5; border-radius: 1px; box-shadow: 0 0 2px #ffffff;"></div>

                    <!-- 4 segmentos de colores de la escala -->
                    <div class="d-flex" style="height: 16px; width: 100%; border-radius: 3px; overflow: hidden; border: 1px solid #777;">
                        <div style="height: 100%; width: 25%; background-color:#ff3933;"></div>
                        <div style="height: 100%; width: 25%; background-color:#ff8a33;"></div>
                        <div style="height: 100%; width: 25%; background-color:#ffff33;"></div>
                        <div style="height: 100%; width: 25%; background-color:#36d048;"></div>
                    </div>
                </div>

                <!-- Escala 0% - 100% con referencia de promedio -->
                <div class="d-flex justify-content-between align-items-center" style="font-size: 0.7rem; font-weight: 600; color: #444; margin-bottom: 4px;">
                    <p style="margin: 0;">0%</p>
                    <p style="margin: 0; font-size: 0.68rem; color: #555; font-weight: 500;">Promedio alcanzado: <strong style="color: #000;">{{ number_format($promedio, 1) }}%</strong> ({{ $estadoPromedio }})</p>
                    <p style="margin: 0;">100%</p>
                </div>

                <!-- Leyenda de convenciones -->
                <div class="d-flex justify-content-center gap-3" style="font-size: 0.65rem; margin-bottom: 5px;">
                    @php $colors = ['#ff3933', '#ff8a33', '#ffff33', '#36d048']; @endphp
                    @foreach($legendLabels as $i => $label)
                        <div class="d-flex align-items-center" style="gap: 3px;">
                            <div style="width: 10px; height: 10px; background-color:{{ $colors[$i] }}; border-radius: 2px; border: 1px solid rgba(0,0,0,0.15);"></div>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="yellow-background sub-title ms-4 mb-0" style="margin: 0.3rem 0 0.3rem 1rem;">{{ $itemTitle }}</div>
            <div class="d-flex gap-2 w-100" style="min-height: 180px;">
                <div style="width: 35%; display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('assets/img/' . $imageName) }}" class="imagen-llantas" alt="{{ $itemTitle }}" onerror="this.style.display='none'">
                </div>
                <div style="width: 65%;">
                    <table class="tabla-llantas">
                        <thead>
                            <tr><th style="width: 50%;">ITEM</th><th style="width: 30%;">CONCEPTO</th><th style="width: 20%;">%</th></tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php $val = $item[1] ?? 0; $clase = obtenerClasePorPorcentaje($val); $estado = $fnEstado($val); @endphp
                                <tr>
                                    <td style="text-align: left; padding-left: 0.5rem;">{{ $item[0] }}</td>
                                    <td class="{{ $clase }}">{{ $estado }}</td>
                                    <td class="{{ $clase }}">{{ number_format($val, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top: 2px solid #555; font-weight: bold;">
                                <td style="text-align: left; padding-left: 0.5rem; background-color: var(--main-color); font-weight: bold;">PROMEDIO</td>
                                <td class="{{ $clasePromedio }}" style="font-weight: bold;">{{ $estadoPromedio }}</td>
                                <td class="{{ $clasePromedio }}" style="font-weight: bold;">{{ number_format($promedio, 2) }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="remarks" style="margin-top: 0.5rem; height: fit-content;">
                OBSERVACIONES: <br> {{ $observations ?? 'Sin observaciones' }}
            </div>
        </div>
    </div>
</section>
