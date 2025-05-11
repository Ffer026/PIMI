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
            $table->string('esquina_superior_izq')->nullable(false);
            $table->decimal('inclinacion_grados', 5, 2)->default(0);
            $table->integer('filas')->nullable(false);
            $table->integer('contenedores_por_fila')->nullable(false);
            $table->integer('longitud_contenedores')->nullable(false);
            $table->decimal('separacion_entre_filas', 5, 2)->nullable(false);
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