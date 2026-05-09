<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Garantiza la columna descripcion en niveles_criterio si la BD no ejecutó la migración anterior.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('niveles_criterio', 'descripcion')) {
            Schema::table('niveles_criterio', function (Blueprint $table) {
                $table->text('descripcion')->nullable()->after('valor');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('niveles_criterio', 'descripcion')) {
            Schema::table('niveles_criterio', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }
    }
};
