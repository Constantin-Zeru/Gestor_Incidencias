<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClienteService;
use App\Models\Cliente;

class ClienteController extends Controller
{
    protected $svc;

    public function __construct(ClienteService $svc)
    {
        $this->middleware(['auth','rol:admin']);
        $this->svc = $svc;
    }

    public function index()
    {
        $clientes = $this->svc->listar();
        return view('clientes.index', compact('clientes'));
    }

    public function create() { return view('clientes.create'); }

    public function store(Request $r)
    {
        $data = $r->validate([
            'cif' => 'required|unique:clientes,cif',
            'nombre' => 'required',
            'telefono' => 'required',
            'email' => 'nullable|email',
            'cuenta_corriente' => 'nullable',
            'pais' => 'required',
            'moneda' => 'required',
            'cuota_mensual' => 'required|numeric'
        ]);

        $this->svc->crear($data);
        return redirect()->route('clientes.index')->with('success','Cliente creado');
    }

    public function edit(Cliente $cliente) { return view('clientes.edit', compact('cliente')); }

    public function update(Request $r, Cliente $cliente)
    {
        $data = $r->validate([
            'nombre' => 'required',
            'telefono' => 'required',
            'email' => 'nullable|email',
            'cuenta_corriente' => 'nullable',
            'pais' => 'required',
            'moneda' => 'required',
            'cuota_mensual' => 'required|numeric'
        ]);
        $this->svc->actualizar($cliente,$data);
        return redirect()->route('clientes.index')->with('success','Cliente actualizado');
    }

    public function destroy(Cliente $cliente)
    {
        $this->svc->eliminar($cliente);
        return redirect()->route('clientes.index')->with('success','Cliente eliminado');
    }
}
