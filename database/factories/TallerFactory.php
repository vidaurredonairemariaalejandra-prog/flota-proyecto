<?php

namespace Database\Factories;

use App\Models\Taller;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Taller> */
class TallerFactory extends Factory
{
    protected $model = Taller::class;

    public function definition(): array
    {
        $talleres = [
            'Taller Mecánico El Alto', 'Servicio Diésel Andino', 'Mecánica Pesada Cochabamba',
            'Taller Autorizado Scania Santa Cruz', 'Frenos y Suspensión Oruro', 'Electromecánica Sucre',
            'Taller Volvo Bolivia', 'Servicentro Los Pinos', 'Diésel Potosí',
        ];

        return [
            'nombre' => fake()->unique()->randomElement($talleres),
            'ciudad' => fake()->randomElement(['La Paz', 'El Alto', 'Cochabamba', 'Santa Cruz', 'Oruro', 'Sucre', 'Potosí']),
            'direccion' => 'Av. ' . fake()->randomElement(['Bolivia', 'Busch', 'Panamericana', 'Circunvalación', 'Las Américas']) . ' #' . fake()->numberBetween(100, 3500),
            'telefono' => fake()->numerify('2#######'),
        ];
    }
}
