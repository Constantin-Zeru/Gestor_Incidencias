<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CuotaService;

/**
 * Comando para generar remesa mensual de cuotas.
 */
class GenerarRemesaMensual extends Command
{
    protected $signature = 'cuotas:generar {fecha?}';
    protected $description = 'Generar remesa mensual (crear una cuota por cliente usando cuota_mensual)';

    protected $svc;

    public function __construct(CuotaService $svc)
    {
        parent::__construct();
        $this->svc = $svc;
    }

    public function handle()
    {
        $fecha = $this->argument('fecha');
        $created = $this->svc->generarRemesaMensual($fecha);
        $this->info('Cuotas creadas: ' . $created->count());
        return 0;
    }
}
