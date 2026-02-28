@extends('layouts.app')

@section('title', 'Migración de Datos - Pritec')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-dark text-white border-secondary">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fas fa-database me-2"></i>Migración desde Pritec v2</h5>
                    <small class="text-muted">Importar datos desde la base de datos anterior</small>
                </div>
                <div class="card-body">

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>

                        @if(session('results'))
                            @php $results = session('results'); @endphp
                            <div class="card bg-secondary bg-opacity-25 border-secondary mb-3">
                                <div class="card-body">
                                    <h6 class="text-info mb-3">Resumen de importación:</h6>
                                    <table class="table table-dark table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tabla</th>
                                                <th class="text-center">Importados</th>
                                                <th class="text-center">Omitidos</th>
                                                <th class="text-center">Errores</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($results as $table => $data)
                                                <tr>
                                                    <td>{{ str_replace('_', ' ', ucfirst($table)) }}</td>
                                                    <td class="text-center text-success">{{ $data['imported'] }}</td>
                                                    <td class="text-center text-warning">{{ $data['skipped'] }}</td>
                                                    <td class="text-center text-danger">{{ count($data['errors']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    @foreach($results as $table => $data)
                                        @if(count($data['errors']) > 0)
                                            <div class="mt-2">
                                                <small class="text-danger">Errores en {{ $table }}:</small>
                                                <ul class="small text-danger mb-0">
                                                    @foreach($data['errors'] as $err)
                                                        <li>{{ $err }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    <form action="{{ route('migration.process') }}" method="POST" enctype="multipart/form-data" id="migrationForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Archivo SQL de Pritec v2</label>
                            <input type="file" name="sql_file" class="form-control bg-dark text-white border-secondary" accept=".sql" required>
                            <small class="text-muted">Sube el archivo .sql exportado de la base de datos anterior</small>
                            @error('sql_file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="import_types[]" value="vehicle_types">
                        <input type="hidden" name="import_types[]" value="clients">

                        <div class="alert alert-danger bg-opacity-25">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atención:</strong>
                            <ul class="mb-0 mt-1 small">
                                <li>Se eliminarán todos los tipos de vehículos, secciones, piezas y clientes actuales antes de importar.</li>
                                <li>Las imágenes de secciones deben copiarse manualmente a <code>public/uploads/vehicle_sections/</code></li>
                                <li>Este proceso puede tardar unos segundos dependiendo del tamaño del archivo.</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnMigrate">
                                <i class="fas fa-upload me-1"></i> Iniciar Migración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('migrationForm').addEventListener('submit', function() {
    var btn = document.getElementById('btnMigrate');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';
});
</script>
@endsection
