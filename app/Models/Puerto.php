<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puerto extends Model
{
    use HasFactory;

    protected $table = 'puertos';
    protected $primaryKey = 'id_puerto';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'direccion',
        'telefono_contacto',
        'coordenadas_vertices',
        'fecha_creacion',
        'ultima_actualizacion',
        'activo'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'ultima_actualizacion' => 'datetime',
        'activo' => 'boolean'
    ];

    public function zonas()
    {
        return $this->hasMany(Zona::class, 'id_puerto', 'id_puerto');
    }
}