<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreignId('cliente_id')->nullable()->after('id')->constrained('clientes')->nullOnDelete();
            $table->string('contacto_nombre')->nullable()->after('cliente_id');
            $table->string('contacto_telefono')->nullable()->after('contacto_nombre');
            $table->string('contacto_email')->nullable()->after('contacto_telefono');
            $table->string('direccion')->nullable()->after('contacto_email');
            $table->string('poblacion')->nullable()->after('direccion');
            $table->string('codigo_postal',5)->nullable()->after('poblacion');
            $table->string('provincia_codigo',2)->nullable()->after('codigo_postal');
            $table->string('estado')->default('P')->after('provincia_codigo');
            $table->date('fecha_realizacion')->nullable()->after('estado');
            $table->text('anotaciones_anteriores')->nullable()->after('fecha_realizacion');
            $table->text('anotaciones_posteriores')->nullable()->after('anotaciones_anteriores');
            $table->string('fichero_resumen')->nullable()->after('anotaciones_posteriores');
        });
    }

    public function down()
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn([
              'cliente_id','contacto_nombre','contacto_telefono','contacto_email','direccion',
              'poblacion','codigo_postal','provincia_codigo','estado','fecha_realizacion',
              'anotaciones_anteriores','anotaciones_posteriores','fichero_resumen'
            ]);
        });
    }
};
