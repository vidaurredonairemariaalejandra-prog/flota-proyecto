<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->foreignId('chofer_id')->constrained('choferes')->restrictOnDelete();
            $table->string('origen', 60);
            $table->string('destino', 60);
            $table->dateTime('fecha_salida');
            $table->dateTime('fecha_llegada')->nullable();
            $table->enum('estado', ['programado', 'en_curso', 'finalizado', 'cancelado'])->default('programado');
            $table->timestamps();

            $table->index(['vehiculo_id', 'fecha_salida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
