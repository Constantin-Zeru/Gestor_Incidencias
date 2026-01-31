<?php

namespace App\Services;

use App\Models\Incidencia;
use App\Models\Cliente;
use App\Models\Empleado;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Servicio que centraliza la lógica de negocio para incidencias.
 */
class IncidenciasService
{
    /**
     * Crear incidencia por un cliente (sin empleado asignado).
     *
     * @param array $data (titulo, descripcion, cif, telefono, cliente_id opcional)
     * @return Incidencia|null
     */
    public function crearPorCliente(array $data)
    {
        // Validamos cliente por cif + telefono
        $cliente = Cliente::where('cif', $data['cif'])
                          ->where('telefono', $data['telefono'])
                          ->first();

        if (! $cliente) {
            return null;
        }

        return Incidencia::create([
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'estado' => 'pendiente',
            'cliente_id' => $cliente->id,
            'empleado_id' => null,
            'creada_por' => 'cliente',
        ]);
    }

    /**
     * Crear incidencia por un administrador (debe venir empleado_id asignado).
     *
     * @param array $data
     * @return Incidencia
     * @throws \InvalidArgumentException
     */
    public function crearPorAdmin(array $data)
    {
        if (empty($data['empleado_id'])) {
            throw new \InvalidArgumentException('Debe especificarse un empleado asignado.');
        }

        // opcional: comprobar que empleado existe
        $empleado = Empleado::findOrFail($data['empleado_id']);

        return Incidencia::create([
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'estado' => 'asignada',
            'cliente_id' => $data['cliente_id'],
            'empleado_id' => $empleado->id,
            'creada_por' => 'admin',
        ]);
    }

    /**
     * Asignar un empleado a una incidencia existente (solo admin).
     *
     * @param int $incidenciaId
     * @param int $empleadoId
     * @return Incidencia
     */
    public function asignarEmpleado(int $incidenciaId, int $empleadoId)
    {
        $inc = Incidencia::findOrFail($incidenciaId);
        $empleado = Empleado::findOrFail($empleadoId);

        $inc->empleado_id = $empleado->id;
        $inc->estado = 'asignada';
        $inc->save();

        return $inc;
    }

    /**
     * Obtener incidencias de un empleado (operario).
     *
     * @param int $empleadoId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerPorEmpleado(int $empleadoId)
    {
        return Incidencia::where('empleado_id', $empleadoId)
                         ->orderBy('created_at', 'desc')
                         ->get();
    }

    /**
     * Obtener todas (admin).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerTodas()
    {
        return Incidencia::with(['cliente','empleado'])->orderBy('created_at','desc')->get();
    }
}
