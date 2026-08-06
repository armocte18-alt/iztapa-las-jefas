<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarFoliosCommand extends Command
{
    protected $signature = 'folios:limpiar';

    protected $description = 'Quita espacios en blanco de más en las columnas de folio de todas las tablas (tu sistema viejo hacía trim() antes de comparar; esto lo replica en la base de datos)';

    protected array $tablas = [
        'acuses' => 'cuarta_linea',
        'tarjetas_stock' => 'folio',
        'nuevas_tarjetas' => 'folio',
        'incidencias' => 'folio',
        'capturas' => 'folio',
    ];

    public function handle(): int
    {
        foreach ($this->tablas as $tabla => $columna) {
            $antes = DB::table($tabla)
                ->whereRaw("`{$columna}` != TRIM(`{$columna}`)")
                ->count();

            DB::statement("UPDATE `{$tabla}` SET `{$columna}` = TRIM(`{$columna}`)");

            $this->line("{$tabla}.{$columna}: {$antes} registro(s) tenían espacios de más — ya corregidos");
        }

        $this->info('Listo. Vuelve a probar la búsqueda de un folio para confirmar el estatus.');

        return self::SUCCESS;
    }
}
