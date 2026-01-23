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
        Schema::create('CATEGORIA', function (Blueprint $table) {
            $table->string('CAT_CODIGO', 5)->primary();
            $table->string('CAT_NOMBRE', 60);
            $table->string('CAT_DESCRIPCION', 60);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CATEGORIA');
    }
};
