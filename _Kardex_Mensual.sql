CREATE PROCEDURE _Kardex_Mensual
(
    @idProducto CHAR(4),
    @mes INT,
    @anio INT
)
AS
BEGIN

    SET NOCOUNT ON;

    -- validaciones

    IF @mes NOT BETWEEN 1 AND 12
    BEGIN
        RAISERROR('El mes debe estar entre 1 y 12.',16,1);
        RETURN;
    END

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

    -- fecha inicio y fin del mes

    DECLARE @fechaInicio DATE;
    DECLARE @fechaFin DATE;

    SET @fechaInicio = DATEFROMPARTS(@anio,@mes,1);

    SET @fechaFin = EOMONTH(@fechaInicio);

    -- delegar a SP de rango de fechas

    EXEC _Kardex_RangoFechas
         @idProducto,
         @fechaInicio,
         @fechaFin;

END 
