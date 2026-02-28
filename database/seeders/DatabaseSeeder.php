<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user (undeletable)
        User::updateOrCreate(
            ['email' => 'admin@pritec.com'],
            [
                'username' => 'admin',
                'full_name' => 'Administrador',
                'password' => 'admin123',
                'status' => 'active',
                'is_admin' => true,
            ]
        );

        // System settings
        $settings = [
            ['setting_key' => 'app_name', 'setting_value' => 'Pritec', 'description' => 'Nombre de la aplicación'],
            ['setting_key' => 'app_version', 'setting_value' => '3.0.0', 'description' => 'Versión de la aplicación'],
            ['setting_key' => 'maintenance_mode', 'setting_value' => '0', 'description' => 'Modo de mantenimiento (0=off, 1=on)'],
            ['setting_key' => 'registration_enabled', 'setting_value' => '1', 'description' => 'Permitir registro de nuevos usuarios (0=no, 1=si)'],
            ['setting_key' => 'session_timeout', 'setting_value' => '3600', 'description' => 'Tiempo de expiración de sesión en segundos'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
