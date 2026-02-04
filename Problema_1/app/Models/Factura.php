<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'cuota_id',
        'numero_factura',
        'fecha',
        'pdf_path',

        'pagada',
        'moneda',
        'importe_euros',
        'tipo_cambio',
        'fecha_pago',
    ];

    protected $casts = [
        'pagada' => 'boolean',
        'fecha' => 'date',
        'fecha_pago' => 'datetime',
        'importe_euros' => 'decimal:2',
        'tipo_cambio' => 'decimal:6',
    ];

    public function cuota()
    {
        return $this->belongsTo(Cuota::class);
    }
}
