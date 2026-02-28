<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    /**
     * Show installer page
     */
    public function index()
    {
        if (file_exists(storage_path('installed'))) {
            return redirect('/');
        }

        return view('install.index');
    }

    /**
     * Test database connection via AJAX
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}",
                $request->db_username,
                $request->db_password ?? '',
                [\PDO::ATTR_TIMEOUT => 5]
            );
            $pdo->query('SELECT 1');

            return response()->json(['success' => true, 'message' => 'Conexión exitosa a la base de datos.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'No se pudo conectar: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Process installation
     */
    public function process(Request $request)
    {
        if (file_exists(storage_path('installed'))) {
            return redirect('/');
        }

        $request->validate([
            'app_url' => 'required|url',
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            // 1. Update .env file
            $this->updateEnv([
                'APP_NAME' => 'Pritec',
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'APP_URL' => $request->app_url,
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_database,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password ?? '',
            ]);

            // 2. Clear config cache so new .env values are used
            Artisan::call('config:clear');

            // 3. Re-set the database config at runtime
            config([
                'database.connections.mysql.host' => $request->db_host,
                'database.connections.mysql.port' => $request->db_port,
                'database.connections.mysql.database' => $request->db_database,
                'database.connections.mysql.username' => $request->db_username,
                'database.connections.mysql.password' => $request->db_password ?? '',
            ]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // 4. Generate app key if not set
            if (empty(config('app.key')) || config('app.key') === 'base64:') {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // 5. Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // 6. Run seeders
            Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            Artisan::call('db:seed', ['--class' => 'InspectionConceptSeeder', '--force' => true]);

            // 7. Mark as installed
            file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));

            // 8. Clear caches for clean start
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect('/install')->with('success', true);

        } catch (\Exception $e) {
            return redirect('/install')
                ->withInput()
                ->with('error', 'Error durante la instalación: ' . $e->getMessage());
        }
    }

    /**
     * Update .env file values
     */
    private function updateEnv(array $values): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            // Wrap in quotes if contains spaces
            $formatted = str_contains($value, ' ') ? '"' . $value . '"' : $value;

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$formatted}", $envContent);
            } else {
                $envContent .= "\n{$key}={$formatted}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
