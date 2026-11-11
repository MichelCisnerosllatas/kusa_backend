<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Calificación que un usuario (DNI) hace a un trabajador.
 * El puntaje de cada criterio va en calificacion_detalles.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();       // ej. "VA-7K2QX" (comprobante para el usuario)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->foreignId('institucion_id')->constrained('instituciones')->cascadeOnDelete();
            $table->decimal('promedio', 3, 2);
            $table->string('comentario', 400)->nullable();
            // Ubicación del dispositivo al calificar (solo se guarda).
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->timestamps();

            // Para validar rápido "una calificación por trabajador al día".
            $table->index(['user_id', 'trabajador_id', 'created_at']);
        });

        Schema::create('calificacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calificacion_id')->constrained('calificaciones')->cascadeOnDelete();
            $table->foreignId('criterio_id')->constrained('criterios')->cascadeOnDelete();
            $table->unsignedTinyInteger('puntaje');      // 1 a 5
            $table->timestamps();

            $table->unique(['calificacion_id', 'criterio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificacion_detalles');
        Schema::dropIfExists('calificaciones');
    }
};
