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
        Schema::create('PROXFAC', function (Blueprint $table) {
            $table->unsignedBigInteger('FAC_CODIGO');
            $table->string('PRD_CODIGO', 8);
            $table->integer('DET_FAC_CANTIDAD');
            $table->decimal('DET_FAC_PRECIO_UNITARIO', 10, 2);
            $table->string('ESTADO_PROXFAC', 3);

            $table->primary(['FAC_CODIGO', 'PRD_CODIGO']);

            $table->foreign('FAC_CODIGO')
                  ->references('FAC_CODIGO')
                  ->on('FACTURAS')
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
        Schema::dropIfExists('PROXFAC');
    }
};
