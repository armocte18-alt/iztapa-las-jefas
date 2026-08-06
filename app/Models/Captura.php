<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Captura extends Model
{
    protected $fillable = ['folio', 'cuenta', 'curp', 'fecha_captura'];

    protected $casts = [
        'fecha_captura' => 'date',
    ];

    public function acuse()
    {
        return $this->belongsTo(Acuse::class, 'folio', 'cuarta_linea');
    }
}
