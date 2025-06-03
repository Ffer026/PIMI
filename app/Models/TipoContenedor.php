<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoContenedor extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue convención de nombres)
    protected $table = 'tipos_contenedor';

    // Clave primaria personalizada
    protected $primaryKey = 'iso_code';

    // Tipo de clave primaria (no es autoincremental)
    public $incrementing = false;

    // Tipo de dato de la clave primaria
    protected $keyType = 'string';

    // Campos asignables masivamente
    protected $fillable = [
        'iso_code',
        'descripcion',
        'longitud_m',
        'anchura_m',
        'altura_m',
        'peso_max_kg'
    ];

    // Campos ocultos en las respuestas JSON
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    // Relación con contenedores
    public function contenedores()
    {
        return $this->hasMany(Contenedor::class, 'tipo_contenedor_iso', 'iso_code');
    }

    // Casts para los atributos
    protected $casts = [
        'longitud_m' => 'decimal:2',
        'anchura_m' => 'decimal:2',
        'altura_m' => 'decimal:2',
        'peso_max_kg' => 'decimal:2',
    ];
}