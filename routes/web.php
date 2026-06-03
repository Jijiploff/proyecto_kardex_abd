<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('home');
});

// ── Home / Dashboard ──────────────────────────────────────────────────────
Route::get('/home', function () {
    return view('home.index', [
        'totalProductos'  => 48,
        'totalAlmacenes'  => 5,
        'movimientosHoy'  => 134,
        'totalPruebas'    => 12,
    ]);
})->name('home');

// ── Consulta del Kardex ───────────────────────────────────────────────────
Route::get('/kardex', function () {
    // Datos de prueba — reemplazar con consultas reales al agregar el backend
    $productos = collect([
        (object)['id' => 1, 'nombre' => 'Cemento Portland Tipo I', 'codigo' => 'CEM-001'],
        (object)['id' => 2, 'nombre' => 'Varilla de Acero 3/8"',   'codigo' => 'VAR-038'],
        (object)['id' => 3, 'nombre' => 'Ladrillo King Kong',       'codigo' => 'LAD-KK'],
    ]);

    $almacenes = collect([
        (object)['id' => 1, 'nombre' => 'Almacén Central'],
        (object)['id' => 2, 'nombre' => 'Almacén Norte'],
        (object)['id' => 3, 'nombre' => 'Almacén Sur'],
    ]);

    // Movimientos de ejemplo
    $movimientosRaw = collect([
        (object)[
            'fecha'           => '2025-01-10',
            'comprobante'     => 'GR-0001',
            'tipo'            => 'entrada',
            'cantidad'        => 200,
            'saldo'           => 200,
            'costo_unitario'  => 28.50,
            'costo_total'     => 5700.00,
            'observacion'     => 'Compra inicial',
            'producto'        => (object)['nombre' => 'Cemento Portland Tipo I'],
            'almacen'         => (object)['nombre' => 'Almacén Central'],
        ],
        (object)[
            'fecha'           => '2025-01-15',
            'comprobante'     => 'GS-0012',
            'tipo'            => 'salida',
            'cantidad'        => 50,
            'saldo'           => 150,
            'costo_unitario'  => 28.50,
            'costo_total'     => 1425.00,
            'observacion'     => 'Despacho obra Lote 3',
            'producto'        => (object)['nombre' => 'Cemento Portland Tipo I'],
            'almacen'         => (object)['nombre' => 'Almacén Central'],
        ],
        (object)[
            'fecha'           => '2025-01-20',
            'comprobante'     => 'AJ-0003',
            'tipo'            => 'ajuste',
            'cantidad'        => 5,
            'saldo'           => 155,
            'costo_unitario'  => 28.50,
            'costo_total'     => 142.50,
            'observacion'     => 'Ajuste por inventario físico',
            'producto'        => (object)['nombre' => 'Cemento Portland Tipo I'],
            'almacen'         => (object)['nombre' => 'Almacén Central'],
        ],
        (object)[
            'fecha'           => '2025-01-22',
            'comprobante'     => 'GR-0008',
            'tipo'            => 'entrada',
            'cantidad'        => 500,
            'saldo'           => 500,
            'costo_unitario'  => 4.20,
            'costo_total'     => 2100.00,
            'observacion'     => 'Reposición de stock',
            'producto'        => (object)['nombre' => 'Varilla de Acero 3/8"'],
            'almacen'         => (object)['nombre' => 'Almacén Norte'],
        ],
        (object)[
            'fecha'           => '2025-01-28',
            'comprobante'     => 'GS-0019',
            'tipo'            => 'salida',
            'cantidad'        => 120,
            'saldo'           => 380,
            'costo_unitario'  => 4.20,
            'costo_total'     => 504.00,
            'observacion'     => 'Despacho obra Av. Principal',
            'producto'        => (object)['nombre' => 'Varilla de Acero 3/8"'],
            'almacen'         => (object)['nombre' => 'Almacén Norte'],
        ],
    ]);

    // Paginación manual simple para el prototipo
    $perPage    = 10;
    $page       = request()->get('page', 1);
    $items      = $movimientosRaw->forPage($page, $perPage);
    $kardex     = new \Illuminate\Pagination\LengthAwarePaginator(
        $items,
        $movimientosRaw->count(),
        $perPage,
        $page,
        ['path' => route('kardex.index')]
    );

    return view('kardex.index', [
        'productos'     => $productos,
        'almacenes'     => $almacenes,
        'kardex'        => $kardex,
        'totalEntradas' => 700,
        'totalSalidas'  => 170,
        'stockActual'   => 535,
    ]);
})->name('kardex.index');

// Exportar kardex (placeholder)
Route::get('/kardex/export', function () {
    return back()->with('info', 'Función de exportación disponible próximamente.');
})->name('kardex.export');

