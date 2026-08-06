<?php

namespace App\Services;

use App\Models\Acuse;
use App\Models\Captura;
use App\Models\LoteImpresion;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Carbon\Carbon;

class AcuseService
{
    protected int $tamanioCaja = 250;

    /**
     * Búsqueda con TODO el cruce de estatus resuelto en una sola tanda de
     * consultas (antes: una consulta por cada fila dentro de un foreach).
     */
    public function buscar(?string $termino, int $porPagina = 50)
    {
        $query = Acuse::with(['incidencia', 'captura', 'nuevaTarjeta', 'tarjetaStock']);

        if (empty($termino)) {
            return Acuse::whereRaw('1 = 0')->paginate($porPagina); // sin búsqueda, no regresamos universo completo
        }

        if (is_numeric($termino)) {
            $query->where(function ($q) use ($termino) {
                $q->where('cuarta_linea', $termino)->orWhere('cuenta', $termino);
            });
        } else {
            foreach (array_filter(explode(' ', $termino)) as $palabra) {
                $query->where(function ($q) use ($palabra) {
                    $q->where('curp', 'like', "%{$palabra}%")
                        ->orWhere('primer_nombre', 'like', "%{$palabra}%")
                        ->orWhere('segundo_nombre', 'like', "%{$palabra}%")
                        ->orWhere('primer_apellido', 'like', "%{$palabra}%")
                        ->orWhere('segundo_apellido', 'like', "%{$palabra}%");
                });
            }
        }

        return $query->latest()->paginate($porPagina)->withQueryString();
    }

    /**
     * Historial de ciclos de carga (antes: un bucle de N consultas por cada
     * fecha detectada). Aquí se agrupa todo en 2 consultas con GROUP BY.
     */
    public function historialCiclos(int $limite = 40)
    {
        $porAcuse = Acuse::whereNotNull('nss_issemym')
            ->select('nss_issemym as fecha', DB::raw('COUNT(*) as total'),
                DB::raw('MIN(folio_numerico) as folio_inicial'), DB::raw('MAX(folio_numerico) as folio_final'))
            ->groupBy('nss_issemym')->get()->keyBy('fecha');

        $porCaptura = Captura::whereNotNull('fecha_captura')
            ->select('fecha_captura as fecha', DB::raw('COUNT(*) as total'))
            ->groupBy('fecha_captura')->get()->keyBy('fecha');

        $fechas = $porAcuse->keys()->merge($porCaptura->keys())->unique()->sort();

        return $fechas->map(function ($fecha) use ($porAcuse, $porCaptura) {
            return (object) [
                'fecha_carga' => $fecha,
                'total_registros' => $porAcuse->get($fecha)->total ?? 0,
                'total_capturados' => $porCaptura->get($fecha)->total ?? 0,
                'folio_inicial' => $porAcuse->get($fecha)->folio_inicial ?? null,
                'folio_final' => $porAcuse->get($fecha)->folio_final ?? null,
            ];
        })->values()->take(-$limite);
    }

    /**
     * Prepara los datos para imprimir un lote (caja completa o rango manual).
     * Regresa un arreglo listo para pasarle a la vista del PDF — la generación
     * del PDF en sí se queda en el controlador (es una responsabilidad de
     * "presentación", no de negocio).
     */
    public function prepararLote(string $tipo, ?int $folioInicio, ?int $folioFin, int $userId): array
    {
        $opciones = [
            'carta_1' => ['paper' => 'letter', 'copias' => 1, 'height' => '6cm'],
            'carta_2' => ['paper' => 'letter', 'copias' => 2, 'height' => '6cm'],
            'oficio_1' => ['paper' => 'legal', 'copias' => 1, 'height' => '7.9cm'],
            'oficio_2' => ['paper' => 'legal', 'copias' => 2, 'height' => '7.9cm'],
            'rango' => ['paper' => 'legal', 'copias' => 2, 'height' => '7.9cm'],
        ];
        $setup = $opciones[$tipo] ?? $opciones['oficio_2'];

        if ($tipo === 'rango') {
            if (!$folioInicio || !$folioFin || $folioInicio > $folioFin) {
                throw new \InvalidArgumentException('El rango de folios ingresado no es válido.');
            }

            $acuses = Acuse::whereBetween('folio_numerico', [$folioInicio, $folioFin])
                ->orderBy('folio_numerico')->get();

            if ($acuses->isEmpty()) {
                throw new \RuntimeException('No se encontraron registros.');
            }

            $descripcionRango = "Rango Manual: {$acuses->first()->cuarta_linea} - {$acuses->last()->cuarta_linea}";
        } else {
            $primerPendiente = Acuse::pendientes()->orderBy('folio_numerico')->first();

            if (!$primerPendiente) {
                throw new \RuntimeException('No hay registros pendientes.');
            }

            $numeroCaja = (int) ceil($primerPendiente->folio_numerico / $this->tamanioCaja);
            $folioMaximoCaja = $numeroCaja * $this->tamanioCaja;

            $acuses = Acuse::pendientes()
                ->where('folio_numerico', '<=', $folioMaximoCaja)
                ->orderBy('folio_numerico')->take($this->tamanioCaja)->get();

            if ($acuses->isEmpty()) {
                throw new \RuntimeException('No se encontraron registros para la caja.');
            }

            $descripcionRango = "Caja {$numeroCaja}: {$acuses->first()->cuarta_linea} - {$acuses->last()->cuarta_linea}";
        }

        LoteImpresion::create([
            'cantidad' => $acuses->count() * $setup['copias'],
            'rango_folios' => $descripcionRango,
            'fecha_generacion' => now(),
            'user_id' => $userId,
        ]);

        Acuse::whereIn('id', $acuses->pluck('id'))->update(['impreso' => true]);

        $this->generarCodigosDeBarras($acuses);

        return [
            'acuses' => $acuses,
            'setup' => $setup,
            'nombre_archivo' => "Lote_({$acuses->first()->cuarta_linea}-{$acuses->last()->cuarta_linea}).pdf",
        ];
    }

