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
  'cliente_id','titulo','descripcion',
  'contacto_nombre','contacto_telefono','contacto_email',
  'direccion','poblacion','codigo_postal','provincia_codigo',
  'estado','fecha_realizacion','anotaciones_anteriores',
  'anotaciones_posteriores','fichero_resumen','empleado_id'
];

public function cliente() { return $this->belongsTo(Cliente::class); }
public function empleado() { return $this->belongsTo(Empleado::class,'empleado_id'); }

}
