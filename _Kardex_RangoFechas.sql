CREATE PROCEDURE _Kardex_RangoFechas
(
    @idProducto CHAR(4),
    @fechaInicio DATE,
    @fechaFin DATE
)
AS
BEGIN

    SET NOCOUNT ON;

    ------------------------------------
    -- SALDO INICIAL
    ------------------------------------

    DECLARE @SaldoInicial TABLE
    (
        FechaHora VARCHAR(19),
        Documento VARCHAR(20),
        TipoMovimiento VARCHAR(50),
        CostoUnitario NUMERIC(18,2),
        Cantidad NUMERIC(18,2),
        ValorTotal NUMERIC(18,2),
        StockCalculado NUMERIC(18,2)
    );

    INSERT INTO @SaldoInicial
    EXEC _Kardex_SaldoInicialDetallado
         @idProducto,
         @fechaInicio;

    DECLARE @StockInicial NUMERIC(18,2);

    SELECT
        @StockInicial = StockCalculado
    FROM @SaldoInicial;

    ------------------------------------
    -- MOVIMIENTOS
    ------------------------------------

    CREATE TABLE #Movimientos
    (
        FechaHora VARCHAR(19),
        Documento CHAR(9),
        TipoDoc CHAR(1),
        TipoMovimiento VARCHAR(20),
        CostoUnitario NUMERIC(18,2),
        Cantidad NUMERIC(18,2),
        ValorTotal NUMERIC(18,2),
        Signo SMALLINT
    );

    INSERT INTO #Movimientos
    EXEC _Kardex_Movimientos
         @idProducto,
         @fechaInicio,
         @fechaFin;

    ------------------------------------
    -- RESULTADO FINAL
    ------------------------------------

    SELECT
        Orden = 0,
        FechaHora,
        Documento,
        TipoMovimiento,
        CostoUnitario,
        Cantidad,
        ValorTotal,
        StockCalculado
    FROM @SaldoInicial

    UNION ALL

    SELECT

        Orden =
            ROW_NUMBER() OVER
            (
                ORDER BY FechaHora
            ),

        FechaHora,

        Documento,

        TipoMovimiento,

        CostoUnitario,

        Cantidad,

        ValorTotal,

        StockCalculado =
            @StockInicial
            +
            SUM(Cantidad * Signo)
            OVER
            (
                ORDER BY FechaHora
                ROWS BETWEEN UNBOUNDED PRECEDING
                AND CURRENT ROW
            )

    FROM #Movimientos

    ORDER BY Orden;

    DROP TABLE #Movimientos;

END
