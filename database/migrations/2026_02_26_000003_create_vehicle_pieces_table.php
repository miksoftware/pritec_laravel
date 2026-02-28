<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('vehicle_sections')->cascadeOnDelete();
            $table->integer('piece_number');
            $table->string('piece_name', 100);
            $table->decimal('position_x', 5, 2)->nullable();
            $table->decimal('position_y', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['section_id', 'piece_number']);
            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_pieces');
    }
};
