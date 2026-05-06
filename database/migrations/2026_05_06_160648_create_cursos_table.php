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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->unsignedBigInteger('id_profesor');
            $table->decimal('nota_minima_aprobatoria', 5, 2);
            $table->decimal('nota_maxima', 5, 2);
            $table->boolean('usa_asistencia');
            $table->decimal('peso_asistencia', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_profesor')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
