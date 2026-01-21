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
        Schema::create('BODEGAS', function (Blueprint $table) {
            $table->string('BOD_CODIGO', 6)->primary();
            $table->string('BOD_DESCRIPCION', 60);
            $table->string('BOD_DIRECCION', 60);
            $table->string('BOD_NOMBRE_ENCARGADO', 60);
            $table->string('BOD_TELEFONO_ENCARGADO', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('BODEGAS');
    }
};
