<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();

            // restrictOnDelete: no se puede borrar un vehículo/taller con historial de mantenimiento
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->foreignId('taller_id')->constrained('talleres')->restrictOnDelete();

            $table->enum('tipo', ['preventivo', 'correctivo']);
            $table->enum('estado', ['en_proceso', 'finalizado'])->default('finalizado');
            $table->date('fecha');
            $table->unsignedInteger('kilometraje');                    // km del bus al momento del servicio
            $table->decimal('costo', 10, 2)->default(0);               // mano de obra / servicio (Bs.)
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();

            // Índices para la alerta de 10.000 km y para el reporte por año
            $table->index(['vehiculo_id', 'tipo', 'kilometraje']);
            $table->index(['vehiculo_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
