<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instalación - Pritec</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #0f1923; color: #e0e6ed; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .install-card { background: #1a2332; border: 1px solid #2a3a4a; border-radius: 12px; max-width: 560px; width: 100%; }
        .install-header { background: linear-gradient(135deg, #1a2332, #243447); border-bottom: 1px solid #2a3a4a; border-radius: 12px 12px 0 0; padding: 2rem; text-align: center; }
        .install-header h2 { color: #3498db; margin: 0; font-weight: 700; }
        .install-header p { color: #8899aa; margin: 0.5rem 0 0; font-size: 0.9rem; }
        .install-body { padding: 2rem; }
        .form-control { background: #0f1923; border: 1px solid #2a3a4a; color: #e0e6ed; }
        .form-control:focus { background: #0f1923; border-color: #3498db; color: #e0e6ed; box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.15); }
        .form-label { color: #8899aa; font-size: 0.82rem; font-weight: 500; }
        .btn-test { background: transparent; border: 1px solid #2ecc71; color: #2ecc71; }
        .btn-test:hover { background: #2ecc71; color: #fff; }
        .btn-install { background: #3498db; border: none; color: #fff; font-weight: 600; }
        .btn-install:hover { background: #2980b9; color: #fff; }
        .connection-status { font-size: 0.82rem; margin-top: 0.5rem; }
        .step-badge { background: #3498db; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; margin-right: 0.5rem; }
        .section-title { font-size: 0.95rem; font-weight: 600; color: #e0e6ed; margin-bottom: 1rem; display: flex; align-items: center; }
        .success-screen { text-align: center; padding: 3rem 2rem; }
        .success-screen i { font-size: 4rem; color: #2ecc71; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="install-card">
        <div class="install-header">
            <h2><i class="fas fa-clipboard-check me-2"></i>Pritec</h2>
            <p>Asistente de instalación</p>
        </div>

        @if(session('success'))
            <div class="success-screen">
                <i class="fas fa-check-circle"></i>
                <h4 class="mb-2">Instalación completada</h4>
                <p class="text-muted mb-3">El sistema está listo. Puedes iniciar sesión con:</p>
                <div class="bg-dark rounded p-3 mb-3 text-start" style="font-size: 0.85rem;">
                    <div><span class="text-muted">Email:</span> admin@pritec.com</div>
                    <div><span class="text-muted">Contraseña:</span> admin123</div>
                </div>
                <p class="text-danger small mb-3"><i class="fas fa-exclamation-triangle me-1"></i>Cambia la contraseña después de iniciar sesión.</p>
                <a href="/login" class="btn btn-install px-4">
                    <i class="fas fa-sign-in-alt me-1"></i> Ir al Login
                </a>
            </div>
        @else
            <div class="install-body">
                @if(session('error'))
                    <div class="alert alert-danger py-2" style="font-size: 0.85rem;">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
                    </div>
                @endif

                <form action="/install/process" method="POST" id="installForm">
                    @csrf

                    {{-- App URL --}}
                    <div class="section-title"><span class="step-badge">1</span> URL de la aplicación</div>
                    <div class="mb-4">
                        <label class="form-label">URL del sitio</label>
                        <input type="url" name="app_url" class="form-control" value="{{ old('app_url', request()->getSchemeAndHttpHost()) }}" required placeholder="https://tudominio.com">
                        @error('app_url') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Database --}}
                    <div class="section-title"><span class="step-badge">2</span> Conexión a base de datos</div>

                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label">Host</label>
                            <input type="text" name="db_host" id="db_host" class="form-control" value="{{ old('db_host', '127.0.0.1') }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Puerto</label>
                            <input type="number" name="db_port" id="db_port" class="form-control" value="{{ old('db_port', '3306') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre de la base de datos</label>
                        <input type="text" name="db_database" id="db_database" class="form-control" value="{{ old('db_database') }}" required placeholder="pritec_db">
                        @error('db_database') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="db_username" id="db_username" class="form-control" value="{{ old('db_username', 'root') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="db_password" id="db_password" class="form-control" value="{{ old('db_password') }}" placeholder="(vacío si no tiene)">
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="button" class="btn btn-test btn-sm" id="btnTest" onclick="testConnection()">
                            <i class="fas fa-plug me-1"></i> Probar Conexión
                        </button>
                        <div id="connectionStatus" class="connection-status"></div>
                    </div>

                    {{-- Install --}}
                    <div class="section-title"><span class="step-badge">3</span> Instalar</div>
                    <p class="text-muted mb-3" style="font-size: 0.82rem;">
                        Se ejecutarán las migraciones y se creará el usuario administrador.
                    </p>

                    <button type="submit" class="btn btn-install w-100 py-2" id="btnInstall">
                        <i class="fas fa-rocket me-1"></i> Instalar Pritec
                    </button>
                </form>
            </div>
        @endif
    </div>

    <script>
        function testConnection() {
            var btn = document.getElementById('btnTest');
            var status = document.getElementById('connectionStatus');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Probando...';
            status.innerHTML = '';

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('db_host', document.getElementById('db_host').value);
            formData.append('db_port', document.getElementById('db_port').value);
            formData.append('db_database', document.getElementById('db_database').value);
            formData.append('db_username', document.getElementById('db_username').value);
            formData.append('db_password', document.getElementById('db_password').value);

            fetch('/install/test-connection', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plug me-1"></i> Probar Conexión';
                if (data.success) {
                    status.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>' + data.message + '</span>';
                } else {
                    status.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>' + data.message + '</span>';
                }
            })
            .catch(function(err) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plug me-1"></i> Probar Conexión';
                status.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Error de conexión</span>';
            });
        }

        document.getElementById('installForm').addEventListener('submit', function() {
            var btn = document.getElementById('btnInstall');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Instalando... esto puede tardar unos segundos';
        });
    </script>
</body>
</html>
