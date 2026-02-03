<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_routes_return_ok()
    {
        $publicRoutes = [
            '/',                            // home (público)
            '/incidencias/public/create',   // formulario público
            '/login',                       // login
        ];

        foreach ($publicRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_protected_routes_redirect_to_login_when_unauthenticated()
    {
        $protected = [
            '/dashboard',
            '/incidencias',
            '/clientes',
            '/cuotas',
        ];

        foreach ($protected as $route) {
            $response = $this->get($route);
            // si la ruta devuelve 403 (control de acceso) lo aceptamos también
            $this->assertTrue(
                in_array($response->status(), [302, 403]),
                "Ruta {$route} devolvió status {$response->status()} (esperado 302 ó 403)"
            );
        }
    }
}
