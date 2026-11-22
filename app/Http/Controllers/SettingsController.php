<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::getValues([
            'company_name' => '',
            'company_subtitle' => '',
            'company_address' => '',
            'contact_phone_1' => '',
            'contact_phone_2' => '',
            'contact_default_phone' => '1',
            'company_web' => '',
            'company_description' => '',
        ]);

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_subtitle' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'contact_phone_1' => 'required|string|max:20',
            'contact_phone_2' => 'nullable|string|max:20',
            'contact_default_phone' => 'required|in:1,2',
            'company_web' => 'nullable|string|max:255',
            'company_description' => 'nullable|string|max:500',
        ]);

        $keys = [
            'company_name', 'company_subtitle', 'company_address',
            'contact_phone_1', 'contact_phone_2', 'contact_default_phone',
            'company_web', 'company_description',
        ];

        DB::transaction(function () use ($keys, $request) {
            foreach ($keys as $key) {
                SystemSetting::setValue($key, $request->input($key, ''));
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Configuración actualizada correctamente',
        ]);
    }
}