    public function prepararIndividual(string $folio, int $userId): array
    {
        $acuse = Acuse::where('cuarta_linea', $folio)->firstOrFail();
        $acuse->update(['impreso' => true]);

        $setup = ['paper' => 'letter', 'copias' => 2, 'height' => '7.9cm'];
        $this->generarCodigosDeBarras(collect([$acuse]));

        return [
            'acuses' => collect([$acuse]),
            'setup' => $setup,
            'nombre_archivo' => "Acuse_Individual_{$folio}.pdf",
        ];
    }

    private function generarCodigosDeBarras($acuses): void
    {
        $generadorBarras = new BarcodeGeneratorPNG();

        foreach ($acuses as $acuse) {
            $folio = $acuse->cuarta_linea ?: '00000000';

            try {
                $acuse->barcode = base64_encode(
                    $generadorBarras->getBarcode($folio, $generadorBarras::TYPE_CODE_128)
                );
            } catch (\Throwable $e) {
                // \Throwable (no solo \Exception): si falta una librería, PHP lanza
                // \Error ("Class not found"), que \Exception NO atrapa. Sin esto,
                // un solo folio con problema podía tumbar la descarga de TODO el PDF.
                $acuse->barcode = null;
            }

            try {
                // Confirmado con Reflection: en esta versión de endroid/qr-code
                // TODO se configura por constructor (con argumentos nombrados),
                // sin métodos encadenados tipo ->setSize().
                $builder = new \Endroid\QrCode\Builder\Builder(
                    writer: new \Endroid\QrCode\Writer\PngWriter(),
                    data: $folio,
                    size: 120,
                    margin: 0,
                );

                $acuse->qr = base64_encode($builder->build()->getString());
            } catch (\Throwable $e) {
                $acuse->qr = null;
            }
        }
    }

    /**
     * Importación del Excel maestro de acuses (una fila = una beneficiaria).
     * Ajusta los nombres de columna del encabezado si tu Excel real usa
     * otros — aquí se listan los que ya usaba tu importación anterior.
     */
    public function importarAcusesDesdeExcel(\Illuminate\Http\UploadedFile $archivo): int
    {
        $hoja = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo->getRealPath())->getActiveSheet();
        $filas = $hoja->toArray(null, true, true, false);

        if (count($filas) < 2) {
            throw new \RuntimeException('El archivo de Excel se encuentra vacío o no contiene registros válidos.');
        }

        $cabecera = array_map(fn ($v) => strtolower(trim((string) $v)), $filas[0]);
        $mapa = [
            'folio' => array_search('folio', $cabecera),
            'primer_nombre' => array_search('primer_nombre', $cabecera),
            'segundo_nombre' => array_search('segundo_nombre', $cabecera),
            'primer_apellido' => array_search('primer_apellido', $cabecera),
            'segundo_apellido' => array_search('segundo_apellido', $cabecera),
            'curp' => array_search('curp', $cabecera),
            'cuenta' => array_search('cuenta', $cabecera),
            'tarjeta' => array_search('tarjeta', $cabecera),
            'nss_issste' => array_search('nss_issste', $cabecera),
            'nss_issemym' => array_search('fecha_convocatoria', $cabecera) !== false
                ? array_search('fecha_convocatoria', $cabecera)
                : array_search('nss_issemym', $cabecera),
        ];

