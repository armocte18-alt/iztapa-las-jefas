<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Generador genérico de .xlsx usando PhpSpreadsheet DIRECTO.
 * Reemplaza a Maatwebsite/Laravel-Excel en todo el proyecto: ese paquete
 * no tiene versión estable para PHP 8.5 (misma lección ya aprendida en SIOS).
 */
class ExcelExportService
{
    public function descargar(string $titulo, array $encabezados, iterable $filas, string $nombreArchivo): StreamedResponse
    {
        $hoja = new Spreadsheet();
        $activa = $hoja->getActiveSheet();
        $activa->setTitle(mb_substr($titulo, 0, 31));

        $activa->fromArray($encabezados, null, 'A1');

        $rangoEncabezado = 'A1:' . $activa->getHighestColumn() . '1';
        $activa->getStyle($rangoEncabezado)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $activa->getStyle($rangoEncabezado)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('065F46'); // verde institucional

        $fila = 2;
        foreach ($filas as $registro) {
            $activa->fromArray($registro, null, "A{$fila}");
            $fila++;
        }

        foreach (range('A', $activa->getHighestColumn()) as $columna) {
            $activa->getColumnDimension($columna)->setAutoSize(true);
        }

        $activa->freezePane('A2'); // el encabezado se queda fijo al hacer scroll

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ];

        return new StreamedResponse(function () use ($hoja) {
            (new Xlsx($hoja))->save('php://output');
        }, 200, $headers);
    }
}
