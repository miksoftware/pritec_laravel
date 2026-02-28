<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePiece extends Model
{
    protected $fillable = [
        'section_id',
        'piece_number',
        'piece_name',
        'position_x',
        'position_y',
    ];

    protected $casts = [
        'position_x' => 'float',
        'position_y' => 'float',
        'piece_number' => 'integer',
    ];

    /**
     * Section relationship
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(VehicleSection::class, 'section_id');
    }
}
