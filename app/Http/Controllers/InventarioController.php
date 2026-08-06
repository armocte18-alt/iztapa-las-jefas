<?php

namespace App\Http\Controllers;

use App\Services\InventarioService;

class InventarioController extends Controller
{
    public function __construct(protected InventarioService $inventario) {}

    // Solo administrador (protegido a nivel de ruta) — exporta CURP + cuenta de todo el padrón
    public function exportarGeneral()
    {
        \App\Models\Auditoria::registrar('exportar_inventario_general', 'Descargó la sábana completa de inventario (CURP y cuenta)');
        return $this->inventario->exportarGeneralCsv();
    }
}
