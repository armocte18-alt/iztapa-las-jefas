<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $fillable = ['folio', 'situacion', 'accion', 'comentarios', 'estatus', 'atendido_por'];

    public function acuse()
    {
        return $this->belongsTo(Acuse::class, 'folio', 'cuarta_linea');
    }

    public function atendidoPor()
    {
        return $this->belongsTo(User::class, 'atendido_por');
    }

    public function scopePendientes($query)
    {
        return $query->where('estatus', 'Pendiente');
    }
}
