<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\ReportesController;


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

// ── Reportes ───────────────────────────────────────────────────────────────
Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
Route::get('/reportes/stock-critico', [ReportesController::class, 'stockCritico'])->name('reportes.stock-critico');
Route::get('/reportes/resumen-mensual', [ReportesController::class, 'resumenMensual'])->name('reportes.resumen-mensual');
Route::get('/reportes/valor-inventario', [ReportesController::class, 'valorInventario'])->name('reportes.valor-inventario');

// Exportaciones PDF
Route::get('/reportes/pdf/stock-critico', [ReportesController::class, 'exportPdfStockCritico'])->name('reportes.pdf.stock-critico');
Route::get('/reportes/pdf/resumen-mensual', [ReportesController::class, 'exportPdfResumenMensual'])->name('reportes.pdf.resumen-mensual');
Route::get('/reportes/pdf/valor-inventario', [ReportesController::class, 'exportPdfValorInventario'])->name('reportes.pdf.valor-inventario');

// Exportaciones Excel
Route::get('/reportes/excel/stock-critico', [ReportesController::class, 'exportExcelStockCritico'])->name('reportes.excel.stock-critico');
Route::get('/reportes/excel/resumen-mensual', [ReportesController::class, 'exportExcelResumenMensual'])->name('reportes.excel.resumen-mensual');
Route::get('/reportes/excel/valor-inventario', [ReportesController::class, 'exportExcelValorInventario'])->name('reportes.excel.valor-inventario');

// Ruta temporal para optimizar SP
Route::get('/kardex/optimizar-sp', function () {
    $sql = "
    ALTER PROCEDURE _Kardex_SaldoInicialDetallado
        @idProducto CHAR(4) = NULL,
        @fechaReferencia DATE = NULL
    AS
    BEGIN
        SET NOCOUNT ON;

        -- Si no se especifica fecha de referencia, usar hoy
        IF @fechaReferencia IS NULL
            SET @fechaReferencia = CAST(GETDATE() AS DATE);

        -- Validar que el producto existe si se especifica
        IF @idProducto IS NOT NULL
           AND NOT EXISTS
           (
               SELECT 1
               FROM PRODUCTO
               WHERE Producto = @idProducto
           )
        BEGIN
            RAISERROR('El producto especificado no existe',16,1);
            RETURN;
        END

        DECLARE @ultimaFecha DATE;
        DECLARE @ultimaHora DATETIME;
        DECLARE @ultimoDocumento CHAR(9) = NULL;
        DECLARE @ultimaTipoDoc CHAR(1);
        DECLARE @ultimaCantidad NUMERIC(9,2);
        DECLARE @ultimoPrecUnit NUMERIC(9,2);
        DECLARE @ultimoSigno INT;
        DECLARE @ultimoTipoMovimiento VARCHAR(30);

        -- Último movimiento anterior a la fecha
        SELECT TOP 1
            @ultimaFecha = d.Fecha,
            @ultimaHora = d.Hora,
            @ultimoDocumento = d.Documento,
            @ultimaTipoDoc = d.TipoDoc,
            @ultimaCantidad = dd.Cantidad,
            @ultimoPrecUnit = dd.PrecUnit,
            @ultimoSigno = td.Signo,
            @ultimoTipoMovimiento =
                CASE
                    WHEN td.Signo = 1 THEN 'INGRESO'
                    WHEN td.Signo = -1 THEN 'SALIDA'
                    ELSE 'RESERVA'
                END
        FROM DOCUMENTO d
            INNER JOIN DETADOC dd
                ON d.Documento = dd.Documento
               AND d.TipoDoc = dd.TipoDoc
            INNER JOIN TIPODOC td
                ON td.TipoDoc = d.TipoDoc
        WHERE (@idProducto IS NULL OR dd.Producto = @idProducto)
          AND d.Fecha < @fechaReferencia
        ORDER BY
            d.Fecha DESC,
            ISNULL(d.Hora,CAST('23:59:59' AS DATETIME)) DESC;

        -- Si existen movimientos anteriores
        IF @ultimoDocumento IS NOT NULL
        BEGIN
            DECLARE @stockAcumulado NUMERIC(9,2) = 0;
            DECLARE @valorAcumulado NUMERIC(9,2) = 0;

            -- Stock acumulado hasta la fecha
            SELECT
                @stockAcumulado =
                    ISNULL(SUM(dd.Cantidad * td.Signo),0),

                @valorAcumulado =
                    ISNULL(SUM(dd.Cantidad * dd.PrecUnit * td.Signo),0)
            FROM DOCUMENTO d
                INNER JOIN DETADOC dd
                    ON d.Documento = dd.Documento
                   AND d.TipoDoc = dd.TipoDoc
                INNER JOIN TIPODOC td
                    ON td.TipoDoc = d.TipoDoc
            WHERE (@idProducto IS NULL OR dd.Producto = @idProducto)
              AND d.Fecha < @fechaReferencia;

            SELECT
                FechaHora =
                    CONVERT(VARCHAR(19),
                            ISNULL(@ultimaHora,@ultimaFecha),
                            121),
                Documento = 'ACUMULADO',
                TipoMovimiento = CONCAT('Saldo Inicial (', @ultimoTipoMovimiento, ')'),
                CostoUnitario = @ultimoPrecUnit,
                Cantidad = @ultimaCantidad,
                ValorTotal = @ultimaCantidad * @ultimoPrecUnit,
                StockCalculado = @stockAcumulado;
        END
        ELSE
        BEGIN
            SELECT
                FechaHora = CONVERT(VARCHAR(19), @fechaReferencia, 121),
                Documento = 'ACUMULADO',
                TipoMovimiento = 'Saldo Inicial',
                CostoUnitario = CAST(0 AS NUMERIC(9,2)),
                Cantidad = CAST(0 AS NUMERIC(9,2)),
                ValorTotal = CAST(0 AS NUMERIC(9,2)),
                StockCalculado = CAST(0 AS NUMERIC(9,2));
        END
    END
    ";
    
    try {
        DB::unprepared($sql);
        return "¡ÉXITO! El Procedimiento Almacenado '_Kardex_SaldoInicialDetallado' ha sido optimizado correctamente. Ahora puedes volver al Kardex y probar la velocidad.";
    } catch (\Exception $e) {
        return "ERROR: " . $e->getMessage();
    }
});

// Exportar kardex (placeholder)
Route::get('/kardex/export', function () {
    return back()->with('info', 'Función de exportación disponible próximamente.');
})->name('kardex.export');

// Fallback para rutas no existentes (404 personalizado)
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});