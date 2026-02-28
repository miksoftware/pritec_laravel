@extends('layouts.app')

@section('title', 'Secciones - ' . $vehicleType->name . ' - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-cogs me-2" style="color: var(--accent);"></i>
                Configurar Secciones
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('vehicle-types.index') }}">Tipos de Vehículos</a></li>
                    <li class="breadcrumb-item active">{{ $vehicleType->name }} — Secciones</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary fs-6 d-flex align-items-center">
                @if($vehicleType->type === 'carro')
                    <i class="fas fa-car me-1"></i> Carro
                @else
                    <i class="fas fa-motorcycle me-1"></i> Moto
                @endif
                — {{ $vehicleType->name }}
            </span>
            <a href="{{ route('vehicle-types.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <div class="row g-4">
        @foreach($sections as $section)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        @if($section->section_name === 'carroceria')
                            <i class="fas fa-car me-2"></i>
                        @elseif($section->section_name === 'estructura')
                            <i class="fas fa-tools me-2"></i>
                        @else
                            <i class="fas fa-cogs me-2"></i>
                        @endif
                        {{ $section->formatted_name }}
                    </h6>
                    <span class="badge bg-primary">{{ $section->pieces_count }} piezas</span>
                </div>
                <div class="card-body text-center p-4">
                    <!-- Section Image -->
                    <div class="section-image-container mb-3" id="imageContainer_{{ $section->id }}">
                        @if($section->image_path)
                            <img src="{{ $section->image_url }}" alt="{{ $section->formatted_name }}"
                                 class="img-fluid rounded" id="sectionImage_{{ $section->id }}"
                                 style="width: 200px; height: 200px; object-fit: cover; border: 2px solid var(--border-color);">
                        @else
                            <div class="text-center py-4" id="noImage_{{ $section->id }}" style="background: var(--light); border-radius: var(--radius); border: 2px dashed var(--border-color);">
                                <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0" style="font-size: 0.82rem;">Sin imagen de diagrama</p>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Button -->
                    <div class="mb-3">
                        <label for="imageUpload_{{ $section->id }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-upload me-1"></i>
                            {{ $section->image_path ? 'Cambiar Imagen' : 'Subir Imagen' }}
                        </label>
                        <input type="file" id="imageUpload_{{ $section->id }}" class="d-none"
                               accept="image/jpeg,image/jpg,image/png"
                               onchange="uploadSectionImage({{ $section->id }}, this)">
                    </div>

                    <!-- Actions -->
                    <a href="{{ route('vehicle-sections.pieces', $section) }}" class="btn btn-primary w-100">
                        <i class="fas fa-puzzle-piece me-2"></i>
                        Gestionar Piezas ({{ $section->pieces_count }})
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($sections->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No hay secciones configuradas</h5>
            <p class="text-muted">Este tipo de vehículo no tiene secciones. Esto es inusual.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function uploadSectionImage(sectionId, input) {
        if (!input.files || !input.files[0]) return;

        const formData = new FormData();
        formData.append('section_id', sectionId);
        formData.append('image', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        Swal.fire({
            title: 'Subiendo imagen...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            background: '#1a2332', color: '#fff'
        });

        fetch('{{ route("vehicle-sections.upload-image") }}', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success', title: '¡Éxito!', text: data.message,
                    timer: 2000, showConfirmButton: false,
                    background: '#1a2332', color: '#fff'
                }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error al subir la imagen.', background: '#1a2332', color: '#fff' }));
    }
</script>
@endpush
