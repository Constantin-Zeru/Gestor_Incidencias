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
    public function marcarPagada(int $cuotaId, ?string $fechaPago = null): Cuota
    {
        $cuota = Cuota::findOrFail($cuotaId);
        $cuota->pagada = true;
        $cuota->fecha_pago = $fechaPago ? Carbon::parse($fechaPago) : Carbon::now();
        $cuota->save();
        return $cuota;
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
