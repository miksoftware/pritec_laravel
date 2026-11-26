{{-- Partial: Inspection Section (Carrocería/Estructura/Chasis) with image + markers + pieces table --}}
@php
    $isPercentageSection = in_array(strtoupper($sectionName), ['CARROCERÍA', 'ESTRUCTURA']);
    $promedio = $sectionData['average_percentage'] ?? 100.0;
    $promedioClamped = max(0, min(100, $promedio));
    $badgeLeft = max(8, min(92, $promedioClamped));
    $statusLabel = $sectionData['status_label'] ?? 'Bueno';
    $statusClass = $sectionData['status_class'] ?? 'estado-nuevas';
    $evaluatedCount = $sectionData['evaluated_count'] ?? count($sectionData['pieces'] ?? []);
    $totalPieces = $sectionData['total_section_pieces'] ?? 0;
    $criticalCount = $sectionData['critical_count'] ?? 0;
    $isCritical = $sectionData['is_critical'] ?? false;
@endphp
<section class="p-2 rounded simple-border">
    <div class="d-flex gap-2">
        <div class="yellow-background sub-title-vertical">{{ $titleType }}</div>
        <div class="d-flex flex-column w-100">
            <div class="yellow-background sub-title w-100">VEHÍCULO: {{ $vehicleName ?? 'N/A' }}</div>
            <p style="font-size: 11px; margin-bottom: 0.3rem;">Indique con un círculo en que parte del vehículo tiene alguna condición.</p>

            @if($isPercentageSection)
                <!-- Barra de colores con indicador de porcentaje estilo Llantas -->
                <div class="mx-auto" style="width: 95%; margin-top: 1.4rem; margin-bottom: 0.5rem;">
                    
                    <!-- Barra relativa con marcador de posición -->
                    <div style="position: relative; width: 100%; margin-bottom: 4px;">
                        
                        <!-- Pin / Marcador de Promedio flotante encima de la barra -->
                        <div style="position: absolute; left: {{ $badgeLeft }}%; bottom: calc(100% + 2px); transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; z-index: 10; pointer-events: none;">
                            <div style="background: #111827; color: #ffffff; font-size: 0.72rem; font-weight: bold; padding: 2px 8px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.3); border: 1px solid #374151; white-space: nowrap; display: flex; align-items: center; gap: 5px;">
                                <span>% {{ $sectionName }}: <strong>{{ number_format($promedio, 1) }}%</strong></span>
                                <span class="{{ $statusClass }}" style="padding: 1px 5px; border-radius: 3px; font-size: 0.65rem; font-weight: 700;">{{ $statusLabel }}</span>
                            </div>
                            <!-- Flecha indicadora hacia la barra -->
                            <div style="width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #111827; margin-top: -1px;"></div>
                        </div>

                        <!-- Aguja / línea vertical sobre la barra en la posición exacta -->
                        <div style="position: absolute; left: {{ $promedioClamped }}%; top: -2px; height: calc(100% + 4px); width: 3px; background: #000000; transform: translateX(-50%); z-index: 5; border-radius: 1px; box-shadow: 0 0 2px #ffffff;"></div>

                        <!-- Pista de 3 segmentos según escala oficial del Excel: 0-59% Malo, 60-89% Aceptable, 90-100% Bueno -->
                        <div class="d-flex" style="height: 16px; width: 100%; border-radius: 3px; overflow: hidden; border: 1px solid #777;">
                            <div style="height: 100%; width: 60%; background-color:#ff3933;" title="0-59% Malo"></div>
                            <div style="height: 100%; width: 30%; background-color:#ffff33;" title="60-89% Aceptable"></div>
                            <div style="height: 100%; width: 10%; background-color:#36d048;" title="90-100% Bueno"></div>
                        </div>
                    </div>

                    <!-- Escala 0% - 100% con referencia -->
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.7rem; font-weight: 600; color: #444; margin-bottom: 3px;">
                        <p style="margin: 0;">0%</p>
                        <p style="margin: 0; font-size: 0.68rem; color: #555; font-weight: 500;">
                            Piezas evaluadas: <strong>{{ $evaluatedCount }} @if($totalPieces > 0) de {{ $totalPieces }} @endif</strong>
                            @if(strtoupper($sectionName) === 'ESTRUCTURA')
                                &nbsp;|&nbsp; Piezas críticas: <strong style="{{ $criticalCount > 0 ? 'color: #dc2626;' : '' }}">{{ $criticalCount }}</strong>
                                @if($isCritical)
                                    &nbsp;<span class="badge bg-danger" style="font-size: 0.65rem; padding: 1px 4px; color: white; background-color: #dc2626;">MALO (Condición crítica F/DF/MR)</span>
                                @endif
                            @endif
                        </p>
                        <p style="margin: 0;">100%</p>
                    </div>

                    <!-- Leyenda de convenciones según el Excel -->
                    <div class="d-flex justify-content-center gap-3" style="font-size: 0.65rem; margin-bottom: 4px;">
                        <div class="d-flex align-items-center" style="gap: 3px;">
                            <div style="width: 10px; height: 10px; background-color:#ff3933; border-radius: 2px; border: 1px solid rgba(0,0,0,0.15);"></div>
                            <span>0-59% Malo</span>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 3px;">
                            <div style="width: 10px; height: 10px; background-color:#ffff33; border-radius: 2px; border: 1px solid rgba(0,0,0,0.15);"></div>
                            <span>60-89% Aceptable</span>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 3px;">
                            <div style="width: 10px; height: 10px; background-color:#36d048; border-radius: 2px; border: 1px solid rgba(0,0,0,0.15);"></div>
                            <span>90-100% Bueno</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="yellow-background sub-title ms-4 mb-0" style="margin: 0.2rem 0 0.2rem 1rem;">{{ $sectionName }}</div>
            <div class="d-flex gap-2 w-100" style="min-height: 160px;">
                @if(!empty($sectionData['image']))
                    <div style="position: relative; width: 260px; height: 260px; flex-shrink: 0;">
                        <img src="{{ asset($sectionData['image']) }}" style="width: 260px; height: 260px; object-fit: contain;" onerror="this.style.display='none'">
                        @if(!empty($sectionData['pieces']))
                            @foreach($sectionData['pieces'] as $pieza)
                                @if(!empty($pieza['position_x']) && !empty($pieza['position_y']))
                                    <div style="position: absolute; left: {{ $pieza['position_x'] }}%; top: {{ $pieza['position_y'] }}%; transform: translate(-50%, -50%); width: 22px; height: 22px; background: #ff0000; border: 2px solid white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                        {{ $pieza['piece_number'] }}
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="w-50" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; height: 160px; border: 1px dashed #ccc;">
                        <p style="color: #999;">Sin imagen</p>
                    </div>
                @endif
                <div class="d-flex flex-column w-50" style="gap: 0.2rem; overflow: hidden;">
                    <div class="d-flex" style="gap: 0.2rem;">
                        <div class="yellow-background text-center" style="width: 10%; padding: 0.2rem; font-size: 9px; font-weight: bold;">No.</div>
                        <div class="yellow-background text-center" style="width: {{ $isPercentageSection ? '46%' : '60%' }}; padding: 0.2rem; font-size: 9px; font-weight: bold;">Descripción pieza</div>
                        <div class="yellow-background text-center" style="width: {{ $isPercentageSection ? '28%' : '30%' }}; padding: 0.2rem; font-size: 9px; font-weight: bold;">Concepto</div>
                        @if($isPercentageSection)
                        <div class="yellow-background text-center" style="width: 16%; padding: 0.2rem; font-size: 9px; font-weight: bold;">%</div>
                        @endif
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.2rem; max-height: 180px; overflow-y: auto;">
                        @forelse($sectionData['pieces'] ?? [] as $pieza)
                            @php
                                $piecePct = $pieza['percentage'] ?? 100;
                                $pieceClass = obtenerClasePorPorcentaje($piecePct);
                            @endphp
                            <div class="d-flex" style="gap: 0.2rem;">
                                <div class="input text-center" style="width: 10%; padding: 0.15rem 0.2rem; font-size: 8px; border: 1px solid var(--main-color); border-radius: 4px;">{{ $pieza['piece_number'] }}</div>
                                <div class="input" style="width: {{ $isPercentageSection ? '46%' : '60%' }}; padding: 0.15rem 0.3rem; font-size: 8px; border: 1px solid var(--main-color); border-radius: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $pieza['piece_name'] }}</div>
                                <div class="input text-center" style="width: {{ $isPercentageSection ? '28%' : '30%' }}; padding: 0.15rem 0.2rem; font-size: 8px; border: 1px solid var(--main-color); border-radius: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $pieza['concept_name'] }}
                                    @if(!empty($pieza['is_critical']))
                                        <span title="Pieza Crítica (F/DF/MR)" style="color: #dc2626; font-weight: bold;">*</span>
                                    @endif
                                </div>
                                @if($isPercentageSection)
                                <div class="input text-center {{ $pieceClass }}" style="width: 16%; padding: 0.15rem 0.2rem; font-size: 8px; border-radius: 4px; font-weight: bold;">
                                    {{ $piecePct }}%
                                </div>
                                @endif
                            </div>
                        @empty
                            <div style="width: 100%; padding: 0.8rem 0.5rem; text-align: center; border: 1px dashed #16a34a; border-radius: 4px; background-color: #f0fdf4;">
                                <div style="font-weight: bold; color: #15803d; font-size: 10px;">✓ SIN AFECTACIONES</div>
                                <div style="color: #166534; font-size: 8.5px; margin-top: 2px;">Sección en óptimas condiciones técnicas (100% Bueno)</div>
                            </div>
                        @endforelse
                    </div>

                    @if($isPercentageSection)
                    <!-- Fila de Promedio General -->
                    <div class="d-flex" style="gap: 0.2rem; margin-top: 2px;">
                        <div style="width: 56%; padding: 0.15rem 0.3rem; font-size: 8px; font-weight: bold; background-color: var(--main-color); border-radius: 4px; border: 1px solid #888;">
                            % {{ $sectionName }}
                        </div>
                        <div class="{{ $statusClass }} text-center" style="width: 28%; padding: 0.15rem 0.2rem; font-size: 8px; font-weight: bold; border-radius: 4px;">
                            {{ $statusLabel }}
                        </div>
                        <div class="{{ $statusClass }} text-center" style="width: 16%; padding: 0.15rem 0.2rem; font-size: 8px; font-weight: bold; border-radius: 4px;">
                            {{ number_format($promedio, 1) }}%
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="remarks" style="margin-top: 0.4rem; height: fit-content;">
                OBSERVACIONES: <br> {{ $observations ?? 'Sin observaciones' }}
            </div>
        </div>
    </div>
</section>
