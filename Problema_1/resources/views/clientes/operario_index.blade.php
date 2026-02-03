@extends('layouts.app')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Clientes relacionados</h2>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Volver al panel</a>
  </div>

  @if($clientes->isEmpty())
    <div class="alert alert-info">No tienes clientes asociados a tus incidencias.</div>
  @else
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr><th>Nombre</th><th>CIF</th><th>Teléfono</th><th>Email</th><th>Cuota</th></tr>
        </thead>
        <tbody>
          @foreach($clientes as $c)
            <tr>
              <td>{{ $c->nombre }}</td>
              <td>{{ $c->cif }}</td>
              <td>{{ $c->telefono }}</td>
              <td>{{ $c->email }}</td>
              <td>{{ $c->cuota_mensual ? number_format($c->cuota_mensual,2) : '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
