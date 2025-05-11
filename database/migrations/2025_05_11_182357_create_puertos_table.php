<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('puertos', function (Blueprint $table) {
            $table->id('id_puerto');
            $table->string('nombre', 100)->nullable(false);
            $table->string('codigo', 10)->unique();
            $table->string('coordenadas_poligono')->nullable(false);
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate();
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('telefono_contacto', 20)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('puertos');
    }
};