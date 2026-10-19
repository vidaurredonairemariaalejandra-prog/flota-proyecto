<?php

namespace App\Console\Commands;

use App\Models\Vehiculo;
use Illuminate\Console\Command;

class AlertasMantenimiento extends Command
{
    protected $signature = 'flota:alertas';

    protected $description = 'Lista los buses que superaron 10.000 km desde su último mantenimiento preventivo';

    public function handle(): int
    {
        $vehiculos = Vehiculo::necesitanMantenimiento()->with('chofer')->orderBy('placa')->get();

        if ($vehiculos->isEmpty()) {
            $this->info('Sin alertas: ningún bus supera el límite.');

            return self::SUCCESS;
        }

        $this->warn("ALERTA: {$vehiculos->count()} bus(es) requieren mantenimiento preventivo.");
        $this->table(
            ['Placa', 'Marca/Modelo', 'Km actual', 'Km desde último preventivo', 'Chofer'],
            $vehiculos->map(fn ($v) => [
                $v->placa, "{$v->marca} {$v->modelo}", number_format($v->kilometraje_actual, 0, ',', '.'),
                number_format($v->kmDesdeUltimoPreventivo(), 0, ',', '.'), $v->chofer?->nombre_completo,
            ])->all()
        );

        return self::FAILURE;
    }
}
