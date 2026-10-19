<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehiculo_id', 'taller_id', 'tipo', 'estado', 'fecha', 'kilometraje', 'costo', 'descripcion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'kilometraje' => 'integer',
        'costo' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        // Al guardar un mantenimiento: actualiza el km del bus (si corresponde) y su estado.
        static::saved(function (Mantenimiento $m) {
            $vehiculo = Vehiculo::find($m->vehiculo_id);

            if (! $vehiculo) {
                return;
            }

            if ($m->kilometraje > $vehiculo->kilometraje_actual) {
                $vehiculo->update(['kilometraje_actual' => $m->kilometraje]);
            }

            $vehiculo->sincronizarEstadoMantenimiento();
        });
    }

    // ---------------- Relaciones ----------------

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function taller(): BelongsTo
    {
        return $this->belongsTo(Taller::class);
    }

    /** Repuestos usados, con cantidad y precio unitario en la tabla pivote. */
    public function repuestos(): BelongsToMany
    {
        return $this->belongsToMany(Repuesto::class, 'mantenimiento_repuesto')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }

    // ---------------- Costos ----------------

    public function getCostoRepuestosAttribute(): float
    {
        return (float) $this->repuestos->sum(
            fn ($r) => $r->pivot->cantidad * $r->pivot->precio_unitario
        );
    }

    /** Costo total = mano de obra + repuestos. */
    public function getCostoTotalAttribute(): float
    {
        return round((float) $this->costo + $this->costo_repuestos, 2);
    }

    /**
     * Reporte: costo total de mantenimiento por vehículo y por año
     * (mano de obra + repuestos), calculado en SQL.
     */
    public static function reporteCostoPorVehiculoAnio(?int $anio = null): Collection
    {
        $anioSql = match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', m.fecha) AS INTEGER)",
            'pgsql' => 'EXTRACT(YEAR FROM m.fecha)::int',
            default => 'YEAR(m.fecha)',
        };

        $repuestosPorMantenimiento = DB::table('mantenimiento_repuesto')
            ->selectRaw('mantenimiento_id, SUM(cantidad * precio_unitario) AS total_repuestos')
            ->groupBy('mantenimiento_id');

        return DB::table('mantenimientos as m')
            ->join('vehiculos as v', 'v.id', '=', 'm.vehiculo_id')
            ->leftJoinSub($repuestosPorMantenimiento, 'r', 'r.mantenimiento_id', '=', 'm.id')
            ->selectRaw("v.placa, {$anioSql} AS anio, COUNT(m.id) AS mantenimientos,
                SUM(m.costo) AS mano_obra,
                COALESCE(SUM(r.total_repuestos), 0) AS repuestos,
                SUM(m.costo + COALESCE(r.total_repuestos, 0)) AS costo_total")
            ->when($anio, fn ($q) => $q->whereRaw("{$anioSql} = ?", [$anio]))
            ->groupBy('v.placa')
            ->groupByRaw($anioSql)
            ->orderBy('v.placa')
            ->orderBy('anio')
            ->get();
    }
}
