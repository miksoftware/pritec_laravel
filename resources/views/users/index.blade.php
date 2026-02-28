@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Gestión de Usuarios
            </h4>
            <small class="text-muted">Administra todos los usuarios del sistema</small>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-primary fs-6">
                <i class="fas fa-users me-1"></i>
                {{ $users->total() }} usuarios
            </span>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nuevo Usuario
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('users.index') }}" id="searchForm">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text"
                           class="form-control border-start-0"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Buscar por usuario, email o nombre..."
                           id="searchInput">
                    @if($search)
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Último Acceso</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-light text-dark">{{ $user->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-sm me-2">
                                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $user->username }}</strong>
                                            @if($user->is_admin)
                                                <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Admin</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $user->full_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->status === 'active')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($user->last_login)
                                        <small>{{ $user->last_login->format('d/m/Y H:i') }}</small>
                                    @else
                                        <small class="text-muted">Nunca</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="btn btn-outline-primary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if($user->id !== Auth::id())
                                            <button type="button"
                                                    class="btn btn-outline-warning"
                                                    title="Cambiar Estado"
                                                    onclick="toggleUserStatus({{ $user->id }}, '{{ $user->status }}')">
                                                <i class="fas fa-ban"></i>
                                            </button>

                                            @if($user->isDeletable())
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        title="Eliminar"
                                                        onclick="deleteUser({{ $user->id }}, '{{ $user->username }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Mostrando {{ $users->firstItem() }} - {{ $users->lastItem() }} de {{ $users->total() }}
                    </small>
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay usuarios registrados</h5>
                    <p class="text-muted">Comienza creando el primer usuario del sistema</p>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Crear Primer Usuario
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Search on enter
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('searchForm').submit();
        }
    });

    // Toggle user status
    function toggleUserStatus(userId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        const action = newStatus === 'active' ? 'activar' : 'inactivar';

        Swal.fire({
            title: '¿Confirmar acción?',
            text: `¿Estás seguro de que quieres ${action} este usuario?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: newStatus === 'active' ? '#28a745' : '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Sí, ${action}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/users/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(error => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cambiar el estado del usuario' });
                });
            }
        });
    }

    // Delete user
    function deleteUser(userId, username) {
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: `¿Estás seguro de que quieres eliminar al usuario "${username}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(error => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error al eliminar el usuario' });
                });
            }
        });
    }

    // Row animation
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#usersTable tbody tr');
        rows.forEach((row, i) => {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            row.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateX(0)';
            }, i * 50 + 100);
        });
    });
</script>
@endpush
