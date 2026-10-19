<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    use HasFactory;

    /** Km máximos permitidos desde el último mantenimiento preventivo. */
    public const LIMITE_KM_PREVENTIVO = 10000;

    protected $fillable = ['placa', 'marca', 'modelo', 'anio', 'kilometraje_actual', 'estado', 'chofer_id'];

    protected $casts = [
        'anio' => 'integer',
        'kilometraje_actual' => 'integer',
    ];

    // ---------------- Relaciones ----------------

    /** Chofer titular asignado al bus. */
    public function chofer(): BelongsTo
    {
        return $this->belongsTo(Chofer::class);
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }

    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class);
    }

    // ---------------- Regla: alerta de 10.000 km ----------------

    /** Kilometraje del último mantenimiento preventivo finalizado (0 si nunca tuvo). */
    public function kilometrajeUltimoPreventivo(): int
    {
        return (int) $this->mantenimientos()
            ->where('tipo', 'preventivo')
            ->where('estado', 'finalizado')
            ->max('kilometraje');
    }

    public function kmDesdeUltimoPreventivo(): int
    {
        return max(0, $this->kilometraje_actual - $this->kilometrajeUltimoPreventivo());
    }

    public function necesitaMantenimiento(): bool
    {
        return $this->kmDesdeUltimoPreventivo() > self::LIMITE_KM_PREVENTIVO;
    }

    /** Vehículos que superaron el límite de km desde su último preventivo (consulta SQL). */
    public function scopeNecesitanMantenimiento(Builder $query): Builder
    {
        return $query->whereRaw(
            'vehiculos.kilometraje_actual - COALESCE((
                SELECT MAX(m.kilometraje) FROM mantenimientos m
                WHERE m.vehiculo_id = vehiculos.id AND m.tipo = ? AND m.estado = ?
            ), 0) > ?',
            ['preventivo', 'finalizado', self::LIMITE_KM_PREVENTIVO]
        );
    }

    // ---------------- Estado según mantenimientos en proceso ----------------

    /** Pasa a "en_mantenimiento" si hay un servicio abierto y vuelve a "disponible" al cerrarse. */
    public function sincronizarEstadoMantenimiento(): void
    {
        if ($this->estado === 'baja') {
            return;
        }

        $enProceso = $this->mantenimientos()->where('estado', 'en_proceso')->exists();

        if ($enProceso && $this->estado !== 'en_mantenimiento') {
            $this->update(['estado' => 'en_mantenimiento']);
        } elseif (! $enProceso && $this->estado === 'en_mantenimiento') {
            $this->update(['estado' => 'disponible']);
        }
    }
}
