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
        Schema::create('USUARIOS', function (Blueprint $table) {
            $table->id('USU_ID');
            $table->unsignedBigInteger('CLI_ID');
            $table->string('USU_NOMBRE', 60);
            $table->string('USU_CONTRASENA', 60);

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
        Schema::dropIfExists('USUARIOS');
    }
};
