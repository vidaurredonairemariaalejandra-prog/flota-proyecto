<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Viaje extends Model
{
    use HasFactory;

    protected $fillable = ['vehiculo_id', 'chofer_id', 'origen', 'destino', 'fecha_salida', 'fecha_llegada', 'estado'];

    protected $casts = [
        'fecha_salida' => 'datetime',
        'fecha_llegada' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Regla de negocio: un vehículo en mantenimiento NO puede asignarse a un viaje.
        static::saving(function (Viaje $viaje) {
            if (! $viaje->isDirty('vehiculo_id')) {
                return;
            }

            $vehiculo = Vehiculo::find($viaje->vehiculo_id);

            if ($vehiculo && $vehiculo->estado !== 'disponible') {
                $motivo = $vehiculo->estado === 'en_mantenimiento' ? 'está en mantenimiento' : 'está dado de baja';
                throw new DomainException("El vehículo {$vehiculo->placa} {$motivo} y no puede asignarse a un viaje.");
            }

            // Si no se indica chofer, se usa el titular del bus.
            if ($vehiculo && $viaje->chofer_id === null) {
                $viaje->chofer_id = $vehiculo->chofer_id;
            }
        });
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function chofer(): BelongsTo
    {
        return $this->belongsTo(Chofer::class);
    }
}
