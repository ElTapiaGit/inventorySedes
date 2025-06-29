<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//////////////////////////////////////////////////////////////////////////////////////
////////////CONTROLADOR PARA LAS PAGINAS DE MANTENIMIENTO DEL EDIFICIO CENTRAL Y LA CLINICA 
class MantenimientoController extends Controller
{
    //Para las paginas del edificio sentral
    public function index()
    {
        try{
            $equipos = DB::table('equipo')
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->whereIn('equipo.estado_equi', ['Para reparar', 'Para mantenimiento']) //equipos con estado para mantenimiento
            ->where('edificio.nombre_edi', 'Edificio Central') //equipos que son del edificio central
            ->where('tipo_ambiente.nombre_amb', 'Laboratorio Odontologia') //equipos que son de los laboratorios odontologicos
            ->select('equipo.Cod_equipo', 'equipo.nombre_equi', 'equipo.observaciones_equi', 'equipo.estado_equi', 'ambiente.nombre as nombre_ambiente')
            ->orderBy('equipo.Cod_equipo')
            ->paginate(10);

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.mantenimiento', compact('equipos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina mantenimiento: ');
        }
    }

    //Para las paginas de la clinica
    public function obtenerMantenimientoClinica()
    {
        try{ 
            $equipos = DB::table('equipo')
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->whereIn('equipo.estado_equi', ['Para reparar', 'Para mantenimiento'])  //equipos con estado para mantenimiento
            ->where('edificio.nombre_edi', 'Clinica Odontologia') // equipos que sean del edificio clinica odontologica
            ->select(
                'equipo.cod_equipo', 
                'equipo.nombre_equi', 
                'equipo.observaciones_equi', 
                'equipo.estado_equi', 
                'ambiente.nombre as nombre_ambiente')
            ->orderBy('equipo.cod_equipo')
            ->paginate(10);

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.clinicas.mantenimientos', compact('equipos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina mantenimiento: ');
        }
    }

    //metodo para el reporte
    public function report()
    {
        try{ 
            $equipos = DB::table('equipo')
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->whereIn('equipo.estado_equi', ['Para reparar', 'Para mantenimiento'])
            ->where('edificio.nombre_edi', 'Clinica Odontologia') //cambiar segun el edificio
            ->select('equipo.Cod_equipo', 'equipo.nombre_equi', 'equipo.observaciones_equi', 'equipo.estado_equi', 'ambiente.nombre as nombre_ambiente')
            ->orderBy('equipo.Cod_equipo')
            ->paginate(10);


            return view('coordinator.reportes.ParaMantenientos', compact('equipos'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina mantenimiento: ');
        }
    }
}
