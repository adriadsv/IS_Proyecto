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
        Schema::create('DETALLE_CARRITO', function (Blueprint $table) {
            $table->string('PRD_CODIGO', 8);
            $table->integer('CAR_CODIGO');
            $table->integer('DET_CAR_CANTIDAD');

            $table->primary(['PRD_CODIGO', 'CAR_CODIGO']);

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
        Schema::dropIfExists('DETALLE_CARRITO');
    }
};
