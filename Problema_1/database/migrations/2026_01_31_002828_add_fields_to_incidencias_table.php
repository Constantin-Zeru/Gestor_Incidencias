<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Para cada columna comprobamos antes de añadirla
        if (! Schema::hasColumn('incidencias', 'cliente_id')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete()->after('id');
            });
        }

        if (! Schema::hasColumn('incidencias', 'contacto_nombre')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('contacto_nombre')->nullable()->after('cliente_id');
            });
        }

        if (! Schema::hasColumn('incidencias', 'contacto_telefono')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('contacto_telefono')->nullable()->after('contacto_nombre');
            });
        }

        if (! Schema::hasColumn('incidencias', 'contacto_email')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('contacto_email')->nullable()->after('contacto_telefono');
            });
        }

        if (! Schema::hasColumn('incidencias', 'direccion')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('direccion')->nullable()->after('contacto_email');
            });
        }

        if (! Schema::hasColumn('incidencias', 'poblacion')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('poblacion')->nullable()->after('direccion');
            });
        }

        if (! Schema::hasColumn('incidencias', 'codigo_postal')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('codigo_postal',5)->nullable()->after('poblacion');
            });
        }

        if (! Schema::hasColumn('incidencias', 'provincia_codigo')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('provincia_codigo',2)->nullable()->after('codigo_postal');
            });
        }

        if (! Schema::hasColumn('incidencias', 'estado')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('estado')->default('P')->after('provincia_codigo');
            });
        }

        if (! Schema::hasColumn('incidencias', 'fecha_realizacion')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->date('fecha_realizacion')->nullable()->after('estado');
            });
        }

        if (! Schema::hasColumn('incidencias', 'anotaciones_anteriores')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->text('anotaciones_anteriores')->nullable()->after('fecha_realizacion');
            });
        }

        if (! Schema::hasColumn('incidencias', 'anotaciones_posteriores')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->text('anotaciones_posteriores')->nullable()->after('anotaciones_anteriores');
            });
        }

        if (! Schema::hasColumn('incidencias', 'fichero_resumen')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('fichero_resumen')->nullable()->after('anotaciones_posteriores');
            });
        }
    }

    public function down()
    {
        Schema::table('incidencias', function (Blueprint $table) {
            if (Schema::hasColumn('incidencias','fichero_resumen')) {
                $table->dropColumn('fichero_resumen');
            }
            if (Schema::hasColumn('incidencias','anotaciones_posteriores')) {
                $table->dropColumn('anotaciones_posteriores');
            }
            if (Schema::hasColumn('incidencias','anotaciones_anteriores')) {
                $table->dropColumn('anotaciones_anteriores');
            }
            if (Schema::hasColumn('incidencias','fecha_realizacion')) {
                $table->dropColumn('fecha_realizacion');
            }
            if (Schema::hasColumn('incidencias','estado')) {
                $table->dropColumn('estado');
            }
            if (Schema::hasColumn('incidencias','provincia_codigo')) {
                $table->dropColumn('provincia_codigo');
            }
            if (Schema::hasColumn('incidencias','codigo_postal')) {
                $table->dropColumn('codigo_postal');
            }
            if (Schema::hasColumn('incidencias','poblacion')) {
                $table->dropColumn('poblacion');
            }
            if (Schema::hasColumn('incidencias','direccion')) {
                $table->dropColumn('direccion');
            }
            if (Schema::hasColumn('incidencias','contacto_email')) {
                $table->dropColumn('contacto_email');
            }
            if (Schema::hasColumn('incidencias','contacto_telefono')) {
                $table->dropColumn('contacto_telefono');
            }
            if (Schema::hasColumn('incidencias','contacto_nombre')) {
                $table->dropColumn('contacto_nombre');
            }
            if (Schema::hasColumn('incidencias','cliente_id')) {
                $table->dropForeign(['cliente_id']);
                $table->dropColumn('cliente_id');
            }
        });
    }
};
