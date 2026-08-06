<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UsuarioActivo
{
    /**
     * Si un usuario se da de baja (activo = false) sin borrar su cuenta,
     * esta capa lo cierra de sesión en su siguiente petición en vez de
     * confiar solo en que alguien recuerde borrar sus credenciales.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta ha sido desactivada. Contacta al administrador.']);
        }

        return $next($request);
    }
}
