@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Listado de incidencias</h2>
  <div>
    @if(auth()->user()->tipo === 'admin')
      <a href="{{ route('incidencias.create') }}" class="btn btn-primary">Crear incidencia</a>
    @else
      <a href="{{ route('incidencias.public.create') }}" class="btn btn-primary">Reportar incidencia</a>
    @endif
    <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Volver al panel</a>
  </div>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

<table class="table table-striped">
  <thead><tr><th>ID</th><th>Título</th><th>Cliente</th><th>Empleado</th><th>Estado</th><th>Acciones</th></tr></thead>
  <tbody>
    @forelse($incidencias as $i)
      <tr>
        <td>{{ $i->id }}</td>
        <td>{{ $i->titulo }}</td>
        <td>{{ optional($i->cliente)->nombre }}</td>
        <td>{{ optional($i->empleado)->nombre ?? 'Sin asignar' }}</td>
        <td>{{ $i->estado }}</td>
        <td>
          <a href="{{ route('incidencias.edit', $i) }}" class="btn btn-sm btn-outline-primary">Editar</a>
          <a href="{{ route('incidencias.show', $i) }}" class="btn btn-sm btn-secondary">Ver</a>
          <form action="{{ route('incidencias.destroy', $i) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar incidencia #{{ $i->id }}?');">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger">Borrar</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="6">No hay incidencias</td></tr>
    @endforelse
  </tbody>
</table>

@if(method_exists($incidencias,'links'))
  {{ $incidencias->links() }}
@endif
@endsection
