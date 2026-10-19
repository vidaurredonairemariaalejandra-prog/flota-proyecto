<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chofer extends Model
{
    use HasFactory;

    protected $table = 'choferes';

    protected $fillable = ['nombre', 'apellido', 'ci', 'licencia', 'categoria_licencia', 'telefono'];

    /** Bus del cual este chofer es titular (0..1). */
    public function vehiculo(): HasOne
    {
        return $this->hasOne(Vehiculo::class);
    }

    /** Viajes realizados por el chofer. */
    public function viajes(): HasMany
    {
        return $this->hasMany(Viaje::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
