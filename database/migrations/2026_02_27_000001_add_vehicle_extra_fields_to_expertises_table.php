<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expertises', function (Blueprint $table) {
            $table->string('organismo_transito', 100)->nullable()->after('fecha_matricula');
            $table->string('codigo_fasecolda', 100)->nullable()->after('organismo_transito');
            $table->string('valor_fasecolda', 100)->nullable()->after('codigo_fasecolda');
            $table->string('valor_sugerido', 100)->nullable()->after('valor_fasecolda');
            $table->string('valor_accesorios', 100)->nullable()->after('valor_sugerido');
        });
    }

    public function down(): void
    {
        Schema::table('expertises', function (Blueprint $table) {
            $table->dropColumn(['organismo_transito', 'codigo_fasecolda', 'valor_fasecolda', 'valor_sugerido', 'valor_accesorios']);
        });
    }
};
