@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Incidencias</h2>
    @if(auth()->user()->tipo === 'admin')
      <a href="{{ route('incidencias.create') }}" class="btn btn-primary">Crear incidencia</a>
    @endif
  </div>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-4"><input name="q" value="{{ request('q') }}" placeholder="buscar..." class="form-control"></div>
    <div class="col-md-2">
      <select name="estado" class="form-select">
        <option value="">Todos</option>
        <option value="pendiente" {{ request('estado')=='pendiente'?'selected':'' }}>pendiente</option>
        <option value="asignada" {{ request('estado')=='asignada'?'selected':'' }}>asignada</option>
        <option value="en_proceso" {{ request('estado')=='en_proceso'?'selected':'' }}>en_proceso</option>
        <option value="finalizada" {{ request('estado')=='finalizada'?'selected':'' }}>finalizada</option>
      </select>
    </div>
    <div class="col-md-3">
      <select name="provincia" class="form-select">
        <option value="">Todas las provincias</option>
        @foreach($provincias as $k=>$v)
          <option value="{{ $k }}" {{ request('provincia')==$k ? 'selected' : '' }}>{{ $v }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-primary">Filtrar</button>
    </div>
  </form>

  @if($incidencias->isEmpty())
    <div class="alert alert-info">No hay incidencias.</div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead><tr><th>ID</th><th>Título</th><th>Cliente</th><th>Empleado</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        @foreach($incidencias as $i)
          <tr>
            <td>#{{ $i->id }}</td>
            <td>{{ $i->titulo }}</td>
            <td>{{ optional($i->cliente)->nombre }}</td>
            <td>{{ optional($i->empleado)->nombre ?? 'Sin asignar' }}</td>
            <td><span class="badge bg-secondary">{{ $i->estado }}</span></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('incidencias.show',$i) }}">Ver</a>
              @if(auth()->user()->tipo === 'admin')
                <a class="btn btn-sm btn-secondary" href="{{ route('incidencias.edit',$i) }}">Editar</a>
                <form action="{{ route('incidencias.destroy',$i) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Borrar incidencia #{{ $i->id }}?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Borrar</button>
                </form>
              @endif

              @if($i->fichero_resumen && (auth()->user()->tipo==='admin' || (auth()->user()->tipo==='operario' && auth()->user()->id == $i->empleado_id)))
                <a class="btn btn-sm btn-outline-info" href="{{ route('incidencias.download',$i->id) }}">Descargar</a>
              @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    {{ $incidencias->withQueryString()->links() }}
  @endif
</div>
@endsection
