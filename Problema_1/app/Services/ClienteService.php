<?php
namespace App\Services;

use App\Models\Cliente;

class ClienteService
{
    public function listar()
    {
        return Cliente::orderBy('nombre')->get();
    }

    public function crear(array $data): Cliente
    {
        return Cliente::create($data);
    }

    public function actualizar(Cliente $cliente, array $data): Cliente
    {
        $cliente->update($data);
        return $cliente;
    }

    public function eliminar(Cliente $cliente): void
    {
        $cliente->delete();
    }
}
