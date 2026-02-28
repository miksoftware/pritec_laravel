<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InspectionConceptSeeder extends Seeder
{
    public function run(): void
    {
        $concepts = [
            // Common to all categories
            ['name' => 'Bueno', 'category' => 'all', 'display_order' => 1],
            ['name' => 'Buena reparación', 'category' => 'all', 'display_order' => 2],
            ['name' => 'Mala reparación', 'category' => 'all', 'display_order' => 3],
            ['name' => 'Bien repintado', 'category' => 'all', 'display_order' => 4],
            ['name' => 'Mal repintado', 'category' => 'all', 'display_order' => 5],
            ['name' => 'Regular', 'category' => 'all', 'display_order' => 6],
            ['name' => 'Regular (Oxidación- corrosión)', 'category' => 'all', 'display_order' => 7],
            ['name' => 'Fisurado', 'category' => 'all', 'display_order' => 8],
            ['name' => 'Deformidad media', 'category' => 'all', 'display_order' => 9],
            ['name' => 'Deformidad fuerte', 'category' => 'all', 'display_order' => 10],
            ['name' => 'Sumido', 'category' => 'all', 'display_order' => 11],
            ['name' => 'Rayón', 'category' => 'all', 'display_order' => 12],
            // Specific to carroceria
            ['name' => 'Hermeticidad deficiente', 'category' => 'carroceria', 'display_order' => 13],
            // Specific to chasis
            ['name' => 'Regular (soldadura no original)', 'category' => 'chasis', 'display_order' => 14],
        ];

        foreach ($concepts as $concept) {
            DB::table('inspection_concepts')->updateOrInsert(
                ['name' => $concept['name'], 'category' => $concept['category']],
                array_merge($concept, ['status' => 'active', 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
