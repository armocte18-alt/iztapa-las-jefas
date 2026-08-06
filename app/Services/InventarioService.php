<?php

namespace App\Services;

use App\Models\Acuse;
use App\Models\Captura;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventarioService
{
    public function exportarGeneralCsv(): StreamedResponse
    {
        $nombreArchivo = 'Reporte_Inventario_General_Folios_' . now()->format('d_m_Y') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$nombreArchivo}",
        ];

        $callback = function () {
            $archivo = fopen('php://output', 'w');
            fwrite($archivo, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM para acentos en Excel

            fputcsv($archivo, [
                'Folio', 'Primer Apellido', 'Segundo Apellido', 'Primer Nombre',
                'Segundo Nombre', 'CURP', 'Estatus', 'Fecha Entrega', 'Cuenta',
            ]);

            Acuse::with(['nuevaTarjeta', 'tarjetaStock'])
                ->orderBy('id')
                ->chunk(1000, function ($acuses) use ($archivo) {
                    $this->adjuntarCapturas($acuses);

                    foreach ($acuses as $acuse) {
                        fputcsv($archivo, [
                            $acuse->cuarta_linea,
                            $acuse->primer_apellido,
                            $acuse->segundo_apellido ?? '',
                            $acuse->primer_nombre,
                            $acuse->segundo_nombre ?? '',
                            $acuse->curp,
                            $acuse->estatus_cruce,
                            optional($acuse->captura?->fecha_captura)->format('d/m/Y') ?? '',
                            $acuse->cuenta ?? '',
                        ]);
                    }
                });

            fclose($archivo);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Mismo cruce de 3 vías (folio, cuenta o CURP) que en AcuseService,
     * aplicado por bloque de 1000 en vez de por fila — evita el N+1 sin
     * cargar toda la tabla de capturas de una sola vez.
     */
    private function adjuntarCapturas($acuses): void
    {
        $folios = $acuses->pluck('cuarta_linea')->filter()->all();
        $cuentas = $acuses->pluck('cuenta')->filter()->all();
        $curps = $acuses->pluck('curp')->filter()->all();

        $capturas = Captura::where(function ($q) use ($folios, $cuentas, $curps) {
            $q->whereIn('folio', $folios)
                ->orWhereIn('cuenta', $cuentas)
                ->orWhereIn('curp', $curps);
        })->get();

        foreach ($acuses as $acuse) {
            $match = $capturas->first(fn ($c) => $c->folio === $acuse->cuarta_linea
                || ($acuse->cuenta && $c->cuenta === $acuse->cuenta)
                || ($acuse->curp && $c->curp === $acuse->curp));

            $acuse->setRelation('captura', $match);
        }
    }
}
