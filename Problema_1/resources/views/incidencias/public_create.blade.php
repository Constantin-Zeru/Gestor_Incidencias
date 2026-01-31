@extends('layouts.app')

@section('content')
<h2>Registrar incidencia (Cliente)</h2>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

@if ($errors->has('identificacion'))
  <div class="alert alert-warning">{{ $errors->first('identificacion') }}</div>
@endif


<form method="POST" action="{{ route('incidencias.public.store') }}">
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
    <label class="form-label">CIF</label>
    <input name="cif" class="form-control" value="{{ old('cif') }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input name="telefono" class="form-control" value="{{ old('telefono') }}" required>
  </div>

  <button class="btn btn-primary">Registrar incidencia</button>
</form>
@endsection
