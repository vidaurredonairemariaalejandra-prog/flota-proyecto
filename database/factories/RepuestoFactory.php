<?php

namespace Database\Factories;

use App\Models\Repuesto;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Repuesto> */
class RepuestoFactory extends Factory
{
    protected $model = Repuesto::class;

    public function definition(): array
    {
        $repuestos = [
            'Filtro de aceite', 'Filtro de aire', 'Filtro de combustible', 'Aceite de motor 15W40 (balde)',
            'Pastillas de freno', 'Cinta de freno', 'Disco de embrague', 'Kit de embrague completo',
            'Amortiguador delantero', 'Amortiguador trasero', 'Bolsa de aire de suspensión', 'Neumático 295/80 R22.5',
            'Batería 12V 200Ah', 'Alternador', 'Motor de arranque', 'Turbocompresor',
            'Inyector diésel', 'Bomba de agua', 'Radiador', 'Correa de distribución',
            'Manguera de refrigerante', 'Rodamiento de rueda', 'Cruceta de cardán', 'Bomba de dirección',
            'Faro delantero', 'Compresor de aire acondicionado', 'Sensor de temperatura', 'Termostato',
            'Empaque de culata', 'Líquido de frenos (litro)',
        ];

        return [
            'codigo' => 'REP-' . fake()->unique()->numerify('####'),
            'nombre' => fake()->unique()->randomElement($repuestos),
            'precio_referencia' => fake()->randomFloat(2, 35, 2800),
        ];
    }
}
