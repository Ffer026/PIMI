<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    protected $table = 'zonas';
    protected $primaryKey = 'id_zona';

    protected $fillable = [
        'id_puerto',
        'nombre',
        'codigo_zona',
        'coordenadas_vertices',
        'espacios_para_contenedores',
        'max_niveles_apilamiento',
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

    public function puerto()
    {
        return $this->belongsTo(Puerto::class, 'id_puerto', 'id_puerto');
    }

    public function espacios()
    {
        return $this->hasMany(Espacio::class, 'id_zona', 'id_zona');
    }
}