<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function index()
    {
        try {
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioActivoId);

            $equipos = DB::table('equipo')
                ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->whereIn('equipo.estado_equi', ['Para reparar', 'Para mantenimiento'])
                ->whereIn('equipo.AMBIENTE_id_ambiente', $ambientesIds)
                ->select(
                    'equipo.Cod_equipo',
                    'equipo.nombre_equi',
                    'equipo.observaciones_equi',
                    'equipo.estado_equi',
                    'ambiente.nombre as nombre_ambiente'
                )
                ->orderBy('equipo.Cod_equipo')
                ->paginate(10);

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.mantenimiento', compact('equipos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina mantenimiento.');
        }
    }

    public function obtenerMantenimientoClinica()
    {
        try {
            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

            $equipos = DB::table('equipo')
                ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->whereIn('equipo.estado_equi', ['Para reparar', 'Para mantenimiento'])
                ->whereIn('equipo.AMBIENTE_id_ambiente', $ambientesIds)
                ->select(
                    'equipo.cod_equipo',
                    'equipo.nombre_equi',
                    'equipo.observaciones_equi',
                    'equipo.estado_equi',
                    'ambiente.nombre as nombre_ambiente'
                )
                ->orderBy('equipo.cod_equipo')
                ->paginate(10);

            $edificios = $edificiosPermitidos;

            return view('coordinator.mantenimiento', compact('equipos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina mantenimiento.');
        }
    }

    public function report()
    {
        try {
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds();

            $equipos = DB::table('equipo')
                ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->whereIn('equipo.estado_equi', ['Para reparar', 'Para mantenimiento'])
                ->whereIn('equipo.AMBIENTE_id_ambiente', $ambientesIds)
                ->select(
                    'equipo.Cod_equipo',
                    'equipo.nombre_equi',
                    'equipo.observaciones_equi',
                    'equipo.estado_equi',
                    'ambiente.nombre as nombre_ambiente'
                )
                ->orderBy('equipo.Cod_equipo')
                ->paginate(10);

            return view('coordinator.reportes.ParaMantenientos', compact('equipos'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener el reporte de mantenimiento.');
        }
    }
}
