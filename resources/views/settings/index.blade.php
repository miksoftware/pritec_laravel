@extends('layouts.app')

@section('title', 'Configuración - Pritec')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-cog me-2"></i>
                Configuración
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Configuración</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content-body">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2"></i>
                        Información de la Empresa
                    </h5>
                </div>
                <div class="card-body">
                    <form id="settingsForm" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="company_name" class="form-label">
                                <i class="fas fa-building me-1"></i>
                                Nombre de la Empresa *
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="company_name"
                                   name="company_name"
                                   value="{{ $settings['company_name'] }}"
                                   required
                                   placeholder="Ej: SALA TÉCNICA EN AUTOMOTORES">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="company_subtitle" class="form-label">
                                <i class="fas fa-heading me-1"></i>
                                Subtítulo
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="company_subtitle"
                                   name="company_subtitle"
                                   value="{{ $settings['company_subtitle'] }}"
                                   placeholder="Ej: CERTIFICACIÓN TÉCNICA EN IDENTIFICACIÓN DE AUTOMOTORES">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="company_address" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Dirección
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="company_address"
                                   name="company_address"
                                   value="{{ $settings['company_address'] }}"
                                   placeholder="Ej: Carrera 16 No. 18-197 Barrio Tenerife">
                            <div class="invalid-feedback"></div>
                        </div>

                        {{-- Teléfonos de contacto --}}
                        <div class="card mb-3" style="border: 1px solid rgba(255,255,255,0.1);">
                            <div class="card-header py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-phone me-1"></i>
                                    Teléfonos de Contacto
                                </h6>
                                <small class="text-muted">El teléfono predeterminado se usará para el código QR de WhatsApp en el PDF</small>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-end mb-3">
                                    <div class="col-md-8">
                                        <label for="contact_phone_1" class="form-label">
                                            Teléfono 1 *
                                        </label>
                                        <input type="text"
                                               class="form-control"
                                               id="contact_phone_1"
                                               name="contact_phone_1"
                                               value="{{ $settings['contact_phone_1'] }}"
                                               required
                                               placeholder="Ej: 3132049245">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="contact_default_phone" id="default_phone_1" value="1"
                                                {{ ($settings['contact_default_phone'] ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="default_phone_1">
                                                <i class="fab fa-whatsapp me-1" style="color: #25D366;"></i> Predeterminado
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <label for="contact_phone_2" class="form-label">
                                            Teléfono 2 <small class="text-muted">(opcional)</small>
                                        </label>
                                        <input type="text"
                                               class="form-control"
                                               id="contact_phone_2"
                                               name="contact_phone_2"
                                               value="{{ $settings['contact_phone_2'] }}"
                                               placeholder="Ej: 3158928492">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="contact_default_phone" id="default_phone_2" value="2"
                                                {{ ($settings['contact_default_phone'] ?? '1') == '2' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="default_phone_2">
                                                <i class="fab fa-whatsapp me-1" style="color: #25D366;"></i> Predeterminado
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="company_web" class="form-label">
                                <i class="fas fa-globe me-1"></i>
                                Sitio Web
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="company_web"
                                   name="company_web"
                                   value="{{ $settings['company_web'] }}"
                                   placeholder="Ej: peritos.pritec.co">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-4">
                            <label for="company_description" class="form-label">
                                <i class="fas fa-info-circle me-1"></i>
                                Descripción
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="company_description"
                                   name="company_description"
                                   value="{{ $settings['company_description'] }}"
                                   placeholder="Ej: Peritos e inspecciones técnicas vehiculares Neiva-Huila">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                <i class="fas fa-save me-2" id="saveIcon"></i>
                                Guardar Configuración
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
    // Disable phone 2 default radio if phone 2 is empty
    function togglePhone2Default() {
        const phone2 = document.getElementById('contact_phone_2').value.trim();
        const radio2 = document.getElementById('default_phone_2');
        if (!phone2) {
            radio2.disabled = true;
            if (radio2.checked) {
                document.getElementById('default_phone_1').checked = true;
            }
        } else {
            radio2.disabled = false;
        }
    }

    document.getElementById('contact_phone_2').addEventListener('input', togglePhone2Default);
    document.addEventListener('DOMContentLoaded', togglePhone2Default);

    document.getElementById('settingsForm').addEventListener('submit', function(e) {
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

        fetch('{{ route("settings.update") }}', {
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
                    confirmButtonColor: '#28a745',
                    background: '#1a2332',
                    color: '#fff'
                });
            } else {
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.closest('.mb-3, .mb-4, .col-md-8')?.querySelector('.invalid-feedback');
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
                    confirmButtonColor: '#e74c3c',
                    background: '#1a2332',
                    color: '#fff'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Error al guardar la configuración.',
                icon: 'error',
                confirmButtonColor: '#e74c3c',
                background: '#1a2332',
                color: '#fff'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            loading.classList.remove('show');
        });
    });
</script>
@endpush
