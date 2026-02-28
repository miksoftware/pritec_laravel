{{-- Partial: Inspection Section (Carrocería/Estructura/Chasis) with image + markers + pieces table --}}
<section class="p-2 rounded simple-border">
    <div class="d-flex gap-2">
        <div class="yellow-background sub-title-vertical">{{ $titleType }}</div>
        <div class="d-flex flex-column w-100">
            <div class="yellow-background sub-title w-100">VEHÍCULO: {{ $vehicleName ?? 'N/A' }}</div>
            <p style="font-size: 11px; margin-bottom: 0.3rem;">Indique con un círculo en que parte del vehículo tiene alguna condición.</p>
            <div class="yellow-background sub-title ms-4 mb-0" style="margin: 0.3rem 0 0.3rem 1rem;">{{ $sectionName }}</div>
            <div class="d-flex gap-2 w-100" style="min-height: 180px;">
                @if(!empty($sectionData['image']))
                    <div style="position: relative; width: 300px; height: 300px; flex-shrink: 0;">
                        <img src="{{ asset($sectionData['image']) }}" style="width: 300px; height: 300px; object-fit: contain;" onerror="this.style.display='none'">
                        @if(!empty($sectionData['pieces']))
                            @foreach($sectionData['pieces'] as $pieza)
                                @if(!empty($pieza['position_x']) && !empty($pieza['position_y']))
                                    <div style="position: absolute; left: {{ $pieza['position_x'] }}%; top: {{ $pieza['position_y'] }}%; transform: translate(-50%, -50%); width: 24px; height: 24px; background: #ff0000; border: 2px solid white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 11px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                        {{ $pieza['piece_number'] }}
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="w-50" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; height: 180px; border: 1px dashed #ccc;">
                        <p style="color: #999;">Sin imagen</p>
                    </div>
                @endif
                <div class="d-flex flex-column w-50" style="gap: 0.2rem; overflow: hidden;">
                    <div class="d-flex" style="gap: 0.2rem;">
                        <div class="yellow-background text-center" style="width: 10%; padding: 0.2rem; font-size: 9px; font-weight: bold;">No.</div>
                        <div class="yellow-background text-center" style="width: 60%; padding: 0.2rem; font-size: 9px; font-weight: bold;">Descripción pieza</div>
                        <div class="yellow-background text-center" style="width: 30%; padding: 0.2rem; font-size: 9px; font-weight: bold;">Concepto</div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.2rem; max-height: 160px; overflow-y: auto;">
                        @forelse($sectionData['pieces'] ?? [] as $pieza)
                            <div class="d-flex" style="gap: 0.2rem;">
                                <div class="input text-center" style="width: 10%; padding: 0.15rem 0.2rem; font-size: 8px; border: 1px solid var(--main-color); border-radius: 4px;">{{ $pieza['piece_number'] }}</div>
                                <div class="input" style="width: 60%; padding: 0.15rem 0.3rem; font-size: 8px; border: 1px solid var(--main-color); border-radius: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $pieza['piece_name'] }}</div>
                                <div class="input text-center" style="width: 30%; padding: 0.15rem 0.2rem; font-size: 8px; border: 1px solid var(--main-color); border-radius: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $pieza['concept_name'] }}</div>
                            </div>
                        @empty
                            <div style="width: 100%; padding: 1rem; text-align: center; color: #999; font-size: 10px; border: 1px dashed #ccc; border-radius: 4px;">No se registraron inspecciones</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="remarks" style="margin-top: 0.5rem; height: fit-content;">
                OBSERVACIONES: <br> {{ $observations ?? 'Sin observaciones' }}
            </div>
        </div>
    </div>
</section>
