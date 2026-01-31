<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cliente
 *
 * Modelo que representa a los clientes de la empresa.
 *
 * @author Constantin Zeru
 * @version 1.0
 * @date 2026-01-29
 */
class Cliente extends Model
{
    /**
     * Tabla asociada.
     *
     * @var string
     */
    protected $table = 'clientes';

    /**
     * Campos asignables.
     *
     * @var array
     */
    protected $fillable = [
        'cif',
        'nombre',
        'telefono',
        'email',
        'cuenta_corriente',
        'pais',
        'moneda',
        'cuota_mensual'
    ];

    /**
     * Incidencias del cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }

    /**
     * Cuotas asociadas al cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cuotas()
    {
        return $this->hasMany(Cuota::class);
    }
}
