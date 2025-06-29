<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistorialManteAdminController extends Controller
{
    //
    public function index(Request $request)
    {
        try{
            // Obtener datos con filtros aplicados
            $mantenimientos = DB::table('inicio_mantenimiento')
            //se usa left join porque solo se esta buscado el mismo codigo en las tabla equipo, mobiliario y material
                ->Join('detalles_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
                ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.cod_equipo')
                ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
                ->leftJoin('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
                ->leftJoin('ambiente', function($join) {
                    $join->on('equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente');
                })
                ->leftJoin('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->leftJoin('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
                ->leftJoin('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->Join('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
                ->select(
                    'inicio_mantenimiento.id_mantenimiento_ini',
                    'detalles_mantenimiento.cod_articulo',
                    'equipo.nombre_equi as nombre_articulo_equipo',
                    'tipo_mobiliario.tipo_mueble as nombre_articulo_mobiliario',
                    'material.descripcion_mate as nombre_articulo_material',
                    'tecnico.nombre as nombre_tecnico',
                    'tecnico.ap_paterno',
                    'tecnico.ap_materno',
                    'inicio_mantenimiento.fch_inicio',
                    'ambiente.nombre as nombre_ambiente',
                    'edificio.nombre_edi as nombre_edificio',
                    'sede.nombre as nombre_sede'
                )

                ->where(function($query) use ($request) {
                   if ($request->filled('cod_equipo')) {
                        $query->where(function($subQuery) use ($request) {
                            $subQuery->where('equipo.cod_equipo', $request->cod_equipo)
                                    ->orWhere('mobiliario.cod_mueble', $request->cod_equipo)
                                    ->orWhere('material.cod_mate', $request->cod_equipo);
                        });
                    }
                    if ($request->filled('fch_inicio')) {
                        $query->whereDate('inicio_mantenimiento.fch_inicio', '>=', $request->fch_inicio);
                    }
                    if ($request->filled('fch_fin')) {
                        $query->whereDate('inicio_mantenimiento.fch_inicio', '<=', $request->fch_fin);
                    }
                })
                ->paginate(10);

            return view('administrator.historialMantenimientos', ['mantenimientos' => $mantenimientos]);
        }catch (\Exception $e) {
            return redirect()->route('mantenimientosAdmin.index')->with('error', 'Error al agregar el Obtener los datos del historial de mantenimeinto: ');
        }
    }
}
