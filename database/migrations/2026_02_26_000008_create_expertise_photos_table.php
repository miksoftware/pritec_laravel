<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expertise_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expertise_id')->constrained('expertises')->cascadeOnDelete();
            $table->string('nombre_original', 255);
            $table->string('nombre_guardado', 255);
            $table->string('ruta', 500);
            $table->string('extension', 10);
            $table->integer('size');
            $table->integer('orden')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('expertise_id');
            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expertise_photos');
    }
};
