<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home'); // home con login + registro de incidencias
});

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
});

/*
|--------------------------------------------------------------------------
| Rutas admin (rol:admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','rol:admin'])->group(function () {
    // Incidencias (resource -> incluye edit/update/destroy/show/index/create/store)
    Route::resource('incidencias', IncidenciaController::class);

    // Clientes (CRUD)
    Route::resource('clientes', ClienteController::class)->except(['show']);

    // Cuotas (CRUD + acciones)
    Route::resource('cuotas', CuotaController::class);
    Route::post('cuotas/{id}/pagar', [CuotaController::class,'marcarPagada'])->name('cuotas.pagar');
    Route::post('cuotas/generar', function () {
        \Artisan::call('cuotas:generar');
        return redirect()->back()->with('success','Remesa generada.');
    })->name('cuotas.generar');

    // Facturas
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas/{id}/download', [FacturaController::class, 'download'])->name('facturas.download');
    Route::post('/cuotas/{id}/factura', [FacturaController::class,'generarParaCuota'])->name('cuotas.factura');
});

/*
|--------------------------------------------------------------------------
| Rutas operario (rol:operario)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','rol:operario'])->group(function () {
    Route::get('/mis-incidencias', [IncidenciaController::class,'misIncidencias'])->name('incidencias.mis');
});
