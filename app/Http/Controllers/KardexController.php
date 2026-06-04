<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Documento;
use App\Models\Detadoc;
use App\Models\Tipodoc;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        // Validación básica de entrada
        $request->validate([
            'producto' => 'nullable|string|max:50',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'mes' => 'nullable|numeric|min:1|max:12',
            'anio' => 'nullable|numeric|min:2000|max:'.date('Y'),
        ]);

        $productoSeleccionadoId = $request->query('producto');
        $estrategia = $request->query('estrategia', 'tradicional');
        $consultarPor = $request->query('consultar_por', 'fechas');

        // Mapeo de nombres de estrategia para el bloque informativo
        $nombresEstrategia = [
            'tradicional'        => 'Tradicional',
            'optimizada'         => 'Optimizada',
            'optimizada_indices' => 'Optimizada + Índices'
        ];
        $nombreEstrategiaDisplay = $nombresEstrategia[$estrategia] ?? 'Tradicional';
        
        $productoModel = null;
        $kardex = collect();
        $totalEntradas = 0;
        $totalSalidas = 0;
        $stockActual = 0;
        $tiempoInicio = microtime(true);
        $sinDatosParaPeriodo = false; // Flag para años sin registros
        
        $tiposDoc = Tipodoc::select('TipoDoc', 'Descripcion')->orderBy('Descripcion')->get();
        
        if ($productoSeleccionadoId) {
            try {
                $productoModel = Producto::find($productoSeleccionadoId);
                $productoId = trim((string)$productoSeleccionadoId);
                
                // Lógica de mapeo estricto según tabla de referencia
                DB::statement('SET ARITHABORT ON; SET ANSI_WARNINGS ON;');
                $pdo = DB::connection()->getPdo();

                if ($estrategia === 'tradicional') {
                    // ... (resto del código de selección de SP)
                    if ($consultarPor === 'fechas' && $request->query('fecha_inicio') && $request->query('fecha_fin')) {
                        $fInicio = (string)$request->query('fecha_inicio');
                        $fFin = (string)$request->query('fecha_fin');
                        
                        $stmt = $pdo->prepare("EXEC _Kardex_RangoFechas_Tradicional ?, ?, ?");
                        $stmt->bindValue(1, $productoId, \PDO::PARAM_STR);
                        $stmt->bindValue(2, $fInicio, \PDO::PARAM_STR);
                        $stmt->bindValue(3, $fFin, \PDO::PARAM_STR);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    } elseif ($consultarPor === 'mes' && $request->query('mes') && $request->query('anio_mes')) {
                        $anio = $request->query('anio_mes');
                        $mes = str_pad($request->query('mes'), 2, '0', STR_PAD_LEFT);
                        $fInicio = date('Y-m-d', strtotime("$anio-$mes-01"));
                        $fFin = date('Y-m-t', strtotime($fInicio));
                        
                        $stmt = $pdo->prepare("EXEC _Kardex_RangoFechas_Tradicional ?, ?, ?");
                        $stmt->bindValue(1, $productoId, \PDO::PARAM_STR);
                        $stmt->bindValue(2, $fInicio, \PDO::PARAM_STR);
                        $stmt->bindValue(3, $fFin, \PDO::PARAM_STR);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    } elseif ($consultarPor === 'anio' && $request->query('anio')) {
                        $anio = $request->query('anio');
                        $fInicio = date('Y-m-d', strtotime("$anio-01-01"));
                        $fFin = date('Y-m-d', strtotime("$anio-12-31"));
                        
                        $stmt = $pdo->prepare("EXEC _Kardex_RangoFechas_Tradicional ?, ?, ?");
                        $stmt->bindValue(1, $productoId, \PDO::PARAM_STR);
                        $stmt->bindValue(2, $fInicio, \PDO::PARAM_STR);
                        $stmt->bindValue(3, $fFin, \PDO::PARAM_STR);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    } else {
                        // Consulta general tradicional
                        $stmt = $pdo->prepare("EXEC _Kardex_Movimientos ?, ?, ?");
                        $stmt->bindValue(1, $productoId, \PDO::PARAM_STR);
                        $stmt->bindValue(2, '1900-01-01', \PDO::PARAM_STR);
                        $stmt->bindValue(3, date('Y-12-31'), \PDO::PARAM_STR);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    }
                } else {
                    // Estrategias Optimizadas
                    if ($consultarPor === 'fechas' && $request->query('fecha_inicio') && $request->query('fecha_fin')) {
                        $stmt = $pdo->prepare("EXEC _Kardex_RangoFechas ?, ?, ?");
                        $stmt->bindValue(1, $productoId, \PDO::PARAM_STR);
                        $stmt->bindValue(2, (string)$request->query('fecha_inicio'), \PDO::PARAM_STR);
                        $stmt->bindValue(3, (string)$request->query('fecha_fin'), \PDO::PARAM_STR);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    } elseif ($consultarPor === 'mes' && $request->query('mes') && $request->query('anio_mes')) {
                        $stmt = $pdo->prepare("EXEC _Kardex_Mensual ?, ?, ?");
                        $stmt->bindValue(1, $productoId, \PDO::PARAM_STR);
                        $stmt->bindValue(2, (int)$request->query('mes'), \PDO::PARAM_INT);
                        $stmt->bindValue(3, (int)$request->query('anio_mes'), \PDO::PARAM_INT);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    } elseif ($consultarPor === 'anio' && $request->query('anio')) {
                        $stmt = $pdo->prepare("EXEC _Kardex_Anual ?, ?");
                        $stmt->bindValue(1, $productoId, \PDO::PARAM_STR);
                        $stmt->bindValue(2, (int)$request->query('anio'), \PDO::PARAM_INT);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    } else {
                        // Optimizado por defecto
                        $stmt = $pdo->prepare("EXEC _Kardex_RangoFechas ?, ?, ?");
                        $stmt->bindValue(1, $productoId, \PDO::PARAM_STR);
                        $stmt->bindValue(2, '1900-01-01', \PDO::PARAM_STR);
                        $stmt->bindValue(3, date('Y-12-31'), \PDO::PARAM_STR);
                        $stmt->execute();
                        $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    }
                }

                // Si no hay resultados o solo hay un acumulado de 0, marcar como sin datos
                if (empty($resultados)) {
                    $sinDatosParaPeriodo = true;
                }

                // Transformar resultados de los SP al formato unificado de la vista
                $kardex = collect($resultados)->map(function($row) {
                $data = (array)$row;
                
                // Mapeo estricto basado en la especificación técnica suministrada
                $fechaHora     = $data['FechaHora'] ?? now();
                $documento     = $data['Documento'] ?? '—';
                $tipoMov       = $data['TipoMovimiento'] ?? '—';
                $costoUnit     = floatval($data['CostoUnitario'] ?? 0);
                $cantidad      = floatval($data['Cantidad'] ?? 0);
                $valorTotal    = floatval($data['ValorTotal'] ?? 0);
                $stockCalc     = floatval($data['StockCalculado'] ?? 0);
                
                // Determinar tipo (entrada/salida) para estilos visuales (badges/colores)
                // REGLA: Si contiene 'SALIDA' es negativo/salida, sin importar si dice 'INICIAL'.
                $esSalida  = (stripos($tipoMov, 'SALIDA') !== false);
                $esEntrada = !$esSalida && (stripos($tipoMov, 'INGRESO') !== false || stripos($tipoMov, 'ENTRADA') !== false || stripos($tipoMov, 'INICIAL') !== false);
                
                // Aplicar signo a la cantidad según el tipo de movimiento
                $cantidadConSigno = abs($cantidad);
                if ($esSalida) {
                    $cantidadConSigno = -$cantidadConSigno;
                }
                
                return (object)[
                    'fecha'          => $fechaHora,
                    'comprobante'    => $documento,
                    'tipo'           => $esSalida ? 'salida' : ($esEntrada ? 'entrada' : 'ajuste'),
                    'tipo_nombre'    => $tipoMov,
                    'cantidad'       => $cantidadConSigno,
                    'costo_unitario' => $costoUnit,
                    'costo_total'    => $valorTotal,
                    'saldo'          => $stockCalc,
                    'observacion'    => '', // Eliminado de la tabla pero mantenido en el objeto por compatibilidad
                ];
            });

            } catch (\Exception $e) {
                // Capturar errores de desbordamiento u otros errores de SQL
                $sinDatosParaPeriodo = true;
                // Loguear error para depuración interna
                Log::error("Error en consulta Kardex: " . $e->getMessage());
            }

            // Recalcular totales para los summary pills basados en los datos reales del SP
            $totalEntradas = 0;
            $totalSalidas = 0;
            foreach ($kardex as $mov) {
                // No sumar el registro acumulado inicial a los totales del periodo
                if ($mov->comprobante === 'ACUMULADO' || stripos($mov->tipo_nombre, 'INICIAL') !== false) continue;
                
                if ($mov->tipo == 'entrada') $totalEntradas += abs($mov->cantidad);
                if ($mov->tipo == 'salida') $totalSalidas += abs($mov->cantidad);
            }
            $stockActual = $kardex->last()->saldo ?? 0;
        }

        $tiempoRespuesta = number_format((microtime(true) - $tiempoInicio), 3); // Segundos con 3 decimales
        $fechaConsulta = now()->format('d/m/Y H:i:s');
        $totalRegistros = $kardex->count();

        // Paginación manual
        $perPage = 15;
        $currentPage = $request->input('page', 1);
        $pagedData = $kardex->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $kardex = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, $totalRegistros, $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('kardex.index', compact(
            'kardex', 
            'totalEntradas', 
            'totalSalidas', 
            'stockActual', 
            'productoModel', 
            'tiposDoc',
            'tiempoRespuesta',
            'estrategia',
            'fechaConsulta',
            'totalRegistros',
            'nombreEstrategiaDisplay',
            'sinDatosParaPeriodo'
        ));
    }

    public function buscarProductos(Request $request)
    {
        $q = $request->query('q');
        
        $productos = Producto::select('Producto', 'Descripcion')
            ->where('Producto', 'LIKE', "%$q%")
            ->orWhere('Descripcion', 'LIKE', "%$q%")
            ->orderBy('Descripcion')
            ->limit(20)
            ->get();

        return response()->json($productos);
    }

    private function obtenerMovimientosReal($productoId, $tipoDocFiltro = null, $estrategia = 'tradicional')
    {
        // Este método ya no es necesario ya que usamos Procedimientos Almacenados
        // Se mantiene vacío o se puede eliminar si no se usa en otras partes
        return collect();
    }

    public function toggleIndices(Request $request): JsonResponse
    {
        $data = $request->validate([
            'accion' => ['required', 'string', 'in:crear,eliminar'],
        ]);

        if (!$this->usaSqlServer()) {
            return response()->json([
                'ok' => false,
                'error' => 'La administración de índices solo está disponible para SQL Server.',
            ], 400);
        }

        try {
            if ($data['accion'] === 'crear') {
                $this->crearIndicesKardex();
                $mensaje = 'Índices creados correctamente';
            } else {
                $this->eliminarIndicesKardex();
                $mensaje = 'Índices eliminados correctamente';
            }

            return response()->json([
                'ok' => true,
                'mensaje' => $mensaje,
                'indices_activos' => $this->verificarIndices(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error administrando índices Kardex: ' . $e->getMessage(), [
                'accion' => $data['accion'],
            ]);

            return response()->json([
                'ok' => false,
                'error' => 'No se pudo actualizar el estado de los índices.',
            ], 500);
        }
    }

    public function estadoIndices(): JsonResponse
    {
        return response()->json($this->verificarIndices());
    }

    private function crearIndicesKardex(): void
    {
        DB::statement("\n            IF NOT EXISTS (\n                SELECT 1\n                FROM sys.indexes\n                WHERE name = 'IX_Detadoc_Producto'\n                  AND object_id = OBJECT_ID('dbo.DETADOC')\n            )\n            CREATE INDEX IX_Detadoc_Producto\n            ON dbo.DETADOC (Producto, TipoDoc)\n            INCLUDE (Cantidad, PrecUnit)\n        ");

        DB::statement("\n            IF NOT EXISTS (\n                SELECT 1\n                FROM sys.indexes\n                WHERE name = 'IX_Documento_Fecha'\n                  AND object_id = OBJECT_ID('dbo.DOCUMENTO')\n            )\n            CREATE INDEX IX_Documento_Fecha\n            ON dbo.DOCUMENTO (Fecha, TipoDoc, Documento)\n            INCLUDE (Hora)\n        ");
    }

    private function eliminarIndicesKardex(): void
    {
        DB::statement("\n            IF EXISTS (\n                SELECT 1\n                FROM sys.indexes\n                WHERE name = 'IX_Detadoc_Producto'\n                  AND object_id = OBJECT_ID('dbo.DETADOC')\n            )\n            DROP INDEX IX_Detadoc_Producto ON dbo.DETADOC\n        ");

        DB::statement("\n            IF EXISTS (\n                SELECT 1\n                FROM sys.indexes\n                WHERE name = 'IX_Documento_Fecha'\n                  AND object_id = OBJECT_ID('dbo.DOCUMENTO')\n            )\n            DROP INDEX IX_Documento_Fecha ON dbo.DOCUMENTO\n        ");
    }

    private function verificarIndices(): array
    {
        if (!$this->usaSqlServer()) {
            return [
                'IX_Detadoc_Producto' => false,
                'IX_Documento_Fecha' => false,
                'todos_activos' => false,
                'motor' => DB::getDriverName(),
            ];
        }

        $indices = DB::select("\n            SELECT\n                name,\n                OBJECT_NAME(object_id) AS tabla\n            FROM sys.indexes\n            WHERE name IN ('IX_Detadoc_Producto', 'IX_Documento_Fecha')\n              AND object_id IN (OBJECT_ID('dbo.DETADOC'), OBJECT_ID('dbo.DOCUMENTO'))\n        ");

        return [
            'IX_Detadoc_Producto' => collect($indices)->contains('name', 'IX_Detadoc_Producto'),
            'IX_Documento_Fecha' => collect($indices)->contains('name', 'IX_Documento_Fecha'),
            'todos_activos' => collect($indices)->count() === 2,
            'motor' => DB::getDriverName(),
        ];
    }

    private function usaSqlServer(): bool
    {
        return DB::getDriverName() === 'sqlsrv';
    }

    public function consultaKardex($producto)
    {
        try {
            $productoModel = Producto::find($producto);
            if (!$productoModel) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $query = Detadoc::select(
                'dbo.DOCUMENTO.Fecha as fecha',
                'dbo.DOCUMENTO.Documento as numero_documento',
                'dbo.TIPODOC.Descripcion as tipo_documento',
                'dbo.DETADOC.Cantidad as cantidad',
                'dbo.DETADOC.PrecUnit as precio_unitario',
                'dbo.TIPODOC.Signo as signo'
            )
            ->join('dbo.DOCUMENTO', function($join) {
                $join->on('dbo.DETADOC.Documento', '=', 'dbo.DOCUMENTO.Documento')
                     ->on('dbo.DETADOC.TipoDoc', '=', 'dbo.DOCUMENTO.TipoDoc');
            })
            ->join('dbo.TIPODOC', 'dbo.DOCUMENTO.TipoDoc', '=', 'dbo.TIPODOC.TipoDoc')
            ->where('dbo.DETADOC.Producto', $producto)
            ->orderBy('dbo.DOCUMENTO.Fecha', 'asc')
            ->get();

            $saldoAcumulado = 0;
            $resultados = [];

            foreach ($query as $row) {
                $movimiento = $row->cantidad * $row->signo;
                $saldoAcumulado += $movimiento;

                $resultados[] = [
                    'fecha' => $row->fecha,
                    'numero_documento' => $row->numero_documento,
                    'tipo_documento' => $row->tipo_documento,
                    'cantidad' => $row->cantidad,
                    'precio_unitario' => $row->precio_unitario,
                    'saldo_acumulado' => $saldoAcumulado
                ];
            }

            return response()->json($resultados);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function consultaPorFechas($producto, Request $request)
    {
        try {
            $productoModel = Producto::find($producto);
            if (!$productoModel) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $fechaInicio = $request->query('fechaInicio');
            $fechaFin = $request->query('fechaFin');

            if (!$fechaInicio || !$fechaFin) {
                return response()->json(['error' => 'Se requieren fechaInicio y fechaFin'], 400);
            }

            $query = Detadoc::select(
                'dbo.DOCUMENTO.Fecha as fecha',
                'dbo.DOCUMENTO.Documento as numero_documento',
                'dbo.TIPODOC.Descripcion as tipo_documento',
                'dbo.DETADOC.Cantidad as cantidad',
                'dbo.DETADOC.PrecUnit as precio_unitario',
                'dbo.TIPODOC.Signo as signo'
            )
            ->join('dbo.DOCUMENTO', function($join) {
                $join->on('dbo.DETADOC.Documento', '=', 'dbo.DOCUMENTO.Documento')
                     ->on('dbo.DETADOC.TipoDoc', '=', 'dbo.DOCUMENTO.TipoDoc');
            })
            ->join('dbo.TIPODOC', 'dbo.DOCUMENTO.TipoDoc', '=', 'dbo.TIPODOC.TipoDoc')
            ->where('dbo.DETADOC.Producto', $producto)
            ->whereBetween('dbo.DOCUMENTO.Fecha', [$fechaInicio, $fechaFin])
            ->orderBy('dbo.DOCUMENTO.Fecha', 'asc')
            ->get();

            $saldoAcumulado = 0;
            $resultados = [];

            foreach ($query as $row) {
                $movimiento = $row->cantidad * $row->signo;
                $saldoAcumulado += $movimiento;

                $resultados[] = [
                    'fecha' => $row->fecha,
                    'numero_documento' => $row->numero_documento,
                    'tipo_documento' => $row->tipo_documento,
                    'cantidad' => $row->cantidad,
                    'precio_unitario' => $row->precio_unitario,
                    'saldo_acumulado' => $saldoAcumulado
                ];
            }

            return response()->json($resultados);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function consultaPorMes($producto, Request $request)
    {
        try {
            $productoModel = Producto::find($producto);
            if (!$productoModel) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $mes = $request->query('mes');
            $anio = $request->query('anio');

            if (!$mes || !$anio) {
                return response()->json(['error' => 'Se requieren mes y anio'], 400);
            }

            $query = Detadoc::select(
                'dbo.DOCUMENTO.Fecha as fecha',
                'dbo.DOCUMENTO.Documento as numero_documento',
                'dbo.TIPODOC.Descripcion as tipo_documento',
                'dbo.DETADOC.Cantidad as cantidad',
                'dbo.DETADOC.PrecUnit as precio_unitario',
                'dbo.TIPODOC.Signo as signo'
            )
            ->join('dbo.DOCUMENTO', function($join) {
                $join->on('dbo.DETADOC.Documento', '=', 'dbo.DOCUMENTO.Documento')
                     ->on('dbo.DETADOC.TipoDoc', '=', 'dbo.DOCUMENTO.TipoDoc');
            })
            ->join('dbo.TIPODOC', 'dbo.DOCUMENTO.TipoDoc', '=', 'dbo.TIPODOC.TipoDoc')
            ->where('dbo.DETADOC.Producto', $producto)
            ->whereRaw('MONTH(dbo.DOCUMENTO.Fecha) = ?', [$mes])
            ->whereRaw('YEAR(dbo.DOCUMENTO.Fecha) = ?', [$anio])
            ->orderBy('dbo.DOCUMENTO.Fecha', 'asc')
            ->get();

            $saldoAcumulado = 0;
            $resultados = [];

            foreach ($query as $row) {
                $movimiento = $row->cantidad * $row->signo;
                $saldoAcumulado += $movimiento;

                $resultados[] = [
                    'fecha' => $row->fecha,
                    'numero_documento' => $row->numero_documento,
                    'tipo_documento' => $row->tipo_documento,
                    'cantidad' => $row->cantidad,
                    'precio_unitario' => $row->precio_unitario,
                    'saldo_acumulado' => $saldoAcumulado
                ];
            }

            return response()->json($resultados);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function consultaPorAnio($producto, Request $request)
    {
        try {
            $productoModel = Producto::find($producto);
            if (!$productoModel) {
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $anio = $request->query('anio');

            if (!$anio) {
                return response()->json(['error' => 'Se requiere anio'], 400);
            }

            $query = Detadoc::select(
                'dbo.DOCUMENTO.Fecha as fecha',
                'dbo.DOCUMENTO.Documento as numero_documento',
                'dbo.TIPODOC.Descripcion as tipo_documento',
                'dbo.DETADOC.Cantidad as cantidad',
                'dbo.DETADOC.PrecUnit as precio_unitario',
                'dbo.TIPODOC.Signo as signo'
            )
            ->join('dbo.DOCUMENTO', function($join) {
                $join->on('dbo.DETADOC.Documento', '=', 'dbo.DOCUMENTO.Documento')
                     ->on('dbo.DETADOC.TipoDoc', '=', 'dbo.DOCUMENTO.TipoDoc');
            })
            ->join('dbo.TIPODOC', 'dbo.DOCUMENTO.TipoDoc', '=', 'dbo.TIPODOC.TipoDoc')
            ->where('dbo.DETADOC.Producto', $producto)
            ->whereRaw('YEAR(dbo.DOCUMENTO.Fecha) = ?', [$anio])
            ->orderBy('dbo.DOCUMENTO.Fecha', 'asc')
            ->get();

            $saldoAcumulado = 0;
            $resultados = [];

            foreach ($query as $row) {
                $movimiento = $row->cantidad * $row->signo;
                $saldoAcumulado += $movimiento;

                $resultados[] = [
                    'fecha' => $row->fecha,
                    'numero_documento' => $row->numero_documento,
                    'tipo_documento' => $row->tipo_documento,
                    'cantidad' => $row->cantidad,
                    'precio_unitario' => $row->precio_unitario,
                    'saldo_acumulado' => $saldoAcumulado
                ];
            }

            return response()->json($resultados);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
