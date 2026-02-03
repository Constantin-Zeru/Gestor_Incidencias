@extends('layouts.app')

@section('content')
<div class="container">
  <h2>Crear empleado</h2>

  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="POST" action="{{ route('empleados.store') }}">
    @csrf

    <div class="mb-3">
      <label class="form-label">DNI</label>
      <input name="dni" class="form-control" value="{{ old('dni') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input name="nombre" class="form-control" value="{{ old('nombre') }}" required>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input name="telefono" class="form-control" value="{{ old('telefono') }}">
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Dirección</label>
      <input name="direccion" class="form-control" value="{{ old('direccion') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha alta</label>
      <input name="fecha_alta" type="date" class="form-control" value="{{ old('fecha_alta', now()->toDateString()) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Tipo</label>
      <select name="tipo" class="form-select" required>
        <option value="operario" {{ old('tipo')=='operario' ? 'selected':'' }}>Operario</option>
        <option value="admin" {{ old('tipo')=='admin' ? 'selected':'' }}>Administrador</option>
      </select>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Contraseña</label>
        <input name="password" type="password" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Confirmar contraseña</label>
        <input name="password_confirmation" type="password" class="form-control" required>
      </div>
    </div>

    <div class="d-grid">
      <button class="btn btn-primary">Crear empleado</button>
      <a href="{{ route('empleados.index') }}" class="btn btn-secondary mt-2">Cancelar</a>
    </div>
  </form>
</div>
@endsection
