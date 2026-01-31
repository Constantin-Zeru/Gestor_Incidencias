@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Clientes</h2>
    <div>
      <a href="{{ route('clientes.create') }}" class="btn btn-primary">Nuevo cliente</a>
      <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Volver al panel</a>
    </div>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  @if(isset($clientes) && $clientes->isEmpty())
    <div class="alert alert-info">No hay clientes.</div>
  @endif

  @if(isset($clientes) && !$clientes->isEmpty())
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr><th>ID</th><th>Nombre</th><th>CIF</th><th>Teléfono</th><th>Correo</th><th>Acciones</th></tr>
        </thead>
        <tbody>
          @foreach($clientes as $c)
            <tr>
              <td>{{ $c->id }}</td>
              <td>{{ $c->nombre }}</td>
              <td>{{ $c->cif }}</td>
              <td>{{ $c->telefono }}</td>
              <td>{{ $c->email }}</td>
              <td>
                <a href="{{ route('clientes.edit', $c) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                <form action="{{ route('clientes.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar cliente {{ $c->nombre }}?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Borrar</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if(method_exists($clientes,'links'))
      {{ $clientes->links() }}
    @endif
  @endif
</div>
@endsection
