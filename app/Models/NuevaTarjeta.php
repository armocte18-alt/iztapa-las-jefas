<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NuevaTarjeta extends Model
{
    protected $table = 'nuevas_tarjetas';

    protected $fillable = [
        'folio', 'nueva_cuenta', 'nueva_tarjeta', 'telefono',
        'correo_electronico', 'motivo_reasignacion', 'registrado_por',
    ];

    public function acuse()
    {
        return $this->belongsTo(Acuse::class, 'folio', 'cuarta_linea');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
