# Problema 2 — Pruebas automatizadas (Fase A - Punto 1)

Este directorio contiene la **estructura de pruebas** para el Proyecto "Gestor-Incidencias".
Las pruebas están pensadas para usarse junto con la aplicación Laravel ubicada en `Problema_1`.

## Qué incluye
- `tests/Feature/RoutesTest.php` — verifica existencia/básico comportamiento de rutas.
- `tests/Feature/IncidenciaFormTest.php` — prueba de envío del formulario público de incidencias.
- `tests/Unit/ExampleTest.php` — test de ejemplo (unidad).
- `run_tests.ps1` — script para copiar los tests a `Problema_1/tests` y ejecutarlos.
- `phpunit.xml.dist` — plantilla de configuración ligera.

## Requisitos previos
- PHP & Composer instalados.
- Haber ejecutado `composer install` en `Problema_1` (dependencias del proyecto).
- Haber corrido migraciones en `Problema_1` al menos una vez (opcional; los tests usan `RefreshDatabase`).

## Uso (PowerShell)
Desde la raíz del repo (`Gestion-Incidencias`) ejecutar:

```powershell
# Ejecutar el script (esto copiará los tests a Problema_1/tests y lanzará php artisan test)
.\Problema_2\run_tests.ps1
