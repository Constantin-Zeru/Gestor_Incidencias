<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\Empleado;

class Incidencia extends Model
{
    protected $table = 'incidencias';

    protected $fillable = [
        'titulo','descripcion','cliente_id','contacto_nombre','contacto_telefono',
        'contacto_email','direccion','poblacion','codigo_postal','provincia_codigo',
        'estado','fecha_realizacion','anotaciones_anteriores','anotaciones_posteriores',
        'fichero_resumen','empleado_id','creada_por'
    ];

    // garantías de valores por defecto (doble protección)
    protected $attributes = [
        'estado' => 'pendiente',
        'creada_por' => 'cliente',
    ];

    // relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
