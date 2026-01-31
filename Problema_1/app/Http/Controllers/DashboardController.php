<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\Incidencia;
use App\Models\Factura;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $data = [
            'clientes_count' => Cliente::count(),
            'cuotas_count'   => Cuota::count(),
            'facturas_count' => Factura::count(),
        ];

        if ($user->tipo === 'operario') {
            $data['incidencias'] = Incidencia::where('empleado_id', $user->id)
                ->with('cliente')
                ->latest()
                ->take(8)
                ->get();
            $data['incidencias_count'] = Incidencia::where('empleado_id', $user->id)->count();
        } else { // admin u otros
            $data['incidencias'] = Incidencia::with('cliente','empleado')->latest()->take(8)->get();
            $data['incidencias_count'] = Incidencia::count();
        }

        return view('dashboard', $data);
    }
}
