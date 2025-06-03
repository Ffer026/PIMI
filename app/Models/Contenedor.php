<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoContenedor;

class Contenedor extends Model
{
    use HasFactory;

    protected $table = 'contenedores';

    public function tipoContenedor()
    {
        return $this->belongsTo(TipoContenedor::class, 'tipo_contenedor_iso', 'iso_code');
    }

    public function espacioContenedor()
    {
        return $this->belongsTo(Espacio::class, 'espacio_contenedor_id', 'id_espacios_contenedores');
    }
}