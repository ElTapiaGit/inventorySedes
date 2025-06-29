<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    //CONTROLADOR PARA HEREDAR LA CONSULTA DE NOMBRE EDIFICOS
    public function __construct()
    {
        // Compartir los edificios con todas las vistas
        view()->composer('*', function ($view) {
            $sedeCentral = Sede::where('nombre', 'Sede Central')->first();
            $edificios = $sedeCentral ? $sedeCentral->edificios : [];
            $view->with('edificios', $edificios);
        });
    }
}
