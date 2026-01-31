<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Incidencia
 *
 * Modelo que representa una incidencia o tarea.
 *
 * @author Constantin Zeru
 * @version 1.0
 * @date 2026-01-29
 */
class Incidencia extends Model
{
    /**
     * Campos asignables.
     *
     * @var array
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'cliente_id',
        'empleado_id',
        'creada_por'
    ];

    /**
     * Cliente al que pertenece la incidencia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Empleado asignado (si existe).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
