<?php

namespace App\Console\Commands;

use App\Models\Acuse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrarDatosViejos extends Command
{
    protected $signature = 'migrar:datos-viejos';

    protected $description = 'Copia los datos reales de la base de datos vieja (acuses_db) a la base nueva, tabla por tabla';

    protected string $conexionVieja = 'mysql_viejo';

    public function handle(): int
    {
        $this->info('Iniciando migración desde la base de datos vieja...');
        $this->newLine();

        $resumen = [];
        $resumen['acuses'] = $this->migrarAcuses();
        $resumen['lotes_impresion'] = $this->migrarTablaSimple('lotes_impresion', 'lotes_impresion', ['cantidad', 'rango_folios', 'fecha_generacion']);
        $resumen['incidencias'] = $this->migrarTablaSimple('incidencias', 'incidencias', ['folio', 'situacion', 'accion', 'comentarios', 'estatus']);
        $resumen['capturas'] = $this->migrarTablaSimple('capturas', 'capturas', ['folio', 'cuenta', 'curp', 'fecha_captura']);
        $resumen['nuevas_tarjetas'] = $this->migrarTablaSimple('nuevas_tarjetas', 'nuevas_tarjetas', ['folio', 'nueva_cuenta', 'nueva_tarjeta', 'telefono', 'correo_electronico', 'motivo_reasignacion']);
        $resumen['tarjetas_stock'] = $this->migrarTablaSimple('tarjetas_stock', 'tarjetas_stock', ['folio', 'caja', 'paquete', 'comentarios', 'observaciones']);

        $this->newLine();
        $this->info('── Resumen de la migración ──');
        foreach ($resumen as $tabla => $cantidad) {
            $this->line(str_pad($tabla, 20) . ": {$cantidad} registros");
        }

        return self::SUCCESS;
    }

    /**
     * La tabla `acuses` es especial: necesitamos calcular `folio_numerico`
     * para cada fila (la columna nueva que reemplaza los CAST(...) del
     * sistema viejo), así que va con su propio método en vez del genérico.
     */
    protected function migrarAcuses(): int
    {
        if (!$this->existeTabla('acuses')) {
            $this->warn('  ⚠ La tabla "acuses" no existe en la base vieja — se omite.');
            return 0;
        }

        $total = 0;
        $this->output->write('  Migrando acuses... ');

        DB::connection($this->conexionVieja)->table('acuses')->orderBy('id')
            ->chunk(1000, function ($filas) use (&$total) {
                $bloque = [];
                $ahora = now();

                foreach ($filas as $fila) {
                    $bloque[] = [
                        'cuarta_linea' => $fila->cuarta_linea,
                        'folio_numerico' => (int) preg_replace('/\D/', '', (string) $fila->cuarta_linea),
                        'primer_nombre' => $fila->primer_nombre,
                        'segundo_nombre' => $fila->segundo_nombre ?? null,
                        'primer_apellido' => $fila->primer_apellido,
                        'segundo_apellido' => $fila->segundo_apellido ?? null,
                        'curp' => $fila->curp,
                        'cuenta' => $fila->cuenta ?? null,
                        'tarjeta' => $fila->tarjeta ?? null,
                        'nss_issste' => $fila->nss_issste ?? null,
                        'nss_issemym' => $fila->nss_issemym ?? null,
                        'impreso' => (bool) ($fila->impreso ?? false),
                        'created_at' => $fila->created_at ?? $ahora,
                        'updated_at' => $fila->updated_at ?? $ahora,
                    ];
                }

                DB::table('acuses')->insertOrIgnore($bloque);
                $total += count($bloque);
            });

        $this->line("{$total} registros");
        return $total;
    }

    /**
     * Migración genérica para las tablas que tienen exactamente las mismas
     * columnas en la base vieja y en la nueva (solo copia tal cual).
     */
    protected function migrarTablaSimple(string $tablaVieja, string $tablaNueva, array $columnas): int
    {
        if (!$this->existeTabla($tablaVieja)) {
            $this->warn("  ⚠ La tabla \"{$tablaVieja}\" no existe en la base vieja (o tiene otro nombre) — se omite.");
            return 0;
        }

        $total = 0;
        $this->output->write("  Migrando {$tablaVieja}... ");

        try {
            DB::connection($this->conexionVieja)->table($tablaVieja)->orderBy('id')
                ->chunk(1000, function ($filas) use (&$total, $tablaNueva, $columnas) {
                    $ahora = now();
                    $bloque = [];

                    foreach ($filas as $fila) {
                        $registro = [];
                        foreach ($columnas as $columna) {
                            $registro[$columna] = $fila->{$columna} ?? null;
                        }
                        $registro['created_at'] = $fila->created_at ?? $ahora;
                        $registro['updated_at'] = $fila->updated_at ?? $ahora;
                        $bloque[] = $registro;
                    }

                    DB::table($tablaNueva)->insertOrIgnore($bloque);
                    $total += count($bloque);
                });

            $this->line("{$total} registros");
        } catch (\Throwable $e) {
            $this->error("falló: " . $e->getMessage());
        }

        return $total;
    }

    protected function existeTabla(string $tabla): bool
    {
        try {
            return DB::connection($this->conexionVieja)->getSchemaBuilder()->hasTable($tabla);
        } catch (\Throwable $e) {
            $this->error("No se pudo conectar a la base de datos vieja: " . $e->getMessage());
            return false;
        }
    }
}
