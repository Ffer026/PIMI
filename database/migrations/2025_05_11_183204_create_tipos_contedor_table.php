<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipos_contenedor', function (Blueprint $table) {
            $table->string('iso_code', 4)->primary();
            $table->string('descripcion', 100)->nullable(false);
            $table->decimal('longitud_m', 4, 2)->nullable(false);
            $table->decimal('anchura_m', 4, 2)->nullable(false);
            $table->decimal('altura_m', 4, 2)->nullable(false);
            $table->decimal('peso_max_kg', 8, 2)->nullable(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipos_contenedor');
    }
};