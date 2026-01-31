<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cuota
 *
 * Modelo que representa una cuota de mantenimiento o trabajo.
 *
 * @author Constantin Zeru
 * @version 1.0
 * @date 2026-01-29
 */
class Cuota extends Model
{
    /**
     * Campos asignables.
     *
     * @var array
     */
    protected $fillable = [
        'cliente_id',
        'concepto',
        'fecha_emision',
        'importe',
        'pagada',
        'fecha_pago',
        'notas'
    ];

    /**
     * Cliente propietario de la cuota.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Factura asociada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function factura()
    {
        return $this->hasOne(Factura::class);
    }
}
