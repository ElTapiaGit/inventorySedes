<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
///////////////////////////////////////////////////////////////////
////////////////CONTROLADORES PARA LAS PAGINAS DE HISTORIAL DE MANTENIEMTO DEL EDIFICIO CENTRAL Y CLINICA

class HistorialMantenimientoController extends Controller
{
    //Para las paginas del edificio central
    public function show(Request $request)
    {
        try{
            $query = DB::table('detalles_mantenimiento')
                ->join('inicio_mantenimiento', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini', '=', 'inicio_mantenimiento.id_mantenimiento_ini')
                ->join('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
                ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.cod_equipo')
                ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
                ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
                ->join('ambiente', function($join) {
                    $join->on('equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente');
                })
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('edificio.nombre_edi', '=', 'Edificio Central')//definir el filtro del edificio, la misma debe estar en la tabla edificio
                ->select(
                    'inicio_mantenimiento.id_mantenimiento_ini',
                    'detalles_mantenimiento.cod_articulo',
                    //DB::raw() se utiliza para insertar fragmentos de SQL sin procesar dentro de las consultas Eloquent.
                    DB::raw("COALESCE(equipo.nombre_equi, mobiliario.descripticion_mueb, material.descripcion_mate) AS nombre_articulo"),
                    DB::raw("CONCAT(tecnico.nombre, ' ', tecnico.ap_paterno, ' ', tecnico.ap_materno) AS nombre_tecnico"),
                    'inicio_mantenimiento.fch_inicio',
                    'ambiente.nombre AS nombre_ambiente'
                );

            // Filtrar por código de articulo
            if ($request->filled('cod_equipo')) {
                $query->where('detalles_mantenimiento.cod_articulo', 'like', '%' . $request->cod_equipo . '%');
            }

            // Filtrar por rango de fechas
            if ($request->filled('fch_inicio')) {
                $query->whereDate('inicio_mantenimiento.fch_inicio', '>=', $request->fch_inicio);
            }

            if ($request->filled('fch_fin')) {
                $query->whereDate('inicio_mantenimiento.fch_inicio', '<=', $request->fch_fin);
            }

            $mantenimientos = $query->paginate(10);

            if($mantenimientos->isEmpty()){
                return redirect()->route('historialmantenimientos.index')->with('errorbuscar', 'No hay resultas en historial de mantenimientos para la busqueda 2');
            }

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.historialMantenimiento', compact('mantenimientos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('mantenimiento.index')->with('errordata', 'Error al obtener los datos para la pagina Historial mantenimiento: ');
        }
    }

    public function mostrar($id)//detalles mantenimiento
    {
        try{
            $id_desencriptado = decrypt($id); // Desencriptamos el ID recibido

            $mantenimiento = DB::table('inicio_mantenimiento')
                ->join('detalles_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
                ->leftJoin('final_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'final_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
                ->join('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
                ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.cod_equipo')
                ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
                ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
                ->leftJoin('foto', function($join) {
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

            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.detallesHistorialMante', compact('mantenimiento', 'articulo', 'personal', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('mantenimiento.index')->with('error', 'Error al obtener los datos para la pagina detalles mantenimiento: ');
        }
    }

    //Para las paginas de la CLINICA ODONTOLOGICA
    public function historial(Request $request)
    {
        try{
            $query = DB::table('detalles_mantenimiento')
                ->join('inicio_mantenimiento', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini', '=', 'inicio_mantenimiento.id_mantenimiento_ini')
                ->join('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
                ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.cod_equipo')
                ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
                ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
                ->join('ambiente', function($join) {
                    $join->on('equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                        ->orOn('material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente');
                })
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('edificio.nombre_edi', '=', 'Clinica Odontologia')//definir el filtro del edificio clinica odontologia
                ->select(
                    'inicio_mantenimiento.id_mantenimiento_ini',
                    'detalles_mantenimiento.cod_articulo',
                    DB::raw("COALESCE(equipo.nombre_equi, mobiliario.descripticion_mueb, material.descripcion_mate) AS nombre_articulo"),
                    DB::raw("CONCAT(tecnico.nombre, ' ', tecnico.ap_paterno, ' ', tecnico.ap_materno) AS nombre_tecnico"),
                    'inicio_mantenimiento.fch_inicio',
                    'ambiente.nombre AS nombre_ambiente'
                );

            // Filtrar por código de equipo
            if ($request->filled('cod_equipo')) {
                $query->where('detalles_mantenimiento.cod_articulo', 'like', '%' . $request->cod_equipo . '%');
            }
            

            // Filtrar por rango de fechas
            if ($request->filled('fch_inicio')) {
                $query->whereDate('inicio_mantenimiento.fch_inicio', '>=', $request->fch_inicio);
            }

            if ($request->filled('fch_fin')) {
                $query->whereDate('inicio_mantenimiento.fch_inicio', '<=', $request->fch_fin);
            }
            
            $mantenimientos = $query->paginate(10);

            if($mantenimientos->isEmpty()){
                return redirect()->route('clinica.historialmantenimientos.index')->with('errorbuscar', 'No hay resultas en historial de mantenimientos para la busqueda en la Clinica');
            }

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.clinicas.historialMantenimientos', compact('mantenimientos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina historial de mantenimiento: ');
        }
    }
    //metodo para mostrar los detalles de mantenimiento hechos
    public function detallesHistorial($id)
    {
        try{
            $id_desencriptado = decrypt($id); // Desencriptamos el ID recibido

            $mantenimiento = DB::table('inicio_mantenimiento')
                ->join('detalles_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
                ->leftJoin('final_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'final_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
                ->join('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
                ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.Cod_equipo')
                ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
                ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
                ->leftJoin('foto', function($join) {
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
            return redirect()->route('clinica.historialmantenimientos.index')->withErrors(['error' => 'Mantenimiento no encontrado']);
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

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.clinicas.detallesHistorialMantenimiento', compact('mantenimiento', 'articulo', 'personal', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para los detalles de historial de mantenimiento: ');
        }
    }
}
