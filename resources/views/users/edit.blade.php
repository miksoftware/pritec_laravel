@extends('layouts.app')

@section('title', 'Editar Usuario - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-user-edit me-2"></i>
                Editar Usuario
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">Editar: {{ $user->full_name }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Volver a Usuarios
        </a>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Información del Usuario
                        @if($user->is_admin)
                            <span class="badge bg-warning text-dark ms-2">Administrador</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <form id="editUserForm" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Nombre de Usuario *
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="username"
                                       name="username"
                                       value="{{ $user->username }}"
                                       required
                                       placeholder="Ej: juan.perez">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>
                                    Email *
                                </label>
                                <input type="email"
                                       class="form-control"
                                       id="email"
                                       name="email"
                                       value="{{ $user->email }}"
                                       required
                                       placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">
                                <i class="fas fa-id-card me-1"></i>
                                Nombre Completo *
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="full_name"
                                   name="full_name"
                                   value="{{ $user->full_name }}"
                                   required
                                   placeholder="Juan Pérez García">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>
                                    Nueva Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control"
                                           id="password"
                                           name="password"
                                           minlength="6"
                                           placeholder="Dejar vacío para no cambiar">
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Dejar vacío si no deseas cambiar la contraseña</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-1"></i>
                                    Confirmar Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Repetir contraseña">
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>
                                Estado del Usuario
                            </label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        <!-- User info -->
                        <div class="bg-light rounded p-3 mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Fecha de registro</small>
                                    <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Último acceso</small>
                                    <strong>{{ $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'Nunca' }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                <i class="fas fa-save me-2" id="saveIcon"></i>
                                Actualizar Usuario
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
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Form submission
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        const loading = submitBtn.querySelector('.loading');
        const formData = new FormData(form);

        // Clear previous errors
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        // Show loading
        submitBtn.disabled = true;
        loading.classList.add('show');

        fetch('{{ route("users.update", $user) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.closest('.mb-3, .col-md-6')?.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.textContent = Array.isArray(messages) ? messages[0] : messages;
                            }
                        }
                    }
                }
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Por favor corrige los errores.',
                    icon: 'error',
                    confirmButtonColor: '#e74c3c'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al actualizar el usuario.',
                icon: 'error',
                confirmButtonColor: '#e74c3c'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            loading.classList.remove('show');
        });
    });

    // Real-time validation for password confirmation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        if (this.value && password !== this.value) {
            this.classList.add('is-invalid');
            this.closest('.col-md-6').querySelector('.invalid-feedback').textContent = 'Las contraseñas no coinciden';
        } else if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
</script>
@endpush
