<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipodoc extends Model
{
    protected $table = 'dbo.TIPODOC';
    protected $primaryKey = 'TipoDoc';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'TipoDoc', 'Descripcion', 'Serie', 'Numero', 'Signo', 'Unegocio'
    ];

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'TipoDoc', 'TipoDoc');
    }
}
