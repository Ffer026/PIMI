<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Puerto extends Model
{
    protected $primaryKey = 'id_puerto';
    
    protected $fillable = [
        'nombre', 'codigo', 'descripcion', 'coordenadas_vertices',
        'direccion', 'telefono_contacto', 'activo'
    ];

    public function zonas(): HasMany
    {
        return $this->hasMany(Zona::class, 'id_puerto');
    }
}