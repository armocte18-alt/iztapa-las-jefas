<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidenciaRequest;
use App\Services\IncidenciaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IncidenciaController extends Controller
{
    public function __construct(protected IncidenciaService $incidencias, protected \App\Services\ExcelExportService $excel) {}

    public function store(StoreIncidenciaRequest $request)
    {
        $this->incidencias->registrar($request->validated());
        return back()->with('swal_incidencia_guardada', 'Incidencia procesada con éxito.');
    }

    // AJAX: sub-tabla de incidencias filtrada por fecha/folio
    public function porFecha(Request $request)
    {
        $incidencias = $this->incidencias->porFecha(
            $request->get('fecha_inicio'),
            $request->get('fecha_fin'),
            $request->get('buscar_sub_incidencia')
        );

        return response()->json([
            'conteo' => $incidencias->total(),
            'html_tabla' => view('partials.tabla_incidencias_fecha', compact('incidencias'))->render(),
        ]);
    }

    public function atender(int $id)
    {
        $this->incidencias->marcarAtendida($id, auth()->id());
        return response()->json(['success' => true, 'message' => 'La incidencia ha sido marcada como Atendida.']);
    }

    // Solo administrador
    public function destroy(int $id)
    {
        \App\Models\Auditoria::registrar('eliminar_incidencia', "Eliminó la incidencia #{$id}");
        $this->incidencias->eliminar($id);
        return back()->with('success', 'Incidencia eliminada con éxito.');
    }

    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        return $this->excel->descargar(
            "Incidencias",
            ['ID Reporte', 'Número de Folio', 'Cuenta', 'Situación / Error', 'Acción Requerida', 'Comentarios Adicionales', 'Estatus', 'Fecha y Hora de Reporte'],
            $this->incidencias->filasParaExcel($fechaInicio, $fechaFin),
            "Reporte_Incidencias_{$fechaInicio}_al_{$fechaFin}.xlsx"
        );
    }

    public function exportarPdf(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $incidencias = $this->incidencias->porFecha($fechaInicio, $fechaFin, null, PHP_INT_MAX);

        if ($incidencias->isEmpty()) {
            return back()->with('error', 'No se encontraron incidencias para el rango seleccionado.');
        }

        return Pdf::loadView('reportes.incidencias', compact('incidencias', 'fechaInicio', 'fechaFin'))
            ->setPaper('letter', 'landscape')
            ->download("Reporte_Incidencias_{$fechaInicio}_al_{$fechaFin}.pdf");
    }
}
