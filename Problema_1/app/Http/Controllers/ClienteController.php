<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nombre')->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'cif' => 'required|string|max:50|unique:clientes,cif',
            'telefono' => 'nullable|string|max:30',
            'email' => 'required|email|unique:clientes,email',
            'cuenta_corriente' => 'nullable|string|max:64',
            'pais' => 'nullable|string|max:64',
            'moneda' => 'nullable|string|max:8',
            'cuota_mensual' => 'nullable|numeric'
        ]);

        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success','Cliente creado.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'cif' => 'required|string|max:50|unique:clientes,cif,'.$cliente->id,
            'telefono' => 'nullable|string|max:30',
            'email' => 'required|email|unique:clientes,email,'.$cliente->id,
            'cuenta_corriente' => 'nullable|string|max:64',
            'pais' => 'nullable|string|max:64',
            'moneda' => 'nullable|string|max:8',
            'cuota_mensual' => 'nullable|numeric'
        ]);

        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success','Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success','Cliente eliminado.');
    }
}
