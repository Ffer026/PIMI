<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espacio extends Model
{
    use HasFactory;

    protected $table = 'espacios_contenedores';
    protected $primaryKey = 'id_espacios_contenedores';

    protected $fillable = [
        'id_zona',
        'nombre',
        'codigo_espacio',
        'observaciones',
        'fecha_creacion',
        'ultima_actualizacion',
        'activa'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'ultima_actualizacion' => 'datetime',
        'activa' => 'boolean'
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
    }
}