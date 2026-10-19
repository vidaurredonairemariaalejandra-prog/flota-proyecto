<?php

namespace Database\Factories;

use App\Models\Chofer;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Vehiculo> */
class VehiculoFactory extends Factory
{
    protected $model = Vehiculo::class;

    public function definition(): array
    {
        $catalogo = [
            'Volvo' => ['B8R', 'B7R', 'B12R'],
            'Scania' => ['K360', 'K410', 'F94'],
            'Mercedes-Benz' => ['O-500 RS', 'OF-1721', 'O-400'],
            'Hyundai' => ['Universe', 'Aero Space'],
            'Yutong' => ['ZK6122H9', 'ZK6118HA'],
        ];

        $marca = fake()->randomElement(array_keys($catalogo));

        return [
            'placa' => strtoupper(fake()->unique()->bothify('####-???')),
            'marca' => $marca,
            'modelo' => fake()->randomElement($catalogo[$marca]),
            'anio' => fake()->numberBetween(2012, 2025),
            'kilometraje_actual' => 0,
            'estado' => 'disponible',
            'chofer_id' => Chofer::factory(),
        ];
    }

    public function enMantenimiento(): static
    {
        return $this->state(fn () => ['estado' => 'en_mantenimiento']);
    }
}
