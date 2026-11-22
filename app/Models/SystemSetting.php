<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue(string $key, $default = null): ?string
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Get multiple setting values at once
     */
    public static function getValues(array $keysWithDefaults = []): array
    {
        if (empty($keysWithDefaults)) {
            return [];
        }

        $isAssoc = !array_is_list($keysWithDefaults);
        $keys = $isAssoc ? array_keys($keysWithDefaults) : $keysWithDefaults;
        $defaults = $isAssoc ? $keysWithDefaults : [];

        $settings = static::whereIn('setting_key', $keys)
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? ($defaults[$key] ?? null);
        }

        return $result;
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, string $value): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }
}
