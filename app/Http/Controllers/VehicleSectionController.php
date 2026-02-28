<?php

namespace App\Http\Controllers;

use App\Models\VehiclePiece;
use App\Models\VehicleSection;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class VehicleSectionController extends Controller
{
    /**
     * Show sections for a vehicle type
     */
    public function index(VehicleType $vehicleType)
    {
        $sections = $vehicleType->sections()->withCount('pieces')->get();

        return view('vehicle-types.sections', compact('vehicleType', 'sections'));
    }

    /**
     * Upload section image (resize to 400x400)
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:vehicle_sections,id',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'image.required' => 'Selecciona una imagen.',
            'image.mimes' => 'Solo se permiten imágenes JPG, JPEG y PNG.',
            'image.max' => 'La imagen no puede pesar más de 5MB.',
        ]);

        try {
            $section = VehicleSection::findOrFail($request->section_id);
            $file = $request->file('image');

            // Ensure upload directory exists
            $uploadDir = public_path('uploads/vehicle_sections');
            if (!File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            // Generate unique filename
            $fileName = 'section_' . $section->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $uploadDir . '/' . $fileName;

            // Resize image to 400x400 using GD
            $this->resizeImage($file->getPathname(), $filePath, 400, 400);

            // Delete old image if exists
            if ($section->image_path && File::exists($uploadDir . '/' . $section->image_path)) {
                File::delete($uploadDir . '/' . $section->image_path);
            }

            // Update database
            $section->update(['image_path' => $fileName]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen subida exitosamente.',
                'image_url' => asset('uploads/vehicle_sections/' . $fileName),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resize image using GD library
     */
    private function resizeImage(string $source, string $destination, int $width, int $height): bool
    {
        $imageInfo = getimagesize($source);
        $mime = $imageInfo['mime'];

        $sourceImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($source),
            'image/png'  => imagecreatefrompng($source),
            default      => throw new \Exception('Tipo de imagen no soportado'),
        };

        $destImage = imagecreatetruecolor($width, $height);

        // Preserve PNG transparency
        if ($mime === 'image/png') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $width, $height, $transparent);
        }

        imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $width, $height, imagesx($sourceImage), imagesy($sourceImage));

        $result = match ($mime) {
            'image/jpeg' => imagejpeg($destImage, $destination, 90),
            'image/png'  => imagepng($destImage, $destination),
            default      => false,
        };

        imagedestroy($sourceImage);
        imagedestroy($destImage);

        return $result;
    }

    /**
     * Show pieces management view for a section
     */
    public function pieces(VehicleSection $section)
    {
        $section->load('vehicleType');
        $vehicleType = $section->vehicleType;
        $allSections = $vehicleType->sections()->withCount('pieces')->get();
        $pieces = $section->pieces;

        // Find next section
        $currentIndex = $allSections->search(fn($s) => $s->id === $section->id);
        $nextSection = $allSections->get($currentIndex + 1);

        return view('vehicle-types.pieces', compact('vehicleType', 'section', 'allSections', 'pieces', 'nextSection'));
    }

    /**
     * Add piece to a section
     */
    public function addPiece(Request $request)
    {
        $request->validate([
            'section_id'   => 'required|exists:vehicle_sections,id',
            'piece_number' => 'required|integer|min:1',
            'piece_name'   => 'nullable|string|max:100',
            'position_x'   => 'required|numeric|min:0|max:100',
            'position_y'   => 'required|numeric|min:0|max:100',
        ], [
            'piece_number.required' => 'El número de pieza es obligatorio.',
            'position_x.required' => 'La posición X es obligatoria.',
            'position_y.required' => 'La posición Y es obligatoria.',
        ]);

        try {
            // Check unique piece number in section
            $exists = VehiclePiece::where('section_id', $request->section_id)
                ->where('piece_number', $request->piece_number)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una pieza con este número en esta sección.',
                ], 422);
            }

            $piece = VehiclePiece::create([
                'section_id'   => $request->section_id,
                'piece_number' => $request->piece_number,
                'piece_name'   => $request->piece_name ?? '',
                'position_x'   => $request->position_x,
                'position_y'   => $request->position_y,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pieza agregada exitosamente.',
                'piece' => $piece,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar la pieza.',
            ], 500);
        }
    }

    /**
     * Update a piece
     */
    public function updatePiece(Request $request)
    {
        $request->validate([
            'piece_id'     => 'required|exists:vehicle_pieces,id',
            'piece_number' => 'required|integer|min:1',
            'piece_name'   => 'nullable|string|max:100',
            'position_x'   => 'required|numeric|min:0|max:100',
            'position_y'   => 'required|numeric|min:0|max:100',
        ]);

        try {
            $piece = VehiclePiece::findOrFail($request->piece_id);

            // Check unique piece number (excluding current)
            $exists = VehiclePiece::where('section_id', $piece->section_id)
                ->where('piece_number', $request->piece_number)
                ->where('id', '!=', $piece->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una pieza con este número en esta sección.',
                ], 422);
            }

            $piece->update([
                'piece_number' => $request->piece_number,
                'piece_name'   => $request->piece_name ?? '',
                'position_x'   => $request->position_x,
                'position_y'   => $request->position_y,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pieza actualizada exitosamente.',
                'piece' => $piece->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la pieza.',
            ], 500);
        }
    }

    /**
     * Update piece position only (drag & drop)
     */
    public function updatePiecePosition(Request $request)
    {
        $request->validate([
            'piece_id'   => 'required|exists:vehicle_pieces,id',
            'position_x' => 'required|numeric|min:0|max:100',
            'position_y' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $piece = VehiclePiece::findOrFail($request->piece_id);
            $piece->update([
                'position_x' => $request->position_x,
                'position_y' => $request->position_y,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Posición actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la posición.',
            ], 500);
        }
    }

    /**
     * Delete a piece
     */
    public function deletePiece(Request $request)
    {
        $request->validate(['piece_id' => 'required|exists:vehicle_pieces,id']);

        try {
            VehiclePiece::findOrFail($request->piece_id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pieza eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la pieza.',
            ], 500);
        }
    }

    /**
     * Clear all pieces from a section
     */
    public function clearPieces(Request $request)
    {
        $request->validate(['section_id' => 'required|exists:vehicle_sections,id']);

        try {
            VehiclePiece::where('section_id', $request->section_id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Todas las piezas han sido eliminadas.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las piezas.',
            ], 500);
        }
    }
}
