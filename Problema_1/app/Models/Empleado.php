<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



/**
 * Class Empleado
 *
 * Modelo que representa a los empleados de la empresa.
 *
 * @author Constantin Zeru
 * @version 1.0
 * @date 2026-01-29
 *
 * @package App\Models
 */
class Empleado extends Authenticatable {
    use Notifiable;
    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'empleados';

    /**
     * Campos asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'dni',
        'nombre',
        'email',
        'telefono',
        'direccion',
        'fecha_alta',
        'tipo',
        'password'
    ];

    /**
     * Incidencias asignadas al empleado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
}
