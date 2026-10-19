<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Repuesto extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'nombre', 'precio_referencia'];

    protected $casts = ['precio_referencia' => 'decimal:2'];

    public function mantenimientos(): BelongsToMany
    {
        return $this->belongsToMany(Mantenimiento::class, 'mantenimiento_repuesto')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }
}
