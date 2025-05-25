<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contenedores', function (Blueprint $table) {
            $table->id();
            
            // Relación con tipos_contenedor (fixed reference)
            $table->string('tipo_contenedor_iso', 4);
            $table->foreign('tipo_contenedor_iso')
                  ->references('iso_code')
                  ->on('tipos_contenedor')
                  ->onDelete('restrict');
            
            // Relación con espacios_contenedores (fixed reference)
            $table->unsignedBigInteger('espacio_contenedor_id')->nullable();
            $table->foreign('espacio_contenedor_id')
                  ->references('id_espacios_contenedores')
                  ->on('espacios_contenedores')
                  ->onDelete('set null');
            
            // Datos básicos
            $table->string('propietario', 100);
            $table->boolean('material_peligroso')->default(false);
            
            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contenedores');
    }
};