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
        Schema::create('evaluaciones_rubrica', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nota');
            $table->unsignedBigInteger('id_criterio');
            $table->unsignedBigInteger('id_nivel');
            $table->foreign('id_nota')->references('id')->on('notas')->onDelete('cascade');
            $table->foreign('id_criterio')->references('id')->on('criterios')->onDelete('cascade');
            $table->foreign('id_nivel')->references('id')->on('niveles_criterio')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_rubrica');
    }
};
