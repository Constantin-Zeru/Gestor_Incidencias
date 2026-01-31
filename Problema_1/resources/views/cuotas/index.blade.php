@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Cuotas</h2>
  <div>
    <a href="{{ route('cuotas.create') }}" class="btn btn-primary">Nueva cuota</a>
    <form method="POST" action="{{ route('cuotas.generar') }}" class="d-inline">
      @csrf
      <button class="btn btn-outline-secondary">Generar remesa mensual</button>
    </form>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Volver al panel</a>
  </div>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

@if($cuotas->isEmpty())
  <div class="alert alert-info">No hay cuotas.</div>
@else
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead><tr><th>ID</th><th>Cliente</th><th>Concepto</th><th>Importe</th><th>Pagada</th><th>Fecha emisión</th><th></th></tr></thead>
      <tbody>
      @foreach($cuotas as $c)
        <tr>
          <td>{{ $c->id }}</td>
          <td>{{ optional($c->cliente)->nombre }}</td>
          <td>{{ $c->concepto }}</td>
          <td>{{ number_format($c->importe,2) }}</td>
          <td>
            @if($c->pagada)
              <span class="badge bg-success">Sí</span>
            @else
              <span class="badge bg-warning text-dark">No</span>
            @endif
          </td>
          <td>{{ $c->fecha_emision }}</td>
          <td>
            <a class="btn btn-sm btn-secondary" href="{{ route('cuotas.edit',$c) }}">Editar</a>
            <form action="{{ route('cuotas.pagar', $c->id) }}" method="POST" style="display:inline">
              @csrf
              <button class="btn btn-sm btn-success">Marcar pagada</button>
            </form>
            <form action="{{ route('cuotas.destroy',$c) }}" method="POST" style="display:inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Borrar</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
@endif
@endsection
