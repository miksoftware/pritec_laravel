<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_type_id')->constrained()->cascadeOnDelete();
            $table->enum('section_name', ['carroceria', 'estructura', 'chasis']);
            $table->string('image_path', 255)->nullable();
            $table->timestamps();

            $table->unique(['vehicle_type_id', 'section_name']);
            $table->index('vehicle_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_sections');
    }
};
