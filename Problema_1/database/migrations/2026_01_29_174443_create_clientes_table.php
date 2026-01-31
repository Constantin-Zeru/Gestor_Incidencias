<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->string('cif')->unique();
        $table->string('nombre');
        $table->string('telefono');
        $table->string('email')->nullable();
        $table->string('cuenta_corriente');
        $table->string('pais');
        $table->string('moneda');
        $table->decimal('cuota_mensual', 10, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
