<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 10)->unique();
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->unsignedSmallInteger('anio');
            $table->unsignedInteger('kilometraje_actual')->default(0);
            $table->enum('estado', ['disponible', 'en_mantenimiento', 'baja'])->default('disponible');

            // Chofer titular: un chofer es titular de un solo bus (unique) y puede no tener bus (nullable)
            $table->foreignId('chofer_id')
                ->nullable()
                ->unique()
                ->constrained('choferes')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
