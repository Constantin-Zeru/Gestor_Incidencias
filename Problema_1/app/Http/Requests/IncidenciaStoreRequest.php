<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncidenciaStoreRequest extends FormRequest
{
    public function authorize() { return auth()->check() && auth()->user()->tipo === 'admin'; }

    public function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'cliente_id' => 'required|exists:clientes,id',
            'contacto_nombre' => 'required|string|max:255',
            'contacto_telefono' => ['required','string','regex:/^[0-9\-\+\s\(\)]{6,30}$/'],
            'contacto_email' => 'required|email',
            'codigo_postal' => ['nullable','regex:/^\d{5}$/'],
            'provincia_codigo' => ['nullable','digits:2'],
            'fecha_realizacion' => ['nullable','date_format:d/m/Y','after:today'],
            'fichero_resumen' => 'nullable|file|max:10240',
            'empleado_id' => 'nullable|exists:empleados,id',
            'estado' => ['nullable','string'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'contacto_telefono' => preg_replace('/\s+/', '', $this->contacto_telefono ?? ''),
            'titulo' => trim($this->titulo ?? ''),
        ]);
    }
}
