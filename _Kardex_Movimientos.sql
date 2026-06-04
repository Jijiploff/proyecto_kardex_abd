CREATE   PROCEDURE _Kardex_Movimientos
(
    @idProducto CHAR(4),
    @fechaInicio DATE,
    @fechaFin DATE
)
AS
BEGIN

    SET NOCOUNT ON;

    -- validaciones

    IF NOT EXISTS
    (
        SELECT 1
        FROM PRODUCTO
        WHERE Producto = @idProducto
    )
    BEGIN
        RAISERROR('El producto especificado no existe.',16,1);
        RETURN;
    END

    IF @fechaInicio > @fechaFin
    BEGIN
        RAISERROR('La fecha inicial no puede ser mayor que la fecha final.',16,1);
        RETURN;
    END

    -- movimientos del rango (con signo para cálculo posterior)

    SELECT

        FechaHora =
            CONVERT(VARCHAR(19),
                ISNULL(d.Hora,d.Fecha),
                121
            ),

        d.Documento,

        d.TipoDoc,

        TipoMovimiento =
            CASE
                WHEN td.Signo = 1 THEN 'INGRESO'
                WHEN td.Signo = -1 THEN 'SALIDA'
                ELSE 'RESERVA'
            END,

        CostoUnitario =
            dd.PrecUnit,

        Cantidad =
            dd.Cantidad,

        ValorTotal =
            dd.Cantidad * dd.PrecUnit,

        Signo =
            td.Signo

    FROM DETADOC dd

        INNER JOIN DOCUMENTO d
            ON d.Documento = dd.Documento
           AND d.TipoDoc = dd.TipoDoc

        INNER JOIN TIPODOC td
            ON td.TipoDoc = d.TipoDoc

    WHERE dd.Producto = @idProducto
      AND d.Fecha BETWEEN @fechaInicio AND @fechaFin

    ORDER BY
        d.Fecha,
        d.Hora,
        d.Documento;

END
