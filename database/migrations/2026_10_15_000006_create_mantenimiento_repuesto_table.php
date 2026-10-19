<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento_repuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('mantenimientos')->cascadeOnDelete();
            $table->foreignId('repuesto_id')->constrained('repuestos')->restrictOnDelete();
            $table->unsignedSmallInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);                 // precio al momento del servicio
            $table->timestamps();

            // un mismo repuesto aparece una sola vez por mantenimiento
            $table->unique(['mantenimiento_id', 'repuesto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_repuesto');
    }
};
