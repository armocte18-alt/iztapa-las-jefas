<?php

namespace App\Services;

use App\Models\Incidencia;
use Carbon\Carbon;

class IncidenciaService
{
    public function registrar(array $datos): Incidencia
    {
        return Incidencia::updateOrCreate(
            ['folio' => trim($datos['folio'])],
            [
                'situacion' => $datos['situacion'],
                'accion' => $datos['accion'],
                'comentarios' => $datos['comentarios'] ?? null,
            ]
        );
    }

    /**
     * Antes: una consulta a `acuses` por cada incidencia dentro de un
     * foreach. Ahora: un solo ->with('acuse') trae todo junto.
     */
    public function porFecha(?string $fechaInicio, ?string $fechaFin, ?string $buscarFolio, int $porPagina = 10)
    {
        $inicio = $fechaInicio ? Carbon::parse($fechaInicio)->startOfDay() : Carbon::today()->startOfDay();
        $fin = $fechaFin ? Carbon::parse($fechaFin)->endOfDay() : Carbon::today()->endOfDay();

        return Incidencia::with('acuse')
            ->whereBetween('created_at', [$inicio, $fin])
            ->when($buscarFolio, fn ($q) => $q->where('folio', 'like', "%{$buscarFolio}%"))
            ->orderBy('folio')
            ->paginate($porPagina, ['*'], 'pagina_incidencias')
            ->withQueryString();
    }

    public function marcarAtendida(int $id, int $userId): void
    {
        Incidencia::findOrFail($id)->update([
            'estatus' => 'Atendido',
            'atendido_por' => $userId,
        ]);
    }

    public function eliminar(int $id): void
    {
        Incidencia::findOrFail($id)->delete();
    }

    /**
     * Filas listas para el Excel de Incidencias (antes: solo un día — ahora
     * exporta el mismo rango que ya se ve filtrado en pantalla).
     */
    public function filasParaExcel(string $fechaInicio, string $fechaFin): array
    {
        $inicio = \Carbon\Carbon::parse($fechaInicio)->startOfDay();
        $fin = \Carbon\Carbon::parse($fechaFin)->endOfDay();

        return \App\Models\Incidencia::with('acuse')
            ->whereBetween('created_at', [$inicio, $fin])
            ->orderBy('folio')
            ->get()
            ->map(fn ($inc) => [
                $inc->id, $inc->folio, $inc->acuse->cuenta ?? 'No Asignada', $inc->situacion, $inc->accion,
                $inc->comentarios, $inc->estatus, $inc->created_at->format('d/m/Y H:i'),
            ])->all();
    }

}
