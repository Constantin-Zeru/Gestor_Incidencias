@extends('layouts.app')

@section('content')
<div class="container">
  <h2>Crear incidencia</h2>

  @if ($errors->any())
    <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="POST" action="{{ route('incidencias.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label class="form-label">Título</label>
      <input name="titulo" class="form-control" value="{{ old('titulo') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" class="form-control" rows="4" required>{{ old('descripcion') }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Cliente (opcional)</label>
      <select name="cliente_id" class="form-select">
        <option value="">-- ninguno --</option>
        @foreach($clientes as $c)
          <option value="{{ $c->id }}" {{ old('cliente_id')==$c->id ? 'selected':'' }}>{{ $c->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Persona de contacto</label>
        <input name="contacto_nombre" class="form-control" value="{{ old('contacto_nombre') }}" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Teléfono</label>
        <input name="contacto_telefono" class="form-control" value="{{ old('contacto_telefono') }}" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Correo</label>
      <input name="contacto_email" type="email" class="form-control" value="{{ old('contacto_email') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Dirección</label>
      <input name="direccion" class="form-control" value="{{ old('direccion') }}">
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Población</label>
        <input name="poblacion" class="form-control" value="{{ old('poblacion') }}">
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Código postal</label>
        <input name="codigo_postal" class="form-control" value="{{ old('codigo_postal') }}">
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Provincia</label>
        <select name="provincia_codigo" class="form-select">
          <option value="">-- seleccionar --</option>
          @foreach($provincias as $code => $name)
            <option value="{{ $code }}" {{ old('provincia_codigo')==$code ? 'selected':'' }}>{{ $name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha de realización (d/m/Y)</label>
      <input name="fecha_realizacion" class="form-control" value="{{ old('fecha_realizacion') }}" placeholder="31/12/2026">
    </div>

    <div class="mb-3">
      <label class="form-label">Fichero resumen</label>
      <input name="fichero_resumen" type="file" class="form-control">
      <div class="form-text">Máx 10MB. Solo accesible a admins y operario asignado.</div>
    </div>

    <button class="btn btn-primary">Crear incidencia</button>
    <a href="{{ route('incidencias.index') }}" class="btn btn-secondary ms-2">Volver</a>
  </form>
</div>
@endsection
