<?php
namespace App\Services;

use App\Models\Cuota;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Servicio para lógica de negocio de Cuotas.
 */
class CuotaService
{
    /**
     * Listar todas las cuotas (admin).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listarTodas()
    {
        return Cuota::with('cliente')->orderBy('fecha_emision','desc')->get();
    }

    /**
     * Listar cuotas de un cliente.
     *
     * @param int $clienteId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listarPorCliente(int $clienteId)
    {
        return Cuota::where('cliente_id', $clienteId)->orderBy('fecha_emision','desc')->get();
    }

    /**
     * Crear una cuota individual.
     *
     * @param array $data
     * @return Cuota
     */
    public function crear(array $data): Cuota
    {
        return Cuota::create([
            'cliente_id' => $data['cliente_id'],
            'concepto' => $data['concepto'],
            'fecha_emision' => $data['fecha_emision'],
            'importe' => $data['importe'],
            'pagada' => $data['pagada'] ?? false,
            'fecha_pago' => $data['fecha_pago'] ?? null,
            'notas' => $data['notas'] ?? null
        ]);
    }

    /**
     * Marcar cuota como pagada.
     *
     * @param int $cuotaId
     * @param string|null $fechaPago
     * @return Cuota
     */
 public function marcarPagada(int $cuotaId, ?string $fechaPago = null): void
{
    $cuota = \App\Models\Cuota::findOrFail($cuotaId);

    // marcar cuota
    $cuota->update([
        'pagada' => true,
        'fecha_pago' => $fechaPago ? \Carbon\Carbon::parse($fechaPago) : now(),
    ]);

    $facturaService = app(\App\Services\FacturaService::class);
    $currencyService = app(\App\Services\CurrencyService::class);

    // crear factura si no existe
    $factura = $cuota->factura;
    if (! $factura) {
        $factura = $facturaService->generarFacturaDesdeCuota($cuota);
    }

    // marcar la factura como pagada (calcula euros y regenera PDF)
    $facturaService->marcarComoPagada($factura, $currencyService);
}


    /**
     * Generar remesa mensual: crea una cuota por cada cliente usando cuota_mensual.
     *
     * @param string|null $fecha Emission date in 'Y-m-d' format (defaults to today)
     * @return Collection created cuotas
     */
    public function generarRemesaMensual(?string $fecha = null): Collection
    {
        $fechaEmision = $fecha ? Carbon::parse($fecha) : Carbon::now();
        $clientes = Cliente::all();
        $created = collect();

        foreach ($clientes as $cliente) {
            $cuota = Cuota::create([
                'cliente_id' => $cliente->id,
                'concepto' => 'Cuota mensual ' . $fechaEmision->format('Y-m'),
                'fecha_emision' => $fechaEmision->toDateString(),
                'importe' => $cliente->cuota_mensual,
                'pagada' => false
            ]);
            $created->push($cuota);
        }

        return $created;
    }

    /**
     * Eliminar cuota.
     */
    public function eliminar(int $id): void
    {
        $cuota = Cuota::findOrFail($id);
        $cuota->delete();
    }
}
