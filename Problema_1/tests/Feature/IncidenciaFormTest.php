<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IncidenciaFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_incidencia_store_creates_incidencia_and_redirects_home()
    {
        // Crear cliente mínimo en la BD para que la validación CIF+tel pase
        $clienteId = DB::table('clientes')->insertGetId([
            'cif' => 'B00000000',
            'nombre' => 'Cliente Test',
            'telefono' => '+34123456789',
            'email' => 'cliente@test.local',
            'direccion' => 'C/ Test, 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Enviar formulario público con CIF y teléfono que coincidan
        $response = $this->post('/incidencias/public/store', [
            'cif' => 'B00000000',
            'telefono' => '+34 123456789',
            'titulo' => 'Prueba incidencia pública',
            'descripcion' => 'Descripción de prueba',
            // opcionales:
            'direccion' => 'C/ Test, 1',
            'poblacion' => 'Testville',
            'codigo_postal' => '28001',
            'provincia_codigo' => '28',
        ]);

        // El controlador redirige a la ruta 'home' con message success
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        // Comprobar que hay una incidencia con el título enviado
        $this->assertDatabaseHas('incidencias', [
            'titulo' => 'Prueba incidencia pública',
            'creada_por' => 'cliente',
        ]);
    }
}
