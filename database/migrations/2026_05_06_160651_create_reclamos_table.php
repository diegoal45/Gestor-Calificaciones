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
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nota');
            $table->unsignedBigInteger('id_estudiante');
            $table->text('mensaje');
            $table->text('respuesta')->nullable();
            $table->enum('estado', ['pendiente', 'respondido', 'cerrado']);
            $table->timestamps();

            $table->foreign('id_nota')->references('id')->on('notas')->onDelete('cascade');
            $table->foreign('id_estudiante')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamos');
    }
};