// ── Pruebas de Rendimiento ────────────────────────────────────────────────
Route::get('/pruebas', function () {
    $productos = collect([
        (object)['id' => 1, 'nombre' => 'Cemento Portland Tipo I', 'codigo' => 'CEM-001'],
        (object)['id' => 2, 'nombre' => 'Varilla de Acero 3/8"',   'codigo' => 'VAR-038'],
        (object)['id' => 3, 'nombre' => 'Ladrillo King Kong',       'codigo' => 'LAD-KK'],
    ]);

    $almacenes = collect([
        (object)['id' => 1, 'nombre' => 'Almacén Central'],
        (object)['id' => 2, 'nombre' => 'Almacén Norte'],
        (object)['id' => 3, 'nombre' => 'Almacén Sur'],
    ]);

    return view('pruebas.index', [
        'productos' => $productos,
        'almacenes' => $almacenes,
        'resultados' => [],   // vacío hasta ejecutar prueba
        'params'     => [],
    ]);
})->name('pruebas.index');

Route::post('/pruebas/ejecutar', function (\Illuminate\Http\Request $request) {
    $productos = collect([
        (object)['id' => 1, 'nombre' => 'Cemento Portland Tipo I', 'codigo' => 'CEM-001'],
        (object)['id' => 2, 'nombre' => 'Varilla de Acero 3/8"',   'codigo' => 'VAR-038'],
        (object)['id' => 3, 'nombre' => 'Ladrillo King Kong',       'codigo' => 'LAD-KK'],
    ]);

    $almacenes = collect([
        (object)['id' => 1, 'nombre' => 'Almacén Central'],
        (object)['id' => 2, 'nombre' => 'Almacén Norte'],
        (object)['id' => 3, 'nombre' => 'Almacén Sur'],
    ]);

    // Resultados de ejemplo — reemplazar con mediciones reales en el backend
    $resultados = [
        [
            'estrategia'          => 'Consulta Tradicional',
            'tipo'                => 'traditional',
            'descripcion'         => 'SELECT sin índices adicionales, recorre toda la tabla secuencialmente para filtrar por producto, almacén y rango de fechas.',
            'tiempo_ms'           => 842.50,
            'registros_obtenidos' => (int) ($request->cant_registros ?? 1000),
            'mejora_pct'          => null,
            'clase_tiempo'        => 'slow',
            'es_mejor'            => false,
        ],
        [
            'estrategia'          => 'Consulta con Índices B-Tree',
            'tipo'                => 'indexed',
            'descripcion'         => 'Índices compuestos en (producto_id, almacen_id, fecha). Permite al motor saltar directamente a los registros relevantes.',
            'tiempo_ms'           => 185.30,
            'registros_obtenidos' => (int) ($request->cant_registros ?? 1000),
            'mejora_pct'          => 78.0,
            'clase_tiempo'        => 'good',
            'es_mejor'            => false,
        ],
        [
            'estrategia'          => 'Vista Materializada',
            'tipo'                => 'materialized',
            'descripcion'         => 'Resultados pre-calculados almacenados en una tabla resumen. Actualización periódica mediante job programado.',
            'tiempo_ms'           => 42.10,
            'registros_obtenidos' => (int) ($request->cant_registros ?? 1000),
            'mejora_pct'          => 95.0,
            'clase_tiempo'        => 'best',
            'es_mejor'            => true,
        ],
        [
            'estrategia'          => 'Caché en Redis',
            'tipo'                => 'cache',
            'descripcion'         => 'Resultado de la consulta almacenado en Redis con TTL de 5 minutos. Ideal para reportes frecuentes con datos no críticos en tiempo real.',
            'tiempo_ms'           => 8.70,
            'registros_obtenidos' => (int) ($request->cant_registros ?? 1000),
            'mejora_pct'          => 99.0,
            'clase_tiempo'        => 'best',
            'es_mejor'            => false,
        ],
        [
            'estrategia'          => 'Particionamiento por Fecha',
            'tipo'                => 'partitioned',
            'descripcion'         => 'Tabla particionada por año/mes. Las consultas por rango de fechas solo acceden a las particiones relevantes, reduciendo I/O.',
            'tiempo_ms'           => 67.40,
            'registros_obtenidos' => (int) ($request->cant_registros ?? 1000),
            'mejora_pct'          => 92.0,
            'clase_tiempo'        => 'best',
            'es_mejor'            => false,
        ],
    ];

    return view('pruebas.index', [
        'productos'        => $productos,
        'almacenes'        => $almacenes,
        'resultados'       => $resultados,
        'params'           => $request->all(),
        'mejorEstrategia'  => 'Caché en Redis',
        'fechaEjecucion'   => now()->format('d/m/Y H:i:s'),
        'colores'          => [
            'Tradicional'  => '#888780',
            'Indexada'     => '#378add',
            'Caché'        => '#1d9e75',
            'Materializada'=> '#ba7517',
            'Particionada' => '#7f77dd',
        ],
    ]);
})->name('pruebas.ejecutar');

// ── Logout placeholder (evita error si el layout lo llama) ───────────────
Route::post('/logout', function () {
    return redirect()->route('home');
})->name('logout');