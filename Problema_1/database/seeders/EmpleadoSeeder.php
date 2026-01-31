<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empleado;

class EmpleadoSeeder extends Seeder
{
    public function run()
    {
        Empleado::updateOrCreate(
            ['email' => 'operario@test.com'],
            [
                'dni' => '87654321B',
                'nombre' => 'Operario Demo',
                'password' => bcrypt('operario123'),
                'fecha_alta' => now(),
                'tipo' => 'operario'
            ]
        );
    }
}
