<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\KardexController;


Route::get('/', function () {
    return redirect()->route('home');
});

// ── Home / Dashboard ──────────────────────────────────────────────────────
Route::get('/home', function () {
    $productosRegistrados = DB::table('dbo.PRODUCTO')->count();
    $documentosEmitidos = DB::table('dbo.DOCUMENTO')->count();
    $movimientosRegistrados = DB::table('dbo.DETADOC')->count();
    $tiposDocumento = DB::table('dbo.TIPODOC')->count();
    $productosConStock = DB::table('dbo.PRODUCTO')->where('StockAc', '>', 0)->count();
    $stockTotal = DB::table('dbo.PRODUCTO')->sum('StockAc');
    $movimientosHoy = DB::table('dbo.DOCUMENTO')
        ->whereDate('Fecha', now()->toDateString())
        ->count();
    $ultimaFechaMovimiento = DB::table('dbo.DOCUMENTO')->max('Fecha');

    return view('home.index', [
        'productosRegistrados' => $productosRegistrados,
        'documentosEmitidos' => $documentosEmitidos,
        'movimientosRegistrados' => $movimientosRegistrados,
        'tiposDocumento' => $tiposDocumento,
        'productosConStock' => $productosConStock,
        'stockTotal' => $stockTotal,
        'movimientosHoy' => $movimientosHoy,
        'ultimaFechaMovimiento' => $ultimaFechaMovimiento,
    ]);
})->name('home');

// ── Consulta del Kardex ───────────────────────────────────────────────────
Route::get('/kardex', [KardexController::class, 'index'])->name('kardex.index');
Route::get('/kardex/indices/estado', [KardexController::class, 'estadoIndices'])->name('kardex.indices.estado');
Route::post('/kardex/indices/toggle', [KardexController::class, 'toggleIndices'])->name('kardex.indices.toggle');
Route::get('/api/productos/buscar', [KardexController::class, 'buscarProductos'])->name('api.productos.buscar');

// Exportar kardex (placeholder)
Route::get('/kardex/export', function () {
    return back()->with('info', 'Función de exportación disponible próximamente.');
})->name('kardex.export');

// Fallback para rutas no existentes (404 personalizado)
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});