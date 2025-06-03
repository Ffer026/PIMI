<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Espacio extends Model
{
    protected $primaryKey = 'id_espacios_contenedores';
    
    protected $fillable = [
        'id_zona', 'nombre', 'codigo_espacio', 'observaciones', 'activa'
    ];

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class, 'id_zona');
    }
}