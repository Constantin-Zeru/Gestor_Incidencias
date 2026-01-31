<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        Cliente::create([
            'cif' => 'CIF123456',
            'nombre' => 'Cliente Demo',
            'telefono' => '600111222',
            'email' => 'cliente@demo.com',
            'cuenta_corriente' => 'ES0000000000000000000000',
            'pais' => 'España',
            'moneda' => 'EUR',
            'cuota_mensual' => 120.00,
        ]);
    }
}
