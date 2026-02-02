@extends('layouts.app')

@section('content')
<div class="container">
  <h2>Editar empleado</h2>

  @if ($errors->any())
    <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <form method="POST" action="{{ route('empleados.update', $empleado) }}">
    @csrf @method('PUT')

    <div class="mb-3">
      <label class="form-label">DNI</label>
      <input name="dni" class="form-control" value="{{ old('dni',$empleado->dni) }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input name="nombre" class="form-control" value="{{ old('nombre',$empleado->nombre) }}" required>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email',$empleado->email) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input name="telefono" class="form-control" value="{{ old('telefono',$empleado->telefono) }}">
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Dirección</label>
      <input name="direccion" class="form-control" value="{{ old('direccion',$empleado->direccion) }}">
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label class="form-label">Fecha alta</label>
        <input name="fecha_alta" type="date" class="form-control" value="{{ old('fecha_alta', optional($empleado->fecha_alta)->format('Y-m-d')) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Tipo</label>
        <select name="tipo" class="form-select" required>
          <option value="operario" {{ old('tipo',$empleado->tipo)=='operario' ? 'selected':'' }}>Operario</option>
          <option value="admin" {{ old('tipo',$empleado->tipo)=='admin' ? 'selected':'' }}>Administrador</option>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Nueva contraseña (dejar en blanco para no cambiar)</label>
        <input name="password" type="password" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Confirmar contraseña</label>
        <input name="password_confirmation" type="password" class="form-control">
      </div>
    </div>

    <div class="d-grid">
      <button class="btn btn-primary">Actualizar empleado</button>
    </div>
  </form>
</div>
@endsection
