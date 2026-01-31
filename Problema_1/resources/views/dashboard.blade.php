@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row mb-4">
    <div class="col">
      <h1 class="mb-1">Panel de control</h1>
      <p class="text-muted">Bienvenido, {{ auth()->user()->nombre }} — Rol: <strong>{{ ucfirst(auth()->user()->tipo) }}</strong></p>
    </div>
  </div>

  {{-- Resumen con tarjetas --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Clientes</h6>
          <h3 class="card-text mb-3">{{ $clientes_count ?? \App\Models\Cliente::count() }}</h3>

          <div class="mt-auto">
            @if(auth()->user()->tipo === 'admin')
              <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">Gestionar clientes</a>
              <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-primary w-100">Crear cliente</a>
            @else
              <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-primary w-100">Ver clientes</a>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Cuotas</h6>
          <h3 class="card-text mb-3">{{ $cuotas_count ?? \App\Models\Cuota::count() }}</h3>

          <div class="mt-auto">
            @if(auth()->user()->tipo === 'admin')
              <a href="{{ route('cuotas.index') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">Gestionar cuotas</a>
              <a href="{{ route('cuotas.create') }}" class="btn btn-sm btn-primary w-100 mb-2">Crear cuota</a>
              <form action="{{ route('cuotas.generar') }}" method="POST" class="d-grid">
                @csrf
                <button class="btn btn-sm btn-warning w-100" type="submit">Generar remesa mensual</button>
              </form>
            @else
              <a href="{{ route('cuotas.index') }}" class="btn btn-sm btn-outline-primary w-100">Ver cuotas</a>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Incidencias</h6>
          <h3 class="card-text mb-3">{{ $incidencias_count ?? \App\Models\Incidencia::count() }}</h3>

          <div class="mt-auto">
            @if(auth()->user()->tipo === 'operario')
              <a href="{{ route('incidencias.mis') }}" class="btn btn-sm btn-primary w-100">Mis incidencias</a>
            @else
              <a href="{{ route('incidencias.index') }}" class="btn btn-sm btn-primary w-100">Ver incidencias</a>
              <a href="{{ route('incidencias.create') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">Crear incidencia</a>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Facturas</h6>
          <h3 class="card-text mb-3">{{ $facturas_count ?? \App\Models\Factura::count() }}</h3>

          <div class="mt-auto">
            @if(auth()->user()->tipo === 'admin')
              <a href="{{ route('facturas.index') }}" class="btn btn-sm btn-outline-primary w-100">Ver facturas</a>
            @else
              <a href="{{ route('facturas.index') }}" class="btn btn-sm btn-outline-primary w-100">Ver facturas</a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>



  {{-- Últimas incidencias --}}
  <div class="row">
    <div class="col-md-8">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Últimas incidencias</h5>

          @if(($incidencias ?? collect())->isEmpty())
            <p class="text-muted mb-0">No hay incidencias recientes.</p>
          @else
            <ul class="list-group list-group-flush">
              @foreach($incidencias as $inc)
                <li class="list-group-item d-flex align-items-center justify-content-between">
                  <div>
                    <strong>#{{ $inc->id }}</strong> - {{ $inc->titulo }}
                    <div class="small text-muted">{{ optional($inc->cliente)->nombre ?? 'Cliente no asignado' }}</div>
                  </div>

                  <div class="d-flex gap-2">
                    @if(auth()->user()->tipo === 'admin')
                      <a href="{{ route('incidencias.index') }}" class="btn btn-sm btn-outline-primary">Ir a incidencias</a>
                    @else
                      <a href="{{ route('incidencias.mis') }}" class="btn btn-sm btn-outline-primary">Ver mis incidencias</a>
                    @endif
                    <span class="badge bg-secondary align-self-center">{{ ucfirst($inc->estado) }}</span>
                  </div>
                </li>
              @endforeach
            </ul>
          @endif

        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Información</h5>
          <p class="small text-muted">Accesos rápidos y estadísticas. Personaliza esta página si quieres añadir gráficos, filtros o widgets.</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
