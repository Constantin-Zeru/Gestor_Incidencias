@extends('layouts.app')

@section('content')
<h2>Crear cuota</h2>

@if ($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ route('cuotas.store') }}">
  @csrf
  <div class="mb-3">
    <label class="form-label">Cliente</label>
    <select name="cliente_id" class="form-select" required>
      <option value="">-- Seleccione --</option>
      @foreach($clientes as $cl)
        <option value="{{ $cl->id }}" {{ old('cliente_id') == $cl->id ? 'selected' : '' }}>{{ $cl->nombre }} ({{ $cl->cif }})</option>
      @endforeach
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Concepto</label>
    <input name="concepto" class="form-control" value="{{ old('concepto') }}" required>
  </div>

  <div class="mb-3 row">
    <div class="col-md-6">
      <label class="form-label">Fecha emisión</label>
      <input name="fecha_emision" type="date" class="form-control" value="{{ old('fecha_emision', date('Y-m-d')) }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Importe</label>
      <input name="importe" type="number" step="0.01" class="form-control" value="{{ old('importe', '0.00') }}" required>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Notas</label>
    <textarea name="notas" class="form-control">{{ old('notas') }}</textarea>
  </div>

  <button class="btn btn-primary">Crear</button>
  <a href="{{ route('cuotas.index') }}" class="btn btn-secondary ms-2">Volver</a>
</form>
@endsection
