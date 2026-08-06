<?php

namespace App\Services;

use App\Models\NuevaTarjeta;
use App\Models\TarjetaStock;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TarjetaService
{
    public function asignar(array $datos, int $userId): NuevaTarjeta
    {
        return NuevaTarjeta::updateOrCreate(
            ['folio' => trim($datos['folio'])],
            [
                'nueva_cuenta' => trim($datos['nueva_cuenta']),
                'nueva_tarjeta' => trim($datos['nueva_tarjeta']),
                'telefono' => trim($datos['telefono']),
                'correo_electronico' => trim($datos['correo_electronico']),
                'motivo_reasignacion' => trim($datos['motivo_reasignacion']),
                'registrado_por' => $userId,
            ]
        );
    }

    /**
     * Carga masiva de folios en stock, leyendo el Excel directamente con
     * PhpSpreadsheet (sin Maatwebsite: no tiene versión estable para PHP 8.5).
     */
    public function cargarStockDesdeExcel(UploadedFile $archivo): int
    {
        $hoja = IOFactory::load($archivo->getRealPath())->getActiveSheet();
        $filas = $hoja->toArray(null, true, true, false);

        if (count($filas) < 2) {
            throw new \RuntimeException('El archivo de Excel se encuentra vacío o no contiene registros válidos.');
        }

        $cabecera = array_map(fn ($v) => strtolower(trim((string) $v)), $filas[0]);
        $idx = [
            'folio' => array_search('folio', $cabecera),
            'caja' => array_search('caja', $cabecera),
            'paquete' => array_search('paquete', $cabecera),
            'comentarios' => array_search('comentarios', $cabecera),
            'observaciones' => array_search('observaciones', $cabecera),
        ];

        if ($idx['folio'] === false) {
            throw new \RuntimeException('No se encontró la columna obligatoria "folio" en la primera fila.');
        }

        $ahora = now();
        $inserts = [];

        foreach (array_slice($filas, 1) as $fila) {
            $folio = trim((string) ($fila[$idx['folio']] ?? ''));
            if ($folio === '') {
                continue;
            }
            // Excel a veces manda folios como "12.0" — nos quedamos solo con la parte entera.
            $folio = strtok($folio, '.');

            $inserts[] = [
                'folio' => $folio,
                'caja' => $idx['caja'] !== false ? trim((string) ($fila[$idx['caja']] ?? '')) ?: null : null,
                'paquete' => $idx['paquete'] !== false ? trim((string) ($fila[$idx['paquete']] ?? '')) ?: null : null,
                'comentarios' => $idx['comentarios'] !== false ? trim((string) ($fila[$idx['comentarios']] ?? '')) ?: null : null,
                'observaciones' => $idx['observaciones'] !== false ? trim((string) ($fila[$idx['observaciones']] ?? '')) ?: null : null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }

        DB::table('tarjetas_stock')->truncate();

        $conteo = 0;
        foreach (array_chunk($inserts, 500) as $bloque) {
            $conteo += DB::table('tarjetas_stock')->insertOrIgnore($bloque);
        }

        return $conteo;
    }

    /**
     * Filas listas para el Excel de Nuevas Tarjetas asignadas (antes: NuevasTarjetasExport).
     */
    public function filasAsignadasParaExcel(): array
    {
        return \App\Models\NuevaTarjeta::with('acuse')->orderByDesc('id')->get()
            ->map(function ($t) {
                $acuse = $t->acuse;
                return [
                    $t->folio,
                    $acuse->cuenta ?? 'No Asignada',
                    $acuse->tarjeta ? substr($acuse->tarjeta, -4) : '****',
                    $acuse->primer_nombre ?? '',
                    $acuse->segundo_nombre ?? '',
                    $acuse->primer_apellido ?? '',
                    $acuse->segundo_apellido ?? '',
                    $acuse->curp ?? '',
                    $t->nueva_cuenta,
                    $t->nueva_tarjeta,
                    $t->telefono,
                    $t->correo_electronico,
                    $t->motivo_reasignacion,
                ];
            })->all();
    }

}
