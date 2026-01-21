<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PROVEEDORES', function (Blueprint $table) {
            $table->id('PRV_ID');
            $table->string('PRV_RUC', 13)->unique();
            $table->string('PRV_RAZON_SOCIAL', 60);
            $table->string('PRV_CORREO', 60);
            $table->string('PRV_DIRECCION', 60);
            $table->string('PRV_TELEFONO', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PROVEEDORES');
    }
};
