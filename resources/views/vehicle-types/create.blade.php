@extends('layouts.app')

@section('title', 'Crear Tipo de Vehículo - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0"><i class="fas fa-car me-2" style="color: var(--accent);"></i>Crear Tipo de Vehículo</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('vehicle-types.index') }}">Tipos de Vehículos</a></li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('vehicle-types.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-car me-2"></i>Información del Tipo de Vehículo</h5>
                </div>
                <div class="card-body">
                    <form id="createForm" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="type" class="form-label"><i class="fas fa-tags me-1"></i>Tipo de Vehículo *</label>
                            <select class="form-select" id="type" name="type" required onchange="updateSectionsPreview()">
                                <option value="">Seleccionar tipo...</option>
                                <option value="carro">🚗 Carro</option>
                                <option value="moto">🏍️ Moto</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label"><i class="fas fa-signature me-1"></i>Nombre del Tipo *</label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Ej: Sedán Compacto, Motocicleta Deportiva">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><i class="fas fa-align-left me-1"></i>Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Descripción opcional..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label"><i class="fas fa-toggle-on me-1"></i>Estado</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" selected>Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>

                        <!-- Sections Preview -->
                        <div id="sectionsPreview" class="mb-4" style="display: none;">
                            <h6><i class="fas fa-eye me-2"></i>Secciones que se crearán:</h6>
                            <div id="sectionsInfo" class="alert alert-info mb-0" style="background: rgba(52, 152, 219, 0.08); border-color: rgba(52, 152, 219, 0.2); color: var(--text-primary);"></div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('vehicle-types.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                <i class="fas fa-arrow-right me-2"></i>Crear y Continuar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateSectionsPreview() {
        const type = document.getElementById('type').value;
        const preview = document.getElementById('sectionsPreview');
        const info = document.getElementById('sectionsInfo');

        if (type) {
            preview.style.display = 'block';
            if (type === 'carro') {
                info.innerHTML = '<strong>Se crearán 3 secciones:</strong><ul class="mb-0 mt-2"><li><i class="fas fa-car me-2"></i>Carrocería</li><li><i class="fas fa-tools me-2"></i>Estructura</li><li><i class="fas fa-cogs me-2"></i>Chasis</li></ul>';
            } else {
                info.innerHTML = '<strong>Se crearán 2 secciones:</strong><ul class="mb-0 mt-2"><li><i class="fas fa-motorcycle me-2"></i>Estructura</li><li><i class="fas fa-cogs me-2"></i>Chasis</li></ul>';
            }
        } else {
            preview.style.display = 'none';
        }
    }

    document.getElementById('createForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = document.getElementById('submitBtn');
        const loading = btn.querySelector('.loading');

        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        btn.disabled = true;
        loading.classList.add('show');

        fetch('{{ route("vehicle-types.store") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Éxito!', text: data.message, icon: 'success',
                    confirmButtonColor: '#3498db', confirmButtonText: 'Continuar al Paso 2',
                    background: '#1a2332', color: '#fff'
                }).then(() => window.location.href = data.redirect);
            } else {
                if (data.errors) {
                    for (const [field, msgs] of Object.entries(data.errors)) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const fb = input.closest('.mb-3')?.querySelector('.invalid-feedback');
                            if (fb) fb.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                        }
                    }
                }
                Swal.fire({ title: 'Error', text: data.message || 'Corrige los errores.', icon: 'error', confirmButtonColor: '#e74c3c', background: '#1a2332', color: '#fff' });
            }
        })
        .catch(() => Swal.fire({ title: 'Error', text: 'Error al crear el tipo.', icon: 'error', confirmButtonColor: '#e74c3c', background: '#1a2332', color: '#fff' }))
        .finally(() => { btn.disabled = false; loading.classList.remove('show'); });
    });
</script>
@endpush
