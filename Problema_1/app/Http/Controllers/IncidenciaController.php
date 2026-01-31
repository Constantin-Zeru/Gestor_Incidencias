<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IncidenciasService;
use App\Models\Empleado;

/**
 * Controlador para acciones administrativas y operario.
 */
class IncidenciaController extends Controller
{
    protected $svc;

    public function __construct(IncidenciasService $svc)
    {
        $this->svc = $svc;

        // Este método middleware() existe porque extendemos Controller
        $this->middleware('auth')->except(['publicCreateForm','publicStore']);
    }

    public function index()
    {
        $incidencias = $this->svc->obtenerTodas();
        return view('incidencias.index', compact('incidencias'));
    }

    public function misIncidencias()
    {
        $user = auth()->user();
        $incidencias = $this->svc->obtenerPorEmpleado($user->id);
        return view('incidencias.mis', compact('incidencias'));
    }

    public function create()
    {
        $empleados = Empleado::where('tipo','operario')->get();
        return view('incidencias.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'empleado_id' => 'required|integer|exists:empleados,id',
        ]);

        if (! $inc) {
    return back()
        ->withInput()
        ->withErrors([
            'identificacion' => 'No se encontró un cliente con esos datos. Si eres cliente, por favor contacta con soporte o verifica CIF y teléfono.'
        ]);
}

        $inc = $this->svc->crearPorAdmin($data);
        return redirect()->route('incidencias.index')->with('success','Incidencia creada y asignada.');
    }

    public function publicCreateForm()
    {
        return view('incidencias.public_create');
    }

    public function publicStore(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'cif' => 'required|string',
            'telefono' => 'required|string',
        ]);

        $inc = $this->svc->crearPorCliente($data);

        if (! $inc) {
            return back()->withErrors(['cif' => 'CIF y teléfono no coinciden con ningún cliente registrado.']);
        }

        return redirect()->back()->with('success','Incidencia registrada. Un administrador la asignará.');
    }
}
