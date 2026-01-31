@extends('layouts.app')

@section('content')
<div class="row">
  <!-- Panel izquierdo: Login (usa la misma action que Breeze) -->
  <div class="col-md-6 mb-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3">Iniciar sesión</h4>

        {{-- Mostrar errores generales del login (si los hay) --}}
        @if ($errors->has('email') || $errors->has('password'))
          <div class="alert alert-danger">
            {{ $errors->first('email') ?? $errors->first('password') }}
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

          <div class="mb-3">
            <label for="email" class="form-label">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control">
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" type="password" name="password" required class="form-control">
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="small">¿Olvidaste la contraseña?</a>
            @endif
          </div>

          <div class="d-grid">
            <button class="btn btn-primary">Entrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Panel derecho: Formulario público de incidencias -->
  <div class="col-md-6 mb-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3">Registrar incidencia (Cliente)</h4>

        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('incidencias.public.store') }}">
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
            <label class="form-label">CIF</label>
            <input name="cif" class="form-control" value="{{ old('cif') }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input name="telefono" class="form-control" value="{{ old('telefono') }}" required>
          </div>

          <div class="d-grid">
            <button class="btn btn-success">Enviar incidencia</button>
          </div>
        </form>

        <div class="mt-3 small text-muted">
          Si eres cliente del servicio y no recuerdas tus datos, contacta con soporte.
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
