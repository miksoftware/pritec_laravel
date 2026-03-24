<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class CompanySettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'company_name',
                'setting_value' => 'SALA TÉCNICA EN AUTOMOTORES',
                'description' => 'Nombre de la empresa',
            ],
            [
                'setting_key' => 'company_subtitle',
                'setting_value' => 'CERTIFICACIÓN TÉCNICA EN IDENTIFICACIÓN DE AUTOMOTORES',
                'description' => 'Subtítulo de la empresa',
            ],
            [
                'setting_key' => 'company_address',
                'setting_value' => 'Carrera 16 No. 18-197 Barrio Tenerife',
                'description' => 'Dirección de la empresa',
            ],
            [
                'setting_key' => 'contact_phone_1',
                'setting_value' => '3132049245',
                'description' => 'Teléfono de contacto 1',
            ],
            [
                'setting_key' => 'contact_phone_2',
                'setting_value' => '3158928492',
                'description' => 'Teléfono de contacto 2',
            ],
            [
                'setting_key' => 'contact_default_phone',
                'setting_value' => '1',
                'description' => 'Teléfono predeterminado para WhatsApp (1 o 2)',
            ],
            [
                'setting_key' => 'company_web',
                'setting_value' => 'peritos.pritec.co',
                'description' => 'Sitio web de la empresa',
            ],
            [
                'setting_key' => 'company_description',
                'setting_value' => 'Peritos e inspecciones técnicas vehiculares Neiva-Huila',
                'description' => 'Descripción de la empresa',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }

        // Remove old company_phones key if it exists
        SystemSetting::where('setting_key', 'company_phones')->delete();
    }
}
