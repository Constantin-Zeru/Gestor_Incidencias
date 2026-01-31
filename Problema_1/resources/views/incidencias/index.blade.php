@extends('layouts.app')

@section('content')
<h2>Listado de incidencias (Admin)</h2>
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
<table class="table table-striped">
    <thead><tr><th>ID</th><th>Título</th><th>Cliente</th><th>Empleado</th><th>Estado</th></tr></thead>
    <tbody>
    @foreach($incidencias as $i)
        <tr>
            <td>{{ $i->id }}</td>
            <td>{{ $i->titulo }}</td>
            <td>{{ optional($i->cliente)->nombre }}</td>
            <td>{{ optional($i->empleado)->nombre ?? 'Sin asignar' }}</td>
            <td>{{ $i->estado }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
