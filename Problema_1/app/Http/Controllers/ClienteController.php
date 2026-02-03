<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Incidencia;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function __construct()
    {
        // Admin para la mayoría de métodos; operario accederá sólo al método operarioIndex (se añade middleware en rutas)
        $this->middleware(['auth','rol:admin'])->except(['operarioIndex']);
    }

    /**
     * Index admin (gestionar clientes)
     */
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($q = $request->input('q')) {
            $q = trim($q);
            $query->where(function($qq) use ($q) {
                $qq->where('nombre','like',"%{$q}%")
                   ->orWhere('cif','like',"%{$q}%")
                   ->orWhere('email','like',"%{$q}%");
            });
        }

        $clientes = $query->orderBy('nombre')->paginate(20)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    /**
     * Mostrar formulario crear (admin)
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Guardar cliente (admin)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cif' => ['required','string','max:50','unique:clientes,cif'],
            'nombre' => ['required','string','max:255'],
            'telefono' => ['nullable','string','max:30','regex:/^[0-9\-\+\s\(\)]{6,30}$/'],
            'email' => ['nullable','email','max:255','unique:clientes,email'],
            'cuenta_corriente' => ['nullable','string','max:50'],
            'pais' => ['nullable','string','max:100'],
            'moneda' => ['nullable','string','max:10'],
            'cuota_mensual' => ['nullable','numeric'],
        ]);

        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success','Cliente creado correctamente.');
    }

    /**
     * Editar cliente (admin)
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualizar cliente (admin)
     */
    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'cif' => ['required','string','max:50', Rule::unique('clientes','cif')->ignore($cliente->id)],
            'nombre' => ['required','string','max:255'],
            'telefono' => ['nullable','string','max:30','regex:/^[0-9\-\+\s\(\)]{6,30}$/'],
            'email' => ['nullable','email','max:255', Rule::unique('clientes','email')->ignore($cliente->id)],
            'cuenta_corriente' => ['nullable','string','max:50'],
            'pais' => ['nullable','string','max:100'],
            'moneda' => ['nullable','string','max:10'],
            'cuota_mensual' => ['nullable','numeric'],
        ]);

        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success','Cliente actualizado correctamente.');
    }

    /**
     * Borrar cliente (admin)
     */
    public function destroy(Cliente $cliente)
    {
        // Si quieres evitar borrar clientes con incidencias, puedes comprobar:
        if ($cliente->incidencias()->exists()) {
            return redirect()->route('clientes.index')->with('error','No se puede borrar: el cliente tiene incidencias asociadas.');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')->with('success','Cliente eliminado.');
    }

    /**
     * Índice read-only para operarios: solo muestra los clientes que tienen incidencias asignadas
     * al operario autenticado. Método pensado para rutas protegidas con rol:operario.
     */
    public function operarioIndex(Request $request)
    {
        $user = auth()->user();

        // obtener IDs de clientes asociados a incidencias del operario
        $clienteIds = Incidencia::where('empleado_id', $user->id)
                        ->pluck('cliente_id')
                        ->unique()
                        ->filter() // elimina nulls
                        ->values()
                        ->all();

        $clientes = Cliente::whereIn('id', $clienteIds)
                    ->orderBy('nombre')
                    ->get();

        return view('clientes.operario_index', compact('clientes'));
    }
}
