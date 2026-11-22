@extends('layouts.app')

@section('title', ($viewMode ? 'Ver' : 'Resumen') . ' Peritaje - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-{{ $viewMode ? 'eye' : 'check-circle' }} me-2" style="color: var(--accent);"></i>
                {{ $viewMode ? 'Detalle del Peritaje' : 'Paso 12: Resumen Final' }}
            </h4>
            <small>Código: {{ $expertise->codigo }} | Estado: <span class="badge bg-{{ $expertise->status === 'completed' ? 'success' : 'warning' }}">{{ $expertise->status_label }}</span></small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('expertise.pdf', $expertise) }}" target="_blank" class="btn btn-warning"><i class="fas fa-print me-2"></i>Imprimir / PDF</a>
            <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
        </div>
    </div>
</div>
<div class="content-body">
    @if(!$viewMode)
        @include('components.expertise-progress', ['step' => 12, 'expertise' => $expertise])
    @endif

    <!-- Step 1: Service & Client -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Servicio y Cliente</h6></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Fecha Servicio:</strong><br>{{ $expertise->service_date->format('d/m/Y') }}</div>
                <div class="col-md-3"><strong>No. Servicio:</strong><br>{{ $expertise->service_number }}</div>
                <div class="col-md-3"><strong>Servicio Para:</strong><br>{{ $expertise->service_for }}</div>
                <div class="col-md-3"><strong>Convenio:</strong><br>{{ $expertise->agreement ?: 'N/A' }}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4"><strong>Cliente:</strong><br>{{ $expertise->client?->first_name }} {{ $expertise->client?->last_name }}</div>
                <div class="col-md-4"><strong>Identificación:</strong><br>{{ $expertise->client?->identification }}</div>
                <div class="col-md-4"><strong>Teléfono:</strong><br>{{ $expertise->client?->phone }}</div>
            </div>
        </div>
    </div>

    <!-- Step 2: Vehicle -->
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0"><i class="fas fa-car me-2"></i>Datos del Vehículo</h6></div>
        <div class="card-body">
            <div class="row g-2">
                @php
                    $vehicleFields = [
                        'Tipo' => $expertise->vehicleType?->name ?? 'N/A',
                        'Placa' => $expertise->placa,
                        'Marca' => $expertise->marca,
                        'Línea' => $expertise->linea,
                        'Modelo' => $expertise->modelo,
                        'Color' => $expertise->color,
                        'VIN' => $expertise->vin,
                        'Motor' => $expertise->numero_motor,
                        'Chasis' => $expertise->numero_chasis,
                        'Kilometraje' => $expertise->kilometraje,
                        'Cilindrada' => $expertise->cilindrada,
                        'Combustible' => $expertise->tipo_combustible,
                    ];
                @endphp
                @foreach($vehicleFields as $label => $value)
                    @if($value)
                    <div class="col-md-3 col-6"><strong>{{ $label }}:</strong><br><small>{{ $value }}</small></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Steps 3/4/5: Inspections -->
    @foreach(['carroceria' => ['Carrocería', $inspCarroceria], 'estructura' => ['Estructura', $inspEstructura], 'chasis' => ['Chasis', $inspChasis]] as $sec => $data)
        @if($sec === 'carroceria' && $expertise->is_moto) @continue @endif
        @if($data[1]->count() > 0)
        @php
            $isSecPct = in_array($sec, ['carroceria', 'estructura']);
            $secSum = 0;
            $secCrit = 0;
            foreach ($data[1] as $insp) {
                $cName = $insp->concept?->name ?? '';
                $secSum += \App\Models\Expertise::getConceptPercentage($sec, $cName);
                if ($sec === 'estructura' && \App\Models\Expertise::isConceptCriticalInEstructura($cName)) {
                    $secCrit++;
                }
            }
            $secAvg = $data[1]->count() > 0 ? round($secSum / $data[1]->count(), 1) : 100.0;
            $secStatusInfo = \App\Models\Expertise::getInspectionSectionStatus($sec, $secAvg, $secCrit);
        @endphp
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-search me-2"></i>Inspección {{ $data[0] }}</h6>
                @if($isSecPct)
                    <span class="badge bg-primary fs-6">% {{ $data[0] }}: {{ $secAvg }}% ({{ $secStatusInfo['label'] }})</span>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Pieza</th>
                                <th>Concepto</th>
                                @if($isSecPct)
                                <th>%</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data[1] as $i => $insp)
                            @php
                                $cName = $insp->concept?->name ?? '';
                                $pct = \App\Models\Expertise::getConceptPercentage($sec, $cName);
                                $isCrit = ($sec === 'estructura') && \App\Models\Expertise::isConceptCriticalInEstructura($cName);
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $insp->piece?->piece_number }}. {{ $insp->piece?->piece_name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $cName }}</span>
                                    @if($isCrit)
                                        <span class="badge bg-danger ms-1" title="Pieza Crítica">Crítica</span>
                                    @endif
                                </td>
                                @if($isSecPct)
                                <td><span class="badge bg-secondary">{{ $pct }}%</span></td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        @if($isSecPct)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="2">PROMEDIO {{ strtoupper($data[0]) }}</td>
                                <td>
                                    <span class="badge bg-{{ $secStatusInfo['label'] === 'Bueno' ? 'success' : ($secStatusInfo['label'] === 'Aceptable' ? 'warning' : 'danger') }}">
                                        {{ $secStatusInfo['label'] }}
                                    </span>
                                    @if($secCrit > 0)
                                        <small class="text-danger ms-1">({{ $secCrit }} crítica{{ $secCrit > 1 ? 's' : '' }})</small>
                                    @endif
                                </td>
                                <td>{{ $secAvg }}%</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                @if($expertise->{'observaciones_' . $sec})
                <div class="p-3 bg-light border-top"><small><strong>Observaciones:</strong> {{ $expertise->{'observaciones_' . $sec} }}</small></div>
                @endif
            </div>
        </div>
        @endif
    @endforeach

    <!-- Step 6: Tires -->
    @php
        $tiresVals = $expertise->is_moto
            ? array_filter([$expertise->llanta_anterior_derecha, $expertise->llanta_posterior_derecha], fn($v) => !is_null($v) && $v !== '')
            : array_filter([$expertise->llanta_anterior_izquierda, $expertise->llanta_anterior_derecha, $expertise->llanta_posterior_izquierda, $expertise->llanta_posterior_derecha], fn($v) => !is_null($v) && $v !== '');
        $promLlantas = count($tiresVals) > 0 ? round(array_sum($tiresVals) / count($tiresVals), 1) : 0;
    @endphp
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-circle me-2"></i>Llantas</h6>
            <span class="badge bg-primary">Promedio: {{ $promLlantas }}%</span>
        </div>
        <div class="card-body">
            <div class="row text-center">
                @if(!$expertise->is_moto)
                <div class="col-md-3"><strong>Ant. Izq.</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->llanta_anterior_izquierda }}%</span></div>
                @endif
                <div class="col-md-3"><strong>Ant. Der.</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->llanta_anterior_derecha }}%</span></div>
                @if(!$expertise->is_moto)
                <div class="col-md-3"><strong>Post. Izq.</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->llanta_posterior_izquierda }}%</span></div>
                @endif
                <div class="col-md-3"><strong>Post. Der.</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->llanta_posterior_derecha }}%</span></div>
            </div>
            @if($expertise->observaciones_llantas)
            <hr><small><strong>Observaciones:</strong> {{ $expertise->observaciones_llantas }}</small>
            @endif
        </div>
    </div>

    <!-- Step 7: Shocks -->
    @php
        $shocksVals = [];
        if (!$expertise->is_moto || ($expertise->cant_amortiguadores_delanteros ?? 1) == 2) {
            if (!is_null($expertise->amortiguador_anterior_izquierdo) && $expertise->amortiguador_anterior_izquierdo !== '') $shocksVals[] = $expertise->amortiguador_anterior_izquierdo;
        }
        if (!is_null($expertise->amortiguador_anterior_derecho) && $expertise->amortiguador_anterior_derecho !== '') $shocksVals[] = $expertise->amortiguador_anterior_derecho;
        if (!$expertise->is_moto || ($expertise->cant_amortiguadores_traseros ?? 1) == 2) {
            if (!is_null($expertise->amortiguador_posterior_izquierdo) && $expertise->amortiguador_posterior_izquierdo !== '') $shocksVals[] = $expertise->amortiguador_posterior_izquierdo;
        }
        if (!is_null($expertise->amortiguador_posterior_derecho) && $expertise->amortiguador_posterior_derecho !== '') $shocksVals[] = $expertise->amortiguador_posterior_derecho;
        $promShocks = count($shocksVals) > 0 ? round(array_sum($shocksVals) / count($shocksVals), 1) : 0;
    @endphp
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-compress-arrows-alt me-2"></i>Amortiguadores</h6>
            <span class="badge bg-primary">Promedio: {{ $promShocks }}%</span>
        </div>
        <div class="card-body">
            <div class="row text-center">
                @if(!$expertise->is_moto || ($expertise->cant_amortiguadores_delanteros ?? 1) == 2)
                <div class="col-md-3"><strong>Ant. Izq.</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->amortiguador_anterior_izquierdo }}%</span></div>
                @endif
                <div class="col-md-3"><strong>Ant. Der.</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->amortiguador_anterior_derecho }}%</span></div>
                @if(!$expertise->is_moto || ($expertise->cant_amortiguadores_traseros ?? 1) == 2)
                <div class="col-md-3"><strong>Post. Izq.</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->amortiguador_posterior_izquierdo }}%</span></div>
                @endif
                <div class="col-md-3"><strong>Post. Der.</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->amortiguador_posterior_derecho }}%</span></div>
            </div>
        </div>
    </div>

    <!-- Step 8: Battery -->
    @php
        $batteryVals = array_filter([$expertise->prueba_bateria, $expertise->prueba_arranque, $expertise->carga_bateria], fn($v) => !is_null($v) && $v !== '');
        $promBattery = count($batteryVals) > 0 ? round(array_sum($batteryVals) / count($batteryVals), 1) : 0;
    @endphp
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-battery-full me-2"></i>Batería</h6>
            <span class="badge bg-primary">Promedio: {{ $promBattery }}%</span>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4"><strong>Prueba Batería</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->prueba_bateria }}%</span></div>
                <div class="col-md-4"><strong>Prueba Arranque</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->prueba_arranque }}%</span></div>
                <div class="col-md-4"><strong>Carga Batería</strong><br><span class="fs-5 fw-bold text-primary">{{ $expertise->carga_bateria }}%</span></div>
            </div>
        </div>
    </div>

    <!-- Step 9: Motor & Systems -->
    @if($expertise->motor_sistemas_data)
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0"><i class="fas fa-cog me-2"></i>Motor y Sistemas</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Sistema</th><th>Estado</th><th>Observación</th></tr></thead>
                    <tbody>
                        @php $motorData = $expertise->motor_sistemas_data; @endphp
                        @foreach(['arranque','radiador','carter_motor','carter_caja','caja_velocidades','soporte_caja','soporte_motor','mangueras_radiador','correas','filtro_aire','externo_bateria','pastilla_freno','discos_freno','punta_eje','axiales','terminales','rotulas','tijeras','caja_direccion','rodamientos','cardan','crucetas','calefaccion','aire_acondicionado','cinturones','tapiceria_asientos','tapiceria_techo','millaret','alfombra','chapas'] as $item)
                        @php $estado = $motorData["estado_{$item}"] ?? ''; $resp = $motorData["respuesta_{$item}"] ?? ''; @endphp
                        @if($estado)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $item)) }}</td>
                            <td><span class="badge bg-{{ $estado === 'Bueno' ? 'success' : ($estado === 'Regular' ? 'warning' : ($estado === 'Malo' ? 'danger' : 'secondary')) }}">{{ $estado }}</span></td>
                            <td><small>{{ $resp }}</small></td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Step 10: Leaks & Levels -->
    @if($expertise->fugas_niveles_data)
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0"><i class="fas fa-tint me-2"></i>Fugas y Niveles</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Componente</th><th>Estado</th></tr></thead>
                    <tbody>
                        @php $fugasData = $expertise->fugas_niveles_data; @endphp
                        @foreach($fugasData as $key => $value)
                            @if($value && $key !== 'prueba_ruta' && $key !== 'observaciones_fugas')
                            <tr>
                                <td>{{ ucfirst(str_replace(['respuesta_fuga_', 'respuesta_estado_', 'respuesta_nivel_', 'respuesta_viscosidad_', '_'], ['', '', '', '', ' '], $key)) }}</td>
                                <td><span class="badge bg-{{ str_contains($value, 'Sin Fuga') || $value === 'Lleno' || $value === 'Bueno' || $value === 'Adecuado' ? 'success' : (str_contains($value, 'Leve') || $value === 'Regular' ? 'warning' : ($value === 'No Aplica' ? 'secondary' : 'danger')) }}">{{ $value }}</span></td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($fugasData['prueba_ruta'] ?? false)
            <div class="p-3 bg-light border-top"><small><strong>Prueba de Ruta:</strong> {{ $fugasData['prueba_ruta'] }}</small></div>
            @endif
        </div>
    </div>
    @endif

    <!-- Step 11: Photos -->
    @if($expertise->photos->count() > 0)
    <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0"><i class="fas fa-camera me-2"></i>Fotografías ({{ $expertise->photos->count() }})</h6></div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($expertise->photos as $photo)
                <div class="col-md-3 col-6">
                    <a href="{{ asset($photo->ruta) }}" target="_blank">
                        <img src="{{ asset($photo->ruta) }}" class="img-fluid rounded shadow-sm" style="height:150px;width:100%;object-fit:cover;">
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="d-flex justify-content-between mt-4">
        @if($viewMode)
            <a href="{{ route('expertise.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Listado</a>
            <a href="{{ route('expertise.pdf', $expertise) }}" target="_blank" class="btn btn-warning btn-lg"><i class="fas fa-print me-2"></i>Imprimir / Guardar PDF</a>
        @else
            <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => 11]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Paso 11</a>
            <button type="button" class="btn btn-success btn-lg" id="completeBtn" onclick="completeExpertise()">
                <span class="loading spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                <i class="fas fa-check-double me-2" aria-hidden="true"></i>Finalizar Peritaje
            </button>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function completeExpertise() {
    Swal.fire({
        title: '¿Finalizar Peritaje?', text: 'Una vez completado, el peritaje se marcará como finalizado.', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#27ae60', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, finalizar', cancelButtonText: 'Revisar', background: '#1a2332', color: '#fff'
    }).then(r => {
        if (r.isConfirmed) {
            const btn = document.getElementById('completeBtn');
            if (btn) {
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');
                btn.querySelector('.loading')?.classList.remove('d-none');
            }
            fetch('{{ route("expertise.complete", $expertise) }}', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Completado!', text: data.message, timer: 2500, background: '#1a2332', color: '#fff' })
                    .then(() => window.location.href = data.redirect);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
                    if (btn) {
                        btn.disabled = false;
                        btn.removeAttribute('aria-busy');
                        btn.querySelector('.loading')?.classList.add('d-none');
                    }
                }
            }).catch(() => {
                Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo procesar la solicitud.', background: '#1a2332', color: '#fff' });
                if (btn) {
                    btn.disabled = false;
                    btn.removeAttribute('aria-busy');
                    btn.querySelector('.loading')?.classList.add('d-none');
                }
            });
        }
    });
}
</script>
@endpush