        foreach (['folio', 'primer_nombre', 'primer_apellido', 'curp'] as $obligatoria) {
            if ($mapa[$obligatoria] === false) {
                throw new \RuntimeException("No se encontró la columna obligatoria \"{$obligatoria}\" en el Excel.");
            }
        }

        $conteo = 0;
        foreach (array_slice($filas, 1) as $fila) {
            $folio = trim((string) ($fila[$mapa['folio']] ?? ''));
            if ($folio === '') {
                continue;
            }
            $folio = strtok($folio, '.');

            \App\Models\Acuse::updateOrCreate(
                ['cuarta_linea' => $folio],
                [
                    'primer_nombre' => trim((string) $fila[$mapa['primer_nombre']]),
                    'segundo_nombre' => $mapa['segundo_nombre'] !== false ? trim((string) ($fila[$mapa['segundo_nombre']] ?? '')) ?: null : null,
                    'primer_apellido' => trim((string) $fila[$mapa['primer_apellido']]),
                    'segundo_apellido' => $mapa['segundo_apellido'] !== false ? trim((string) ($fila[$mapa['segundo_apellido']] ?? '')) ?: null : null,
                    'curp' => trim((string) $fila[$mapa['curp']]),
                    'cuenta' => $mapa['cuenta'] !== false ? trim((string) ($fila[$mapa['cuenta']] ?? '')) ?: null : null,
                    'tarjeta' => $mapa['tarjeta'] !== false ? trim((string) ($fila[$mapa['tarjeta']] ?? '')) ?: null : null,
                    'nss_issste' => $mapa['nss_issste'] !== false ? trim((string) ($fila[$mapa['nss_issste']] ?? '')) ?: null : null,
                    'nss_issemym' => $mapa['nss_issemym'] !== false && !empty($fila[$mapa['nss_issemym']])
                        ? \Carbon\Carbon::parse($fila[$mapa['nss_issemym']])->format('Y-m-d')
                        : null,
                ]
            );
            $conteo++;
        }

        return $conteo;
    }

    /**
     * Importación del Excel de capturas (confirmación de entrega ya hecha).
     */
    public function importarCapturasDesdeExcel(\Illuminate\Http\UploadedFile $archivo): int
    {
        $hoja = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo->getRealPath())->getActiveSheet();
        $filas = $hoja->toArray(null, true, true, false);

        if (count($filas) < 2) {
            throw new \RuntimeException('El archivo de Excel se encuentra vacío o no contiene registros válidos.');
        }

        $cabecera = array_map(fn ($v) => strtolower(trim((string) $v)), $filas[0]);
        $idxFolio = array_search('folio', $cabecera);
        $idxCuenta = array_search('cuenta', $cabecera);
        $idxCurp = array_search('curp', $cabecera);
        $idxFecha = array_search('fecha_captura', $cabecera);

        if ($idxFolio === false) {
            throw new \RuntimeException('No se encontró la columna obligatoria "folio" en el Excel de capturas.');
        }

        $conteo = 0;
        foreach (array_slice($filas, 1) as $fila) {
            $folio = trim((string) ($fila[$idxFolio] ?? ''));
            if ($folio === '') {
                continue;
            }

            \App\Models\Captura::updateOrCreate(
                ['folio' => strtok($folio, '.')],
                [
                    'cuenta' => $idxCuenta !== false ? trim((string) ($fila[$idxCuenta] ?? '')) ?: null : null,
                    'curp' => $idxCurp !== false ? trim((string) ($fila[$idxCurp] ?? '')) ?: null : null,
                    'fecha_captura' => $idxFecha !== false && !empty($fila[$idxFecha])
                        ? \Carbon\Carbon::parse($fila[$idxFecha])->format('Y-m-d')
                        : now()->format('Y-m-d'),
                ]
            );
            $conteo++;
        }

        return $conteo;
    }

    /**
     * Filas listas para el Excel de Historial de Cajas (antes: HistorialExport
     * de Maatwebsite). La consulta vive aquí; el formato .xlsx lo arma
     * ExcelExportService.
     */
    public function filasHistorialParaExcel(): array
    {
        return \App\Models\LoteImpresion::orderByDesc('id')->get()
            ->map(fn ($lote) => [
                $lote->id,
                $lote->cantidad,
                $lote->rango_folios,
                optional($lote->fecha_generacion)->format('d/m/Y H:i'),
            ])->all();
    }

}
