<?php

namespace Database\Factories;

use App\Models\Contenedor;
use App\Models\TipoContenedor;
use App\Models\Espacio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContenedorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contenedor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Obtener un tipo de contenedor existente o crear uno si no existe
        $tipoContenedor = TipoContenedor::inRandomOrder()->first() ?? 
            TipoContenedor::factory()->create();
            
        // Obtener un espacio existente o crear uno si no existe
        $espacio = Espacio::inRandomOrder()->first() ?? 
            Espacio::factory()->create();

        return [
            'tipo_contenedor_iso' => $tipoContenedor->iso_code,
            'espacio_contenedor_id' => $espacio->id_espacios_contenedores,
            'propietario' => $this->faker->company,
            'material_peligroso' => $this->faker->boolean(20), // 20% de probabilidad de ser peligroso
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indica que el contenedor tiene material peligroso
     */
    public function peligroso()
    {
        return $this->state(function (array $attributes) {
            return [
                'material_peligroso' => true,
            ];
        });
    }

    /**
     * Indica que el contenedor no tiene material peligroso
     */
    public function noPeligroso()
    {
        return $this->state(function (array $attributes) {
            return [
                'material_peligroso' => false,
            ];
        });
    }

    /**
     * Indica un propietario específico
     */
    public function dePropietario($propietario)
    {
        return $this->state(function (array $attributes) use ($propietario) {
            return [
                'propietario' => $propietario,
            ];
        });
    }
}