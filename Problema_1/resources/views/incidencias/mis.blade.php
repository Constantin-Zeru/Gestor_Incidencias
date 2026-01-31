@extends('layouts.app')

@section('content')
<h2>Mis incidencias</h2>

@if($incidencias->isEmpty())
  <div class="alert alert-info">No tienes incidencias asignadas.</div>
@else
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>Cliente</th>
          <th>Estado</th>
          <th>Creada</th>
        </tr>
      </thead>
      <tbody>
      @foreach($incidencias as $i)
        <tr>
          <td>{{ $i->id }}</td>
          <td>{{ $i->titulo }}</td>
          <td>{{ optional($i->cliente)->nombre ?? '—' }}</td>
          <td>{{ ucfirst($i->estado) }}</td>
          <td>{{ $i->created_at->diffForHumans() }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
@endif
@endsection
