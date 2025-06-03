<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zona extends Model
{
    protected $primaryKey = 'id_zona';
    
    protected $fillable = [
        'id_puerto', 'nombre', 'codigo_zona', 'coordenadas_vertices',
        'espacios_para_contenedores', 'max_niveles_apilamiento',
        'observaciones', 'activa'
    ];

    public function puerto(): BelongsTo
    {
        return $this->belongsTo(Puerto::class, 'id_puerto');
    }

    public function espacios(): HasMany
    {
        return $this->hasMany(Espacio::class, 'id_zona');
    }
}