<?php

namespace App\Http\Controllers;

use App\Models\Acuse;
use App\Models\LoteImpresion;
use App\Services\AcuseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AcuseController extends Controller
{
    public function __construct(protected AcuseService $acuses, protected \App\Services\ExcelExportService $excel) {}

    public function index(Request $request)
    {
        // Búsqueda vía AJAX (barra superior)
        if ($request->ajax() && $request->filled('buscar')) {
            $resultados = $this->acuses->buscar($request->get('buscar'));
            return response()->json([
                'html' => view('partials.tabla_resultados', ['acuses' => $resultados])->render(),
                'total_resultados' => $resultados->total(),
            ]);
        }

        return view('acuses.index', [
            'total' => Acuse::count(),
            'impresos' => Acuse::impresos()->count(),
            'pendientes' => Acuse::pendientes()->count(),
            'cajasRestantes' => (int) ceil(Acuse::pendientes()->count() / 250),
            'historial' => $this->acuses->historialCiclos(),
            'acuses' => $request->filled('buscar') ? $this->acuses->buscar($request->get('buscar')) : collect(),
        ]);
    }

    // AJAX: contadores frescos (antes intercepción manual dentro de index())
    public function contadores()
    {
        $pendientes = Acuse::pendientes()->count();
        return response()->json([
            'impresos' => number_format(Acuse::impresos()->count()),
            'cajasRestantes' => number_format(ceil($pendientes / 250)),
        ]);
    }

    public function downloadPDF(Request $request)
    {
        try {
            $datos = $this->acuses->prepararLote(
                $request->get('tipo', 'oficio_2'),
                $request->integer('folio_inicio'),
                $request->integer('folio_fin'),
                auth()->id()
            );
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        $datos['logoPath'] = public_path('logo_pdf.png');

        return Pdf::loadView('reportes.acuse_lote', $datos)
            ->setPaper($datos['setup']['paper'], 'portrait')
            ->download($datos['nombre_archivo']);
    }

    public function imprimirIndividual(string $folio)
    {
        $datos = $this->acuses->prepararIndividual($folio, auth()->id());

        $datos['logoPath'] = public_path('logo_pdf.png');

        return Pdf::loadView('reportes.acuse_lote', $datos)
            ->setPaper($datos['setup']['paper'], 'portrait')
            ->stream($datos['nombre_archivo']);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:20480']);

        try {
            $total = $this->acuses->importarAcusesDesdeExcel($request->file('file'));
            return back()->with('success', "¡Archivo cargado correctamente! ({$total} registros procesados)");
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function importCapturas(Request $request)
    {
        $request->validate(['archivo_excel' => 'required|file|mimes:xlsx,xls,csv|max:20480']);

        try {
            $total = $this->acuses->importarCapturasDesdeExcel($request->file('archivo_excel'));
            return back()->with('success', "¡Reporte de capturas actualizado! ({$total} registros procesados)");
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function exportHistorial()
    {
        return $this->excel->descargar(
            'Historial',
            ['ID Caja', 'Cantidad de Acuses', 'Rango de Folios', 'Fecha de Generación'],
            $this->acuses->filasHistorialParaExcel(),
            'Historial_Cajas_Iztapalapa.xlsx'
        );
    }

    public function reimprimirLote(int $id)
    {
        LoteImpresion::findOrFail($id);
        return back()->with('success', 'Lote relanzado con éxito.');
    }

    // Solo administrador (protegido también a nivel de ruta)
    public function truncate()
    {
        \App\Models\Auditoria::registrar('truncate_sistema', 'Reinició el sistema completo (acuses, lotes y capturas)');

        Acuse::truncate();
        LoteImpresion::truncate();
        \App\Models\Captura::truncate();

        return back()->with('success', '¡El sistema ha sido reiniciado correctamente!');
    }
}
