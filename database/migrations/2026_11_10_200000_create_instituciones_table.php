<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sedes donde se atiende al público (hospital, comisaría, colegio...).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instituciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();      // ej. "hospital-maynas" (se usará en el QR)
            $table->string('nombre');
            $table->string('tipo', 100);                  // Hospital, Comisaría, Colegio · UGEL
            $table->string('grupo', 100)->index();        // Centros de Salud, Comisarías, Centros Educativos
            $table->string('direccion')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('correo')->nullable();
            $table->string('horario')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instituciones');
    }
};
