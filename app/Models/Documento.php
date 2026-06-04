<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'dbo.DOCUMENTO';
    protected $primaryKey = ['Documento', 'TipoDoc'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'Documento', 'TipoDoc', 'Proveedor', 'Pedido', 'Cliente', 
        'Fecha', 'Estado', 'DocRefer', 'Personal', 'pagado', 
        'IdTienda', 'FormaPago', 'Hora'
    ];

    public function detadocs()
    {
        return $this->hasMany(Detadoc::class, ['Documento', 'TipoDoc'], ['Documento', 'TipoDoc']);
    }

    public function tipodoc()
    {
        return $this->belongsTo(Tipodoc::class, 'TipoDoc', 'TipoDoc');
    }
}
