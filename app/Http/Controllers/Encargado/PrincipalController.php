<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UsoAmbiente;
use App\Models\Prestamo;
use App\Models\Mantenimiento;
use App\Models\Usuario;

class PrincipalController extends Controller
{
    public function principal()
    {
        $edificioId = Auth::user()->EDIFICIO_id_edificio;

        try {
            $ambientesEnUso = UsoAmbiente::whereDoesntHave('finalUsos')
                ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                    $q->where('EDIFICIO_id_edificio', $edificioId);
                })
                ->count();
        } catch (\Exception $e) {
            $ambientesEnUso = 0;
        }

        try {
            $prestamosPendientes = Prestamo::whereDoesntHave('devolucion')
                ->whereHas('personal', function ($q) use ($edificioId) {
                    $q->where('EDIFICIO_id_edificio', $edificioId);
                })
                ->count();
        } catch (\Exception $e) {
            $prestamosPendientes = 0;
        }

        try {
            $mantenimientosEnProceso = Mantenimiento::whereDoesntHave('finalMantenimiento')
                ->whereHas('personal', function ($q) use ($edificioId) {
                    $q->where('EDIFICIO_id_edificio', $edificioId);
                })
                ->count();
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
