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
        Schema::table('cuotas', function (Blueprint $table) {
            // ISO 3-letter code (EUR/USD/GBP/PLN/...). Nullable y default 'EUR' para no romper datos existentes.
            $table->string('moneda', 3)->nullable()->default('EUR')->after('importe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuotas', function (Blueprint $table) {
            $table->dropColumn('moneda');
        });
    }
};
