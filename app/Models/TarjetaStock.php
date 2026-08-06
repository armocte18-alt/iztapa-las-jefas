<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarjetaStock extends Model
{
    protected $table = 'tarjetas_stock';

    protected $fillable = ['folio', 'caja', 'paquete', 'comentarios', 'observaciones'];

    public function acuse()
    {
        return $this->belongsTo(Acuse::class, 'folio', 'cuarta_linea');
    }
}
