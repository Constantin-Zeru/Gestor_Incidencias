<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','rol:admin']);
    }

    public function index()
    {
        $empleados = Empleado::orderBy('created_at','desc')->paginate(20);
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dni' => 'required|string|max:20|unique:empleados,dni',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:empleados,email',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:255',
            'fecha_alta' => 'nullable|date',
            'tipo' => ['required', 'in:admin,operario'],
            'password' => 'required|string|min:4|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['fecha_alta'] = $data['fecha_alta'] ?? now();

        Empleado::create($data);

        return redirect()->route('empleados.index')->with('success','Empleado creado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $data = $request->validate([
            'dni' => 'required|string|max:20|unique:empleados,dni,'.$empleado->id,
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:empleados,email,'.$empleado->id,
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:255',
            'fecha_alta' => 'nullable|date',
            'tipo' => ['required', 'in:admin,operario'],
            'password' => 'nullable|string|min:4|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $empleado->update($data);

        return redirect()->route('empleados.index')->with('success','Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success','Empleado eliminado.');
    }
}
