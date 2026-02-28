@extends('layouts.app')

@section('title', 'Piezas - ' . $section->formatted_name . ' - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-puzzle-piece me-2" style="color: var(--accent);"></i>
                Definir Piezas
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('vehicle-types.index') }}">Tipos de Vehículos</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicle-types.sections', $vehicleType) }}">{{ $vehicleType->name }}</a></li>
                    <li class="breadcrumb-item active">{{ $section->formatted_name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <!-- Section Navigation -->
            @foreach($allSections as $s)
                <a href="{{ route('vehicle-sections.pieces', $s) }}"
                   class="btn btn-sm {{ $s->id === $section->id ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $s->formatted_name }} ({{ $s->pieces_count }})
                </a>
            @endforeach
        </div>
    </div>
</div>

<div class="content-body">
    <div class="row g-4">
        <!-- Image Canvas -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-image me-2"></i>{{ $section->formatted_name }} — Diagrama</h6>
                    <div>
                        @if($pieces->count() > 0)
                            <button class="btn btn-sm btn-outline-danger" onclick="clearAllPieces()">
                                <i class="fas fa-trash me-1"></i>Limpiar Todo
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($section->image_path)
                        <div class="d-flex justify-content-center">
                        <div id="pieceCanvas" style="position: relative; width: 400px; height: 400px; cursor: crosshair; overflow: hidden;"
                             onclick="handleCanvasClick(event)">
                            <img src="{{ $section->image_url }}" alt="{{ $section->formatted_name }}"
                                 class="rounded" style="width: 400px; height: 400px; display: block; object-fit: cover;" id="diagramImage">

                            @foreach($pieces as $piece)
                                <div class="piece-marker" id="piece_{{ $piece->id }}"
                                     style="position: absolute; left: {{ $piece->position_x }}%; top: {{ $piece->position_y }}%;
                                            transform: translate(-50%, -50%); width: 28px; height: 28px;
                                            background: var(--accent); color: white; border-radius: 50%;
                                            display: flex; align-items: center; justify-content: center;
                                            font-size: 0.7rem; font-weight: 700; cursor: pointer;
                                            box-shadow: 0 2px 8px rgba(0,0,0,0.3); border: 2px solid white;
                                            transition: transform 0.2s; z-index: 10;"
                                     onclick="event.stopPropagation(); selectPiece({{ $piece->id }})"
                                     title="{{ $piece->piece_name ?: 'Pieza #' . $piece->piece_number }}">
                                    {{ $piece->piece_number }}
                                </div>
                            @endforeach
                        </div>
                        </div>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.78rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            Haz clic en la imagen para agregar una pieza en esa posición
                        </p>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No hay imagen de diagrama</h6>
                            <p class="text-muted">Primero sube una imagen en la configuración de secciones</p>
                            <a href="{{ route('vehicle-types.sections', $vehicleType) }}" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Subir Imagen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Piece Form & List -->
        <div class="col-lg-4">
            <!-- Add/Edit Piece Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0" id="formTitle"><i class="fas fa-plus me-2"></i>Agregar Pieza</h6>
                </div>
                <div class="card-body">
                    <form id="pieceForm">
                        @csrf
                        <input type="hidden" name="section_id" value="{{ $section->id }}">
                        <input type="hidden" name="piece_id" id="pieceIdInput">

                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.82rem;">Número de Pieza *</label>
                            <input type="number" class="form-control" name="piece_number" id="pieceNumber" required min="1"
                                   placeholder="Ej: 1, 2, 3...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.82rem;">Nombre (opcional)</label>
                            <input type="text" class="form-control" name="piece_name" id="pieceName"
                                   placeholder="Nombre de la pieza">
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.82rem;">Posición X (%)</label>
                                <input type="number" class="form-control" name="position_x" id="positionX"
                                       required step="0.01" min="0" max="100">
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.82rem;">Posición Y (%)</label>
                                <input type="number" class="form-control" name="position_y" id="positionY"
                                       required step="0.01" min="0" max="100">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm" id="submitPieceBtn">
                                <i class="fas fa-save me-1"></i>Guardar Pieza
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="cancelEditBtn" onclick="cancelEdit()">
                                <i class="fas fa-times me-1"></i>Cancelar Edición
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pieces List -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Piezas ({{ $pieces->count() }})</h6>
                </div>
                <div class="card-body p-0">
                    @if($pieces->count() > 0)
                        <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                            @foreach($pieces as $piece)
                                <div class="list-group-item d-flex justify-content-between align-items-center"
                                     id="pieceList_{{ $piece->id }}" style="font-size: 0.85rem;">
                                    <div>
                                        <span class="badge bg-primary me-2">{{ $piece->piece_number }}</span>
                                        {{ $piece->piece_name ?: 'Sin nombre' }}
                                        <br>
                                        <small class="text-muted">X: {{ $piece->position_x }}% — Y: {{ $piece->position_y }}%</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editPiece({{ json_encode($piece) }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deletePiece({{ $piece->id }})" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">No hay piezas definidas</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Navigation -->
            @if($nextSection)
                <div class="mt-3">
                    <a href="{{ route('vehicle-sections.pieces', $nextSection) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-right me-2"></i>
                        Siguiente: {{ $nextSection->formatted_name }}
                    </a>
                </div>
            @else
                <div class="mt-3">
                    <a href="{{ route('vehicle-types.index') }}" class="btn btn-success w-100">
                        <i class="fas fa-check me-2"></i>
                        Finalizar Configuración
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let isEditing = false;
    let previewMarker = null;

    // Create or update preview marker on the canvas
    function showPreviewMarker(x, y) {
        const canvas = document.getElementById('pieceCanvas');
        if (!canvas) return;

        // Remove previous preview
        if (previewMarker) previewMarker.remove();

        // Get the piece number from the form
        const pieceNumber = document.getElementById('pieceNumber').value || '?';

        previewMarker = document.createElement('div');
        previewMarker.id = 'previewMarker';
        previewMarker.style.cssText = `
            position: absolute; left: ${x}%; top: ${y}%;
            transform: translate(-50%, -50%); width: 32px; height: 32px;
            background: #2ecc71; color: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; cursor: pointer;
            box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.3), 0 2px 8px rgba(0,0,0,0.3);
            border: 2px solid white; z-index: 20;
            animation: previewPulse 1.5s ease-in-out infinite;
        `;
        previewMarker.textContent = pieceNumber;
        previewMarker.title = 'Vista previa — Aún no guardado';
        previewMarker.onclick = function(e) { e.stopPropagation(); };

        canvas.appendChild(previewMarker);
    }

    // Handle canvas click to set position
    function handleCanvasClick(event) {
        const img = document.getElementById('diagramImage');
        const rect = img.getBoundingClientRect();

        const x = ((event.clientX - rect.left) / rect.width * 100).toFixed(2);
        const y = ((event.clientY - rect.top) / rect.height * 100).toFixed(2);

        document.getElementById('positionX').value = x;
        document.getElementById('positionY').value = y;

        // Auto-suggest next piece number
        if (!isEditing && !document.getElementById('pieceNumber').value) {
            const existingNumbers = @json($pieces->pluck('piece_number')->toArray());
            const maxNumber = existingNumbers.length > 0 ? Math.max(...existingNumbers) : 0;
            document.getElementById('pieceNumber').value = maxNumber + 1;
        }

        // Show preview marker
        showPreviewMarker(x, y);
    }

    // Update preview marker number when input changes
    document.getElementById('pieceNumber').addEventListener('input', function() {
        if (previewMarker) {
            previewMarker.textContent = this.value || '?';
        }
    });

    // Select piece (highlight)
    function selectPiece(pieceId) {
        document.querySelectorAll('.piece-marker').forEach(m => {
            m.style.background = 'var(--accent)';
            m.style.transform = 'translate(-50%, -50%)';
        });
        const marker = document.getElementById('piece_' + pieceId);
        if (marker) {
            marker.style.background = '#e74c3c';
            marker.style.transform = 'translate(-50%, -50%) scale(1.3)';
        }
    }

    // Edit piece
    function editPiece(piece) {
        isEditing = true;
        // Remove preview when editing existing piece
        if (previewMarker) { previewMarker.remove(); previewMarker = null; }

        document.getElementById('pieceIdInput').value = piece.id;
        document.getElementById('pieceNumber').value = piece.piece_number;
        document.getElementById('pieceName').value = piece.piece_name || '';
        document.getElementById('positionX').value = piece.position_x;
        document.getElementById('positionY').value = piece.position_y;
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Editar Pieza #' + piece.piece_number;
        document.getElementById('submitPieceBtn').innerHTML = '<i class="fas fa-save me-1"></i>Actualizar Pieza';
        document.getElementById('cancelEditBtn').classList.remove('d-none');
        selectPiece(piece.id);
    }

    // Cancel edit
    function cancelEdit() {
        isEditing = false;
        document.getElementById('pieceForm').reset();
        document.getElementById('pieceIdInput').value = '';
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Agregar Pieza';
        document.getElementById('submitPieceBtn').innerHTML = '<i class="fas fa-save me-1"></i>Guardar Pieza';
        document.getElementById('cancelEditBtn').classList.add('d-none');
        // Remove preview marker
        if (previewMarker) { previewMarker.remove(); previewMarker = null; }
        document.querySelectorAll('.piece-marker').forEach(m => {
            m.style.background = 'var(--accent)';
            m.style.transform = 'translate(-50%, -50%)';
        });
    }

    // Form submit (add or update)
    document.getElementById('pieceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = isEditing ? '{{ route("vehicle-sections.update-piece") }}' : '{{ route("vehicle-sections.add-piece") }}';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success', title: '¡Éxito!', text: data.message,
                    timer: 1500, showConfirmButton: false,
                    background: '#1a2332', color: '#fff'
                }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar la pieza.', background: '#1a2332', color: '#fff' }));
    });

    // Delete piece
    function deletePiece(pieceId) {
        Swal.fire({
            title: '¿Eliminar pieza?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#1a2332', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('piece_id', pieceId);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("vehicle-sections.delete-piece") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '¡Eliminada!', text: data.message, timer: 1500, showConfirmButton: false, background: '#1a2332', color: '#fff' })
                        .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#1a2332', color: '#fff' });
                    }
                });
            }
        });
    }

    // Clear all pieces
    function clearAllPieces() {
        Swal.fire({
            title: '¿Eliminar TODAS las piezas?',
            text: 'Se eliminarán todas las piezas de esta sección. Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar todas',
            cancelButtonText: 'Cancelar',
            background: '#1a2332', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('section_id', '{{ $section->id }}');
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("vehicle-sections.clear-pieces") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '¡Limpiado!', text: data.message, timer: 1500, showConfirmButton: false, background: '#1a2332', color: '#fff' })
                        .then(() => location.reload());
                    }
                });
            }
        });
    }
</script>
@endpush
