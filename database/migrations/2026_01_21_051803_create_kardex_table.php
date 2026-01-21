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
        Schema::create('KARDEX', function (Blueprint $table) {
            $table->id('KAR_ID');
            $table->string('PRD_CODIGO', 8);
            $table->string('BOD_CODIGO', 6);
            $table->unsignedBigInteger('FAC_CODIGO')->nullable();
            $table->unsignedBigInteger('CMP_CODIGO')->nullable();
            $table->unsignedBigInteger('TRN_ID')->nullable();
            $table->dateTime('KAR_FECHA');
            $table->integer('KAR_SALDO');

            $table->foreign('PRD_CODIGO')
                  ->references('PRD_CODIGO')
                  ->on('PRODUCTOS')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('BOD_CODIGO')
                  ->references('BOD_CODIGO')
                  ->on('BODEGAS')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('FAC_CODIGO')
                  ->references('FAC_CODIGO')
                  ->on('FACTURAS')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('CMP_CODIGO')
                  ->references('CMP_CODIGO')
                  ->on('COMPRAS')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('TRN_ID')
                  ->references('TRN_ID')
                  ->on('TRANSACCION')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KARDEX');
    }
};
