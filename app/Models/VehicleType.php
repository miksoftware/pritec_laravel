<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    protected $fillable = [
        'type',
        'name',
        'description',
        'status',
    ];

    /**
     * Sections relationship
     */
    public function sections(): HasMany
    {
        return $this->hasMany(VehicleSection::class)
            ->orderByRaw("FIELD(section_name, 'carroceria', 'estructura', 'chasis')");
    }

    /**
     * Check if active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the default sections for this vehicle type
     */
    public function getDefaultSectionNames(): array
    {
        return $this->type === 'carro'
            ? ['carroceria', 'estructura', 'chasis']
            : ['estructura', 'chasis'];
    }

    /**
     * Create default sections for this vehicle type
     */
    public function createDefaultSections(): void
    {
        foreach ($this->getDefaultSectionNames() as $sectionName) {
            $this->sections()->create(['section_name' => $sectionName]);
        }
    }

    /**
     * Get statistics for all vehicle types
     */
    public static function getStatistics(): array
    {
        return [
            'total'     => static::count(),
            'activos'   => static::where('status', 'active')->count(),
            'inactivos' => static::where('status', 'inactive')->count(),
            'carros'    => static::where('type', 'carro')->count(),
            'motos'     => static::where('type', 'moto')->count(),
        ];
    }

    /**
     * Format section name for display
     */
    public static function formatSectionName(string $sectionName): string
    {
        return match ($sectionName) {
            'carroceria' => 'Carrocería',
            'estructura' => 'Estructura',
            'chasis'     => 'Chasis',
            default      => ucfirst($sectionName),
        };
    }
}
