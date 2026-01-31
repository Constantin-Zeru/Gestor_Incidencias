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
    Schema::create('incidencias', function (Blueprint $table) {
        $table->id();
        $table->string('titulo');
        $table->text('descripcion');
        $table->enum('estado', ['pendiente', 'asignada', 'en_proceso', 'finalizada']);
        $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
        $table->foreignId('empleado_id')->nullable()->constrained('empleados')->nullOnDelete();
        $table->enum('creada_por', ['admin', 'cliente']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
