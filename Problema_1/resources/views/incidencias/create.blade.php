@extends('layouts.app')

@section('content')
<h2>Crear incidencia (Administrador)</h2>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('incidencias.store') }}">
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
    <label class="form-label">Cliente (ID)</label>
    <input name="cliente_id" class="form-control" value="{{ old('cliente_id') }}" required>
    <div class="form-text">Introduce el ID del cliente (puedes buscarlo en la lista de clientes).</div>
  </div>

  <div class="mb-3">
    <label class="form-label">Operario asignado</label>
    <select name="empleado_id" class="form-select" required>
      <option value="">-- Selecciona operario --</option>
      @foreach($empleados as $e)
        <option value="{{ $e->id }}" {{ old('empleado_id') == $e->id ? 'selected' : '' }}>
          {{ $e->nombre }} ({{ $e->dni }})
        </option>
      @endforeach
    </select>
  </div>

  <button class="btn btn-primary">Crear y asignar</button>
  <a href="{{ route('incidencias.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
</form>
@endsection
