@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row mb-4">
    <div class="col">
      <h1 class="mb-1">Panel de control</h1>
      <p class="text-muted">
        Bienvenido, {{ auth()->user()->nombre }}
        — Rol: <strong>{{ ucfirst(auth()->user()->tipo) }}</strong>
      </p>
    </div>
  </div>

  {{-- TARJETAS --}}
  <div class="row g-3 mb-4">

    {{-- ================= ADMIN ================= --}}
    @if(auth()->user()->tipo === 'admin')

    {{-- EMPLEADOS --}}
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Empleados</h6>
          <h3 class="mb-3">{{ \App\Models\Empleado::count() }}</h3>
          <div class="mt-auto">
            <a href="{{ route('empleados.index') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
              Gestionar empleados
            </a>
            <a href="{{ route('empleados.create') }}" class="btn btn-sm btn-primary w-100">
              Crear empleado
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CLIENTES --}}
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Clientes</h6>
          <h3 class="mb-3">{{ \App\Models\Cliente::count() }}</h3>
          <div class="mt-auto">
            <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
              Gestionar clientes
            </a>
            <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-primary w-100">
              Crear cliente
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- CUOTAS --}}
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Cuotas</h6>
          <h3 class="mb-3">{{ \App\Models\Cuota::count() }}</h3>
          <div class="mt-auto">
            <a href="{{ route('cuotas.index') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
              Gestionar cuotas
            </a>
            <a href="{{ route('cuotas.create') }}" class="btn btn-sm btn-primary w-100 mb-2">
              Crear cuota
            </a>
            <form method="POST" action="{{ route('cuotas.generar') }}">
              @csrf
              <button class="btn btn-sm btn-warning w-100">
                Generar remesa
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    {{-- FACTURAS --}}
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Facturas</h6>
          <h3 class="mb-3">{{ \App\Models\Factura::count() }}</h3>
          <div class="mt-auto">
            <a href="{{ route('facturas.index') }}" class="btn btn-sm btn-outline-primary w-100">
              Ver facturas
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- INCIDENCIAS ADMIN --}}
    <div class="col-sm-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Incidencias</h6>
          <h3 class="mb-3">{{ \App\Models\Incidencia::count() }}</h3>
          <div class="mt-auto">
            <a href="{{ route('incidencias.index') }}" class="btn btn-sm btn-primary w-100 mb-2">
              Ver incidencias
            </a>
            <a href="{{ route('incidencias.create') }}" class="btn btn-sm btn-outline-primary w-100">
              Crear incidencia
            </a>
          </div>
        </div>
      </div>
    </div>

    @endif

    {{-- ================= OPERARIO ================= --}}
    @if(auth()->user()->tipo === 'operario')

    <div class="col-sm-6 col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h6 class="card-title">Mis incidencias</h6>
          <h3 class="mb-3">
            {{ \App\Models\Incidencia::where('empleado_id', auth()->id())->count() }}
          </h3>
          <div class="mt-auto">
            <a href="{{ route('incidencias.mis') }}" class="btn btn-sm btn-primary w-100">
              Ver mis incidencias
            </a>
          </div>
        </div>
      </div>
    </div>

    @endif

  </div>
</div>
@endsection
