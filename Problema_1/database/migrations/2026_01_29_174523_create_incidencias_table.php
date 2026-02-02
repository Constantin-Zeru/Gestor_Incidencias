<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            // Estado controlado: usamos valores legibles en BD y chequeo
            $table->string('estado')->default('pendiente'); // pendiente, asignada, en_proceso, finalizada
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->string('creada_por')->default('cliente'); // 'cliente' o 'admin'
            $table->string('contacto_nombre');
            $table->string('contacto_telefono');
            $table->string('contacto_email');
            $table->string('direccion')->nullable();
            $table->string('poblacion')->nullable();
            $table->string('codigo_postal',5)->nullable();
            $table->string('provincia_codigo',2)->nullable();
            $table->date('fecha_realizacion')->nullable();
            $table->text('anotaciones_anteriores')->nullable();
            $table->text('anotaciones_posteriores')->nullable();
            // ruta al fichero en storage (no en public)
            $table->string('fichero_resumen')->nullable();
            $table->timestamps();
        });

        // Si tu driver es MySQL y quieres un CHECK:
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE incidencias ADD CONSTRAINT chk_estado CHECK (estado IN ('pendiente','asignada','en_proceso','finalizada'))");
        }
        // Para sqlite MySQL/SQLite differences: en SQLite el CHECK puede definirse en CREATE TABLE; si ya creado, puedes dejar sin CHECK.
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
