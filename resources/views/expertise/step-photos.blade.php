@extends('layouts.app')

@section('title', 'Peritaje - Paso 11: Fotos - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4 class="mb-0"><i class="fas fa-camera me-2" style="color: var(--accent);"></i>Paso 11: Fijación Fotográfica</h4><small>Código: {{ $expertise->codigo }} | Placa: {{ $expertise->placa }}</small></div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>
<div class="content-body">
    @include('components.expertise-progress', ['step' => 11, 'expertise' => $expertise])

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-camera me-2"></i>Fotografías del Vehículo</h5></div>
        <div class="card-body">
            <form id="stepForm" enctype="multipart/form-data">
                @csrf
                <p class="text-muted mb-3">Seleccione las fotografías del vehículo. Se recomienda incluir fotos de todas las vistas del vehículo.</p>

                <div class="border rounded p-4 text-center mb-4" id="dropZone" style="border-style: dashed !important; cursor: pointer;">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-1">Arrastra las fotos aquí o haz clic para seleccionar</p>
                    <small class="text-muted">Formatos: JPG, PNG, WEBP | Máximo 10MB por foto</small>
                    <input type="file" name="fotos[]" id="fileInput" class="d-none" multiple accept="image/*">
                </div>

                <div id="photoPreview" class="row g-3 mb-4">
                    @foreach($photos as $photo)
                    <div class="col-md-3 col-6">
                        <div class="card h-100">
                            <img src="{{ asset($photo->ruta) }}" class="card-img-top" style="height:150px; object-fit:cover;">
                            <div class="card-body p-2 text-center">
                                <small class="text-muted">{{ $photo->nombre_original }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div id="newPhotoPreview" class="row g-3 mb-4"></div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('expertise.step', ['expertise' => $expertise->id, 'step' => 10]) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Paso Anterior</a>
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        <i class="fas fa-check-double me-2"></i>Finalizar y Ver Resumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const newPreview = document.getElementById('newPhotoPreview');

dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-primary'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-primary'));
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary');
    fileInput.files = e.dataTransfer.files;
    showPreviews();
});

fileInput.addEventListener('change', showPreviews);

function showPreviews() {
    newPreview.innerHTML = '';
    Array.from(fileInput.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'col-md-3 col-6';
            div.innerHTML = `
                <div class="card h-100 border-success">
                    <img src="${e.target.result}" class="card-img-top" style="height:150px; object-fit:cover;">
                    <div class="card-body p-2 text-center">
                        <small class="text-success"><i class="fas fa-check me-1"></i>${file.name}</small>
                    </div>
                </div>
            `;
            newPreview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

document.getElementById('stepForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.querySelector('.loading').classList.add('show');

    fetch('{{ route("expertise.save-step", ["expertise" => $expertise->id, "step" => 11]) }}', {
        method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.href = data.redirect;
        else { Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' }); btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});
</script>
@endpush
