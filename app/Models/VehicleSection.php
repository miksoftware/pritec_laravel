<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleSection extends Model
{
    protected $fillable = [
        'vehicle_type_id',
        'section_name',
        'image_path',
    ];

    /**
     * Vehicle type relationship
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Pieces relationship
     */
    public function pieces(): HasMany
    {
        return $this->hasMany(VehiclePiece::class, 'section_id')->orderBy('piece_number');
    }

    /**
     * Get formatted section name
     */
    public function getFormattedNameAttribute(): string
    {
        return VehicleType::formatSectionName($this->section_name);
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? asset('uploads/vehicle_sections/' . $this->image_path)
            : null;
    }
}
