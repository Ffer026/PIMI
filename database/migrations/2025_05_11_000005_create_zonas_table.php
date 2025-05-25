<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id('id_zona');
            $table->foreignId('id_puerto')->constrained('puertos', 'id_puerto');
            $table->string('nombre', 100)->nullable(false);
            $table->string('codigo_zona', 10)->nullable();
            $table->string('coordenadas_vertices')->nullable(false);
            $table->integer('espacios_para_contenedores')->nullable(false);
            $table->integer('max_niveles_apilamiento')->default(4);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('activa')->default(true);
            $table->text('observaciones')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('zonas');
    }
};