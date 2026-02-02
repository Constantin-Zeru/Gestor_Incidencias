@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Incidencia #{{ $incidencia->id }} - {{ $incidencia->titulo }}</h2>
    <div>
      <a href="{{ route('incidencias.index') }}" class="btn btn-outline-secondary">Volver al listado</a>
      @if(auth()->user()->tipo === 'admin')
        <a href="{{ route('incidencias.edit', $incidencia) }}" class="btn btn-primary ms-2">Editar</a>
      @endif
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <p class="mb-1"><strong>Cliente:</strong> {{ optional($incidencia->cliente)->nombre ?? '—' }} (ID: {{ $incidencia->cliente_id }})</p>
      <p class="mb-1"><strong>Creada por:</strong> {{ $incidencia->creada_por }}</p>
      <p class="mb-1"><strong>Estado:</strong> <span class="badge bg-info text-dark">{{ $incidencia->estado }}</span></p>
      <p class="mb-1"><strong>Empleado asignado:</strong> {{ optional($incidencia->empleado)->nombre ?? 'Sin asignar' }}</p>
      <p class="mb-1"><strong>Fecha creación:</strong> {{ $incidencia->created_at }}</p>
      @if($incidencia->fecha_realizacion)
        <p class="mb-1"><strong>Fecha realización:</strong> {{ $incidencia->fecha_realizacion }}</p>
      @endif
      <hr>
      <h5>Datos de contacto</h5>
      <p class="mb-1"><strong>Persona:</strong> {{ $incidencia->contacto_nombre }}</p>
      <p class="mb-1"><strong>Teléfono:</strong> {{ $incidencia->contacto_telefono }}</p>
      <p class="mb-1"><strong>Email:</strong> {{ $incidencia->contacto_email }}</p>

      <hr>
      <h5>Dirección</h5>
      <p class="mb-1">{{ $incidencia->direccion }} — {{ $incidencia->poblacion }} {{ $incidencia->codigo_postal }} ({{ $incidencia->provincia_codigo }})</p>

      <hr>
      <h5>Descripción</h5>
      <p class="mb-0">{{ $incidencia->descripcion }}</p>

      @if($incidencia->anotaciones_anteriores)
        <hr><h6>Anotaciones anteriores</h6><p>{{ $incidencia->anotaciones_anteriores }}</p>
      @endif

      @if($incidencia->anotaciones_posteriores)
        <hr><h6>Anotaciones posteriores</h6><p>{{ $incidencia->anotaciones_posteriores }}</p>
      @endif

      @if($incidencia->fichero_resumen && (auth()->user()->tipo === 'admin' || (auth()->user()->tipo === 'operario' && auth()->user()->id == $incidencia->empleado_id)))
        <hr>
        <a href="{{ route('incidencias.download', $incidencia->id) }}" class="btn btn-sm btn-outline-primary">Descargar fichero resumen</a>
      @endif

      @if(auth()->user()->tipo === 'operario' && auth()->user()->id == $incidencia->empleado_id)
        <hr>
        <h5>Completar tarea</h5>
        <form method="POST" action="{{ route('incidencias.completar', $incidencia) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">Anotaciones posteriores</label>
            <textarea name="anotaciones_posteriores" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Fichero resumen (opcional)</label>
            <input type="file" name="fichero_resumen" class="form-control">
          </div>
          <button class="btn btn-success">Marcar como realizada</button>
        </form>
      @endif

    </div>
  </div>
</div>
@endsection
