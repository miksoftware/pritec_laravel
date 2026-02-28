{{-- Partial: Percentage Section (Llantas/Amortiguadores/Batería) with color bar + table --}}
<section class="p-2 rounded my-2 simple-border">
    <div class="d-flex gap-2">
        <div class="yellow-background sub-title-vertical">{{ $title }}</div>
        <div class="d-flex flex-column w-100">
            <div class="yellow-background sub-title w-100">{{ $subtitle }}</div>

            <!-- Barra de colores -->
            <div class="mx-auto" style="width: 95%; margin-bottom: 0.5rem;">
                <div class="d-flex" style="margin-bottom: 3px;">
                    <div style="height: 15px; width: 25%; background-color:#ff3933;"></div>
                    <div style="height: 15px; width: 25%; background-color:#ff8a33;"></div>
                    <div style="height: 15px; width: 25%; background-color:#ffff33;"></div>
                    <div style="height: 15px; width: 25%; background-color:#36d048;"></div>
                </div>
                <div class="d-flex justify-content-between" style="font-size: 0.7rem; margin-bottom: 5px;">
                    <p style="margin: 0;">0%</p><p style="margin: 0;">100%</p>
                </div>
                <div class="d-flex justify-content-center gap-2" style="font-size: 0.65rem; margin-bottom: 5px;">
                    @php $colors = ['#ff3933', '#ff8a33', '#ffff33', '#36d048']; @endphp
                    @foreach($legendLabels as $i => $label)
                        <div class="d-flex align-items-center" style="gap: 3px;">
                            <div style="width: 10px; height: 10px; background-color:{{ $colors[$i] }};"></div>
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
                                    <td class="{{ $clase }}">{{ $val }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="remarks" style="margin-top: 0.5rem; height: fit-content;">
                OBSERVACIONES: <br> {{ $observations ?? 'Sin observaciones' }}
            </div>
        </div>
    </div>
</section>
