<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function () use ($section, $inspections, $step, $observacion) {
            // Delete existing inspections for this section
            $this->inspections()->where('section', $section)->delete();

            // Bulk insert new ones with timestamps in a single query
            $now = now();
            $records = [];
            foreach ($inspections as $insp) {
                $records[] = [
                    'expertise_id' => $this->id,
                    'section'      => $section,
                    'pieza_id'     => $insp['pieza_id'],
                    'concepto_id'  => $insp['concepto_id'],
                    'created_at'   => $now,
                ];
            }

            if (!empty($records)) {
                ExpertiseInspection::insert($records);
            }

            // Update observations and step
            $obsColumn = 'observaciones_' . $section;
            $this->update([
                $obsColumn => $observacion,
                'current_step' => max($this->current_step, $step),
                'status' => 'in_progress',
            ]);
        });
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
        $now = now();
        $stats = static::selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
            COUNT(CASE WHEN status IN ('draft', 'in_progress') THEN 1 END) as in_progress,
            COUNT(CASE WHEN MONTH(service_date) = ? AND YEAR(service_date) = ? THEN 1 END) as this_month
        ", [$now->month, $now->year])->first();

        $total = (int) ($stats->total ?? 0);
        $completed = (int) ($stats->completed ?? 0);
        $inProgress = (int) ($stats->in_progress ?? 0);
        $thisMonth = (int) ($stats->this_month ?? 0);
        $totalInspections = ExpertiseInspection::count();
        $totalPhotos = ExpertisePhoto::count();

        return compact('total', 'completed', 'inProgress', 'thisMonth', 'totalInspections', 'totalPhotos');
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

    // ───── Inspection Percentages & Concepts (Carrocería / Estructura) ─────

    /**
     * Map an inspection concept name to its percentage in a given section (carroceria/estructura)
     * Values based on the official technical appraisal scale:
     * Carrocería: B=100%, BR=85%, MR=40%, BP=95%, MP=65%, R=70%, F=30%, DL=75%, DM=50%, DF=20%, S=60%, RY=90%, HD=70%
     * Estructura: B=100%, BR=70%, MR=20%, BP=80%, MP=55%, R=65%, F=10%, DL=60%, DM=30%, DF=0%, S=40%, RY=90%
     */
    public static function getConceptPercentage(string $section, string $conceptName): int
    {
        $normalized = mb_strtolower(trim($conceptName), 'UTF-8');

        if ($section === 'carroceria') {
            return match (true) {
                str_contains($normalized, 'sin afectaci') => 100,
                str_contains($normalized, 'buena rep') => 85,
                str_contains($normalized, 'mala rep') => 40,
                str_contains($normalized, 'bien rep') => 95,
                str_contains($normalized, 'mal rep') => 65,
                str_contains($normalized, 'regular') => 70,
                str_contains($normalized, 'fisurad') => 30,
                str_contains($normalized, 'deformidad leve') => 75,
                str_contains($normalized, 'deformidad med') => 50,
                str_contains($normalized, 'deformidad fuer') => 20,
                str_contains($normalized, 'sumid') => 60,
                str_contains($normalized, 'ray') => 90,
                str_contains($normalized, 'hermeticidad') => 70,
                str_contains($normalized, 'buen') => 100,
                default => 100,
            };
        }

        if ($section === 'estructura') {
            return match (true) {
                str_contains($normalized, 'sin afectaci') => 100,
                str_contains($normalized, 'buena rep') => 70,
                str_contains($normalized, 'mala rep') => 20,
                str_contains($normalized, 'bien rep') => 80,
                str_contains($normalized, 'mal rep') => 55,
                str_contains($normalized, 'regular') => 65,
                str_contains($normalized, 'fisurad') => 10,
                str_contains($normalized, 'deformidad leve') => 60,
                str_contains($normalized, 'deformidad med') => 30,
                str_contains($normalized, 'deformidad fuer') => 0,
                str_contains($normalized, 'sumid') => 40,
                str_contains($normalized, 'ray') => 90,
                str_contains($normalized, 'buen') => 100,
                default => 100,
            };
        }

        return 100;
    }

    /**
     * Check if a concept is critical in Estructura (F=Fisurado, DF=Deformidad fuerte, MR=Mala reparación)
     */
    public static function isConceptCriticalInEstructura(string $conceptName): bool
    {
        $normalized = mb_strtolower(trim($conceptName), 'UTF-8');
        return str_contains($normalized, 'fisurad')
            || str_contains($normalized, 'deformidad fuer')
            || str_contains($normalized, 'mala rep');
    }

    /**
     * Get condition status label, badge class and color from percentage and critical pieces
     * Scale: Bueno 90-100% | Aceptable 60-89% | Malo 0-59%
     * Special rule in Estructura: Con F, DF o MR en cualquier pieza: MALO
     */
    public static function getInspectionSectionStatus(string $section, float $percentage, int $criticalCount = 0): array
    {
        if ($section === 'estructura' && $criticalCount > 0) {
            return [
                'label' => 'Malo',
                'class' => 'estado-peligroso',
                'color' => '#ff3933',
                'is_critical' => true,
            ];
        }

        if ($percentage >= 90) {
            return [
                'label' => 'Bueno',
                'class' => 'estado-nuevas',
                'color' => '#36d048',
                'is_critical' => false,
            ];
        }

        if ($percentage >= 60) {
            return [
                'label' => 'Aceptable',
                'class' => 'estado-precaucion',
                'color' => '#ffff33',
                'is_critical' => false,
            ];
        }

        return [
            'label' => 'Malo',
            'class' => 'estado-peligroso',
            'color' => '#ff3933',
            'is_critical' => false,
        ];
    }
}
