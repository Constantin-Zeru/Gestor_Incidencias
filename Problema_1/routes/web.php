<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home'); // home con login + registro público de incidencias
})->name('home');

// Rutas públicas para clientes (no auth)
Route::get('/incidencias/public/create', [IncidenciaController::class,'publicCreateForm'])->name('incidencias.public.create');
Route::post('/incidencias/public/store', [IncidenciaController::class,'publicStore'])->name('incidencias.public.store')->middleware('throttle:10,1');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Dashboard (protegido)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rutas protegidas por auth
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Descarga de fichero de incidencia (el controller valida permisos)
    Route::get('/incidencias/{id}/download', [IncidenciaController::class,'downloadFichero'])->name('incidencias.download');
});

/*
|--------------------------------------------------------------------------
| Rutas admin (rol:admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','rol:admin'])->group(function () {
    // Incidencias (todas las rutas resource excepto show, show está accesible a auth general)
    Route::resource('incidencias', IncidenciaController::class)->except(['show']);

    // cambiar estado
    Route::post('/incidencias/{incidencia}/estado', [IncidenciaController::class,'cambiarEstado'])->name('incidencias.estado');

    // Clientes (CRUD)
    Route::resource('clientes', ClienteController::class)->except(['show']);

    // Empleados (gestión por UI)
    Route::resource('empleados', EmpleadoController::class)->except(['show']);

    // Cuotas (CRUD + acciones)
    Route::resource('cuotas', CuotaController::class);
    Route::post('cuotas/{id}/pagar', [CuotaController::class,'marcarPagada'])->name('cuotas.pagar');
    Route::post('cuotas/generar', function () {
        \Artisan::call('cuotas:generar');
        return redirect()->back()->with('success','Remesa generada.');
    })->name('cuotas.generar');
    // Dentro del group de admin
Route::post('/cuotas/{id}/factura', [FacturaController::class,'generarParaCuota'])->name('cuotas.factura');


    // Facturas
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas/{id}/download', [FacturaController::class, 'download'])->name('facturas.download');
    Route::post('/cuotas/{id}/factura', [FacturaController::class,'generarParaCuota'])->name('cuotas.factura');
});

/*
|--------------------------------------------------------------------------
| Mostrar incidencia (autenticado)
|--------------------------------------------------------------------------
| Usuarios autenticados pueden ver una incidencia; el controller controla permisos
*/
Route::get('/incidencias/{incidencia}', [IncidenciaController::class,'show'])
    ->name('incidencias.show')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rutas operario (rol:operario)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','rol:operario'])->group(function () {
    Route::get('/mis-incidencias', [IncidenciaController::class,'misIncidencias'])->name('incidencias.mis');
    Route::post('/incidencias/{incidencia}/completar', [IncidenciaController::class,'completarTarea'])->name('incidencias.completar');
});
// dentro del group de operario:
Route::get('/operario/clientes', [\App\Http\Controllers\ClienteController::class, 'operarioIndex'])
    ->name('clientes.operario.index')
    ->middleware(['auth','rol:operario']);

