<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('choferes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('apellido', 80);
            $table->string('ci', 20)->unique();                // cédula de identidad
            $table->string('licencia', 20)->unique();          // nro. de licencia profesional
            $table->enum('categoria_licencia', ['B', 'C'])->default('C');
            $table->string('telefono', 20)->nullable();
            $table->timestamps();

            $table->index(['apellido', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('choferes');
    }
};
