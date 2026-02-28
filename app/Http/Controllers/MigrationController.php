<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\VehicleType;
use App\Models\VehicleSection;
use App\Models\VehiclePiece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MigrationController extends Controller
{
    /**
     * Show migration form (admin only)
     */
    public function index()
    {
        if (Auth::user()->email !== 'admin@pritec.com') {
            abort(403, 'No autorizado');
        }

        return view('migration.index');
    }

    /**
     * Process SQL file and import data
     */
    public function process(Request $request)
    {
        if (Auth::user()->email !== 'admin@pritec.com') {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'sql_file' => 'required|file|max:51200',
            'import_types' => 'required|array|min:1',
        ]);

        $file = $request->file('sql_file');
        $path = $file->getRealPath() ?: $file->getPathname();
        $sqlContent = file_get_contents($path);

        $results = [
            'vehicle_types' => ['imported' => 0, 'skipped' => 0, 'errors' => []],
            'vehicle_sections' => ['imported' => 0, 'skipped' => 0, 'errors' => []],
            'vehicle_pieces' => ['imported' => 0, 'skipped' => 0, 'errors' => []],
            'clients' => ['imported' => 0, 'skipped' => 0, 'errors' => []],
        ];

        DB::beginTransaction();

        try {
            $importTypes = $request->input('import_types');

            // Disable foreign key checks for clean import
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Clean tables before import
            if (in_array('vehicle_types', $importTypes)) {
                VehiclePiece::query()->delete();
                VehicleSection::query()->delete();
                VehicleType::query()->delete();
            }

            if (in_array('clients', $importTypes)) {
                Client::query()->delete();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Maps old IDs to new IDs
            $vehicleTypeMap = [];
            $sectionMap = [];

            if (in_array('vehicle_types', $importTypes)) {
                $this->importVehicleTypes($sqlContent, $results, $vehicleTypeMap);
                $this->importVehicleSections($sqlContent, $results, $vehicleTypeMap, $sectionMap);
                $this->importVehiclePieces($sqlContent, $results, $sectionMap);
            }

            if (in_array('clients', $importTypes)) {
                $this->importClients($sqlContent, $results);
            }

            DB::commit();

            return redirect()->route('migration.index')
                ->with('success', 'Migración completada exitosamente.')
                ->with('results', $results);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('migration.index')
                ->with('error', 'Error durante la migración: ' . $e->getMessage());
        }
    }

    /**
     * Parse INSERT statements from SQL for a given table
     */
    private function parseInserts(string $sql, string $table): array
    {
        $rows = [];
        // Match INSERT INTO `table` (...) VALUES followed by multiple value rows
        $pattern = '/INSERT INTO `' . preg_quote($table) . '`\s*\(([^)]+)\)\s*VALUES\s*(.*?);/s';

        if (!preg_match($pattern, $sql, $match)) {
            return $rows;
        }

        // Parse column names
        $columns = array_map(function ($col) {
            return trim($col, " `\t\n\r");
        }, explode(',', $match[1]));

        // Parse each row using a proper parser that handles parentheses in values
        $valuesStr = $match[2];
        $rowStrings = $this->extractRows($valuesStr);

        foreach ($rowStrings as $rowStr) {
            $values = $this->parseSqlRow($rowStr);
            if (count($values) === count($columns)) {
                $row = array_combine($columns, $values);
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Extract individual row strings from VALUES clause, handling nested parentheses and quotes
     */
    private function extractRows(string $valuesStr): array
    {
        $rows = [];
        $i = 0;
        $len = strlen($valuesStr);

        while ($i < $len) {
            // Find opening parenthesis
            if ($valuesStr[$i] === '(') {
                $i++; // skip (
                $depth = 1;
                $row = '';
                $inString = false;
                $escape = false;

                while ($i < $len && $depth > 0) {
                    $char = $valuesStr[$i];

                    if ($escape) {
                        $row .= $char;
                        $escape = false;
                        $i++;
                        continue;
                    }

                    if ($char === '\\') {
                        $escape = true;
                        $row .= $char;
                        $i++;
                        continue;
                    }

                    if ($char === "'" ) {
                        $inString = !$inString;
                        $row .= $char;
                        $i++;
                        continue;
                    }

                    if (!$inString) {
                        if ($char === '(') $depth++;
                        if ($char === ')') {
                            $depth--;
                            if ($depth === 0) {
                                $i++;
                                break;
                            }
                        }
                    }

                    $row .= $char;
                    $i++;
                }

                $rows[] = $row;
            } else {
                $i++;
            }
        }

        return $rows;
    }

    /**
     * Parse a single SQL row values string handling quoted strings
     */
    private function parseSqlRow(string $rowStr): array
    {
        $values = [];
        $current = '';
        $inString = false;
        $escape = false;

        for ($i = 0; $i < strlen($rowStr); $i++) {
            $char = $rowStr[$i];

            if ($escape) {
                $current .= $char;
                $escape = false;
                continue;
            }

            if ($char === '\\') {
                $escape = true;
                continue;
            }

            if ($char === "'" && !$inString) {
                $inString = true;
                continue;
            }

            if ($char === "'" && $inString) {
                // Check for escaped quote ''
                if ($i + 1 < strlen($rowStr) && $rowStr[$i + 1] === "'") {
                    $current .= "'";
                    $i++;
                    continue;
                }
                $inString = false;
                continue;
            }

            if ($char === ',' && !$inString) {
                $values[] = $this->cleanSqlValue(trim($current));
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $values[] = $this->cleanSqlValue(trim($current));

        return $values;
    }

    /**
     * Clean a parsed SQL value
     */
    private function cleanSqlValue(string $val): ?string
    {
        if (strtoupper($val) === 'NULL') {
            return null;
        }
        return stripslashes($val);
    }

    /**
     * Import vehicle types
     */
    private function importVehicleTypes(string $sql, array &$results, array &$map): void
    {
        $rows = $this->parseInserts($sql, 'vehicle_types');

        foreach ($rows as $row) {
            $oldId = $row['id'];
            $existing = VehicleType::where('name', $row['name'])->where('type', $row['type'])->first();

            if ($existing) {
                $map[$oldId] = $existing->id;
                $results['vehicle_types']['skipped']++;
                continue;
            }

            $vt = VehicleType::create([
                'type' => $row['type'],
                'name' => $row['name'],
                'description' => $row['description'] ?? '',
                'status' => $row['status'] ?? 'active',
            ]);

            $map[$oldId] = $vt->id;
            $results['vehicle_types']['imported']++;
        }
    }

    /**
     * Import vehicle sections
     */
    private function importVehicleSections(string $sql, array &$results, array $typeMap, array &$sectionMap): void
    {
        $rows = $this->parseInserts($sql, 'vehicle_sections');

        foreach ($rows as $row) {
            $oldId = $row['id'];
            $oldTypeId = $row['vehicle_type_id'];

            if (!isset($typeMap[$oldTypeId])) {
                $results['vehicle_sections']['errors'][] = "Sección #{$oldId}: tipo de vehículo #{$oldTypeId} no encontrado";
                continue;
            }

            $newTypeId = $typeMap[$oldTypeId];

            // Check if section already exists for this type
            $existing = VehicleSection::where('vehicle_type_id', $newTypeId)
                ->where('section_name', $row['section_name'])
                ->first();

            if ($existing) {
                $sectionMap[$oldId] = $existing->id;
                // Update image if the old one had one and the new one doesn't
                if ($row['image_path'] && !$existing->image_path) {
                    $existing->update(['image_path' => $row['image_path']]);
                }
                $results['vehicle_sections']['skipped']++;
                continue;
            }

            $section = VehicleSection::create([
                'vehicle_type_id' => $newTypeId,
                'section_name' => $row['section_name'],
                'image_path' => $row['image_path'],
            ]);

            $sectionMap[$oldId] = $section->id;
            $results['vehicle_sections']['imported']++;
        }
    }

    /**
     * Import vehicle pieces
     * Old system stored positions as pixels (in a 400x400 container).
     * New system stores positions as percentages (0-100).
     */
    private function importVehiclePieces(string $sql, array &$results, array $sectionMap): void
    {
        // Old system canvas size was 400x400 pixels
        $oldCanvasWidth = 400;
        $oldCanvasHeight = 400;

        $rows = $this->parseInserts($sql, 'vehicle_pieces');

        foreach ($rows as $row) {
            $oldSectionId = $row['section_id'];

            if (!isset($sectionMap[$oldSectionId])) {
                $results['vehicle_pieces']['errors'][] = "Pieza #{$row['id']}: sección #{$oldSectionId} no encontrada";
                continue;
            }

            $newSectionId = $sectionMap[$oldSectionId];

            // Check if piece already exists
            $existing = VehiclePiece::where('section_id', $newSectionId)
                ->where('piece_number', $row['piece_number'])
                ->first();

            if ($existing) {
                $results['vehicle_pieces']['skipped']++;
                continue;
            }

            // Convert pixel positions to percentages
            $posX = round(((float) $row['position_x'] / $oldCanvasWidth) * 100, 2);
            $posY = round(((float) $row['position_y'] / $oldCanvasHeight) * 100, 2);

            // Clamp to 0-100 range
            $posX = max(0, min(100, $posX));
            $posY = max(0, min(100, $posY));

            VehiclePiece::create([
                'section_id' => $newSectionId,
                'piece_number' => (int) $row['piece_number'],
                'piece_name' => $row['piece_name'],
                'position_x' => $posX,
                'position_y' => $posY,
            ]);

            $results['vehicle_pieces']['imported']++;
        }
    }

    /**
     * Import clients
     */
    private function importClients(string $sql, array &$results): void
    {
        $rows = $this->parseInserts($sql, 'clients');

        foreach ($rows as $row) {
            $existing = Client::where('identification', $row['identification'])->first();

            if ($existing) {
                $results['clients']['skipped']++;
                continue;
            }

            Client::create([
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'identification' => $row['identification'],
                'phone' => $row['phone'],
                'address' => $row['address'],
                'email' => $row['email'],
                'status' => $row['status'] ?? 'active',
            ]);

            $results['clients']['imported']++;
        }
    }
}
