{{-- Print Format - Peritaje Completo - Pritec --}}
@php
    // Helper functions
    function obtenerEstadoPorPorcentaje($p) {
        $p = intval($p);
        if ($p <= 24) return 'Peligroso';
        if ($p <= 49) return 'Precaución';
        if ($p <= 74) return 'Seguro';
        return 'Nuevas';
    }
    function obtenerClasePorPorcentaje($p) {
        $p = intval($p);
        if ($p <= 24) return 'estado-peligroso';
        if ($p <= 49) return 'estado-precaucion';
        if ($p <= 74) return 'estado-seguro';
        return 'estado-nuevas';
    }
    function obtenerEstadoBateria($p) {
        $p = intval($p);
        if ($p <= 24) return 'Crítico';
        if ($p <= 49) return 'Bajo';
        if ($p <= 74) return 'Bueno';
        return 'Excelente';
    }
    $esCarro = !$expertise->is_moto;
    $motorData = $expertise->motor_sistemas_data ?? [];
    $fugasData = $expertise->fugas_niveles_data ?? [];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peritaje - {{ $expertise->placa }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/pdf-expertise.css') }}">
</head>
<body>

<!-- Botones de acción (se ocultan al imprimir) -->
<div class="action-buttons no-print">
    <button onclick="window.print()" class="btn-print">🖨️ Imprimir / Guardar PDF</button>
    <a href="{{ route('expertise.show', $expertise) }}" class="btn-close-pdf" style="text-decoration:none;text-align:center;">← Volver</a>
</div>

<main class="w-100">

    <!-- ================== PÁGINA 1 ================== -->
    <div class="page">
        <div class="page-content">
            <div class="d-flex flex-column gap-3">

                <!-- Encabezado -->
                <section class="d-flex" style="gap: 1rem; align-items: flex-end;">
                    <div style="width: 160px; min-width: 260px; display: flex; align-items: flex-end; justify-content: center;">
                        <img src="{{ asset('assets/img/pritec.jpeg') }}" style="width: 83%; height: 90%; object-fit: contain; border-radius: 4px;" alt="Pritec Logo" onerror="this.style.display='none'">
                    </div>
                    <div class="d-flex flex-column" style="flex: 1;">
                        <h1 class="text-left">{{ $company['name'] }}</h1>
                        <h3 class="text-left mb-3">{{ $company['subtitle'] }}</h3>
                        <div class="d-flex gap-4">
                            <div class="me-3">
                                <p>Dirección: {{ $company['address'] }}</p>
                                <p>Teléfono: {{ $company['phones'] }}</p>
                                <p>Web: {{ $company['web'] }}</p>
                                <p>{{ $company['description'] }}</p>
                                <div class="d-flex gap-2" style="margin-top: 4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#E4405F"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#000000"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                </div>
                            </div>
                            <div>
                                <p>Fecha: {{ $expertise->service_date?->format('Y-m-d') }}</p>
                                <p>No. Servicio: {{ $expertise->service_number }}</p>
                                <p>Servicio para: {{ $expertise->service_for }}</p>
                                <p>Convenio: {{ $expertise->agreement ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Datos del Vehículo y Solicitante -->
                <section class="d-flex gap-2 rounded p-2 simple-border">
                    <div class="d-flex" style="width: 33%;">
                        <div class="yellow-background sub-title-vertical">DATOS DEL VEHÍCULO</div>
                        <div class="d-flex flex-column gap-2 w-100">
                            @foreach(['Clase' => $expertise->clase_vehiculo, 'Marca' => $expertise->marca, 'Línea' => $expertise->linea, 'Cilindraje' => $expertise->cilindrada, 'Kilometraje' => $expertise->kilometraje, 'Servicio' => $expertise->tipo_combustible, 'Modelo' => $expertise->modelo, 'Color' => $expertise->color, 'No. de chasis' => $expertise->numero_chasis] as $lbl => $val)
                            <div class="d-flex gap-2">
                                <div class="yellow-background label">{{ $lbl }}</div>
                                <div class="input">{{ $val ?? 'N/A' }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex" style="width: 33%;">
                        <div class="d-flex flex-column gap-2 w-100">
                            @foreach(['No. de motor' => $expertise->numero_motor, 'No. de serie' => $expertise->numero_serie, 'Tipo de carrocería' => $expertise->tipo_carroceria, 'Organismo de<br>tránsito' => $expertise->organismo_transito, 'Código fasecolda' => $expertise->codigo_fasecolda, 'Valor fasecolda' => $expertise->valor_fasecolda, 'Valor sugerido' => $expertise->valor_sugerido, 'Valor accesorios' => $expertise->valor_accesorios] as $lbl => $val)
                            <div class="d-flex gap-2">
                                <div class="yellow-background label">{!! $lbl !!}</div>
                                <div class="input">{{ $val ?? 'N/A' }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="me-2" style="width: 33%;">
                        <div class="plate">{{ $expertise->placa }}</div>
                        <div class="yellow-background sub-title">DATOS DEL SOLICITANTE</div>
                        <div class="d-flex flex-column gap-2">
                            @foreach(['Nombres y<br>apellidos' => ($expertise->client?->first_name ?? '') . ' ' . ($expertise->client?->last_name ?? ''), 'Identificación' => $expertise->client?->identification, 'Teléfono' => $expertise->client?->phone, 'Dirección' => $expertise->client?->address, 'Correo' => $expertise->client?->email] as $lbl => $val)
                            <div class="d-flex gap-2">
                                <div class="yellow-background label">{!! $lbl !!}</div>
                                <div class="input" @if($lbl === 'Correo') style="font-size: 10px;" @endif>{{ $val ?? 'N/A' }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                @if($esCarro)
                    <!-- CARROCERÍA - Solo para carros -->
                    @include('expertise.partials.pdf-inspection-section', ['sectionName' => 'CARROCERÍA', 'titleType' => 'INSPECCIÓN VISUAL EXTERNA', 'sectionData' => $carroceria, 'vehicleName' => $expertise->vehicleType?->name, 'observations' => $expertise->observaciones_carroceria])
                @endif

                <!-- ESTRUCTURA - Para carros y motos -->
                @include('expertise.partials.pdf-inspection-section', ['sectionName' => 'ESTRUCTURA', 'titleType' => 'INSPECCIÓN VISUAL INTERNA', 'sectionData' => $estructura, 'vehicleName' => $expertise->vehicleType?->name, 'observations' => $expertise->observaciones_estructura])

                @if(!$esCarro)
                    <!-- CHASIS - En página 1 para motos -->
                    @include('expertise.partials.pdf-inspection-section', ['sectionName' => 'CHASIS', 'titleType' => 'INSPECCIÓN VISUAL INTERNA', 'sectionData' => $chasis, 'vehicleName' => $expertise->vehicleType?->name, 'observations' => $expertise->observaciones_chasis])
                @endif
            </div>
        </div>
        <div class="footer"><span class="footer-text">LA MEJOR FORMA DE COMPRAR UN CARRO USADO</span><span class="footer-page">Página 1 de 6</span></div>
    </div>

    <!-- ================== PÁGINA 2 ================== -->
    <div class="page">
        <div class="page-content">
            <div class="d-flex flex-column gap-3 w-100">
            @if($esCarro)
                <!-- CHASIS - En página 2 para carros -->
                @include('expertise.partials.pdf-inspection-section', ['sectionName' => 'CHASIS', 'titleType' => 'INSPECCIÓN VISUAL INTERNA', 'sectionData' => $chasis, 'vehicleName' => $expertise->vehicleType?->name, 'observations' => $expertise->observaciones_chasis])
            @endif

            <!-- LLANTAS -->
            @include('expertise.partials.pdf-percentage-section', [
                'title' => 'LLANTAS Y AMORTIGUADORES', 'subtitle' => 'CONVENCIONES LLANTA', 'itemTitle' => 'LLANTAS',
                'legendLabels' => ['0-24% Peligroso', '25-49% Precaución', '50-74% Seguro', '75-100% Nuevas'],
                'imageName' => $expertise->is_moto ? 'LLANTAS MOTO.png' : 'llantas.png',
                'items' => $expertise->is_moto ? [
                    ['Llanta anterior derecha', $expertise->llanta_anterior_derecha ?? 0],
                    ['Llanta posterior derecha', $expertise->llanta_posterior_derecha ?? 0],
                ] : [
                    ['Llanta anterior izquierda', $expertise->llanta_anterior_izquierda ?? 0],
                    ['Llanta anterior derecha', $expertise->llanta_anterior_derecha ?? 0],
                    ['Llanta posterior izquierda', $expertise->llanta_posterior_izquierda ?? 0],
                    ['Llanta posterior derecha', $expertise->llanta_posterior_derecha ?? 0],
                ],
                'observations' => $expertise->observaciones_llantas, 'fnEstado' => 'obtenerEstadoPorPorcentaje'
            ])

            <!-- AMORTIGUADORES -->
            @include('expertise.partials.pdf-percentage-section', [
                'title' => 'AMORTIGUADORES', 'subtitle' => 'CONVENCIONES AMORTIGUADORES', 'itemTitle' => 'AMORTIGUADORES',
                'legendLabels' => ['0-24% Pésimo', '25-49% Supervisión', '50-74% Bueno', '75-100% Nuevos'],
                'imageName' => $expertise->is_moto ? 'AMORTIGUADORES MOTO.png' : 'amortiguadores.png',
                'items' => $expertise->is_moto ? [
                    ['Amortiguador anterior derecho', $expertise->amortiguador_anterior_derecho ?? 0],
                    ['Amortiguador posterior derecho', $expertise->amortiguador_posterior_derecho ?? 0],
                ] : [
                    ['Amortiguador anterior izquierdo', $expertise->amortiguador_anterior_izquierdo ?? 0],
                    ['Amortiguador anterior derecho', $expertise->amortiguador_anterior_derecho ?? 0],
                    ['Amortiguador posterior izquierdo', $expertise->amortiguador_posterior_izquierdo ?? 0],
                    ['Amortiguador posterior derecho', $expertise->amortiguador_posterior_derecho ?? 0],
                ],
                'observations' => $expertise->observaciones_amortiguadores, 'fnEstado' => 'obtenerEstadoPorPorcentaje'
            ])

            @if(!$esCarro)
                <!-- BATERÍA (motos en página 2) -->
                @include('expertise.partials.pdf-percentage-section', [
                    'title' => 'ACTUACIÓN DE LA BATERÍA', 'subtitle' => 'CONVENCIONES BATERÍA', 'itemTitle' => 'BATERÍA',
                    'legendLabels' => ['0-24% Crítico', '25-49% Bajo', '50-74% Bueno', '75-100% Excelente'],
                    'imageName' => 'BATERIA.png',
                    'items' => [
                        ['Prueba de batería', $expertise->prueba_bateria ?? 0],
                        ['Prueba de arranque', $expertise->prueba_arranque ?? 0],
                        ['Carga de batería', $expertise->carga_bateria ?? 0],
                    ],
                    'observations' => $expertise->observaciones_bateria, 'fnEstado' => 'obtenerEstadoBateria'
                ])
            @endif
            </div>
        </div>
        <div class="footer"><span class="footer-text">LA MEJOR FORMA DE COMPRAR UN CARRO USADO</span><span class="footer-page">Página 2 de 6</span></div>
    </div>

    <!-- ================== PÁGINA 3 ================== -->
    <div class="page">
        <div class="page-content">
            @if($esCarro)
                <!-- BATERÍA (carros en página 3) -->
                @include('expertise.partials.pdf-percentage-section', [
                    'title' => 'ACTUACIÓN DE LA BATERÍA', 'subtitle' => 'CONVENCIONES BATERÍA', 'itemTitle' => 'BATERÍA',
                    'legendLabels' => ['0-24% Crítico', '25-49% Bajo', '50-74% Bueno', '75-100% Excelente'],
                    'imageName' => 'BATERIA.png',
                    'items' => [
                        ['Prueba de batería', $expertise->prueba_bateria ?? 0],
                        ['Prueba de arranque', $expertise->prueba_arranque ?? 0],
                        ['Carga de batería', $expertise->carga_bateria ?? 0],
                    ],
                    'observations' => $expertise->observaciones_bateria, 'fnEstado' => 'obtenerEstadoBateria'
                ])
            @endif

            <!-- PRUEBA SCANNER -->
            <section class="p-2 rounded my-2 simple-border">
                <div class="yellow-background sub-title w-100 text-center seccion-titulo-con-icono" style="margin-bottom: 0.5rem;">
                    PRUEBA DE OBSERVACIÓN Y DIAGNÓSTICO SCANNER
                </div>
                <div style="padding: 0.5rem 1rem; font-size: 0.75rem; line-height: 1.4; text-align: justify; color: #000 !important;">
                    El scanner automotriz es una herramienta que se utiliza para diagnosticar las fallas registradas en la computadora del vehículo.
                </div>
                <div class="remarks" style="margin-top: 0.5rem; height: fit-content;">
                    OBSERVACIONES: <br> {{ $expertise->prueba_escaner ?? 'Sin observaciones' }}
                </div>
            </section>

            <!-- TREN MOTRIZ Y DIRECCIÓN -->
            @include('expertise.partials.pdf-table-section', [
                'title' => 'TREN MOTRIZ Y DIRECCIÓN', 'hasEstado' => true,
                'fields' => $campos_tren_motriz, 'data' => $motorData,
                'observations' => $motorData['observaciones_motor'] ?? $expertise->observaciones_motor ?? 'Sin observaciones'
            ])

            <!-- NIVEL DE LÍQUIDOS -->
            @include('expertise.partials.pdf-table-section', [
                'title' => 'NIVEL DE LÍQUIDOS', 'hasEstado' => true,
                'fields' => $campos_liquidos, 'data' => $fugasData,
                'observations' => $fugasData['observaciones_fugas'] ?? 'Sin observaciones'
            ])

            @if(!$esCarro)
                <!-- MOTOR (motos en página 3) -->
                @include('expertise.partials.pdf-table-section', [
                    'title' => 'MOTOR', 'hasEstado' => true,
                    'fields' => $campos_motor, 'data' => $motorData,
                    'observations' => $motorData['observaciones_motor'] ?? $expertise->observaciones_motor ?? 'Sin observaciones'
                ])
            @endif
        </div>
        <div class="footer"><span class="footer-text">LA MEJOR FORMA DE COMPRAR UN CARRO USADO</span><span class="footer-page">Página 3 de 6</span></div>
    </div>

    <!-- ================== PÁGINA 4 ================== -->
    <div class="page">
        <div class="page-content">
            <div class="d-flex flex-column gap-3 w-100">
            @if($esCarro)
                <!-- MOTOR (carros en página 4) -->
                @include('expertise.partials.pdf-table-section', [
                    'title' => 'MOTOR', 'hasEstado' => true,
                    'fields' => $campos_motor, 'data' => $motorData,
                    'observations' => $motorData['observaciones_motor'] ?? $expertise->observaciones_motor ?? 'Sin observaciones'
                ])
            @endif

            <!-- INTERIOR DEL AUTOMOTOR -->
            @include('expertise.partials.pdf-table-section', [
                'title' => 'INTERIOR DEL AUTOMOTOR', 'hasEstado' => true,
                'fields' => $campos_interior, 'data' => $motorData,
                'observations' => $motorData['observaciones_interior'] ?? $expertise->observaciones_interior ?? 'Sin observaciones'
            ])

            <!-- FUGAS -->
            @include('expertise.partials.pdf-table-section', [
                'title' => 'FUGAS', 'hasEstado' => true,
                'fields' => $campos_fugas, 'data' => $fugasData,
                'observations' => $fugasData['observaciones_fugas'] ?? 'Sin observaciones'
            ])

            <!-- COMPONENTES -->
            @include('expertise.partials.pdf-table-section', [
                'title' => 'COMPONENTES', 'hasEstado' => true,
                'fields' => $campos_estado_componentes, 'data' => $fugasData,
                'observations' => $fugasData['observaciones_estado_componentes'] ?? 'Sin observaciones'
            ])
            </div>
        </div>
        <div class="footer"><span class="footer-text">LA MEJOR FORMA DE COMPRAR UN CARRO USADO</span><span class="footer-page">Página 4 de 6</span></div>
    </div>

    <!-- ================== PÁGINA 5 - FIJACIÓN FOTOGRÁFICA ================== -->
    <div class="page">
        <div class="page-content">
            <section class="p-2 rounded my-2 simple-border">
                <div class="d-flex gap-2">
                    <div class="yellow-background sub-title-vertical">FIJACIÓN FOTOGRÁFICA</div>
                    <div class="d-flex flex-column w-100">
                        <p class="descripcion-fotografica">Observación y clasificación de las características del automotor de acuerdo al punto 1</p>
                        <div class="grid-fotografias">
                            @for($i = 0; $i < 9; $i++)
                                @if($i < $photos->count() && $photos[$i]->ruta)
                                    <div class="contenedor-imagen">
                                        <img src="{{ asset($photos[$i]->ruta) }}" class="imagen-fijacion" alt="Foto {{ $i + 1 }}">
                                    </div>
                                @else
                                    <div class="contenedor-imagen contenedor-vacio">
                                        <div class="placeholder-imagen">Sin imagen</div>
                                    </div>
                                @endif
                            @endfor
                        </div>

                        <div class="prueba-ruta">
                            <strong>PRUEBA DE RUTA:</strong><br>
                            {{ $fugasData['prueba_ruta'] ?? $expertise->prueba_ruta ?? '' }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- FIRMAS Y AVISO LEGAL -->
            <section class="seccion-firmas" style="margin-top: 1.5rem;">
                <div class="contenedor-firmas" style="margin-top: 2rem;">
                    <div class="firma-izquierda">
                        <div class="d-flex align-items-center gap-2" style="margin-top: 2.5rem;">
                            <span style="font-size: 0.85rem; font-weight: bold; white-space: nowrap;">Firma Inspector estructura vehicular:</span>
                            <div style="flex: 1; border-bottom: 2px solid #000;"></div>
                        </div>
                        <p class="campo-cc">CC:</p>
                    </div>
                    <div class="firma-derecha">
                        <div class="d-flex align-items-center gap-2" style="margin-top: 2.5rem;">
                            <span style="font-size: 0.85rem; font-weight: bold; white-space: nowrap;">Firma Cliente:</span>
                            <div style="flex: 1; border-bottom: 2px solid #000;"></div>
                        </div>
                        <p class="campo-cc">CC:</p>
                    </div>
                </div>
                <div class="contenedor-firma-mecanico" style="margin-top: 2.5rem;">
                    <div class="firma-mecanico">
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size: 0.85rem; font-weight: bold; white-space: nowrap;">Firma Perito:</span>
                            <div style="flex: 1; border-bottom: 2px solid #000;"></div>
                        </div>
                        <p class="campo-cc">CC:</p>
                    </div>
                </div>
                <div class="aviso-legal">
                    <p><strong>AVISO LEGAL:</strong> Pritec Informa que la revisión realizada corresponde al estado del vehículo en la fecha y hora de la misma y con el recorrido del kilometraje que revela el odómetro en el momento, se advierte que, debido a la vulnerabilidad a que se ven expuestos este tipo de bienes, en cuanto a la afectación, modificación, avería, deterioro y desgaste de cualquiera de sus componentes, el informe que se pone de presente no garantiza de ningún modo que el estado del vehículo sea el mismo en fechas posteriores a la fecha de la revisión.</p>
                </div>
            </section>
        </div>
        <div class="footer"><span class="footer-text">LA MEJOR FORMA DE COMPRAR UN CARRO USADO</span><span class="footer-page">Página 5 de 6</span></div>
    </div>

    <!-- ================== PÁGINA 6 - CONSIDERACIONES Y CLÁUSULAS ================== -->
    <div class="page">
        <div class="page-content">
            <div style="text-align: center; margin-bottom: 0.8rem;">
                <h2 style="font-size: 1rem; font-weight: bold; margin-bottom: 0.3rem;">CONSIDERACIONES Y ACLARACIONES DEL SERVICIO.</h2>
                <h3 style="font-size: 0.80rem; font-weight: bold;">CLAUSULAS DE EXCLUSIÓN Y LIMITACION DE RESPONSABILIDAD PRITEC.</h3>
            </div>
            <div style="column-count: 2; column-gap: 1.2rem; line-height: 1.5; text-align: justify;">
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>ALCANCE DEL PERITAJE.</strong> El peritaje se realiza mediante inspección visual y técnica no invasiva, por lo cual no se garantiza la detección de fallas ocultas, internas, electrónicas, estructurales no visibles o que se manifiesten con posterioridad a la fecha de la inspección.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>DAÑOS FUTUROS O SOBREVINIENTES.</strong> La empresa no asume responsabilidad por daños, averías, desperfectos mecánicos, eléctricos o estructurales que se presenten con posterioridad a la fecha del peritaje, aun cuando estén relacionados con el vehículo inspeccionado.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>RESPONSABILIDAD POR DECISIONES DEL CLIENTE.</strong> Cualquier decisión comercial, contractual o técnica tomada con base en el presente informe será de exclusiva responsabilidad del cliente, exonerando a la empresa y a sus peritos de cualquier reclamación derivada de dichas decisiones.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>INFORMACIÓN SUMINISTRADA POR TERCEROS.</strong> La empresa no será responsable por errores u omisiones derivados de información falsa, incompleta o inexacta suministrada por el propietario, poseedor o terceros relacionados con el vehículo.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>LIMITACIÓN CUANTITATIVA DE RESPONSABILIDAD.</strong> En ningún caso la responsabilidad civil de la empresa excederá el valor efectivamente pagado por el servicio de peritaje contratado.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>EXCLUSIÓN DE LUCRO CESANTE Y DAÑOS INDIRECTOS.</strong> La empresa no será responsable por lucro cesante, daño emergente, pérdida de oportunidad, perjuicios indirectos o consecuencias económicas derivadas del uso del informe de peritaje.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>USO Y DESTINACIÓN DEL INFORME.</strong> El informe emitido por la empresa PRITEC está destinado exclusivamente para el uso del CLIENTE y para los fines específicos estipulados en el contrato o en la orden de servicio, cualquier reproducción, divulgación o utilización por parte de terceros requerirá la autorización previa y por escrito de la empresa. La empresa no asume responsabilidad alguna por el uso indebido, alteración o descontextualización del informe.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>FUERZA MAYOR Y CASO FORTUITO.</strong> La Empresa no será responsable por el incumplimiento total o parcial de sus obligaciones cuando este derive de hechos de fuerza mayor o caso fortuito, conforme a lo dispuesto en el artículo 64 del Código Civil Colombiano, incluyendo, pero sin limitarse a, desastres naturales, conflictos laborales, fallas en sistemas informáticos o disposiciones de autoridad competente.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>VIGENCIA DE LAS OPINIONES TÉCNICAS.</strong> Las conclusiones del informe emitido por la empresa PRITEC son válidas únicamente respecto de las condiciones existentes al momento de la inspección o análisis, PRITEC no se hace responsable por variaciones, alteraciones o deterioros posteriores que puedan afectar la validez de las conclusiones.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;">Respecto a las consultas obtenidas para la información consignada en el presente informe, en lo referente a datos administrativos, antecedentes, características registrales y estado general del vehículo, corresponde única y exclusivamente a la información reportada y disponible en el Registro Único Nacional de Tránsito (RUNT) a la fecha de la consulta.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;">PRITEC no es responsable por la veracidad, integridad, actualización, omisiones, errores o inconsistencias de la información contenida en el RUNT, ni por modificaciones, actualizaciones o correcciones que se realicen con posterioridad a la fecha del peritaje.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;">Así mismo, PRITEC no garantiza que la información registrada en el RUNT refleje de manera completa el historial real del vehículo, incluyendo pero sin limitarse a siniestros, afectaciones estructurales, reclamaciones ante aseguradoras, modificaciones, limitaciones legales o eventos no reportados o no cargados en dicha base de datos.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;">Cualquier interpretación, uso o decisión tomada con base en la información obtenida del RUNT será de exclusiva responsabilidad del cliente, quien acepta que dicha información tiene carácter referencial e informativo.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>KILOMETRAJE:</strong> "Se deja expresa constancia de que el kilometraje reportado por el vehículo no ha sido considerado como criterio vinculante en el presente informe pericial. Dicha decisión obedece al reconocimiento legal y técnico de que el odómetro —ya sea mecánico o digital— es susceptible a manipulaciones mediante intervenciones físicas o electrónicas.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>SUSPENSION:</strong> la verificación del sistema de suspensión incluida en este peritaje ha sido realizada utilizando equipo especializado, como elevadores hidráulicos de dos columnas, elevadores móviles tipo tijera y otras herramientas técnicas apropiadas, las cuales permiten una inspección visual precisa de componentes accesibles del sistema de suspensión (por ejemplo, fugas, bujes, tijeras, espirales, tensores y barras estabilizadoras). No obstante, se aclara que no se incluye en esta evaluación el estado funcional de elementos no accesibles mediante inspección visual o estática, tales como espirales internos, ballestas, bombonas, sistemas hidráulicos o neumáticos, sensores eléctricos, controladores, terminales, pines, torres de amortiguadores, en consecuencia, no asume responsabilidad alguna por fallas futuras o rendimientos inadecuados del sistema de suspensión que no hayan sido detectados durante este procedimiento técnico limitado al alcance descrito.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>LLANTAS:</strong> la inspección del sistema de llantas realizada en este peritaje fue ejecutada con equipos especializados, tales como calibradores de presión, medidores de profundidad (vernier/pie de rey) y herramientas de diagnóstico de precisión, permitiendo verificar aspectos observables como presión interna, profundidad del labrado, desgaste superficial y presencia de daños visibles en los rines, neumático de repuesto y otros componentes externos. No obstante, se aclara que esta evaluación no comprende la verificación de deformaciones internas o daños estructurales no visibles al ojo, como grietas internas, deformaciones internas del rin, desbalanceo interno o microfisuras en materiales, cuya detección requiere técnicas avanzadas (por ejemplo, ultrasonido, rayos X o ensayos no destructivos). En consecuencia, Pritec no asume responsabilidad alguna por fallas futuras o efectos adversos derivados de dichos aspectos no cubiertos en este procedimiento técnico limitado al alcance descrito."</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>SISTEMA ELÉCTRICO:</strong> informa que la inspección ha sido realizada utilizando un escáner especializado con licencia autorizada, el cual permite la lectura de códigos de error, diagnóstico de sensores y evaluación de parámetros electrónicos accesibles. Sin embargo, se deja expresa constancia de que este servicio no incluye la evaluación de: Componentes electrónicos internos no accesibles, como módulos de control, circuitos integrados o microprocesadores, daños ocultos o fallas intermitentes que no se manifiestan durante la inspección o que requieren condiciones específicas de operación para su detección, Interferencias o conflictos entre sistemas electrónicos que puedan afectar el rendimiento del vehículo sin generar códigos de error evidentes, Este servicio no comprende: vida útil de elementos electrónicos, estado de alternador, no se valida funcionamiento del alarmas originales de fabrica ni genéricas instaladas en el vehiculo, no se valida calefacción o aire acondicionado de los asientos en caso que aplique, no se valida función de retracción de los espejos retrovisores, no se valida funcionamiento de lunetas térmicas.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>CHASIS:</strong> informa que la inspección del chasis del vehículo se realiza mediante una revisión visual detallada de los componentes accesibles sin necesidad de desmontar piezas. Este procedimiento incluye: Verificación de las puntas de chasis delanteras y traseras. Inspección de largueros y traviesas del chasis. Revisión de la estructura del vehículo, incluyendo traviesas, parales y travesaños. Evaluación de la originalidad de soldaduras y sellantes de piezas estructurales. Determinación de la existencia de reparaciones anteriores o daños visibles. Comprobación de la originalidad del sistema de identificación del vehículo. Variaciones estructurales que se manifiesten con posterioridad a la fecha del peritaje. Interpretaciones técnicas diferentes realizadas por terceros, talleres, aseguradoras o autoridades. Cualquier reclamación derivada de decisiones comerciales, contractuales o de uso del vehículo basadas en esta inspección.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;"><strong>BATERÍA:</strong> efectuada por la empresa PRITEC, se utiliza mediante tester eléctrico (multímetro) tiene carácter meramente diagnóstico y referencial, con base en las condiciones observadas y los valores de voltaje registrados al momento de la medición. El resultado obtenido refleja únicamente el estado de carga y voltaje instantáneo de la batería, sin que ello constituya garantía de funcionamiento futuro, durabilidad, ni certificación de conformidad técnica. La empresa no asume responsabilidad alguna por fallas posteriores, deterioros internos, defectos ocultos o pérdidas de rendimiento que puedan presentarse en la batería o en el sistema eléctrico del vehículo con posterioridad a la revisión. La Empresa PRITEC se exonera expresamente de cualquier reclamo, daño directo o indirecto, lucro cesante o perjuicio derivado de la interpretación o uso del resultado de esta medición.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;">La evaluación del sistema de motor y transmisión realizada por PRITEC se fundamenta en una inspección técnica de carácter visual, funcional básica y no invasiva, efectuada bajo las condiciones existentes al momento del peritaje y sin desmontaje de componentes, apertura de motor o caja, pruebas destructivas, mediciones internas, análisis de lubricantes ni verificación profunda de sistemas electrónicos asociados. En consecuencia, el concepto emitido respecto al sistema de motor y transmisión no constituye garantía de funcionamiento, vida útil, rendimiento ni ausencia de fallas, ni reemplaza diagnósticos especializados realizados mediante escáner avanzado, pruebas de compresión, análisis dinámicos, pruebas de ruta prolongadas o evaluaciones técnicas en taller especializado.</p>
                <p style="margin-bottom: 0.4rem; font-size: 0.8rem;">Cualquier decisión relacionada con la compra, venta, reparación, uso o disposición del vehículo, basada total o parcialmente en el presente informe, será asumida bajo el exclusivo criterio y responsabilidad del cliente, quien reconoce que el peritaje refleja únicamente el estado aparente y funcional básico del sistema al momento de la inspección.</p>
                <p style="margin-bottom: 0; font-size: 0.8rem;">En virtud de lo anterior, PRITEC no asume responsabilidad civil por averías mecánicas posteriores, fallas súbitas, pérdida de potencia, ruidos, vibraciones, daños progresivos, costos de reparación, lucro cesante o perjuicios económicos derivados del sistema de motor y transmisión, salvo aquellos eventos que, conforme a la legislación colombiana, no sean susceptibles de exclusión por tratarse de dolo, culpa grave o fraude debidamente probado.</p>
            </div>

            <!-- Cuadro de contacto con QR WhatsApp -->
            <div style="margin-top: 1rem; border: 2px solid #E5A100; border-radius: 8px; padding: 0.8rem 1rem; display: flex; align-items: center; gap: 1rem; background: #FFFBEA;">
                <div style="flex: 1;">
                    <p style="font-size: 0.85rem; font-weight: bold; margin-bottom: 0.4rem; color: #333;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#E5A100" style="vertical-align: middle; margin-right: 4px;">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        ¿Tienes dudas respecto a este documento?
                    </p>
                    <p style="font-size: 0.8rem; color: #555; margin-bottom: 0.3rem;">Escríbenos y con gusto te ayudaremos:</p>
                    <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                        <p style="font-size: 0.85rem; margin: 0; color: #333;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#25D366" style="vertical-align: middle; margin-right: 4px;">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            {{ $company['phone_1'] }}
                            @if($company['whatsapp_phone'] === $company['phone_1'])
                                <span style="font-size: 0.7rem; color: #25D366; font-weight: bold;">(WhatsApp)</span>
                            @endif
                        </p>
                        @if($company['phone_2'])
                        <p style="font-size: 0.85rem; margin: 0; color: #333;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#25D366" style="vertical-align: middle; margin-right: 4px;">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            {{ $company['phone_2'] }}
                            @if($company['whatsapp_phone'] === $company['phone_2'])
                                <span style="font-size: 0.7rem; color: #25D366; font-weight: bold;">(WhatsApp)</span>
                            @endif
                        </p>
                        @endif
                    </div>
                    <p style="font-size: 0.7rem; color: #888; margin-top: 0.4rem; margin-bottom: 0;">Escanea el código QR para escribirnos directamente por WhatsApp</p>
                </div>
                <div style="min-width: 100px; text-align: center;">
                    <div id="qrcode-contact" style="display: inline-block;"></div>
                </div>
            </div>
        </div>
        <div class="footer"><span class="footer-text">LA MEJOR FORMA DE COMPRAR UN CARRO USADO</span><span class="footer-page">Página 6 de 6</span></div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var whatsappPhone = '57{{ $company["whatsapp_phone"] }}';
        var whatsappUrl = 'https://wa.me/' + whatsappPhone + '?text=' + encodeURIComponent('Hola, tengo una consulta sobre mi peritaje.');

        new QRCode(document.getElementById('qrcode-contact'), {
            text: whatsappUrl,
            width: 100,
            height: 100,
            colorDark: '#000000',
            colorLight: '#FFFBEA',
            correctLevel: QRCode.CorrectLevel.M
        });
    });
</script>
</body>
</html>
