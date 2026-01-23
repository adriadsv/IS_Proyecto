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
        Schema::create('PROXBOD', function (Blueprint $table) {
            $table->string('BOD_CODIGO', 6);
            $table->string('PRD_CODIGO', 8);
            $table->integer('DET_BOD_CANTIDAD');
            $table->string('DET_BOD_UBICACION', 60);

            $table->primary(['BOD_CODIGO', 'PRD_CODIGO']);

            $table->foreign('BOD_CODIGO')
                  ->references('BOD_CODIGO')
                  ->on('BODEGAS')
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
        Schema::dropIfExists('PROXBOD');
    }
};
