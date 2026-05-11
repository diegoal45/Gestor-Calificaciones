<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            // ponderacion: usa porcentajes de tareas (si existen)
            // promedio: ignora porcentajes y promedia todas las tareas con nota
            $table->string('metodo_calificacion', 20)->default('ponderacion')->after('peso_asistencia');
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('metodo_calificacion');
        });
    }
};

