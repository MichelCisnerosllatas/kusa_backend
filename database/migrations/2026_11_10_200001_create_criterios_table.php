<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Preguntas que se califican (1 a 5 estrellas) en cada institución.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criterios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institucion_id')->constrained('instituciones')->cascadeOnDelete();
            $table->string('nombre');                     // ej. "Trato y respeto"
            $table->string('descripcion')->nullable();    // ej. "Amabilidad, escucha y respeto"
            $table->string('icono', 30)->default('star'); // heart | clock | chat | shield | star
            $table->unsignedTinyInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criterios');
    }
};
