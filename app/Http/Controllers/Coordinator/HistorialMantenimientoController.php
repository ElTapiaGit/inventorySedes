<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistorialMantenimientoController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function show(Request $request)
    {
        try {
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioActivoId);

            $query = DB::table('detalles_mantenimiento')
                ->join('inicio_mantenimiento', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini', '=', 'inicio_mantenimiento.id_mantenimiento_ini')
                ->join('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
                ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.cod_equipo')
                ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
                ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
                ->join('ambiente', function ($join) {
                    $join->on('equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente');
                })
                ->whereIn('ambiente.id_ambiente', $ambientesIds)
                ->select(
                    'inicio_mantenimiento.id_mantenimiento_ini',
                    'detalles_mantenimiento.cod_articulo',
                    DB::raw("COALESCE(equipo.nombre_equi, mobiliario.descripticion_mueb, material.descripcion_mate) AS nombre_articulo"),
                    DB::raw("CONCAT(tecnico.nombre, ' ', tecnico.ap_paterno, ' ', tecnico.ap_materno) AS nombre_tecnico"),
                    'inicio_mantenimiento.fch_inicio',
                    'ambiente.nombre AS nombre_ambiente'
                );

            if ($request->filled('cod_equipo')) {
                $query->where('detalles_mantenimiento.cod_articulo', 'like', '%' . $request->cod_equipo . '%');
            }

            if ($request->filled('fch_inicio')) {
                $query->whereDate('inicio_mantenimiento.fch_inicio', '>=', $request->fch_inicio);
            }

            if ($request->filled('fch_fin')) {
                $query->whereDate('inicio_mantenimiento.fch_inicio', '<=', $request->fch_fin);
            }

            $mantenimientos = $query->paginate(10);

            if ($mantenimientos->isEmpty() && ($request->filled('cod_equipo') || $request->filled('fch_inicio') || $request->filled('fch_fin'))) {
                session()->flash('errorbuscar', 'No hay resultados en historial de mantenimientos para la busqueda realizada.');
            }

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.historialMantenimiento', compact('mantenimientos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina Historial mantenimiento.');
        }
    }

    public function mostrar($id)
    {
        try {
            $id_desencriptado = decrypt($id);

            $mantenimiento = DB::table('inicio_mantenimiento')
                ->join('detalles_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
                ->leftJoin('final_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'final_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
                ->join('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
                ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.cod_equipo')
                ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
                ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
                ->leftJoin('foto', function ($join) {
                    $join->on('equipo.FOTO_id_foto', '=', 'foto.id_foto')
                        ->orOn('mobiliario.FOTO_id_foto', '=', 'foto.id_foto')
                        ->orOn('material.FOTO_id_foto', '=', 'foto.id_foto');
                })
                ->select(
                    'detalles_mantenimiento.cod_articulo', 
                    'inicio_mantenimiento.*', 
                    'final_mantenimiento.informe_final', 
                    'final_mantenimiento.fch_final', 
                    'tecnico.nombre', 
                    'tecnico.ap_paterno', 
                    'tecnico.ap_materno', 
                    'tecnico.celular', 
                    'tecnico.direccion',
                    'foto.ruta_foto'
                )
                ->where('inicio_mantenimiento.id_mantenimiento_ini', $id_desencriptado)
                ->first();

            if (!$mantenimiento) {
                return redirect()->route('historialmantenimientos.index')->withErrors(['error' => 'Mantenimiento no encontrado']);
            }

            $articulo = DB::table('equipo')
                ->select('cod_equipo as codigo', 'nombre_equi as nombre', 'AMBIENTE_id_ambiente as id_ambiente')
                ->where('cod_equipo', $mantenimiento->cod_articulo)
                ->union(
                    DB::table('mobiliario')
                        ->select('cod_mueble as codigo', 'tipo_mueble as nombre', 'AMBIENTE_id_ambiente as id_ambiente')
                        ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                        ->where('cod_mueble', $mantenimiento->cod_articulo)
                )
                ->union(
                    DB::table('material')
                        ->select('cod_mate as codigo', 'descripcion_mate as nombre', 'AMBIENTE_id_ambiente as id_ambiente')
                        ->where('cod_mate', $mantenimiento->cod_articulo)
                )
                ->first();

            // Blindaje IDOR: validar pertenencia del ambiente al coordinador
            if ($articulo && $articulo->id_ambiente) {
                $this->scopeService->validarAccesoAmbiente((int) $articulo->id_ambiente);
            }

            $personal = DB::table('personal')
                ->join('tipo_personal', 'personal.TIPO_PERSONAL_id_tipo_per', '=', 'tipo_personal.id_tipo_per')
                ->select(
                    'personal.nombre',
                    'personal.ap_paterno',
                    'personal.ap_materno',
                    'personal.celular',
                    DB::raw("CONCAT(personal.nombre, ' ', personal.ap_paterno, ' ', personal.ap_materno) AS nombre_completo")
                )
                ->where('personal.id_personal', $mantenimiento->PERSONAL_id_personal)
                ->first();

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesHistorialMante', compact('mantenimiento', 'articulo', 'personal', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('coordinator.inicio')->with('error', 'Error al obtener los detalles del mantenimiento.');
        }
    }

    public function historial(Request $request)
    {
        try {
            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

            $query = DB::table('detalles_mantenimiento')
                ->join('inicio_mantenimiento', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini', '=', 'inicio_mantenimiento.id_mantenimiento_ini')
                ->join('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
                ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.cod_equipo')
                ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
                ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
                ->join('ambiente', function ($join) {
                    $join->on('equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente');
                })
                ->whereIn('ambiente.id_ambiente', $ambientesIds)
                ->select(
                    'inicio_mantenimiento.id_mantenimiento_ini',
                    'detalles_mantenimiento.cod_articulo',
                    DB::raw("COALESCE(equipo.nombre_equi, mobiliario.descripticion_mueb, material.descripcion_mate) AS nombre_articulo"),
                    DB::raw("CONCAT(tecnico.nombre, ' ', tecnico.ap_paterno, ' ', tecnico.ap_materno) AS nombre_tecnico"),
                    'inicio_mantenimiento.fch_inicio',
                    'ambiente.nombre AS nombre_ambiente'
                );

            if ($request->filled('cod_equipo')) {
                $query->where('detalles_mantenimiento.cod_articulo', 'like', '%' . $request->cod_equipo . '%');
            }

            if ($request->filled('fch_inicio')) {
                $query->whereDate('inicio_mantenimiento.fch_inicio', '>=', $request->fch_inicio);
            }

            if ($request->filled('fch_fin')) {
                $query->whereDate('inicio_mantenimiento.fch_inicio', '<=', $request->fch_fin);
            }

            $mantenimientos = $query->paginate(10);

            $edificios = $edificiosPermitidos;

            return view('coordinator.clinicas.historialMantenimientos', compact('mantenimientos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina historial de mantenimiento.');
        }
    }

    public function detallesHistorial($id)
    {
        return $this->mostrar($id);
    }
}
