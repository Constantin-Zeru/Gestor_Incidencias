<?php

namespace App\Http\Controllers;

use App\Services\FacturaService;
use App\Models\Factura;
use Illuminate\Support\Facades\Storage;

class FacturaController extends Controller
{
    protected $svc;

    public function __construct(FacturaService $svc)
    {
        $this->middleware(['auth','rol:admin']);
        $this->svc = $svc;
    }

    // generar factura para cuota (ruta post /cuotas/{id}/factura)
    public function generarParaCuota($id)
    {
        $factura = $this->svc->generarFacturaDesdeCuota($id, true);
        return redirect()->back()->with('success','Factura generada y enviada (si cliente tiene email).');
    }

    // listado
    public function index()
    {
        $facturas = Factura::with('cuota.cliente')->orderBy('created_at','desc')->get();
        return view('facturas.index', compact('facturas'));
    }

    // descargar PDF
    public function download($id)
    {
        $factura = Factura::findOrFail($id);
        $path = $factura->pdf_path;
        if (! Storage::disk('public')->exists($path)) {
            return redirect()->back()->withErrors(['file' => 'Fichero PDF no encontrado.']);
        }
        return Storage::disk('public')->download($path, basename($path));
    }
}
