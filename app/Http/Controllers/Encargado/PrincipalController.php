<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsoAmbiente;
use App\Models\Prestamo;
use App\Models\Mantenimiento;
use App\Models\Usuario;

class PrincipalController extends Controller
{
    public function principal()
    {
        try {
            $ambientesEnUso = UsoAmbiente::whereDoesntHave('finalUsos')->count();
        } catch (\Exception $e) {
            $ambientesEnUso = 0;
        }

        try {
            $prestamosPendientes = Prestamo::whereDoesntHave('devolucion')->count();
        } catch (\Exception $e) {
            $prestamosPendientes = 0;
        }

        try {
            $mantenimientosEnProceso = Mantenimiento::whereDoesntHave('finalMantenimiento')->count();
        } catch (\Exception $e) {
            $mantenimientosEnProceso = 0;
        }

        try {
            $totalUsuarios = Usuario::count();
        } catch (\Exception $e) {
            $totalUsuarios = 0;
        }

        return view('encargados.index', compact(
            'ambientesEnUso',
            'prestamosPendientes',
            'mantenimientosEnProceso',
            'totalUsuarios'
        ));
    }
}
