<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next,  $role)//$role es una regla que definire
    {
        $user = Auth::user();

        if ($user && $user->tipoPersonal->descripcion_per == $role) {
            return $next($request);
        }

        return redirect('/');// Redirigir si no tiene el rol adecuado
    }
}
