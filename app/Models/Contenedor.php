<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contenedor extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contenedores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tipo_contenedor_iso',
        'espacio_contenedor_id',
        'propietario',
        'material_peligroso'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'material_peligroso' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el tipo de contenedor
     */
    public function tipoContenedor()
    {
        return $this->belongsTo(TipoContenedor::class, 'tipo_contenedor_iso', 'iso_code');
    }

    /**
     * Relación con el espacio contenedor
     */
    public function espacioContenedor()
    {
        return $this->belongsTo(Espacio::class, 'espacio_contenedor_id', 'id_espacios_contenedores');
    }

    /**
     * Accesor para mostrar el estado de material peligroso de forma legible
     */
    public function getMaterialPeligrosoTextoAttribute()
    {
        return $this->material_peligroso ? 'Sí' : 'No';
    }

    /**
     * Scope para filtrar contenedores peligrosos
     */
    public function scopePeligrosos($query)
    {
        return $query->where('material_peligroso', true);
    }

    /**
     * Scope para filtrar contenedores no peligrosos
     */
    public function scopeNoPeligrosos($query)
    {
        return $query->where('material_peligroso', false);
    }
}