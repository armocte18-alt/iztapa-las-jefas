<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteImpresion extends Model
{
    protected $table = 'lotes_impresion';

    protected $fillable = ['cantidad', 'rango_folios', 'fecha_generacion', 'user_id'];

    protected $casts = [
        'fecha_generacion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
