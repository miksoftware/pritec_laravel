<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleTypeController extends Controller
{
    /**
     * List vehicle types with search, pagination, and statistics
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $vehicleTypes = VehicleType::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $statistics = VehicleType::getStatistics();

        return view('vehicle-types.index', compact('vehicleTypes', 'search', 'statistics'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('vehicle-types.create');
    }

    /**
     * Store new vehicle type + auto-create sections
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:carro,moto',
            'name' => 'required|string|min:3|max:100|unique:vehicle_types,name',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'type.required' => 'Selecciona un tipo de vehículo.',
            'type.in' => 'Tipo de vehículo no válido.',
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.unique' => 'Ya existe un tipo de vehículo con este nombre.',
        ]);

        try {
            $vehicleType = VehicleType::create($request->only('type', 'name', 'description', 'status'));

            // Auto-create default sections
            $vehicleType->createDefaultSections();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de vehículo creado exitosamente.',
                'redirect' => route('vehicle-types.sections', $vehicleType),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de vehículo.',
            ], 500);
        }
    }

    /**
     * Show edit form
     */
    public function edit(VehicleType $vehicleType)
    {
        return view('vehicle-types.edit', compact('vehicleType'));
    }

    /**
     * Update vehicle type
     */
    public function update(Request $request, VehicleType $vehicleType)
    {
        $request->validate([
            'type' => 'required|in:carro,moto',
            'name' => ['required', 'string', 'min:3', 'max:100', Rule::unique('vehicle_types')->ignore($vehicleType->id)],
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ], [
            'type.required' => 'Selecciona un tipo de vehículo.',
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.unique' => 'Ya existe un tipo de vehículo con este nombre.',
        ]);

        try {
            $vehicleType->update($request->only('type', 'name', 'description', 'status'));

            return response()->json([
                'success' => true,
                'message' => 'Tipo de vehículo actualizado exitosamente.',
                'redirect' => route('vehicle-types.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de vehículo.',
            ], 500);
        }
    }

    /**
     * Delete vehicle type (cascades to sections and pieces)
     */
    public function destroy(VehicleType $vehicleType)
    {
        try {
            $vehicleType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de vehículo eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de vehículo.',
            ], 500);
        }
    }

    /**
     * Toggle status active/inactive
     */
    public function toggleStatus(VehicleType $vehicleType)
    {
        try {
            $newStatus = $vehicleType->status === 'active' ? 'inactive' : 'active';
            $vehicleType->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente.',
                'new_status' => $newStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado.',
            ], 500);
        }
    }
}
