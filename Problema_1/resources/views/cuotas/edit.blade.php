@extends('layouts.app')

@section('content')
<h2>Editar cuota #{{ $cuota->id }}</h2>

@if ($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ route('cuotas.update', $cuota) }}">
  @csrf @method('PUT')

  <div class="mb-3">
    <label class="form-label">Cliente</label>
    <select name="cliente_id" class="form-select" required>
      @foreach($clientes as $cl)
        <option value="{{ $cl->id }}" {{ old('cliente_id', $cuota->cliente_id) == $cl->id ? 'selected' : '' }}>
          {{ $cl->nombre }} ({{ $cl->cif }})
        </option>
      @endforeach
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Concepto</label>
    <input name="concepto" class="form-control" value="{{ old('concepto', $cuota->concepto) }}" required>
  </div>

  <div class="mb-3 row">
    <div class="col-md-6">
      <label class="form-label">Fecha emisión</label>
      <input name="fecha_emision" type="date" class="form-control" value="{{ old('fecha_emision', $cuota->fecha_emision) }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Importe</label>
      <input name="importe" type="number" step="0.01" class="form-control" value="{{ old('importe', $cuota->importe) }}" required>
    </div>
  </div>

  <div class="mb-3">
  <label class="form-label">Moneda</label>
  <select name="moneda" class="form-select">
    @php
      $currencies = [
        'EUR'=>'EUR - Euro',
        'USD'=>'USD - Dólar',
        'GBP'=>'GBP - Libra',
        'PLN'=>'PLN - Zł',
        'CZK'=>'CZK - Koruna'
      ];
    @endphp
    @foreach($currencies as $code => $label)
      <option value="{{ $code }}" {{ old('moneda', $cuota->moneda ?? 'EUR') == $code ? 'selected' : '' }}>
        {{ $label }}
      </option>
    @endforeach
  </select>
</div>


  <div class="mb-3">
    <label class="form-label">Notas</label>
    <textarea name="notas" class="form-control">{{ old('notas', $cuota->notas) }}</textarea>
  </div>

  <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" name="pagada" value="1" {{ $cuota->pagada ? 'checked' : '' }}>
    <label class="form-check-label">Marcada como pagada</label>
  </div>

  <button class="btn btn-primary">Actualizar</button>
  <a href="{{ route('cuotas.index') }}" class="btn btn-secondary ms-2">Volver</a>
</form>
@endsection
