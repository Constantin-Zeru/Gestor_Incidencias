<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Factura
 *
 * Modelo que representa una factura generada.
 *
 * @author Constantin Zeru
 * @version 1.0
 * @date 2026-01-29
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
        'pdf_path'
    ];

    /**
     * Cuota asociada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cuota()
    {
        return $this->belongsTo(Cuota::class);
    }
}
