<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Personal que atiende al público en cada institución.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institucion_id')->constrained('instituciones')->cascadeOnDelete();
            $table->string('titulo', 20)->nullable();     // Dr., Dra., Lic., Prof., Sub. ...
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('cargo');
            $table->string('foto_url')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
