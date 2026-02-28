@extends('layouts.app')

@section('title', 'Nuevo Peritaje - Paso 1 - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0"><i class="fas fa-clipboard-check me-2" style="color: var(--accent);"></i>Nuevo Peritaje Completo</h4>
            <small>Paso 1: Información del Servicio y Cliente</small>
        </div>
        <a href="{{ route('expertise.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Servicio</h5></div>
                <div class="card-body">
                    <form id="step1Form">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-calendar me-1"></i>Fecha del Servicio *</label>
                                <input type="date" class="form-control" name="service_date" value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-hashtag me-1"></i>Número de Servicio *</label>
                                <input type="text" class="form-control" name="service_number" placeholder="Ej: SRV-001" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-briefcase me-1"></i>Servicio Para *</label>
                                <input type="text" class="form-control" name="service_for" placeholder="Nombre del servicio" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-handshake me-1"></i>Convenio</label>
                                <input type="text" class="form-control" name="agreement" placeholder="Convenio (opcional)">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3"><i class="fas fa-user me-2"></i>Seleccionar Cliente</h5>
                        <input type="hidden" name="client_id" id="client_id" required>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="clientSearch" placeholder="Buscar cliente por nombre, identificación, teléfono o email (mín. 3 caracteres)..." autocomplete="off">
                            </div>
                        </div>

                        <div id="clientResults" class="mb-3" style="display:none;">
                            <div class="list-group" id="clientList"></div>
                        </div>

                        <div id="selectedClient" class="alert alert-info" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong id="selectedClientName"></strong>
                                    <br><small id="selectedClientInfo"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearClient()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('expertise.index') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Cancelar</a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                Continuar al Paso 2 <i class="fas fa-arrow-right ms-2"></i>
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
let searchTimeout;
const clientSearch = document.getElementById('clientSearch');
const clientResults = document.getElementById('clientResults');
const clientList = document.getElementById('clientList');

clientSearch.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    if (q.length < 3) { clientResults.style.display = 'none'; return; }

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('expertise.search-clients') }}?search=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json()).then(data => {
            clientList.innerHTML = '';
            if (data.success && data.clients.length > 0) {
                data.clients.forEach(c => {
                    const item = document.createElement('a');
                    item.className = 'list-group-item list-group-item-action';
                    item.href = '#';
                    item.innerHTML = `<strong>${c.first_name} ${c.last_name}</strong> <small class="text-muted">| ${c.identification} | ${c.phone}</small>`;
                    item.onclick = (e) => { e.preventDefault(); selectClient(c); };
                    clientList.appendChild(item);
                });
                clientResults.style.display = 'block';
            } else {
                clientList.innerHTML = '<div class="list-group-item text-muted">No se encontraron clientes</div>';
                clientResults.style.display = 'block';
            }
        });
    }, 300);
});

function selectClient(client) {
    document.getElementById('client_id').value = client.id;
    document.getElementById('selectedClientName').textContent = `${client.first_name} ${client.last_name}`;
    document.getElementById('selectedClientInfo').textContent = `ID: ${client.identification} | Tel: ${client.phone} | ${client.email}`;
    document.getElementById('selectedClient').style.display = 'block';
    clientResults.style.display = 'none';
    clientSearch.value = '';
    document.getElementById('submitBtn').disabled = false;
}

function clearClient() {
    document.getElementById('client_id').value = '';
    document.getElementById('selectedClient').style.display = 'none';
    document.getElementById('submitBtn').disabled = true;
}

document.getElementById('step1Form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.querySelector('.loading').classList.add('show');

    fetch('{{ route("expertise.store") }}', { method: 'POST', body: new FormData(this), headers: { 'Accept': 'application/json' } })
    .then(r => r.json()).then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
            btn.disabled = false; btn.querySelector('.loading').classList.remove('show');
        }
    }).catch(() => { btn.disabled = false; btn.querySelector('.loading').classList.remove('show'); });
});
</script>
@endpush
