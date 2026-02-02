@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Empleados</h2>
    <a href="{{ route('empleados.create') }}" class="btn btn-primary">Nuevo empleado</a>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  @if($empleados->isEmpty())
    <div class="alert alert-info">No hay empleados.</div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Tipo</th><th>Fecha alta</th><th></th></tr></thead>
        <tbody>
          @foreach($empleados as $e)
            <tr>
              <td>{{ $e->id }}</td>
              <td>{{ $e->nombre }}</td>
              <td>{{ $e->email }}</td>
              <td>{{ $e->tipo }}</td>
              <td>{{ optional($e->fecha_alta)->format('Y-m-d') ?? $e->created_at->format('Y-m-d') }}</td>
              <td>
                <a class="btn btn-sm btn-secondary" href="{{ route('empleados.edit', $e) }}">Editar</a>
                <form action="{{ route('empleados.destroy', $e) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Seguro que deseas borrar este empleado?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Borrar</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{ $empleados->links() }}
  @endif
</div>
@endsection
