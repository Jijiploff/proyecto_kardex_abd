<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detadoc extends Model
{
    protected $table = 'dbo.DETADOC';
    protected $primaryKey = ['Documento', 'TipoDoc', 'Producto'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'Documento', 'TipoDoc', 'Producto', 'Cantidad', 'Igv', 'PrecUnit'
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, ['Documento', 'TipoDoc'], ['Documento', 'TipoDoc']);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'Producto', 'Producto');
    }
}
