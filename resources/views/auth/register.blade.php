@extends('layouts.app')

@section('title', 'Registrarse - Pritec')

@section('content')
<div class="auth-container">
    <div class="auth-card auth-card-wide">
        <div class="auth-header">
            <div class="auth-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2>Crear Cuenta</h2>
            <p>Sistema de Peritajes</p>
        </div>

        <div class="auth-body">
            <form id="registerForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-1"></i>
                            Nombre de Usuario
                        </label>
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Tu nombre de usuario" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">
                            <i class="fas fa-id-card me-1"></i>
                            Nombre Completo
                        </label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               placeholder="Tu nombre completo" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        Correo Electrónico
                    </label>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="usuario@ejemplo.com" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Contraseña
                        </label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Mínimo 6 caracteres" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Confirmar Contraseña
                        </label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                               placeholder="Repite la contraseña" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary" id="registerBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        CREAR CUENTA
                    </button>
                </div>

                <div class="text-center">
                    <p class="mb-0">
                        ¿Ya tienes cuenta?
                        <a href="{{ route('login') }}">Inicia sesión</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = document.getElementById('registerBtn');
        const loading = btn.querySelector('.loading');

        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        btn.disabled = true;
        loading.classList.add('show');

        fetch('{{ route("register.process") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Registro Exitoso!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#3498db',
                    background: '#1a2332',
                    color: '#fff'
                }).then(() => window.location.href = data.redirect);
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
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Corrige los errores.',
                    icon: 'error',
                    confirmButtonColor: '#e74c3c',
                    background: '#1a2332',
                    color: '#fff'
                });
            }
        })
        .catch(() => {
            Swal.fire({
                title: 'Error',
                text: 'Error de conexión.',
                icon: 'error',
                confirmButtonColor: '#e74c3c',
                background: '#1a2332',
                color: '#fff'
            });
        })
        .finally(() => {
            btn.disabled = false;
            loading.classList.remove('show');
        });
    });
</script>
@endpush
