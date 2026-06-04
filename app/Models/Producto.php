<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'dbo.PRODUCTO';
    protected $primaryKey = 'Producto';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'Producto', 'Marca', 'Descripcion', 'StockAc', 'StockMax', 
        'StockMin', 'PrecVenta', 'PrecCosto', 'Peso', 'ConIgv', 
        'UniMed', 'idProducto', 'idProd'
    ];
}
