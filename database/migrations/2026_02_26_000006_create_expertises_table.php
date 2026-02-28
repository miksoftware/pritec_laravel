<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expertises', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código único PRT-YYYYMMDD-XXXXXX');

            // Relations
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->nullOnDelete();

            // Step 1: Service info
            $table->date('service_date');
            $table->string('service_number', 100)->nullable();
            $table->string('service_for', 100)->nullable();
            $table->string('agreement', 100)->nullable();

            // Step 2: Vehicle data
            $table->string('placa', 20)->default('');
            $table->string('marca', 100)->nullable();
            $table->string('linea', 100)->nullable();
            $table->string('modelo', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('clase_vehiculo', 50)->nullable();
            $table->string('tipo_vehiculo', 50)->nullable();
            $table->string('tipo_carroceria', 50)->nullable();
            $table->string('tipo_combustible', 50)->nullable();
            $table->string('numero_motor', 100)->nullable();
            $table->string('numero_chasis', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->string('vin', 100)->nullable();
            $table->string('kilometraje', 50)->nullable();
            $table->string('cilindrada', 50)->nullable();
            $table->string('capacidad_carga', 50)->nullable();
            $table->string('numero_ejes', 20)->nullable();
            $table->string('numero_pasajeros', 20)->nullable();
            $table->date('fecha_matricula')->nullable();

            // Step 3/4/5: Observations per section
            $table->text('observaciones_carroceria')->nullable();
            $table->text('observaciones_estructura')->nullable();
            $table->text('observaciones_chasis')->nullable();

            // Step 6: Tires (%)
            $table->decimal('llanta_anterior_izquierda', 5, 2)->nullable()->default(0);
            $table->decimal('llanta_anterior_derecha', 5, 2)->nullable()->default(0);
            $table->decimal('llanta_posterior_izquierda', 5, 2)->nullable()->default(0);
            $table->decimal('llanta_posterior_derecha', 5, 2)->nullable()->default(0);
            $table->text('observaciones_llantas')->nullable();

            // Step 7: Shock absorbers (%)
            $table->decimal('amortiguador_anterior_izquierdo', 5, 2)->nullable()->default(0);
            $table->decimal('amortiguador_anterior_derecho', 5, 2)->nullable()->default(0);
            $table->decimal('amortiguador_posterior_izquierdo', 5, 2)->nullable()->default(0);
            $table->decimal('amortiguador_posterior_derecho', 5, 2)->nullable()->default(0);
            $table->integer('cant_amortiguadores_delanteros')->nullable()->default(1);
            $table->integer('cant_amortiguadores_traseros')->nullable()->default(1);
            $table->text('observaciones_amortiguadores')->nullable();

            // Step 8: Battery (%)
            $table->decimal('prueba_bateria', 5, 2)->nullable()->default(0);
            $table->decimal('prueba_arranque', 5, 2)->nullable()->default(0);
            $table->decimal('carga_bateria', 5, 2)->nullable()->default(0);
            $table->text('observaciones_bateria')->nullable();

            // Step 9: Motor & Systems (JSON)
            $table->json('motor_sistemas_data')->nullable();
            $table->text('observaciones_motor')->nullable();
            $table->text('observaciones_interior')->nullable();

            // Step 10: Leaks & Levels (JSON)
            $table->json('fugas_niveles_data')->nullable();
            $table->text('prueba_ruta')->nullable();
            $table->text('observaciones_fugas')->nullable();

            // Control
            $table->enum('status', ['draft', 'in_progress', 'completed', 'approved', 'rejected'])->default('draft');
            $table->integer('current_step')->default(1);
            $table->integer('total_fotos')->default(0);
            $table->timestamps();

            $table->index('client_id');
            $table->index('user_id');
            $table->index('placa');
            $table->index('codigo');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expertises');
    }
};
