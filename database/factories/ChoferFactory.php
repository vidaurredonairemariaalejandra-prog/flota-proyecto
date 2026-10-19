<?php

namespace Database\Factories;

use App\Models\Chofer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Chofer> */
class ChoferFactory extends Factory
{
    protected $model = Chofer::class;

    public function definition(): array
    {
        $nombres = ['Juan', 'Carlos', 'Luis', 'Marco', 'Jorge', 'Víctor', 'Freddy', 'Édgar', 'Rolando', 'Mario', 'Hugo', 'Walter', 'Óscar', 'Ramiro'];
        $apellidos = ['Mamani', 'Quispe', 'Flores', 'Choque', 'Condori', 'Gutiérrez', 'Vargas', 'Rojas', 'Mendoza', 'Villca', 'Copa', 'Alanoca', 'Salazar', 'Fernández'];

        return [
            'nombre' => fake()->randomElement($nombres),
            'apellido' => fake()->randomElement($apellidos) . ' ' . fake()->randomElement($apellidos),
            'ci' => fake()->unique()->numerify('#######') . ' LP',
            'licencia' => fake()->unique()->numerify('LIC-########'),
            'categoria_licencia' => fake()->randomElement(['B', 'C']),
            'telefono' => fake()->numerify('7#######'),
        ];
    }
}
