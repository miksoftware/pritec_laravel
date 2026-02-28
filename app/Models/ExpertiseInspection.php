<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertiseInspection extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'expertise_id', 'section', 'pieza_id', 'concepto_id', 'observacion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function expertise(): BelongsTo
    {
        return $this->belongsTo(Expertise::class);
    }

    public function piece(): BelongsTo
    {
        return $this->belongsTo(VehiclePiece::class, 'pieza_id');
    }

    public function concept(): BelongsTo
    {
        return $this->belongsTo(InspectionConcept::class, 'concepto_id');
    }
}
