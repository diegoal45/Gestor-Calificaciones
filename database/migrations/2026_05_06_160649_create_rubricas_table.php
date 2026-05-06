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
        Schema::create('rubricas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tarea');
            $table->string('nombre');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_tarea')->references('id')->on('tareas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubricas');
    }
};
