<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CLIENTES', function (Blueprint $table) {
            $table->id('CLI_ID');
            $table->string('CLI_CEDULA_RUC', 13)->unique();
            $table->string('CLI_NOMBRE', 60);
            $table->string('CLI_TELEFONO', 10);
            $table->string('CLI_CORREO', 60);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CLIENTES');
    }
};
