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
        Schema::create('niveles_criterio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_criterio');
            $table->string('nombre');
            $table->decimal('valor', 5, 2);
            $table->foreign('id_criterio')->references('id')->on('criterios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niveles_criterio');
    }
};
