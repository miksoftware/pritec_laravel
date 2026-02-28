<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expertise_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expertise_id')->constrained('expertises')->cascadeOnDelete();
            $table->enum('section', ['carroceria', 'estructura', 'chasis']);
            $table->unsignedBigInteger('pieza_id');
            $table->unsignedBigInteger('concepto_id');
            $table->text('observacion')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('expertise_id');
            $table->index('section');
            $table->index('pieza_id');
            $table->index('concepto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expertise_inspections');
    }
};
