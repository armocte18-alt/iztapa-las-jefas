<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsignarTarjetaRequest;
use App\Http\Requests\CargarStockRequest;
use App\Services\TarjetaService;

class TarjetaController extends Controller
{
    public function __construct(protected TarjetaService $tarjetas, protected \App\Services\ExcelExportService $excel) {}

    public function asignar(AsignarTarjetaRequest $request)
    {
        $tarjeta = $this->tarjetas->asignar($request->validated(), auth()->id());
        return back()->with('success', "¡La tarjeta y cuenta para el folio {$tarjeta->folio} se asignaron correctamente!");
    }

    public function exportarExcel()
    {
        return $this->excel->descargar(
            'Nuevas Tarjetas',
            ['folio', 'cuenta', 'tarjeta_anterior', 'primer_nombre', 'segundo_nombre', 'primer_apellido',
             'segundo_apellido', 'curp', 'nueva_cuenta', 'nueva_tarjeta', 'telefono', 'correo electrónico', 'motivo reasignacion'],
            $this->tarjetas->filasAsignadasParaExcel(),
            'Reporte_Nuevas_Tarjetas_Asignadas.xlsx'
        );
    }

    public function cargarStock(CargarStockRequest $request)
    {
        try {
            $total = $this->tarjetas->cargarStockDesdeExcel($request->file('archivo_excel'));
            return back()->with('swal_incidencia_guardada', "¡Inventario actualizado! Se cargaron {$total} tarjetas físicas.");
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
