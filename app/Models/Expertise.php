<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Expertise extends Model
{
    protected $fillable = [
        'codigo', 'client_id', 'user_id', 'vehicle_type_id',
        // Step 1
        'service_date', 'service_number', 'service_for', 'agreement',
        // Step 2
        'placa', 'marca', 'linea', 'modelo', 'color',
        'clase_vehiculo', 'tipo_vehiculo', 'tipo_carroceria', 'tipo_combustible',
        'numero_motor', 'numero_chasis', 'numero_serie', 'vin',
        'kilometraje', 'cilindrada', 'capacidad_carga', 'numero_ejes', 'numero_pasajeros',
        'fecha_matricula', 'organismo_transito', 'codigo_fasecolda', 'valor_fasecolda',
        'valor_sugerido', 'valor_accesorios',
        // Observations
        'observaciones_carroceria', 'observaciones_estructura', 'observaciones_chasis',
        // Step 6: Tires
        'llanta_anterior_izquierda', 'llanta_anterior_derecha',
        'llanta_posterior_izquierda', 'llanta_posterior_derecha',
        'observaciones_llantas',
        // Step 7: Shocks
        'amortiguador_anterior_izquierdo', 'amortiguador_anterior_derecho',
        'amortiguador_posterior_izquierdo', 'amortiguador_posterior_derecho',
        'cant_amortiguadores_delanteros', 'cant_amortiguadores_traseros',
        'observaciones_amortiguadores',
        // Step 8: Battery
        'prueba_bateria', 'prueba_arranque', 'carga_bateria', 'observaciones_bateria',
        // Step 9: Motor/Systems (JSON)
        'motor_sistemas_data', 'observaciones_motor', 'observaciones_interior',
        // Step 10: Leaks/Levels (JSON)
        'fugas_niveles_data', 'prueba_ruta', 'observaciones_fugas',
        // Control
        'status', 'current_step', 'total_fotos',
    ];

    protected $casts = [
        'service_date' => 'date',
        'fecha_matricula' => 'date',
        'motor_sistemas_data' => 'array',
        'fugas_niveles_data' => 'array',
        'llanta_anterior_izquierda' => 'decimal:2',
        'llanta_anterior_derecha' => 'decimal:2',
        'llanta_posterior_izquierda' => 'decimal:2',
        'llanta_posterior_derecha' => 'decimal:2',
        'amortiguador_anterior_izquierdo' => 'decimal:2',
        'amortiguador_anterior_derecho' => 'decimal:2',
        'amortiguador_posterior_izquierdo' => 'decimal:2',
        'amortiguador_posterior_derecho' => 'decimal:2',
        'prueba_bateria' => 'decimal:2',
        'prueba_arranque' => 'decimal:2',
        'carga_bateria' => 'decimal:2',
    ];

    // ───── Relationships ─────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(ExpertiseInspection::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ExpertisePhoto::class)->orderBy('orden');
    }

    // ───── Scopes ─────

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['draft', 'in_progress']);
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    // ───── Accessors ─────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Borrador',
            'in_progress' => 'En Progreso',
            'completed' => 'Completado',
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            default => $this->status,
        };
    }

    public function getVehicleTypeNameAttribute(): string
    {
        return $this->vehicleType?->name ?? 'N/A';
    }

    public function getIsCarAttribute(): bool
    {
        return $this->vehicleType?->type === 'carro';
    }

    public function getIsMotoAttribute(): bool
    {
        return $this->vehicleType?->type === 'moto';
    }

    // ───── Draft Creation ─────

    public static function createDraft(array $data): self
    {
        $data['codigo'] = 'PRT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $data['status'] = 'draft';
        $data['current_step'] = 1;
        $data['placa'] = $data['placa'] ?? '';

        return static::create($data);
    }

    // ───── Step Updates ─────

    public function updateStep2(array $data): bool
    {
        return $this->update(array_merge($data, [
            'current_step' => max($this->current_step, 2),
            'status' => 'in_progress',
        ]));
    }

    public function updateInspections(string $section, array $inspections, int $step, ?string $observacion = null): void
    {
        // Delete existing inspections for this section
        $this->inspections()->where('section', $section)->delete();

        // Insert new ones
        foreach ($inspections as $insp) {
            $this->inspections()->create([
                'section' => $section,
                'pieza_id' => $insp['pieza_id'],
                'concepto_id' => $insp['concepto_id'],
            ]);
        }

        // Update observations and step
        $obsColumn = 'observaciones_' . $section;
        $this->update([
            $obsColumn => $observacion,
            'current_step' => max($this->current_step, $step),
            'status' => 'in_progress',
        ]);
    }

    public function updateStep6(array $data): bool
    {
        return $this->update(array_merge($data, [
            'current_step' => max($this->current_step, 6),
            'status' => 'in_progress',
        ]));
    }

    public function updateStep7(array $data): bool
    {
        return $this->update(array_merge($data, [
            'current_step' => max($this->current_step, 7),
            'status' => 'in_progress',
        ]));
    }

    public function updateStep8(array $data): bool
    {
        return $this->update(array_merge($data, [
            'current_step' => max($this->current_step, 8),
            'status' => 'in_progress',
        ]));
    }

    public function updateStep9(array $data): bool
    {
        return $this->update([
            'motor_sistemas_data' => $data,
            'observaciones_motor' => $data['observaciones_motor'] ?? null,
            'observaciones_interior' => $data['observaciones_interior'] ?? null,
            'current_step' => max($this->current_step, 9),
            'status' => 'in_progress',
        ]);
    }

    public function updateStep10(array $data): bool
    {
        return $this->update([
            'fugas_niveles_data' => $data,
            'prueba_ruta' => $data['prueba_ruta'] ?? null,
            'observaciones_fugas' => $data['observaciones_fugas'] ?? null,
            'current_step' => max($this->current_step, 10),
            'status' => 'in_progress',
        ]);
    }

    public function completeExpertise(): bool
    {
        return $this->update([
            'status' => 'completed',
            'current_step' => 12,
        ]);
    }

    // ───── Inspections by Section ─────

    public function getInspectionsBySection(string $section)
    {
        return $this->inspections()->where('section', $section)->orderBy('id')->get();
    }

    // ───── Statistics ─────

    public static function getStatistics(): array
    {
        $total = static::completed()->count();
        $thisMonth = static::completed()
            ->whereMonth('service_date', now()->month)
            ->whereYear('service_date', now()->year)
            ->count();
        $totalInspections = ExpertiseInspection::count();
        $totalPhotos = ExpertisePhoto::count();

        return compact('total', 'thisMonth', 'totalInspections', 'totalPhotos');
    }

    // ───── Step Navigation Helpers ─────

    public function getNextStep(int $currentStep): int
    {
        $next = $currentStep + 1;
        if ($next === 3 && $this->is_moto) return 4;
        return $next;
    }

    public function getPreviousStep(int $currentStep): int
    {
        $prev = $currentStep - 1;
        if ($prev === 3 && $this->is_moto) return 2;
        return $prev;
    }

    public function getTotalSteps(): int
    {
        return $this->is_moto ? 11 : 12;
    }

    public function getValidSteps(): array
    {
        if ($this->is_moto) {
            return [1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        }
        return [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    }
}
