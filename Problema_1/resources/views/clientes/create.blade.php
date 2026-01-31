@extends('layouts.app')

@section('content')
<h2>Nuevo Cliente</h2>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('clientes.store') }}">
  @csrf

  <div class="mb-3">
    <label class="form-label">CIF</label>
    <input name="cif" class="form-control" value="{{ old('cif') }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input name="nombre" class="form-control" value="{{ old('nombre') }}" required>
  </div>

  <div class="mb-3 row">
    <div class="col-md-6">
      <label class="form-label">Teléfono</label>
      <input name="telefono" class="form-control" value="{{ old('telefono') }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input name="email" type="email" class="form-control" value="{{ old('email') }}">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Cuenta Corriente</label>
    <input name="cuenta_corriente" class="form-control" value="{{ old('cuenta_corriente') }}">
  </div>

  <div class="mb-3 row">
    <div class="col-md-6">
      <label class="form-label">País</label>
      <input name="pais" class="form-control" value="{{ old('pais','España') }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Moneda</label>
      <input name="moneda" class="form-control" value="{{ old('moneda','EUR') }}" required>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Cuota Mensual</label>
    <input name="cuota_mensual" type="number" step="0.01" class="form-control" value="{{ old('cuota_mensual',120.00) }}" required>
  </div>

  <div class="d-grid">
    <button class="btn btn-primary">Crear cliente</button>
  </div>
</form>
@endsection
