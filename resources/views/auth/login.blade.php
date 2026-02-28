@extends('layouts.app')

@section('title', 'Iniciar Sesión - Pritec')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">
                <i class="fas fa-user"></i>
            </div>
            <h2>Pritec</h2>
            <p>Sistema de Peritajes</p>
        </div>

        <div class="auth-body">
            <form id="loginForm">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        Correo Electrónico
                    </label>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           placeholder="usuario@ejemplo.com"
                           required
                           autocomplete="email">
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i>
                        Contraseña
                    </label>
                    <div class="position-relative">
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               placeholder="Tu contraseña"
                               required
                               autocomplete="current-password">
                        <button type="button"
                                class="btn btn-link position-absolute end-0 top-50 translate-middle-y"
                                onclick="togglePassword()"
                                style="border: none; background: none; padding: 0 15px;">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary" id="loginBtn">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        LOGIN
                    </button>
                </div>

                <div class="text-center">
                    <p class="mb-0">
                        ¿No tienes cuenta?
                        <a href="{{ route('register') }}">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = document.getElementById('loginBtn');
        const loading = btn.querySelector('.loading');

        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        btn.disabled = true;
        loading.classList.add('show');

        fetch('{{ route("login.process") }}', {
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
                    title: '¡Bienvenido!',
                    text: data.message,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500,
                    background: '#1a2332',
                    color: '#fff'
                }).then(() => window.location.href = data.redirect);
            } else {
                if (data.errors) {
                    for (const [field, msgs] of Object.entries(data.errors)) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const fb = input.closest('.mb-3, .mb-4')?.querySelector('.invalid-feedback');
                            if (fb) fb.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                        }
                    }
                }
                Swal.fire({
                    title: 'Error',
                    text: data.message,
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
