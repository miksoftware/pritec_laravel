@php
    $currentStep = $step ?? 1;
    $isMoto = $expertise->is_moto ?? false;
    $totalSteps = $isMoto ? 11 : 12;
    $percentage = round(($currentStep / $totalSteps) * 100);

    $steps = [
        1 => ['name' => 'Info', 'icon' => 'fas fa-info-circle'],
        2 => ['name' => 'Vehículo', 'icon' => 'fas fa-car'],
        3 => ['name' => 'Carrocería', 'icon' => 'fas fa-car-side'],
        4 => ['name' => 'Estructura', 'icon' => 'fas fa-building'],
        5 => ['name' => 'Chasis', 'icon' => 'fas fa-cogs'],
        6 => ['name' => 'Llantas', 'icon' => 'fas fa-circle'],
        7 => ['name' => 'Amortiguadores', 'icon' => 'fas fa-compress-arrows-alt'],
        8 => ['name' => 'Batería', 'icon' => 'fas fa-battery-full'],
        9 => ['name' => 'Motor', 'icon' => 'fas fa-cog'],
        10 => ['name' => 'Fugas', 'icon' => 'fas fa-tint'],
        11 => ['name' => 'Fotos', 'icon' => 'fas fa-camera'],
        12 => ['name' => 'Resumen', 'icon' => 'fas fa-check-circle'],
    ];

    $progressClass = $currentStep >= 11 ? 'bg-success' : ($currentStep >= 7 ? 'bg-info' : 'bg-primary');
@endphp

<div class="mb-4">
    <div class="card">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">
                    <i class="fas fa-tasks me-2"></i>Progreso del Peritaje
                    @if($isMoto)
                        <span class="badge bg-warning text-dark ms-2"><i class="fas fa-motorcycle"></i> Moto</span>
                    @endif
                </span>
                <span class="badge {{ $progressClass }}">Paso {{ $currentStep }} de {{ $totalSteps }}</span>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar {{ $progressClass }}" style="width: {{ $percentage }}%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2 small flex-wrap gap-1">
                @foreach($steps as $num => $info)
                    @if($num === 3 && $isMoto) @continue @endif
                    @php
                        if ($num < $currentStep) { $cls = 'text-success'; $ic = 'fas fa-check-circle'; }
                        elseif ($num === $currentStep) { $cls = 'text-primary'; $ic = 'fas fa-circle'; }
                        else { $cls = 'text-muted'; $ic = 'far fa-circle'; }
                    @endphp
                    <small class="{{ $cls }}" title="{{ $info['name'] }}">
                        <i class="{{ $ic }} me-1"></i>{{ $info['name'] }}
                    </small>
                @endforeach
            </div>
        </div>
    </div>
</div>
