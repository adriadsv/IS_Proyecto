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
        Schema::create('COMPRAS', function (Blueprint $table) {
            $table->id('CMP_CODIGO');
            $table->unsignedBigInteger('PRV_ID');
            $table->date('CMP_FECHA_ENTREGA');
            $table->string('CMP_ESTADO', 10);

            $table->foreign('PRV_ID')
                  ->references('PRV_ID')
                  ->on('PROVEEDORES')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('COMPRAS');
    }
};
