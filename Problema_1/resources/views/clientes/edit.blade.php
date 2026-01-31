@extends('layouts.app')

@section('content')
<h2>Editar Cliente</h2>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('clientes.update', $cliente) }}">
  @csrf
  @method('PUT')

  <div class="mb-3">
    <label class="form-label">CIF</label>
    <input name="cif" class="form-control" value="{{ old('cif', $cliente->cif) }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input name="nombre" class="form-control" value="{{ old('nombre', $cliente->nombre) }}" required>
  </div>

  <div class="mb-3 row">
    <div class="col-md-6">
      <label class="form-label">Teléfono</label>
      <input name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input name="email" type="email" class="form-control" value="{{ old('email', $cliente->email) }}">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Cuenta Corriente</label>
    <input name="cuenta_corriente" class="form-control" value="{{ old('cuenta_corriente', $cliente->cuenta_corriente) }}">
  </div>

  <div class="mb-3 row">
    <div class="col-md-6">
      <label class="form-label">País</label>
      <input name="pais" class="form-control" value="{{ old('pais', $cliente->pais) }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Moneda</label>
      <input name="moneda" class="form-control" value="{{ old('moneda', $cliente->moneda) }}" required>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Cuota Mensual</label>
    <input name="cuota_mensual" type="number" step="0.01" class="form-control" value="{{ old('cuota_mensual', $cliente->cuota_mensual) }}" required>
  </div>

  <div class="d-grid">
    <button class="btn btn-primary">Actualizar cliente</button>
  </div>
</form>
@endsection
