<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contenedores', function (Blueprint $table) {
            $table->string('id_contenedor', 20)->primary();
            $table->foreignId('id_zona')->constrained('zonas', 'id_zona');
            $table->string('iso_code', 4);
            $table->integer('fila')->nullable(false);
            $table->integer('posicion')->nullable(false);
            $table->integer('nivel')->nullable(false);
            $table->timestamp('fecha_ingreso')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->enum('estado', ['vacio', 'lleno', 'en_transito', 'inspeccion', 'reparacion'])->default('vacio');
            $table->decimal('peso_actual_kg', 8, 2)->nullable();
            $table->boolean('es_peligroso')->default(false);
            
            // Add foreign key separately
            $table->foreign('iso_code')->references('iso_code')->on('tipos_contenedor');
        });

        // Add CHECK constraints using raw SQL
        DB::statement('ALTER TABLE contenedores ADD CONSTRAINT chk_nivel_positivo CHECK (nivel > 0)');
        DB::statement('ALTER TABLE contenedores ADD CONSTRAINT chk_peso_positivo CHECK (peso_actual_kg IS NULL OR peso_actual_kg > 0)');
    }

    public function down()
    {
        Schema::dropIfExists('contenedores');
    }
};