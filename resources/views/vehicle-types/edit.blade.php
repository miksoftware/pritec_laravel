@extends('layouts.app')

@section('title', 'Editar Tipo de Vehículo - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0"><i class="fas fa-car me-2" style="color: var(--accent);"></i>Editar Tipo de Vehículo</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('vehicle-types.index') }}">Tipos de Vehículos</a></li>
                    <li class="breadcrumb-item active">Editar: {{ $vehicleType->name }}</li>
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
                    <h5 class="card-title mb-0"><i class="fas fa-edit me-2"></i>Información del Tipo</h5>
                </div>
                <div class="card-body">
                    <form id="editForm" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="type" class="form-label"><i class="fas fa-tags me-1"></i>Tipo de Vehículo *</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="carro" {{ $vehicleType->type === 'carro' ? 'selected' : '' }}>🚗 Carro</option>
                                <option value="moto" {{ $vehicleType->type === 'moto' ? 'selected' : '' }}>🏍️ Moto</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label"><i class="fas fa-signature me-1"></i>Nombre del Tipo *</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $vehicleType->name }}" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><i class="fas fa-align-left me-1"></i>Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $vehicleType->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label"><i class="fas fa-toggle-on me-1"></i>Estado</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" {{ $vehicleType->status === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ $vehicleType->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        <div class="bg-light rounded p-3 mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Fecha de creación</small>
                                    <strong>{{ $vehicleType->created_at->format('d/m/Y H:i') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Última actualización</small>
                                    <strong>{{ $vehicleType->updated_at->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('vehicle-types.sections', $vehicleType) }}" class="btn btn-info text-white">
                                <i class="fas fa-cogs me-2"></i>Configurar Secciones
                            </a>
                            <a href="{{ route('vehicle-types.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                <i class="fas fa-save me-2"></i>Actualizar
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
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = document.getElementById('submitBtn');
        const loading = btn.querySelector('.loading');

        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        btn.disabled = true;
        loading.classList.add('show');

        fetch('{{ route("vehicle-types.update", $vehicleType) }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ title: '¡Éxito!', text: data.message, icon: 'success', confirmButtonColor: '#3498db', background: '#1a2332', color: '#fff' })
                .then(() => window.location.href = data.redirect);
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
        .catch(() => Swal.fire({ title: 'Error', text: 'Error al actualizar.', icon: 'error', confirmButtonColor: '#e74c3c', background: '#1a2332', color: '#fff' }))
        .finally(() => { btn.disabled = false; loading.classList.remove('show'); });
    });
</script>
@endpush
