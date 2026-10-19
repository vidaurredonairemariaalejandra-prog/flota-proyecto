<?php

namespace Database\Seeders;

use App\Models\Chofer;
use App\Models\Mantenimiento;
use App\Models\Repuesto;
use App\Models\Taller;
use App\Models\Vehiculo;
use App\Models\Viaje;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FlotaSeeder extends Seeder
{
    public function run(): void
    {
        $choferes = Chofer::factory(12)->create();     // 10 titulares + 2 suplentes sin bus
        $talleres = Taller::factory(5)->create();
        $repuestos = Repuesto::factory(25)->create();

        $vehiculos = collect();

        foreach (range(0, 9) as $i) {
            $vehiculo = Vehiculo::factory()->create(['chofer_id' => $choferes[$i]->id]);
            $conAlerta = $i < 3;                        // los 3 primeros superarán los 10.000 km
            $cantidad = rand(5, 7);
            $km = 0;
            $inicio = Carbon::create(2024, 1, 10)->addDays(rand(0, 30));

            for ($n = 1; $n <= $cantidad; $n++) {
                $km += rand(6000, 11000);

                $tipo = ($n % 2 === 0) ? 'correctivo' : 'preventivo';
                if ($n === $cantidad) {
                    // Sin alerta: último servicio preventivo. Con alerta: último correctivo.
                    $tipo = $conAlerta ? 'correctivo' : 'preventivo';
                }

                $mantenimiento = Mantenimiento::factory()->{$tipo}()->create([
                    'vehiculo_id' => $vehiculo->id,
                    'taller_id' => $talleres->random()->id,
                    'estado' => 'finalizado',
                    'fecha' => $inicio->copy()->addDays($n * 120 + rand(0, 20))->toDateString(),
                    'kilometraje' => $km,
                ]);

                $this->adjuntarRepuestos($mantenimiento, $repuestos);
            }

            // Km actual: con alerta > 10.000 km tras el último preventivo; sin alerta < 10.000 km
            $actual = $conAlerta ? $km + rand(10500, 14000) : $km + rand(500, 8000);
            $vehiculo->update(['kilometraje_actual' => $actual]);

            $vehiculos->push($vehiculo->fresh());
        }

        // Viajes: solo con buses disponibles (la regla de negocio bloquea el resto)
        foreach ($vehiculos as $vehiculo) {
            Viaje::factory(rand(3, 5))->create(['vehiculo_id' => $vehiculo->id]);
            Viaje::factory()->programado()->create(['vehiculo_id' => $vehiculo->id]);
        }

        // El último bus entra a mantenimiento correctivo (queda "en_mantenimiento" automáticamente)
        $enTaller = $vehiculos->last();
        $abierto = Mantenimiento::factory()->correctivo()->enProceso()->create([
            'vehiculo_id' => $enTaller->id,
            'taller_id' => $talleres->random()->id,
            'fecha' => now()->toDateString(),
            'kilometraje' => $enTaller->kilometraje_actual,
        ]);
        $this->adjuntarRepuestos($abierto, $repuestos);
    }

    private function adjuntarRepuestos(Mantenimiento $mantenimiento, $repuestos): void
    {
        $repuestos->random(rand(1, 4))->each(function ($repuesto) use ($mantenimiento) {
            $mantenimiento->repuestos()->attach($repuesto->id, [
                'cantidad' => rand(1, 4),
                'precio_unitario' => round($repuesto->precio_referencia * rand(95, 115) / 100, 2),
            ]);
        });
    }
}
