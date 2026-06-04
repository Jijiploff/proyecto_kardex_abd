CREATE PROCEDURE _Kardex_Anual 
(
    @idProducto CHAR(4),
    @anio INT = NULL
)
AS
BEGIN
    SET NOCOUNT ON;

    -- Si no especifica año, usar el actual
    IF @anio IS NULL
        SET @anio = YEAR(GETDATE());

    IF NOT EXISTS(SELECT 1 FROM PRODUCTO WHERE Producto = @idProducto)
    BEGIN
        RAISERROR('El producto especificado no existe.', 16, 1);
        RETURN;
    END

    DECLARE @fechaInicio DATE = DATEFROMPARTS(@anio, 1, 1);
    DECLARE @fechaFin DATE = DATEFROMPARTS(@anio, 12, 31);

    EXEC _Kardex_RangoFechas @idProducto, @fechaInicio, @fechaFin;
END
