<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CuotaService;
use App\Models\Cuota;
use App\Models\Cliente;

/**
 * Controller para CRUD de cuotas (admin).
 */
class CuotaController extends Controller
{
    protected $svc;

    public function __construct(CuotaService $svc)
    {
        $this->middleware(['auth','rol:admin']);
        $this->svc = $svc;
    }

    public function index()
    {
        $cuotas = $this->svc->listarTodas();
        return view('cuotas.index', compact('cuotas'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('cuotas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
    'cliente_id' => 'required|exists:clientes,id',
    'concepto' => 'required|string|max:255',
    'fecha_emision' => 'required|date',
    'importe' => 'required|numeric',
    'notas' => 'nullable|string',
    // moneda: ISO 3-letter, puedes ampliar la lista a las que necesites
    'moneda' => 'nullable|string|size:3|in:EUR,USD,GBP,PLN,CZK',
]);
$data['moneda'] = $data['moneda'] ?? 'EUR';
        $this->svc->crear($data);
        return redirect()->route('cuotas.index')->with('success','Cuota creada.');
    }

    public function edit(Cuota $cuota)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('cuotas.edit', compact('cuota','clientes'));
    }

    public function update(Request $request, Cuota $cuota)
    {
       $data = $request->validate([
    'cliente_id' => 'required|exists:clientes,id',
    'concepto' => 'required|string|max:255',
    'fecha_emision' => 'required|date',
    'importe' => 'required|numeric',
    'notas' => 'nullable|string',
    'pagada' => 'nullable|boolean',
    'fecha_pago' => 'nullable|date',
    'moneda' => 'nullable|string|size:3|in:EUR,USD,GBP,PLN,CZK',
]);

        $cuota->update($data);
        return redirect()->route('cuotas.index')->with('success','Cuota actualizada.');
    }

    public function destroy(Cuota $cuota)
    {
        $this->svc->eliminar($cuota->id);
        return redirect()->route('cuotas.index')->with('success','Cuota eliminada.');
    }

    public function marcarPagada(Request $request, $id)
    {
        $this->svc->marcarPagada($id, $request->input('fecha_pago'));
        return redirect()->route('cuotas.index')->with('success','Cuota marcada como pagada.');
    }
}
