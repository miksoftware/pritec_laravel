@extends('layouts.app')

@section('title', 'Editar Cliente - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0"><i class="fas fa-user-edit me-2" style="color: var(--accent);"></i>Editar Cliente</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clientes</a></li>
                    <li class="breadcrumb-item active">Editar: {{ $client->full_name }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-edit me-2"></i>Datos del Cliente</h5></div>
                <div class="card-body">
                    <form id="editForm" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label"><i class="fas fa-user me-1"></i>Nombres *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $client->first_name }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label"><i class="fas fa-user me-1"></i>Apellidos *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $client->last_name }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="identification" class="form-label"><i class="fas fa-id-card me-1"></i>Identificación *</label>
                                <input type="text" class="form-control" id="identification" name="identification" value="{{ $client->identification }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label"><i class="fas fa-phone me-1"></i>Teléfono *</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $client->phone }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Correo Electrónico *</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $client->email }}" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Dirección *</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required>{{ $client->address }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label"><i class="fas fa-toggle-on me-1"></i>Estado</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" {{ $client->status === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ $client->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        <div class="bg-light rounded p-3 mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Fecha de creación</small>
                                    <strong>{{ $client->created_at->format('d/m/Y H:i') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Última actualización</small>
                                    <strong>{{ $client->updated_at->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-info text-white"><i class="fas fa-eye me-2"></i>Ver Detalles</a>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancelar</a>
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

        fetch('{{ route("clients.update", $client) }}', {
            method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json' }
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
                            const fb = input.closest('.mb-3, .col-md-6')?.querySelector('.invalid-feedback');
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
