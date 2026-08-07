<?php

use App\Http\Controllers\AcuseController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TarjetaController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'usuario.activo'])->group(function () {

    // Breeze, después de iniciar sesión, siempre intenta mandarte a una
    // ruta llamada "dashboard" — como tu pantalla principal real es "/",
    // solo la redirigimos ahí.
    Route::redirect('/dashboard', '/')->name('dashboard');

    // ── Acuses ──────────────────────────────────────────────
    Route::get('/', [AcuseController::class, 'index'])->name('index');
    Route::get('/acuses/contadores', [AcuseController::class, 'contadores'])->name('acuses.contadores');
    Route::post('/import', [AcuseController::class, 'import'])->name('import');
    Route::get('/download-pdf', [AcuseController::class, 'downloadPDF'])->name('download.pdf');
    Route::get('/export-historial', [AcuseController::class, 'exportHistorial'])->name('export.historial');
    Route::get('/reimprimir-lote/{id}', [AcuseController::class, 'reimprimirLote'])->name('reimprimir.lote');
    Route::get('/acuses/imprimir-individual/{folio}', [AcuseController::class, 'imprimirIndividual'])->name('acuses.imprimir_individual');
    Route::post('/import-capturas', [AcuseController::class, 'importCapturas'])->name('import.capturas');

    // ── Incidencias ─────────────────────────────────────────
    Route::post('/incidencias/store', [IncidenciaController::class, 'store'])->name('incidencias.store');
    Route::get('/incidencias/por-fecha', [IncidenciaController::class, 'porFecha'])->name('incidencias.por_fecha');
    Route::post('/incidencias/atender/{id}', [IncidenciaController::class, 'atender'])->name('incidencias.atender');
    Route::get('/incidencias/exportar-excel', [IncidenciaController::class, 'exportarExcel'])->name('incidencias.excel');
    Route::get('/incidencias/exportar-pdf', [IncidenciaController::class, 'exportarPdf'])->name('incidencias.pdf');

    // ── Tarjetas ────────────────────────────────────────────
    Route::post('/tarjetas/asignar', [TarjetaController::class, 'asignar'])->name('tarjetas.asignar');
    Route::get('/tarjetas/exportar-excel', [TarjetaController::class, 'exportarExcel'])->name('tarjetas.excel');
    Route::post('/tarjetas/cargar-stock', [TarjetaController::class, 'cargarStock'])->name('tarjetas.cargar_stock');

    // ── Mi cuenta (generadas por Breeze, se habían perdido al reescribir web.php) ──
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    /*
    |----------------------------------------------------------------------
    | SOLO ADMINISTRADOR — destructivas o de exportación masiva de datos
    | personales sensibles (CURP, cuentas bancarias)
    |----------------------------------------------------------------------
    */
    Route::middleware('es.administrador')->group(function () {
        Route::post('/truncate', [AcuseController::class, 'truncate'])->name('truncate');
        Route::delete('/incidencias/{id}', [IncidenciaController::class, 'destroy'])->name('incidencias.destroy');
        Route::get('/inventario/exportar-general', [InventarioController::class, 'exportarGeneral'])->name('inventario.exportar_general');
    });
});
