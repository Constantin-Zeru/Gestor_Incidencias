<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Estado de pago
            $table->boolean('pagada')->default(false)->after('pdf_path');

            // Moneda del cliente (ISO 4217: EUR, USD, PLN…)
            $table->string('moneda', 3)->nullable()->after('pagada');

            // Importe convertido a euros en el momento del pago
            $table->decimal('importe_euros', 10, 2)->nullable()->after('moneda');

            // Tipo de cambio aplicado en el pago
            $table->decimal('tipo_cambio', 10, 6)->nullable()->after('importe_euros');

            // Fecha real de pago
            $table->dateTime('fecha_pago')->nullable()->after('tipo_cambio');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn([
                'pagada',
                'moneda',
                'importe_euros',
                'tipo_cambio',
                'fecha_pago',
            ]);
        });
    }
};
