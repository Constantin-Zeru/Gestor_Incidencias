@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Facturas</h2>
   <div>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al panel</a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($facturas->isEmpty())
  <div class="alert alert-info">No hay facturas generadas.</div>
@else
<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nº factura</th>
        <th>Cliente</th>
        <th>Cuota</th>
        <th>Fecha</th>
        <th>PDF</th>
      </tr>
    </thead>
    <tbody>
    @foreach($facturas as $f)
      <tr>
        <td>{{ $f->id }}</td>
        <td>{{ $f->numero_factura }}</td>
        <td>{{ optional($f->cuota->cliente)->nombre ?? '—' }}</td>
        <td>{{ optional($f->cuota)->concepto ?? '—' }}</td>
        <td>{{ $f->fecha->format('d/m/Y') }}</td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="{{ route('facturas.download', $f->id) }}">Descargar PDF</a>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endif
@endsection
