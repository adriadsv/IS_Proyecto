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
        Schema::create('PRODUCTOS', function (Blueprint $table) {
            $table->string('PRD_CODIGO', 8)->primary();
            $table->string('CAT_CODIGO', 5);
            $table->string('PRD_DESCRIPCION', 60);
            $table->decimal('PRD_PRECIO', 6, 2);
            $table->decimal('PRD_COSTO_ADQUISICION', 6, 2);

            $table->foreign('CAT_CODIGO')
                  ->references('CAT_CODIGO')
                  ->on('CATEGORIA')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PRODUCTOS');
    }
};
