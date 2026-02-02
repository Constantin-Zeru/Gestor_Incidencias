@extends('layouts.app')

@section('content')
<div class="container">
  <h2>Registrar incidencia (cliente)</h2>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  @if ($errors->any())
    <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="POST" action="{{ route('incidencias.public.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label class="form-label">CIF (cliente)</label>
      <input name="cif" class="form-control" value="{{ old('cif') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Teléfono asociado (cliente)</label>
      <input name="telefono" class="form-control" value="{{ old('telefono') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Título</label>
      <input name="titulo" class="form-control" value="{{ old('titulo') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" class="form-control" rows="4" required>{{ old('descripcion') }}</textarea>
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
      <label class="form-label">Fichero resumen (opcional)</label>
      <input name="fichero_resumen" type="file" class="form-control">
      <div class="form-text">Máx 10MB.</div>
    </div>

    <div class="d-grid">
      <button class="btn btn-primary">Enviar incidencia</button>
    </div>
  </form>
</div>
@endsection
