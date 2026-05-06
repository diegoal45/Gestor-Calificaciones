<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_curso');
            $table->unsignedBigInteger('id_estudiante');
            $table->date('fecha');
            $table->enum('estado', ['presente', 'ausente']);
            $table->foreign('id_curso')->references('id')->on('cursos')->onDelete('cascade');
            $table->foreign('id_estudiante')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia');
    }
};
