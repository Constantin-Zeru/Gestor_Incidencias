# run_tests.ps1
# Copia tests desde Problema_2/tests -> Problema_1/tests y ejecuta los tests principales
# Ejecutar desde la raíz del repo (Gestion-Incidencias):
#   .\Problema_2\run_tests.ps1

# Permitir ejecución en esta sesión si PowerShell lo bloquea:
# Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope Process

$root = (Get-Location).Path
$src = Join-Path $root "Problema_2\tests"
$dest = Join-Path $root "Problema_1\tests"

Write-Host "Origen tests: $src"
Write-Host "Destino tests: $dest"

if (-not (Test-Path $src)) {
    Write-Error "No existe $src. Ejecuta el script desde la raíz del repositorio (Gestion-Incidencias)."
    exit 1
}

# Crear carpeta destino si no existe
if (-not (Test-Path $dest)) {
    New-Item -ItemType Directory -Path $dest -Force | Out-Null
}

# Copiar (sobrescribe)
Write-Host "Copiando tests desde Problema_2/tests -> Problema_1/tests..."
Get-ChildItem -Path $src -Recurse | ForEach-Object {
    $relPath = $_.FullName.Substring($src.Length).TrimStart('\','/')
    $target = Join-Path $dest $relPath
    $targetDir = Split-Path $target -Parent
    if (-not (Test-Path $targetDir)) { New-Item -ItemType Directory -Path $targetDir -Force | Out-Null }
    Copy-Item -Path $_.FullName -Destination $target -Force
}

# Ir al proyecto Laravel
Set-Location -Path (Join-Path $root "Problema_1")

# Comprobaciones mínimas
if (-not (Test-Path ".\artisan")) {
    Write-Error "No se encuentra artisan en Problema_1. Asegúrate de ejecutar 'composer install' en Problema_1 y ejecuta el script desde la raíz del repo."
    exit 1
}

Write-Host "`nEjecutando test: RoutesTest..."
php artisan test tests/Feature/RoutesTest.php

Write-Host "`nEjecutando test: IncidenciaFormTest..."
php artisan test tests/Feature/IncidenciaFormTest.php

Write-Host "`n(Opcional) Ejecutar todos los tests copiados:"
Write-Host "php artisan test"

# Volver al directorio inicial por cortesía
Set-Location -Path $root

Write-Host "`nScript finalizado."
