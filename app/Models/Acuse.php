<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acuse extends Model
{
    use HasFactory;

    protected $fillable = [
        'cuarta_linea', 'primer_nombre', 'segundo_nombre',
        'primer_apellido', 'segundo_apellido', 'curp', 'cuenta',
        'tarjeta', 'nss_issste', 'nss_issemym', 'impreso',
    ];

    protected $casts = [
        'impreso' => 'boolean',
        'nss_issemym' => 'date',
    ];

    // ⚡ Antes: `folio_numerico` había que calcularlo a mano o hacer
    // CAST(cuarta_linea AS UNSIGNED) en cada consulta. Ahora se llena solo
    // cada vez que se guarda un acuse, y ya vive indexado en la BD.
    protected static function booted(): void
    {
        static::saving(function (Acuse $acuse) {
            $acuse->folio_numerico = (int) preg_replace('/\D/', '', $acuse->cuarta_linea);
        });
    }

    /**
     * Relaciones — esto es lo que elimina los N+1 que tenía el controlador viejo:
     * en vez de una consulta por cada fila dentro de un foreach, un solo
     * ->with('incidencia', 'captura', 'nuevaTarjeta', 'tarjetaStock')
     * trae TODO en 4-5 consultas totales, sin importar si son 50 o 5,000 filas.
     */
    public function incidencia()
    {
        return $this->hasOne(Incidencia::class, 'folio', 'cuarta_linea');
    }

    public function captura()
    {
        return $this->hasOne(Captura::class, 'folio', 'cuarta_linea');
    }

    public function nuevaTarjeta()
    {
        return $this->hasOne(NuevaTarjeta::class, 'folio', 'cuarta_linea');
    }

    public function tarjetaStock()
    {
        return $this->hasOne(TarjetaStock::class, 'folio', 'cuarta_linea');
    }

    // Accesor de conveniencia para vistas y PDFs: nombre completo en un solo lugar
    // en vez de concatenar los 4 campos en cada blade (como pasaba antes).
    public function getNombreCompletoAttribute(): string
    {
        return trim(mb_strtoupper(
            "{$this->primer_apellido} {$this->segundo_apellido} {$this->primer_nombre} {$this->segundo_nombre}",
            'UTF-8'
        ));
    }

    /**
     * Estatus de cruce homologado — antes era un bloque de if/elseif repetido
     * en el controlador Y en la vista Blade. Ahora vive en un solo lugar.
     * Requiere haber cargado la relación correspondiente con ->with(...).
     */
    public function getEstatusCruceAttribute(): string
    {
        if ($this->relationLoaded('nuevaTarjeta') && $this->nuevaTarjeta) {
            return 'Tarjeta Reasignada';
        }
        if ($this->relationLoaded('captura') && $this->captura) {
            return 'Tarjeta Entregada';
        }
        if ($this->relationLoaded('tarjetaStock') && $this->tarjetaStock) {
            return 'En Stock';
        }
        return 'Pendiente';
    }

    public function scopePendientes($query)
    {
        return $query->where('impreso', false);
    }

    public function scopeImpresos($query)
    {
        return $query->where('impreso', true);
    }
}
