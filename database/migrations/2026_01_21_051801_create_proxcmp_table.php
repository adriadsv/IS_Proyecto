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
        Schema::create('PROXCMP', function (Blueprint $table) {
            $table->unsignedBigInteger('CMP_CODIGO');
            $table->string('PRD_CODIGO', 8);
            $table->integer('DET_CMP_CANTIDAD');
            $table->decimal('DET_CMP_COSTO_UNITARIO', 10, 2);
            $table->string('ESTADO_PROXCMP', 3);

            $table->primary(['CMP_CODIGO', 'PRD_CODIGO']);

            $table->foreign('CMP_CODIGO')
                  ->references('CMP_CODIGO')
                  ->on('COMPRAS')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('PRD_CODIGO')
                  ->references('PRD_CODIGO')
                  ->on('PRODUCTOS')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PROXCMP');
    }
};
