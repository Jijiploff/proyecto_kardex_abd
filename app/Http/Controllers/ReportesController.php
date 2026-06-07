<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockCriticoExport;
use App\Exports\ResumenMensualExport;
use App\Exports\ValorInventarioExport;
use Carbon\Carbon;

class ReportesController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function stockCritico(): JsonResponse
    {
        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("EXEC _Kardex_StockCritico");
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return response()->json([
                'ok' => true,
                'data' => $resultados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resumenMensual(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'idProducto' => 'required|string|max:50',
                'anio' => 'required|integer|min:2000|max:' . date('Y'),
            ]);

            $idProducto = $request->query('idProducto');
            $anio = (int)$request->query('anio');

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("EXEC _Kardex_ResumenMensual ?, ?");
            $stmt->bindValue(1, $idProducto, \PDO::PARAM_STR);
            $stmt->bindValue(2, $anio, \PDO::PARAM_INT);
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return response()->json([
                'ok' => true,
                'data' => $resultados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function valorInventario(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'idProducto' => 'nullable|string|max:50',
            ]);

            $idProducto = $request->query('idProducto');

            $pdo = DB::connection()->getPdo();
            
            if ($idProducto) {
                $stmt = $pdo->prepare("EXEC _Kardex_ValorInventario ?");
                $stmt->bindValue(1, $idProducto, \PDO::PARAM_STR);
            } else {
                $stmt = $pdo->prepare("EXEC _Kardex_ValorInventario");
            }
            
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return response()->json([
                'ok' => true,
                'data' => $resultados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // PDF Export Methods
    public function exportPdfStockCritico()
    {
        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("EXEC _Kardex_StockCritico");
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);

            $fechaPeru = Carbon::now('America/Lima')->format('d/m/Y H:i:s');
            $fechaArchivo = Carbon::now('America/Lima')->format('Y-m-d');

            $pdf = Pdf::loadView('reportes.pdf.stock-critico', [
                'data' => $resultados,
                'fecha' => $fechaPeru
            ]);

            return $pdf->download('stock_critico_' . $fechaArchivo . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    public function exportPdfResumenMensual(Request $request)
    {
        try {
            $request->validate([
                'idProducto' => 'required|string|max:50',
                'anio' => 'required|integer|min:2000|max:' . Carbon::now('America/Lima')->format('Y'),
            ]);

            $idProducto = $request->query('idProducto');
            $anio = (int)$request->query('anio');

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("EXEC _Kardex_ResumenMensual ?, ?");
            $stmt->bindValue(1, $idProducto, \PDO::PARAM_STR);
            $stmt->bindValue(2, $anio, \PDO::PARAM_INT);
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);

            // Obtener descripción del producto
            $producto = DB::table('dbo.PRODUCTO')->where('Producto', $idProducto)->first();

            $fechaPeru = Carbon::now('America/Lima')->format('d/m/Y H:i:s');
            $fechaArchivo = Carbon::now('America/Lima')->format('Y-m-d');

            $pdf = Pdf::loadView('reportes.pdf.resumen-mensual', [
                'data' => $resultados,
                'producto' => $producto,
                'idProducto' => $idProducto,
                'anio' => $anio,
                'fecha' => $fechaPeru
            ]);

            return $pdf->download('resumen_mensual_' . $idProducto . '_' . $anio . '_' . $fechaArchivo . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    public function exportPdfValorInventario(Request $request)
    {
        try {
            $idProducto = $request->query('idProducto');

            $pdo = DB::connection()->getPdo();
            
            if ($idProducto) {
                $stmt = $pdo->prepare("EXEC _Kardex_ValorInventario ?");
                $stmt->bindValue(1, $idProducto, \PDO::PARAM_STR);
            } else {
                $stmt = $pdo->prepare("EXEC _Kardex_ValorInventario");
            }
            
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_OBJ);

            $producto = null;
            if ($idProducto) {
                $producto = DB::table('dbo.PRODUCTO')->where('Producto', $idProducto)->first();
            }

            $fechaPeru = Carbon::now('America/Lima')->format('d/m/Y H:i:s');
            $fechaArchivo = Carbon::now('America/Lima')->format('Y-m-d');

            $pdf = Pdf::loadView('reportes.pdf.valor-inventario', [
                'data' => $resultados,
                'producto' => $producto,
                'idProducto' => $idProducto,
                'fecha' => $fechaPeru
            ]);

            $filename = $idProducto 
                ? 'valor_inventario_' . $idProducto . '_' . $fechaArchivo . '.pdf'
                : 'valor_inventario_todos_' . $fechaArchivo . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    // Excel Export Methods
    public function exportExcelStockCritico()
    {
        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("EXEC _Kardex_StockCritico");
            $stmt->execute();
            $resultados = collect($stmt->fetchAll(\PDO::FETCH_OBJ));

            $fechaArchivo = Carbon::now('America/Lima')->format('Y-m-d');
            return Excel::download(new StockCriticoExport($resultados), 'stock_critico_' . $fechaArchivo . '.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar Excel: ' . $e->getMessage());
        }
    }

    public function exportExcelResumenMensual(Request $request)
    {
        try {
            $request->validate([
                'idProducto' => 'required|string|max:50',
                'anio' => 'required|integer|min:2000|max:' . Carbon::now('America/Lima')->format('Y'),
            ]);

            $idProducto = $request->query('idProducto');
            $anio = (int)$request->query('anio');

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("EXEC _Kardex_ResumenMensual ?, ?");
            $stmt->bindValue(1, $idProducto, \PDO::PARAM_STR);
            $stmt->bindValue(2, $anio, \PDO::PARAM_INT);
            $stmt->execute();
            $resultados = collect($stmt->fetchAll(\PDO::FETCH_OBJ));

            $producto = DB::table('dbo.PRODUCTO')->where('Producto', $idProducto)->first();
            $fechaArchivo = Carbon::now('America/Lima')->format('Y-m-d');

            return Excel::download(new ResumenMensualExport($resultados, $producto, $anio), 'resumen_mensual_' . $idProducto . '_' . $anio . '_' . $fechaArchivo . '.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar Excel: ' . $e->getMessage());
        }
    }

    public function exportExcelValorInventario(Request $request)
    {
        try {
            $idProducto = $request->query('idProducto');

            $pdo = DB::connection()->getPdo();
            
            if ($idProducto) {
                $stmt = $pdo->prepare("EXEC _Kardex_ValorInventario ?");
                $stmt->bindValue(1, $idProducto, \PDO::PARAM_STR);
            } else {
                $stmt = $pdo->prepare("EXEC _Kardex_ValorInventario");
            }
            
            $stmt->execute();
            $resultados = collect($stmt->fetchAll(\PDO::FETCH_OBJ));

            $producto = null;
            if ($idProducto) {
                $producto = DB::table('dbo.PRODUCTO')->where('Producto', $idProducto)->first();
            }

            $fechaArchivo = Carbon::now('America/Lima')->format('Y-m-d');
            $filename = $idProducto 
                ? 'valor_inventario_' . $idProducto . '_' . $fechaArchivo . '.xlsx'
                : 'valor_inventario_todos_' . $fechaArchivo . '.xlsx';

            return Excel::download(new ValorInventarioExport($resultados, $producto), $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar Excel: ' . $e->getMessage());
        }
    }
}
