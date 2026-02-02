@extends('layouts.app')

@section('content')
<div class="container">
  <h2>Editar incidencia #{{ $incidencia->id }}</h2>

  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="POST" action="{{ route('incidencias.update', $incidencia) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label class="form-label">Título</label>
      <input name="titulo" class="form-control" value="{{ old('titulo', $incidencia->titulo) }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" class="form-control" rows="4" required>{{ old('descripcion', $incidencia->descripcion) }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Cliente</label>
      <select name="cliente_id" class="form-select" required>
        <option value="">-- seleccionar cliente --</option>
        @foreach($clientes as $c)
          <option value="{{ $c->id }}" {{ (old('cliente_id', $incidencia->cliente_id) == $c->id) ? 'selected' : '' }}>
            {{ $c->nombre }} ({{ $c->cif }})
          </option>
        @endforeach
      </select>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Persona de contacto</label>
        <input name="contacto_nombre" class="form-control" value="{{ old('contacto_nombre', $incidencia->contacto_nombre) }}" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Teléfono</label>
        <input name="contacto_telefono" class="form-control" value="{{ old('contacto_telefono', $incidencia->contacto_telefono) }}" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Correo</label>
      <input name="contacto_email" type="email" class="form-control" value="{{ old('contacto_email', $incidencia->contacto_email) }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Dirección</label>
      <input name="direccion" class="form-control" value="{{ old('direccion', $incidencia->direccion) }}">
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Población</label>
        <input name="poblacion" class="form-control" value="{{ old('poblacion', $incidencia->poblacion) }}">
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Código postal</label>
        <input name="codigo_postal" class="form-control" value="{{ old('codigo_postal', $incidencia->codigo_postal) }}">
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Provincia</label>
        <select name="provincia_codigo" class="form-select">
          <option value="">-- seleccionar --</option>
          @foreach($provincias as $code => $name)
            <option value="{{ $code }}" {{ (old('provincia_codigo', $incidencia->provincia_codigo) == $code) ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha de realización (d/m/Y)</label>
      <input name="fecha_realizacion" class="form-control" value="{{ old('fecha_realizacion', optional($incidencia->fecha_realizacion)->format('d/m/Y') ?? '') }}" placeholder="31/12/2026">
    </div>

    <div class="mb-3">
      <label class="form-label">Operario asignado</label>
      <select name="empleado_id" class="form-select">
        <option value="">-- ninguno --</option>
        @foreach($empleados as $emp)
          <option value="{{ $emp->id }}" {{ (old('empleado_id', $incidencia->empleado_id) == $emp->id) ? 'selected' : '' }}>{{ $emp->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Fichero resumen (reemplaza si subes uno nuevo)</label>
      <input name="fichero_resumen" type="file" class="form-control">
      @if($incidencia->fichero_resumen)
        <div class="mt-2">
          <small class="text-muted">Fichero actual: {{ basename($incidencia->fichero_resumen) }}</small>
          <a href="{{ route('incidencias.download',$incidencia->id) }}" class="btn btn-sm btn-link">Descargar</a>
        </div>
      @endif
    </div>

    <div class="mb-3">
      <label class="form-label">Estado</label>
      <select name="estado" class="form-select">
        <option value="pendiente" {{ old('estado', $incidencia->estado) == 'pendiente' ? 'selected' : '' }}>pendiente</option>
        <option value="asignada" {{ old('estado', $incidencia->estado) == 'asignada' ? 'selected' : '' }}>asignada</option>
        <option value="en_proceso" {{ old('estado', $incidencia->estado) == 'en_proceso' ? 'selected' : '' }}>en_proceso</option>
        <option value="finalizada" {{ old('estado', $incidencia->estado) == 'finalizada' ? 'selected' : '' }}>finalizada</option>
      </select>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary">Guardar cambios</button>
      <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
@endsection
