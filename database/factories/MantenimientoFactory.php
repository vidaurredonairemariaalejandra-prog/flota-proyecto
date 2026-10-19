<?php

namespace Database\Factories;

use App\Models\Mantenimiento;
use App\Models\Taller;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Mantenimiento> */
class MantenimientoFactory extends Factory
{
    protected $model = Mantenimiento::class;

    public function definition(): array
    {
        $tipo = fake()->randomElement(['preventivo', 'correctivo']);

        return [
            'vehiculo_id' => Vehiculo::factory(),
            'taller_id' => Taller::factory(),
            'tipo' => $tipo,
            'estado' => 'finalizado',
            'fecha' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'kilometraje' => fake()->numberBetween(5000, 400000),
            'costo' => fake()->randomFloat(2, 150, 1800),
            'descripcion' => $this->descripcionPara($tipo),
        ];
    }

    public function preventivo(): static
    {
        return $this->state(fn () => ['tipo' => 'preventivo', 'descripcion' => $this->descripcionPara('preventivo')]);
    }

    public function correctivo(): static
    {
        return $this->state(fn () => ['tipo' => 'correctivo', 'descripcion' => $this->descripcionPara('correctivo')]);
    }

    public function enProceso(): static
    {
        return $this->state(fn () => ['estado' => 'en_proceso']);
    }

    private function descripcionPara(string $tipo): string
    {
        $opciones = $tipo === 'preventivo'
            ? ['Cambio de aceite y filtros', 'Revisión de frenos y neumáticos', 'Engrase, alineación y balanceo', 'Revisión general de 10.000 km']
            : ['Reparación de caja de cambios', 'Reemplazo de embrague', 'Reparación del sistema de frenos', 'Cambio de turbocompresor', 'Reparación de suspensión neumática'];

        return fake()->randomElement($opciones);
    }
}
