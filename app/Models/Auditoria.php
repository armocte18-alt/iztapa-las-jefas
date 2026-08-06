<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditorias';

    protected $fillable = ['user_id', 'accion', 'descripcion', 'ip'];

    /**
     * Registra una acción sensible del sistema (descarga masiva, truncado, eliminación, etc.)
     * Uso: Auditoria::registrar('exportar_inventario_general', 'Exportó 99,360 registros');
     */
    public static function registrar(string $accion, ?string $descripcion = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'accion' => $accion,
            'descripcion' => $descripcion,
            'ip' => request()->ip(),
        ]);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
