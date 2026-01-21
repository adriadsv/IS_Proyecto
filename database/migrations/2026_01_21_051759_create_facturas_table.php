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
        Schema::create('FACTURAS', function (Blueprint $table) {
            $table->id('FAC_CODIGO');
            $table->dateTime('FAC_FECHA');
            $table->decimal('FAC_SUBTOTAL', 10, 2);
            $table->decimal('FAC_IVA', 10, 2);
            $table->decimal('FAC_MONTO_TOTAL', 10, 2);
            $table->string('FAC_ESTADO', 3);
            $table->integer('ID_CARRITO');
            $table->unsignedBigInteger('CLI_ID');

            $table->foreign('CLI_ID')
                  ->references('CLI_ID')
                  ->on('CLIENTES')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('FACTURAS');
    }
};
