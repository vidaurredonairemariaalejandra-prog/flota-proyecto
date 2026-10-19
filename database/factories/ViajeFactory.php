<?php

namespace Database\Factories;

use App\Models\Vehiculo;
use App\Models\Viaje;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Viaje> */
class ViajeFactory extends Factory
{
    protected $model = Viaje::class;

    public function definition(): array
    {
        [$origen, $destino] = fake()->randomElements(
            ['La Paz', 'Cochabamba', 'Santa Cruz', 'Oruro', 'Sucre', 'Potosí', 'Tarija', 'Tupiza'],
            2
        );

        $salida = fake()->dateTimeBetween('-8 months', '-1 week');

        return [
            'vehiculo_id' => Vehiculo::factory(),
            'chofer_id' => null,                       // se completa con el titular del bus
            'origen' => $origen,
            'destino' => $destino,
            'fecha_salida' => $salida,
            'fecha_llegada' => (clone $salida)->modify('+' . fake()->numberBetween(6, 16) . ' hours'),
            'estado' => 'finalizado',
        ];
    }

    public function programado(): static
    {
        return $this->state(fn () => [
            'estado' => 'programado',
            'fecha_salida' => now()->addDays(fake()->numberBetween(1, 15)),
            'fecha_llegada' => null,
        ]);
    }
}
