<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpleadoStoreRequest extends FormRequest
{
    public function authorize() { return auth()->check() && auth()->user()->tipo === 'admin'; }

    public function rules()
    {
        return [
            'dni' => ['required','string','max:20','regex:/^[0-9]{7,8}[A-Za-z]$/','unique:empleados,dni'],
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:empleados,email',
            'telefono' => ['nullable','string','max:30','regex:/^[0-9\-\+\s\(\)]{6,30}$/'],
            'direccion' => 'nullable|string|max:255',
            'fecha_alta' => 'nullable|date',
            'tipo' => ['required','in:admin,operario'],
            'password' => 'required|string|min:8|confirmed', // política mínima
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'dni' => strtoupper(trim($this->dni ?? '')),
            'email' => strtolower(trim($this->email ?? '')),
            'telefono' => preg_replace('/\s+/', ' ', trim($this->telefono ?? '')),
        ]);
    }
}
