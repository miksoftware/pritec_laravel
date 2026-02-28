@extends('layouts.app')

@section('title', 'Detalles del Cliente - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0"><i class="fas fa-user me-2" style="color: var(--accent);"></i>Detalles del Cliente</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clientes</a></li>
                    <li class="breadcrumb-item active">{{ $client->full_name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Editar</a>
            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
        </div>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-id-card me-2"></i>{{ $client->full_name }}</h5>
                    @if($client->status === 'active')
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-danger">Inactivo</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label text-muted" style="font-size: 0.78rem;"><i class="fas fa-user me-1"></i>NOMBRES</label>
                                <p class="fw-bold mb-0">{{ $client->first_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label text-muted" style="font-size: 0.78rem;"><i class="fas fa-user me-1"></i>APELLIDOS</label>
                                <p class="fw-bold mb-0">{{ $client->last_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label text-muted" style="font-size: 0.78rem;"><i class="fas fa-id-card me-1"></i>IDENTIFICACIÓN</label>
                                <p class="fw-bold mb-0"><code>{{ $client->identification }}</code></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label text-muted" style="font-size: 0.78rem;"><i class="fas fa-phone me-1"></i>TELÉFONO</label>
                                <p class="fw-bold mb-0">{{ $client->phone }}</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label text-muted" style="font-size: 0.78rem;"><i class="fas fa-envelope me-1"></i>CORREO ELECTRÓNICO</label>
                                <p class="fw-bold mb-0"><a href="mailto:{{ $client->email }}">{{ $client->email }}</a></p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label text-muted" style="font-size: 0.78rem;"><i class="fas fa-map-marker-alt me-1"></i>DIRECCIÓN</label>
                                <p class="fw-bold mb-0">{{ $client->address }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Estado</small>
                            @if($client->status === 'active')
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Fecha de Registro</small>
                            <strong>{{ $client->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Última Actualización</small>
                            <strong>{{ $client->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
