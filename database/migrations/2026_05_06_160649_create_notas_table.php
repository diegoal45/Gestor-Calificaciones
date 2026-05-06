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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tarea');
            $table->unsignedBigInteger('id_estudiante');
            $table->decimal('nota', 5, 2);
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->foreign('id_tarea')->references('id')->on('tareas')->onDelete('cascade');
            $table->foreign('id_estudiante')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
