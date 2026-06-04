<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

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

            TipoMovimiento =
                CONCAT('Saldo Inicial (',
                       @ultimoTipoMovimiento,
                       ')'),

            CostoUnitario = @ultimoPrecUnit,

            Cantidad = @ultimaCantidad,

            ValorTotal = @ultimaCantidad * @ultimoPrecUnit,

            StockCalculado = @stockAcumulado;
    END
    ELSE
    BEGIN
        SELECT
            FechaHora =
                CONVERT(VARCHAR(19),
                        @fechaReferencia,
                        121),

            Documento = 'ACUMULADO',

            TipoMovimiento = 'Saldo Inicial',

            CostoUnitario =
                CAST(0 AS NUMERIC(9,2)),

            Cantidad =
                CAST(0 AS NUMERIC(9,2)),

            ValorTotal =
                CAST(0 AS NUMERIC(9,2)),

            StockCalculado =
                CAST(0 AS NUMERIC(9,2));
    END
END
";

try {
    Illuminate\Support\Facades\DB::unprepared($sql);
    echo "Stored procedure successfully altered.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
