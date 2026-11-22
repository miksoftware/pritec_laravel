<?php

namespace App\Http\Controllers;

use App\Models\Expertise;
use App\Models\ExpertiseInspection;
use App\Models\ExpertisePhoto;
use App\Models\InspectionConcept;
use App\Models\Client;
use App\Models\VehicleType;
use App\Models\VehicleSection;
use App\Models\VehiclePiece;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpertiseController extends Controller
{
    // ═══════════════════════════════════════════
    //  INDEX — Listado de peritajes
    // ═══════════════════════════════════════════

    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $month = $request->get('month', '');
        $status = $request->get('status', 'all');

        $query = Expertise::with(['client', 'vehicleType', 'user'])
            ->withCount(['inspections', 'photos']);

        // Filtro por estado
        if ($status === 'completed') {
            $query->completed();
        } elseif ($status === 'in_progress') {
            $query->inProgress();
        }

        // Filtro de búsqueda por texto
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('service_number', 'like', "%{$search}%")
                  ->orWhere('placa', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('linea', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('identification', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por mes (año-mes ej: 2026-06 o 2026-07 en service_date o created_at)
        if ($month) {
            $query->where(function ($q) use ($month) {
                $q->whereRaw("DATE_FORMAT(service_date, '%Y-%m') = ?", [$month])
                  ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
            });
        }

        $expertises = $query->orderByDesc('service_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $statistics = Expertise::getStatistics();

        $statusCounts = [
            'all' => $statistics['total'],
            'in_progress' => $statistics['inProgress'],
            'completed' => $statistics['completed'],
        ];

        return view('expertise.index', compact('expertises', 'statistics', 'statusCounts', 'search', 'month', 'status'));
    }

    // ═══════════════════════════════════════════
    //  PASO 1 — Información del Servicio y Cliente
    // ═══════════════════════════════════════════

    public function create()
    {
        return view('expertise.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_date' => 'required|date',
            'service_number' => 'required|string|max:100',
            'service_for' => 'required|string|max:100',
            'client_id' => 'required|exists:clients,id',
        ]);

        try {
            $expertise = Expertise::createDraft([
                'client_id' => $request->client_id,
                'user_id' => Auth::id(),
                'service_date' => $request->service_date,
                'service_number' => $request->service_number,
                'service_for' => $request->service_for,
                'agreement' => $request->agreement ?? '',
            ]);

            session(['expertise_id' => $expertise->id]);

            return response()->json([
                'success' => true,
                'message' => 'Peritaje iniciado correctamente',
                'redirect' => route('expertise.step', ['expertise' => $expertise->id, 'step' => 2]),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════
    //  SHOW STEP — Método genérico para pasos 2-11
    // ═══════════════════════════════════════════

    public function step(Expertise $expertise, int $step)
    {
        session(['expertise_id' => $expertise->id]);
        $expertise->load(['client', 'vehicleType']);

        // Moto skips step 3
        if ($step === 3 && $expertise->is_moto) {
            return redirect()->route('expertise.step', ['expertise' => $expertise->id, 'step' => 4]);
        }

        $viewData = ['expertise' => $expertise, 'step' => $step];

        return match ($step) {
            2 => view('expertise.step-vehicle', $viewData),
            3, 4, 5 => $this->showInspectionStep($expertise, $step, $viewData),
            6 => view('expertise.step-tires', $viewData),
            7 => view('expertise.step-shocks', $viewData),
            8 => view('expertise.step-battery', $viewData),
            9 => view('expertise.step-motor', $viewData),
            10 => view('expertise.step-leaks', $viewData),
            11 => $this->showPhotosStep($expertise, $viewData),
            12 => $this->showSummary($expertise),
            default => redirect()->route('expertise.index'),
        };
    }

    private function showInspectionStep(Expertise $expertise, int $step, array $viewData)
    {
        $sectionMap = [3 => 'carroceria', 4 => 'estructura', 5 => 'chasis'];
        $section = $sectionMap[$step];
        $existingInspections = $expertise->getInspectionsBySection($section);

        return view('expertise.step-inspection', array_merge($viewData, [
            'section' => $section,
            'existingInspections' => $existingInspections,
        ]));
    }

    private function showPhotosStep(Expertise $expertise, array $viewData)
    {
        $photos = $expertise->photos;
        return view('expertise.step-photos', array_merge($viewData, ['photos' => $photos]));
    }

    // ═══════════════════════════════════════════
    //  SAVE STEP — Método genérico para guardar pasos
    // ═══════════════════════════════════════════

    public function saveStep(Request $request, Expertise $expertise, int $step)
    {
        try {
            match ($step) {
                2 => $this->saveVehicleStep($request, $expertise),
                3 => $this->saveInspectionStep($request, $expertise, 'carroceria', 3),
                4 => $this->saveInspectionStep($request, $expertise, 'estructura', 4),
                5 => $this->saveInspectionStep($request, $expertise, 'chasis', 5),
                6 => $this->saveTiresStep($request, $expertise),
                7 => $this->saveShocksStep($request, $expertise),
                8 => $this->saveBatteryStep($request, $expertise),
                9 => $this->saveMotorStep($request, $expertise),
                10 => $this->saveLeaksStep($request, $expertise),
                11 => $this->savePhotosStep($request, $expertise),
                default => throw new \Exception('Paso no válido'),
            };

            $nextStep = $expertise->getNextStep($step);

            return response()->json([
                'success' => true,
                'message' => 'Paso guardado correctamente',
                'redirect' => route('expertise.step', ['expertise' => $expertise->id, 'step' => $nextStep]),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ───── Step 2: Vehicle Data ─────

    private function saveVehicleStep(Request $request, Expertise $expertise): void
    {
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'placa' => 'required|string|max:20',
        ]);

        $expertise->updateStep2([
            'vehicle_type_id' => $request->vehicle_type_id,
            'tipo_vehiculo' => $request->vehicle_type_id,
            'placa' => strtoupper(trim($request->placa)),
            'marca' => $request->marca,
            'linea' => $request->linea,
            'modelo' => $request->modelo,
            'color' => $request->color,
            'clase_vehiculo' => $request->clase_vehiculo,
            'tipo_carroceria' => $request->tipo_carroceria,
            'tipo_combustible' => $request->tipo_combustible,
            'numero_motor' => $request->numero_motor,
            'numero_chasis' => $request->numero_chasis,
            'numero_serie' => $request->numero_serie,
            'vin' => $request->vin,
            'kilometraje' => $request->kilometraje,
            'cilindrada' => $request->cilindrada,
            'capacidad_carga' => $request->capacidad_carga,
            'numero_ejes' => $request->numero_ejes,
            'numero_pasajeros' => $request->numero_pasajeros,
            'fecha_matricula' => $request->fecha_matricula,
            'organismo_transito' => $request->organismo_transito,
            'codigo_fasecolda' => $request->codigo_fasecolda,
            'valor_fasecolda' => $request->valor_fasecolda,
            'valor_sugerido' => $request->valor_sugerido,
            'valor_accesorios' => $request->valor_accesorios,
        ]);
    }

    // ───── Steps 3/4/5: Inspections ─────

    private function saveInspectionStep(Request $request, Expertise $expertise, string $section, int $step): void
    {
        $piezas = $request->input('pieza_id', []);
        $conceptos = $request->input('concepto_id', []);

        $inspections = [];
        for ($i = 0; $i < count($piezas); $i++) {
            if (!empty($piezas[$i]) && !empty($conceptos[$i])) {
                $inspections[] = [
                    'pieza_id' => $piezas[$i],
                    'concepto_id' => $conceptos[$i],
                ];
            }
        }

        if (empty($inspections)) {
            throw new \Exception('Debe agregar al menos una inspección de pieza');
        }

        $observacionKey = 'observaciones_' . $section;
        $expertise->updateInspections($section, $inspections, $step, $request->input($observacionKey));
    }

    // ───── Step 6: Tires ─────

    private function saveTiresStep(Request $request, Expertise $expertise): void
    {
        $request->validate([
            'llanta_anterior_izquierda' => 'nullable|numeric|min:0|max:100',
            'llanta_anterior_derecha' => 'nullable|numeric|min:0|max:100',
            'llanta_posterior_izquierda' => 'nullable|numeric|min:0|max:100',
            'llanta_posterior_derecha' => 'nullable|numeric|min:0|max:100',
            'observaciones_llantas' => 'nullable|string|max:2000',
        ]);

        $expertise->updateStep6([
            'llanta_anterior_izquierda' => $request->input('llanta_anterior_izquierda', 0),
            'llanta_anterior_derecha' => $request->input('llanta_anterior_derecha', 0),
            'llanta_posterior_izquierda' => $request->input('llanta_posterior_izquierda', 0),
            'llanta_posterior_derecha' => $request->input('llanta_posterior_derecha', 0),
            'observaciones_llantas' => $request->observaciones_llantas,
        ]);
    }

    // ───── Step 7: Shocks ─────

    private function saveShocksStep(Request $request, Expertise $expertise): void
    {
        $request->validate([
            'amortiguador_anterior_izquierdo' => 'nullable|numeric|min:0|max:100',
            'amortiguador_anterior_derecho' => 'nullable|numeric|min:0|max:100',
            'amortiguador_posterior_izquierdo' => 'nullable|numeric|min:0|max:100',
            'amortiguador_posterior_derecho' => 'nullable|numeric|min:0|max:100',
            'cant_amortiguadores_delanteros' => 'nullable|integer|in:1,2',
            'cant_amortiguadores_traseros' => 'nullable|integer|in:1,2',
            'observaciones_amortiguadores' => 'nullable|string|max:2000',
        ]);

        $expertise->updateStep7([
            'amortiguador_anterior_izquierdo' => $request->input('amortiguador_anterior_izquierdo', 0),
            'amortiguador_anterior_derecho' => $request->input('amortiguador_anterior_derecho', 0),
            'amortiguador_posterior_izquierdo' => $request->input('amortiguador_posterior_izquierdo', 0),
            'amortiguador_posterior_derecho' => $request->input('amortiguador_posterior_derecho', 0),
            'cant_amortiguadores_delanteros' => $request->input('cant_amortiguadores_delanteros', 1),
            'cant_amortiguadores_traseros' => $request->input('cant_amortiguadores_traseros', 1),
            'observaciones_amortiguadores' => $request->observaciones_amortiguadores,
        ]);
    }

    // ───── Step 8: Battery ─────

    private function saveBatteryStep(Request $request, Expertise $expertise): void
    {
        $request->validate([
            'prueba_bateria' => 'nullable|numeric|min:0|max:100',
            'prueba_arranque' => 'nullable|numeric|min:0|max:100',
            'carga_bateria' => 'nullable|numeric|min:0|max:100',
            'observaciones_bateria' => 'nullable|string|max:2000',
        ]);

        $expertise->updateStep8([
            'prueba_bateria' => $request->input('prueba_bateria', 0),
            'prueba_arranque' => $request->input('prueba_arranque', 0),
            'carga_bateria' => $request->input('carga_bateria', 0),
            'observaciones_bateria' => $request->observaciones_bateria,
        ]);
    }

    // ───── Step 9: Motor & Systems (JSON) ─────

    private function saveMotorStep(Request $request, Expertise $expertise): void
    {
        $motorFields = [
            'estado_arranque', 'respuesta_arranque',
            'estado_radiador', 'respuesta_radiador',
            'estado_carter_motor', 'respuesta_carter_motor',
            'estado_carter_caja', 'respuesta_carter_caja',
            'estado_caja_velocidades', 'respuesta_caja_velocidades',
            'estado_soporte_caja', 'respuesta_soporte_caja',
            'estado_soporte_motor', 'respuesta_soporte_motor',
            'estado_mangueras_radiador', 'respuesta_mangueras_radiador',
            'estado_correas', 'respuesta_correas',
            'tension_correas', 'respuesta_tension_correas',
            'estado_filtro_aire', 'respuesta_filtro_aire',
            'estado_externo_bateria', 'respuesta_externo_bateria',
            'estado_pastilla_freno', 'respuesta_pastilla_freno',
            'estado_discos_freno', 'respuesta_discos_freno',
            'estado_punta_eje', 'respuesta_punta_eje',
            'estado_axiales', 'respuesta_axiales',
            'estado_terminales', 'respuesta_terminales',
            'estado_rotulas', 'respuesta_rotulas',
            'estado_tijeras', 'respuesta_tijeras',
            'estado_caja_direccion', 'respuesta_caja_direccion',
            'estado_rodamientos', 'respuesta_rodamientos',
            'estado_cardan', 'respuesta_cardan',
            'estado_crucetas', 'respuesta_crucetas',
            'estado_calefaccion', 'respuesta_calefaccion',
            'estado_aire_acondicionado', 'respuesta_aire_acondicionado',
            'estado_cinturones', 'respuesta_cinturones',
            'estado_tapiceria_asientos', 'respuesta_tapiceria_asientos',
            'estado_tapiceria_techo', 'respuesta_tapiceria_techo',
            'estado_millaret', 'respuesta_millaret',
            'estado_alfombra', 'respuesta_alfombra',
            'estado_chapas', 'respuesta_chapas',
        ];

        $data = [];
        foreach ($motorFields as $field) {
            $data[$field] = $request->input($field, '');
        }
        $data['observaciones_motor'] = $request->input('observaciones_motor', '');
        $data['observaciones_interior'] = $request->input('observaciones_interior', '');

        $expertise->updateStep9($data);
    }

    // ───── Step 10: Leaks & Levels (JSON) ─────

    private function saveLeaksStep(Request $request, Expertise $expertise): void
    {
        $leakFields = [
            'respuesta_fuga_aceite_motor',
            'respuesta_fuga_aceite_caja_velocidades',
            'respuesta_fuga_aceite_caja_transmision',
            'respuesta_fuga_liquido_frenos',
            'respuesta_fuga_aceite_direccion_hidraulica',
            'respuesta_fuga_liquido_bomba_embrague',
            'respuesta_fuga_tanque_combustible',
            'respuesta_estado_tanque_silenciador',
            'respuesta_estado_tubo_exhosto',
            'respuesta_estado_tanque_catalizador_gases',
            'respuesta_estado_guardapolvo_caja_direccion',
            'respuesta_estado_tuberia_frenos',
            'respuesta_viscosidad_aceite_motor',
            'respuesta_nivel_refrigerante_motor',
            'respuesta_nivel_liquido_frenos',
            'respuesta_nivel_agua_limpiavidrios',
            'respuesta_nivel_aceite_direccion_hidraulica',
            'respuesta_nivel_liquido_embrague',
            'respuesta_nivel_aceite_motor',
        ];

        $data = [];
        foreach ($leakFields as $field) {
            $data[$field] = $request->input($field, '');
        }
        $data['prueba_ruta'] = $request->input('prueba_ruta', '');
        $data['observaciones_fugas'] = $request->input('observaciones_fugas', '');

        $expertise->updateStep10($data);
    }

    // ───── Step 11: Photos ─────

    private function savePhotosStep(Request $request, Expertise $expertise): void
    {
        $request->validate([
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|mimes:jpeg,jpg,png,webp|max:10240',
        ], [
            'fotos.*.image' => 'El archivo debe ser una imagen válida.',
            'fotos.*.mimes' => 'Solo se permiten formatos JPG, PNG o WEBP.',
            'fotos.*.max' => 'Cada imagen no debe exceder los 10MB.',
        ]);

        $files = $request->file('fotos', []);

        // Filter only valid uploaded files
        $newFiles = [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $newFiles[] = $file;
            }
        }

        DB::transaction(function () use ($expertise, $newFiles) {
            $uploadDir = 'uploads/expertises/' . $expertise->id;
            $fullUploadDir = public_path($uploadDir);
            if (!is_dir($fullUploadDir)) {
                mkdir($fullUploadDir, 0755, true);
            }

            // Get current max order
            $maxOrder = $expertise->photos()->max('orden') ?? 0;

            // Only add new photos, keep existing ones
            foreach ($newFiles as $file) {
                $maxOrder++;
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize() ?? 0;
                $savedName = 'foto_' . $maxOrder . '_' . time() . '.' . $extension;

                $file->move($fullUploadDir, $savedName);

                ExpertisePhoto::create([
                    'expertise_id' => $expertise->id,
                    'nombre_original' => $originalName,
                    'nombre_guardado' => $savedName,
                    'ruta' => $uploadDir . '/' . $savedName,
                    'extension' => $extension,
                    'size' => $fileSize,
                    'orden' => $maxOrder,
                ]);
            }

            $expertise->update([
                'total_fotos' => $expertise->photos()->count(),
                'current_step' => max($expertise->current_step, 11),
                'status' => 'in_progress',
            ]);
        });
    }

    // ═══════════════════════════════════════════
    //  PASO 12 — Resumen y Completar
    // ═══════════════════════════════════════════

    private function showSummary(Expertise $expertise)
    {
        $expertise->load([
            'client',
            'vehicleType',
            'photos',
            'inspections.piece',
            'inspections.concept',
        ]);

        $grouped = $expertise->inspections->groupBy('section');
        $inspCarroceria = $grouped->get('carroceria', collect())->sortBy('id');
        $inspEstructura = $grouped->get('estructura', collect())->sortBy('id');
        $inspChasis = $grouped->get('chasis', collect())->sortBy('id');

        return view('expertise.summary', [
            'expertise' => $expertise,
            'inspCarroceria' => $inspCarroceria,
            'inspEstructura' => $inspEstructura,
            'inspChasis' => $inspChasis,
            'viewMode' => false,
            'step' => 12,
        ]);
    }

    public function complete(Request $request, Expertise $expertise)
    {
        try {
            if ($expertise->current_step < 11) {
                throw new \Exception('Debe completar todos los pasos antes de finalizar');
            }

            $expertise->completeExpertise();
            session()->forget('expertise_id');

            return response()->json([
                'success' => true,
                'message' => '¡Peritaje completado! Código: ' . $expertise->codigo,
                'redirect' => route('expertise.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ═══════════════════════════════════════════
    //  SHOW — Ver peritaje completado (read-only)
    // ═══════════════════════════════════════════

    public function show(Expertise $expertise)
    {
        $expertise->load([
            'client',
            'vehicleType',
            'photos',
            'inspections.piece',
            'inspections.concept',
        ]);

        $grouped = $expertise->inspections->groupBy('section');
        $inspCarroceria = $grouped->get('carroceria', collect())->sortBy('id');
        $inspEstructura = $grouped->get('estructura', collect())->sortBy('id');
        $inspChasis = $grouped->get('chasis', collect())->sortBy('id');

        return view('expertise.summary', [
            'expertise' => $expertise,
            'inspCarroceria' => $inspCarroceria,
            'inspEstructura' => $inspEstructura,
            'inspChasis' => $inspChasis,
            'viewMode' => true,
            'step' => 12,
        ]);
    }

    // ═══════════════════════════════════════════
    //  DELETE — Eliminar peritaje con relaciones
    // ═══════════════════════════════════════════

    public function destroy(Expertise $expertise)
    {
        try {
            DB::transaction(function () use ($expertise) {
                // Delete photo files
                foreach ($expertise->photos as $photo) {
                    $fullPath = public_path($photo->ruta);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }

                $uploadDir = public_path('uploads/expertises/' . $expertise->id);
                if (is_dir($uploadDir)) {
                    @rmdir($uploadDir);
                }

                // Cascade deletes inspections and photos (FK cascade)
                $expertise->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Peritaje eliminado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ═══════════════════════════════════════════
    //  AJAX ENDPOINTS
    // ═══════════════════════════════════════════

    public function searchClients(Request $request)
    {
        $search = $request->get('search', '');
        if (strlen($search) < 3) {
            return response()->json(['success' => false, 'message' => 'Mínimo 3 caracteres']);
        }

        $clients = Client::where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('identification', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('first_name')
            ->limit(20)
            ->get(['id', 'first_name', 'last_name', 'identification', 'phone', 'email', 'address']);

        return response()->json(['success' => true, 'clients' => $clients, 'count' => $clients->count()]);
    }

    public function searchVehicleTypes(Request $request)
    {
        $search = $request->get('search', '');

        $query = VehicleType::where('status', 'active');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $types = $query->orderByDesc('type')->orderBy('name')->limit(20)
            ->get(['id', 'name', 'description', 'type']);

        return response()->json(['success' => true, 'vehicle_types' => $types, 'count' => $types->count()]);
    }

    public function getPieces(Request $request)
    {
        $vehicleTypeId = (int) $request->get('vehicle_type_id', 0);
        $section = $request->get('section', 'carroceria');

        $pieces = VehiclePiece::join('vehicle_sections', 'vehicle_pieces.section_id', '=', 'vehicle_sections.id')
            ->where('vehicle_sections.vehicle_type_id', $vehicleTypeId)
            ->where('vehicle_sections.section_name', $section)
            ->orderBy('vehicle_pieces.piece_number')
            ->get(['vehicle_pieces.id', 'vehicle_pieces.piece_number', 'vehicle_pieces.piece_name']);

        return response()->json(['success' => true, 'pieces' => $pieces, 'count' => $pieces->count()]);
    }

    public function getConcepts(Request $request)
    {
        $category = $request->get('category', 'carroceria');
        $concepts = InspectionConcept::getForSection($category);

        return response()->json(['success' => true, 'concepts' => $concepts, 'count' => $concepts->count()]);
    }

    // ═══════════════════════════════════════════
    //  GENERATE PDF — Formato imprimible
    // ═══════════════════════════════════════════

    public function deletePhoto(ExpertisePhoto $photo)
    {
        try {
            DB::transaction(function () use ($photo) {
                $fullPath = public_path($photo->ruta);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }

                $expertiseId = $photo->expertise_id;
                $photo->delete();

                // Update photo count
                $count = ExpertisePhoto::where('expertise_id', $expertiseId)->count();
                Expertise::where('id', $expertiseId)->update(['total_fotos' => $count]);
            });

            return response()->json(['success' => true, 'message' => 'Foto eliminada.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar la foto.'], 500);
        }
    }

    public function generatePdf(Expertise $expertise)
    {
        $expertise->load(['client', 'vehicleType', 'inspections', 'photos']);

        $photos = $expertise->photos;

        // Pre-load all vehicle sections for this vehicle type in a single query
        $sections = VehicleSection::where('vehicle_type_id', $expertise->vehicle_type_id)
            ->get()
            ->keyBy('section_name');

        // Get inspection data with images for each section
        $carroceria = $this->getInspectionDataWithImage($expertise, 'carroceria', $sections);
        $estructura = $this->getInspectionDataWithImage($expertise, 'estructura', $sections);
        $chasis = $this->getInspectionDataWithImage($expertise, 'chasis', $sections);

        // Field maps for motor/systems and liquids/leaks tables
        $campos_tren_motriz = [
            'estado_punta_eje' => 'Punta eje',
            'estado_discos_freno' => 'Discos freno',
            'estado_pastilla_freno' => 'Pastilla freno',
            'estado_axiales' => 'Axiales',
            'estado_terminales' => 'Terminales',
            'estado_rotulas' => 'Rótulas',
            'estado_chapas' => 'Chapas',
            'estado_caja_direccion' => 'Caja dirección',
            'estado_rodamientos' => 'Rodamientos',
            'estado_cardan' => 'Cardán',
            'estado_crucetas' => 'Crucetas',
        ];

        $campos_motor = [
            'estado_arranque' => 'Arranque',
            'estado_radiador' => 'Radiador',
            'estado_carter_motor' => 'Carter motor',
            'estado_carter_caja' => 'Carter caja',
            'estado_caja_velocidades' => 'Caja de velocidades',
            'estado_soporte_caja' => 'Soporte caja',
            'estado_soporte_motor' => 'Estado soporte motor',
            'estado_mangueras_radiador' => 'Estado mangueras radiador',
            'estado_correas' => 'Estado correas',
            'tension_correas' => 'Tensión correas',
            'estado_filtro_aire' => 'Estado filtro de aire',
            'estado_externo_bateria' => 'Estado externo baterías',
        ];

        $campos_interior = [
            'estado_calefaccion' => 'Calefacción',
            'estado_aire_acondicionado' => 'Aire acondicionado',
            'estado_cinturones' => 'Cinturones',
            'estado_tapiceria_asientos' => 'Tapicería asientos',
            'estado_tapiceria_techo' => 'Tapicería Techo',
            'estado_millaret' => 'Millaret',
            'estado_alfombra' => 'Alfombra',
            'estado_chapas' => 'Chapas',
        ];

        $campos_fugas = [
            'estado_fuga_aceite_motor' => 'Fuga aceite motor',
            'estado_fuga_aceite_caja_velocidades' => 'Fuga aceite caja de velocidades',
            'estado_fuga_aceite_caja_transmision' => 'Fuga aceite caja de transmisión',
            'estado_fuga_liquido_frenos' => 'Fuga líquido de frenos',
            'estado_fuga_aceite_direccion_hidraulica' => 'Fuga aceite dirección hidráulica',
            'estado_fuga_liquido_bomba_embrague' => 'Fuga líquido bomba embrague',
            'estado_fuga_tanque_combustible' => 'Fuga tanque de combustible',
        ];

        $campos_estado_componentes = [
            'estado_comp_tanque_silenciador' => 'Tanque silenciador',
            'estado_comp_tubo_exhosto' => 'Tubo exhosto',
            'estado_comp_tanque_catalizador_gases' => 'Catalizador de gases',
            'estado_comp_guardapolvo_caja_direccion' => 'Guardapolvo caja dirección',
            'estado_comp_tuberia_frenos' => 'Tubería frenos',
        ];

        $campos_liquidos = [
            'estado_viscosidad_aceite_motor' => 'Viscosidad aceite motor',
            'estado_nivel_refrigerante_motor' => 'Nivel refrigerante motor',
            'estado_nivel_liquido_frenos' => 'Nivel líquido de frenos',
            'estado_nivel_agua_limpiavidrios' => 'Nivel agua limpiavidrios',
            'estado_nivel_aceite_direccion_hidraulica' => 'Nivel aceite dirección hidráulica',
            'estado_nivel_liquido_embrague' => 'Nivel líquido embrague',
            'estado_nivel_aceite_motor' => 'Nivel aceite motor',
        ];

        $settings = SystemSetting::getValues([
            'contact_phone_1' => '3132049245',
            'contact_phone_2' => '3158928492',
            'contact_default_phone' => '1',
            'company_name' => 'SALA TÉCNICA EN AUTOMOTORES',
            'company_subtitle' => 'CERTIFICACIÓN TÉCNICA EN IDENTIFICACIÓN DE AUTOMOTORES',
            'company_address' => 'Carrera 16 No. 18-197 Barrio Tenerife',
            'company_web' => 'peritos.pritec.co',
            'company_description' => 'Peritos e inspecciones técnicas vehiculares Neiva-Huila',
        ]);

        $phone1 = $settings['contact_phone_1'];
        $phone2 = $settings['contact_phone_2'];
        $defaultPhone = $settings['contact_default_phone'];
        $phonesDisplay = $phone1 . ($phone2 ? '-' . $phone2 : '');
        $whatsappPhone = $defaultPhone === '2' && $phone2 ? $phone2 : $phone1;

        $company = [
            'name' => $settings['company_name'],
            'subtitle' => $settings['company_subtitle'],
            'address' => $settings['company_address'],
            'phones' => $phonesDisplay,
            'phone_1' => $phone1,
            'phone_2' => $phone2,
            'whatsapp_phone' => $whatsappPhone,
            'web' => $settings['company_web'],
            'description' => $settings['company_description'],
        ];

        return view('expertise.pdf', compact(
            'expertise', 'photos',
            'carroceria', 'estructura', 'chasis',
            'campos_tren_motriz', 'campos_liquidos', 'campos_motor',
            'campos_interior', 'campos_fugas', 'campos_estado_componentes',
            'company'
        ));
    }

    /**
     * Get inspection data with section image for PDF
     */
    private function getInspectionDataWithImage(Expertise $expertise, string $section, $sections = null): array
    {
        // Get section image
        $sectionRecord = $sections ? ($sections[$section] ?? null) : VehicleSection::where('vehicle_type_id', $expertise->vehicle_type_id)
            ->where('section_name', $section)
            ->first();

        $imagePath = '';
        $totalSectionPieces = 0;
        if ($sectionRecord) {
            if ($sectionRecord->image_path) {
                $imagePath = 'uploads/vehicle_sections/' . $sectionRecord->image_path;
            }
            $totalSectionPieces = VehiclePiece::where('section_id', $sectionRecord->id)->count();
        }

        // Get pieces with positions and concepts
        $rawPieces = DB::table('expertise_inspections as ei')
            ->join('vehicle_pieces as vp', 'ei.pieza_id', '=', 'vp.id')
            ->join('inspection_concepts as ic', 'ei.concepto_id', '=', 'ic.id')
            ->where('ei.expertise_id', $expertise->id)
            ->where('ei.section', $section)
            ->orderBy('vp.piece_number')
            ->select('vp.piece_number', 'vp.piece_name', 'vp.position_x', 'vp.position_y', 'ic.name as concept_name')
            ->get();

        $pieces = [];
        $sumPercentages = 0;
        $criticalCount = 0;

        foreach ($rawPieces as $p) {
            $pct = Expertise::getConceptPercentage($section, $p->concept_name);
            $isCritical = ($section === 'estructura') && Expertise::isConceptCriticalInEstructura($p->concept_name);
            if ($isCritical) {
                $criticalCount++;
            }
            $sumPercentages += $pct;

            $pieces[] = [
                'piece_number' => $p->piece_number,
                'piece_name' => $p->piece_name,
                'position_x' => $p->position_x,
                'position_y' => $p->position_y,
                'concept_name' => $p->concept_name,
                'percentage' => $pct,
                'is_critical' => $isCritical,
            ];
        }

        $evaluatedCount = count($pieces);
        $averagePercentage = $evaluatedCount > 0 ? round($sumPercentages / $evaluatedCount, 1) : 100.0;
        $statusInfo = Expertise::getInspectionSectionStatus($section, $averagePercentage, $criticalCount);

        return [
            'image' => $imagePath,
            'pieces' => $pieces,
            'total_section_pieces' => $totalSectionPieces,
            'evaluated_count' => $evaluatedCount,
            'average_percentage' => $averagePercentage,
            'critical_count' => $criticalCount,
            'status_label' => $statusInfo['label'],
            'status_class' => $statusInfo['class'],
            'status_color' => $statusInfo['color'],
            'is_critical' => $statusInfo['is_critical'],
        ];
    }
}
