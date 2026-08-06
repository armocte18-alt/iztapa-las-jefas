<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsAdministrador
{
    /**
     * Protege acciones destructivas o de exportación masiva: solo administradores.
     * Un 'operador' que intente entrar recibe 403, no un error confuso.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->rol !== 'administrador') {
            abort(403, 'No tienes permisos de administrador para realizar esta acción.');
        }

        return $next($request);
    }
}
