<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Factura
 *
 * Modelo que representa una factura generada.
 *
 * @author Constantin Zeru
 * @version 1.1
 * @date 2026-02-03
 */
class Factura extends Model
{
    /**
     * Campos asignables.
     *
     * @var array
     */
    protected $fillable = [
        'cuota_id',
        'numero_factura',
        'fecha',
        'pdf_path',

        // Nuevos campos Problema 3.1
        'pagada',
        'moneda',
        'importe_euros',
        'tipo_cambio',
        'fecha_pago',
    ];

    /**
     * Conversión automática de tipos.
     *
     * @var array
     */
    protected $casts = [
        'pagada' => 'boolean',
        'fecha' => 'date',
        'fecha_pago' => 'datetime',
        'importe_euros' => 'decimal:2',
        'tipo_cambio' => 'decimal:6',
    ];

    /**
     * Cuota asociada a la factura.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cuota()
    {
        return $this->belongsTo(Cuota::class);
    }
}
