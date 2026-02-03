<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

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
        $data['fecha_alta'] = $data['fecha_alta'] ?? now()->toDateString();

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
            'dni' => [
                'required','string','max:20',
                Rule::unique('empleados','dni')->ignore($empleado->id),
            ],
            'nombre' => 'required|string|max:255',
            'email' => [
                'required','email',
                Rule::unique('empleados','email')->ignore($empleado->id),
            ],
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:255',
            'fecha_alta' => 'nullable|date',
            'tipo' => ['required', 'in:admin,operario'],
            'password' => 'nullable|string|min:4|confirmed',
        ]);

        // Build update payload carefully to avoid sending nulls that rompan constraints
        $update = [
            'dni' => $data['dni'],
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'tipo' => $data['tipo'],
        ];

        // fecha_alta: solo incluir si viene y no está vacía
        if (!empty($data['fecha_alta'])) {
            try {
                $update['fecha_alta'] = Carbon::parse($data['fecha_alta'])->toDateString();
            } catch (\Exception $e) {
                // no incluir si no se puede parsear
            }
        }

        // password: solo si se envía
        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $empleado->update($update);

        return redirect()->route('empleados.index')->with('success','Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success','Empleado eliminado.');
    }
}
