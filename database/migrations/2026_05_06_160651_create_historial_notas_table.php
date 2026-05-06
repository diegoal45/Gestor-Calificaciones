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
        Schema::create('historial_notas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nota');
            $table->decimal('nota_anterior', 5, 2);
            $table->decimal('nota_nueva', 5, 2);
            $table->timestamp('changed_at')->useCurrent();

            $table->foreign('id_nota')->references('id')->on('notas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_notas');
    }
};
