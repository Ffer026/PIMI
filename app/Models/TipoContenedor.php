<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoContenedor extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipos_contenedor';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'iso_code';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'iso_code',
        'descripcion',
        'longitud_m',
        'anchura_m',
        'altura_m',
        'peso_max_kg'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'longitud_m' => 'decimal:2',
        'anchura_m' => 'decimal:2',
        'altura_m' => 'decimal:2',
        'peso_max_kg' => 'decimal:2',
    ];

    /**
     * Relación con los contenedores de este tipo
     */
    public function contenedores()
    {
        return $this->hasMany(Contenedor::class, 'tipo_contenedor_iso', 'iso_code');
    }

    /**
     * Accesor para las dimensiones formateadas
     */
    public function getDimensionesAttribute()
    {
        return "{$this->longitud_m}m x {$this->anchura_m}m x {$this->altura_m}m";
    }

    /**
     * Accesor para el volumen calculado
     */
    public function getVolumenAttribute()
    {
        return $this->longitud_m * $this->anchura_m * $this->altura_m;
    }
}