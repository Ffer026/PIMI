<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('espacios_contenedores', function (Blueprint $table) {
            $table->id('id_espacios_contenedores');
            $table->foreignId('id_zona')->constrained('zonas', 'id_zona');
            $table->string('nombre', 100)->nullable(false);
            $table->string('codigo_espacio', 10)->nullable();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('activa')->default(true);
            $table->text('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('espacios_contenedores');
    }
};
