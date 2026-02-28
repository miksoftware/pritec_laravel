@extends('layouts.app')

@section('title', 'Citas - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-calendar-alt me-2" style="color: var(--accent);"></i>Citas
            </h4>
            <small class="text-muted">{{ $todayCount }} citas hoy · {{ $upcomingCount }} próximas</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
            <i class="fas fa-plus me-1"></i> Nueva Cita
        </button>
    </div>
</div>

<div class="content-body">
    {{-- Filtros --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('appointments.index', ['filter' => 'upcoming']) }}"
           class="btn btn-sm {{ $filter === 'upcoming' ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-clock me-1"></i>Próximas
        </a>
        <a href="{{ route('appointments.index', ['filter' => 'today']) }}"
           class="btn btn-sm {{ $filter === 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-calendar-day me-1"></i>Hoy
        </a>
        <a href="{{ route('appointments.index', ['filter' => 'all']) }}"
           class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-list me-1"></i>Todas
        </a>
        <a href="{{ route('appointments.index', ['filter' => 'completed']) }}"
           class="btn btn-sm {{ $filter === 'completed' ? 'btn-success' : 'btn-outline-success' }}">
            <i class="fas fa-check me-1"></i>Completadas
        </a>
        <a href="{{ route('appointments.index', ['filter' => 'cancelled']) }}"
           class="btn btn-sm {{ $filter === 'cancelled' ? 'btn-danger' : 'btn-outline-danger' }}">
            <i class="fas fa-times me-1"></i>Canceladas
        </a>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-body p-0">
            @if($appointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Vehículo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $apt->appointment_date->format('d/m/Y') }}</span>
                                        @if($apt->appointment_date->isToday())
                                            <span class="badge bg-info ms-1" style="font-size: 0.65rem;">HOY</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</td>
                                    <td>
                                        <div>{{ $apt->client->full_name ?? 'N/A' }}</div>
                                        <small class="text-muted">CC: {{ $apt->client->identification ?? '' }}</small>
                                    </td>
                                    <td>{{ $apt->client->phone ?? '-' }}</td>
                                    <td>{{ $apt->vehicle_description ?: '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $apt->status_color }}">{{ $apt->status_label }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($apt->status === 'pending')
                                                <button class="btn btn-outline-info" onclick="changeStatus({{ $apt->id }}, 'confirmed')" title="Confirmar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            @if(in_array($apt->status, ['pending', 'confirmed']))
                                                <button class="btn btn-outline-success" onclick="changeStatus({{ $apt->id }}, 'completed')" title="Completar">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="changeStatus({{ $apt->id }}, 'cancelled')" title="Cancelar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-outline-danger" onclick="deleteAppointment({{ $apt->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @if($apt->notes)
                                    <tr>
                                        <td colspan="7" class="py-1 ps-4" style="font-size: 0.78rem; border-top: none;">
                                            <i class="fas fa-sticky-note text-muted me-1"></i>
                                            <span class="text-muted">{{ $apt->notes }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $appointments->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">No hay citas para mostrar</h6>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Nueva Cita --}}
<div class="modal fade" id="newAppointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm">
                    @csrf
                    {{-- Buscar cliente --}}
                    <div class="mb-3">
                        <label class="form-label">Cliente *</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="clientSearch" placeholder="Buscar por nombre o cédula..." autocomplete="off">
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#newClientModal" title="Crear cliente">
                                <i class="fas fa-user-plus"></i>
                            </button>
                        </div>
                        <input type="hidden" name="client_id" id="clientId" required>
                        <div id="clientResults" class="list-group mt-1" style="position: absolute; z-index: 1050; width: calc(100% - 2rem); display: none;"></div>
                        <div id="selectedClient" class="mt-2" style="display: none;">
                            <span class="badge bg-primary p-2" id="selectedClientBadge"></span>
                            <button type="button" class="btn btn-sm btn-link text-danger" onclick="clearClient()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Fecha *</label>
                            <input type="date" class="form-control" name="appointment_date" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Hora *</label>
                            <input type="time" class="form-control" name="appointment_time" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vehículo (descripción breve)</label>
                        <input type="text" class="form-control" name="vehicle_description" placeholder="Ej: Mazda 3 2020 Blanco - ABC123">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSaveAppointment" onclick="saveAppointment()">
                    <i class="fas fa-save me-1"></i> Agendar Cita
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Nuevo Cliente --}}
<div class="modal fade" id="newClientModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nuevo Cliente</h5>
                <button type="button" class="btn-close" onclick="closeClientModal()"></button>
            </div>
            <div class="modal-body">
                <form id="clientForm">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Nombres *</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Identificación *</label>
                            <input type="text" class="form-control" name="identification" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Opcional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="address" placeholder="Opcional">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeClientModal()">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="saveClient()">
                    <i class="fas fa-save me-1"></i> Crear Cliente
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    let searchTimeout;

    // Buscar clientes
    document.getElementById('clientSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        const results = document.getElementById('clientResults');
        if (q.length < 2) { results.style.display = 'none'; return; }

        searchTimeout = setTimeout(() => {
            fetch('{{ route("appointments.search-clients") }}?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(clients => {
                if (clients.length === 0) {
                    results.innerHTML = '<div class="list-group-item text-muted" style="font-size:0.85rem;">No se encontraron clientes</div>';
                } else {
                    results.innerHTML = clients.map(c =>
                        `<a href="#" class="list-group-item list-group-item-action" style="font-size:0.85rem;" onclick="selectClient(${c.id}, '${c.first_name} ${c.last_name}', '${c.identification}'); return false;">
                            <strong>${c.first_name} ${c.last_name}</strong> <small class="text-muted">CC: ${c.identification} · Tel: ${c.phone}</small>
                        </a>`
                    ).join('');
                }
                results.style.display = 'block';
            });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#clientResults') && !e.target.closest('#clientSearch')) {
            document.getElementById('clientResults').style.display = 'none';
        }
    });

    function selectClient(id, name, identification) {
        document.getElementById('clientId').value = id;
        document.getElementById('clientSearch').value = '';
        document.getElementById('clientSearch').style.display = 'none';
        document.getElementById('selectedClient').style.display = 'block';
        document.getElementById('selectedClientBadge').textContent = name + ' - CC: ' + identification;
        document.getElementById('clientResults').style.display = 'none';
    }

    function clearClient() {
        document.getElementById('clientId').value = '';
        document.getElementById('clientSearch').value = '';
        document.getElementById('clientSearch').style.display = 'block';
        document.getElementById('selectedClient').style.display = 'none';
    }

    function saveAppointment() {
        const form = document.getElementById('appointmentForm');
        const btn = document.getElementById('btnSaveAppointment');
        if (!document.getElementById('clientId').value) {
            Swal.fire({ icon: 'warning', title: 'Selecciona un cliente', background: '#1a2332', color: '#fff' });
            return;
        }
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

        fetch('{{ route("appointments.store") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Cita agendada', text: data.message, timer: 1500, showConfirmButton: false, background: '#1a2332', color: '#fff' })
                .then(() => location.reload());
            } else {
                let msg = data.message || 'Error al guardar.';
                if (data.errors) msg = Object.values(data.errors).flat().join('\n');
                Swal.fire({ icon: 'error', title: 'Error', text: msg, background: '#1a2332', color: '#fff' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión.', background: '#1a2332', color: '#fff' }))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-1"></i> Agendar Cita'; });
    }

    function changeStatus(id, status) {
        const labels = { confirmed: 'confirmar', completed: 'completar', cancelled: 'cancelar' };
        Swal.fire({
            title: '¿' + labels[status].charAt(0).toUpperCase() + labels[status].slice(1) + ' esta cita?',
            icon: 'question', showCancelButton: true,
            confirmButtonText: 'Sí', cancelButtonText: 'No',
            background: '#1a2332', color: '#fff'
        }).then(result => {
            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('_token', CSRF);
                fd.append('status', status);
                fetch('/appointments/' + id + '/status', { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) location.reload();
                });
            }
        });
    }

    function deleteAppointment(id) {
        Swal.fire({
            title: '¿Eliminar esta cita?', text: 'No se puede deshacer.', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#e74c3c', confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar',
            background: '#1a2332', color: '#fff'
        }).then(result => {
            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('_token', CSRF);
                fd.append('_method', 'DELETE');
                fetch('/appointments/' + id, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => { if (data.success) location.reload(); });
            }
        });
    }

    function saveClient() {
        const form = document.getElementById('clientForm');
        fetch('{{ route("appointments.store-client") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                selectClient(data.client.id, data.client.first_name + ' ' + data.client.last_name, data.client.identification);
                closeClientModal();
                form.reset();
                Swal.fire({ icon: 'success', title: 'Cliente creado', timer: 1500, showConfirmButton: false, background: '#1a2332', color: '#fff' });
            } else {
                let msg = data.message || 'Error';
                if (data.errors) msg = Object.values(data.errors).flat().join('\n');
                Swal.fire({ icon: 'error', title: 'Error', text: msg, background: '#1a2332', color: '#fff' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión.', background: '#1a2332', color: '#fff' }));
    }

    function closeClientModal() {
        var modal = bootstrap.Modal.getInstance(document.getElementById('newClientModal'));
        if (modal) modal.hide();
    }
</script>
@endpush
