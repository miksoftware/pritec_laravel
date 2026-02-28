<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertisePhoto extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'expertise_id', 'nombre_original', 'nombre_guardado', 'ruta', 'extension', 'size', 'orden',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function expertise(): BelongsTo
    {
        return $this->belongsTo(Expertise::class);
    }

    public function getUrlAttribute(): string
    {
        return asset($this->ruta);
    }
}
