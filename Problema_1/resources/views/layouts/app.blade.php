<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gestor Incidencias</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  @stack('head')
  <style>
    /* pequeño ajuste para evitar solapamiento en pantallas pequeñas */
    .nav-user-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; display: inline-block; vertical-align: middle; }
    .navbar-brand-center {
      font-size: 1.8rem; /* Tamaño más grande */
      font-weight: 600; /* Negrita para mayor énfasis */
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
    }
    /* Asegurar que el contenedor sea relativo para el posicionamiento absoluto de la marca */
    .navbar > .container-fluid { position: relative; }
    /* Ajuste responsivo: en pantallas pequeñas, la marca vuelve al flujo normal */
    @media (max-width: 991.98px) {
      .navbar-brand-center {
        position: static;
        transform: none;
        margin-right: auto; /* Empuja el botón de hamburguesa a la derecha */
      }
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ url('/dashboard') }}">Gestor De Incidencias</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-center">
        @auth
          <!-- Información del usuario: nombre + rol en badge + dropdown para acciones -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="nav-user-name me-2">{{ auth()->user()->nombre }}</span>
              <span class="badge bg-info text-dark me-1">{{ ucfirst(auth()->user()->tipo) }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
              <li class="dropdown-item-text small text-muted ps-3 pe-3">Conectado</li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">Mi perfil</a>
              </li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">Salir</button>
                </form>
              </li>
            </ul>
          </li>
        @else
          
        @endauth
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
