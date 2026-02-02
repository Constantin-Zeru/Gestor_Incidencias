<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('empleados', function (\Illuminate\Database\Schema\Blueprint $table) {
        if (! Schema::hasColumn('empleados', 'remember_token')) {
            $table->string('remember_token', 100)->nullable()->after('password');
        }
        if (! Schema::hasColumn('empleados', 'email_verified_at')) {
            $table->timestamp('email_verified_at')->nullable()->after('email');
        }
    });
}

public function down(): void
{
    Schema::table('empleados', function (\Illuminate\Database\Schema\Blueprint $table) {
        if (Schema::hasColumn('empleados', 'remember_token')) {
            $table->dropColumn('remember_token');
        }
        if (Schema::hasColumn('empleados', 'email_verified_at')) {
            $table->dropColumn('email_verified_at');
        }
    });
}

};
