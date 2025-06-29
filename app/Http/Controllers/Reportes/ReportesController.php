<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Ambiente;
use App\Models\TipoAmbiente;
use App\Models\Piso;
use App\Models\Edificio;
use App\Models\Sede;
use App\Models\Personal;
use App\Models\TipoPersonal;
use App\Models\Descarte;
use App\Models\Accesorio;
use App\Models\Mantenimiento;
use App\Models\DetallesMantenimiento;
use App\Models\Tecnico;


class ReportesController extends Controller
{
    //para reporte de personal para admin
    public function reportePersonal(Request $request)
    {
        // Obtener los tipos de personal para el filtro
        $tipos_personal = TipoPersonal::all();

        // Construir la consulta
        $query = Personal::join('tipo_personal', 'personal.TIPO_PERSONAL_id_tipo_per', '=', 'tipo_personal.id_tipo_per')
            ->select('personal.*', 'tipo_personal.descripcion_per');

        // Aplicar filtro si se selecciona un tipo de personal
        if ($request->filled('tipo_personal')) {
            $query->where('personal.TIPO_PERSONAL_id_tipo_per', $request->tipo_personal);
        }

        // Aplicar filtro por estado
        if ($request->filled('estado')) {
            $query->where('personal.estado', $request->estado);
        }

        // Obtener los datos filtrados
        $personal = $query->get();

        // Obtener todos los estados para el filtro

        return view('Reportes.reportePersonal', compact('personal', 'tipos_personal'));
    }

    //para desacartes admin
    public function reportArticulosDescartados(Request $request)
    {
        $query = Descarte::with('personal');
        
        if ($request->has('start_date') && $request->start_date) {
            $query->where('fch_descarte', '>=', $request->start_date);
        }
    
        if ($request->has('end_date') && $request->end_date) {
            $query->where('fch_descarte', '<=', $request->end_date);
        }

        // Filtrar por nombre (búsqueda parcial)
        if ($request->has('name') && $request->name) {
            $query->where('nombre', 'like', '%' . $request->name . '%');
        }
    
        // Obtener los resultados
        $descartes = $query->get();
    
        return view('Reportes.descartesArticulos', compact('descartes'));
    }
    
    //reporte de accesorio que mas se cambian admin
    public function reportAccesorios(Request $request)
    {
        // Obtener los filtros de fecha
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Construir la consulta
        $accesorios = Accesorio::whereHas('historial', function ($query) use ($fechaInicio, $fechaFin) {
            // Aplicar filtro de fechas si se proporciona
            if ($fechaInicio && $fechaFin) {
                $query->whereBetween('fch_cambio', [$fechaInicio, $fechaFin]);
            }
        })
        ->with(['historial' => function ($query) use ($fechaInicio, $fechaFin) {
            // Aplicar filtro de fechas si se proporciona
            if ($fechaInicio && $fechaFin) {
                $query->whereBetween('fch_cambio', [$fechaInicio, $fechaFin]);
            }
            $query->orderBy('fch_cambio', 'desc');
        }])
        ->get();

        return view('Reportes.reportAccesorio', ['accesorios' => $accesorios]);
    }

    //reporte para el admin sobre los equipos
    public function reportesEquiposAdquiridos(Request $request)
    {
        // Obtenemos las fechas del request
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Obtener todos los equipos con sus relaciones con filtro por rango de fechas
        $equipos = Equipo::with(['ambiente.piso.edificios.sede'])
            ->when($fechaInicio, function ($query, $fechaInicio) {
                return $query->whereDate('fch_registro', '>=', $fechaInicio);
            })
            ->when($fechaFin, function ($query, $fechaFin) {
                return $query->whereDate('fch_registro', '<=', $fechaFin);
            })
        ->get();

        // Retornar la vista con los datos
        return view('Reportes.reportArticulos', compact('equipos'));
    }

    //reportes de mobiliarios y materiales adquiridos
    public function reportMobiliarioMaterial(Request $request)
    {
        $queryMobiliario = DB::table('mobiliario')
            ->select('mobiliario.cod_mueble', 'tipo_mobiliario.tipo_mueble AS nombre', 'mobiliario.fch_registro', 'ambiente.nombre AS ambiente', 'piso.numero_piso', 'edificio.nombre_edi', 'sede.nombre AS sede')
            ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
            ->join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede');

        $queryMaterial = DB::table('material')
            ->select('material.cod_mate AS cod_mueble', 'material.tipo_mate AS nombre', 'material.fch_registrada AS fch_registro', 'ambiente.nombre AS ambiente', 'piso.numero_piso', 'edificio.nombre_edi', 'sede.nombre AS sede')
            ->join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede');

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $queryMobiliario->whereBetween('mobiliario.fch_registro', [$request->fecha_inicio, $request->fecha_fin]);
            $queryMaterial->whereBetween('material.fch_registrada', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $resultados = $queryMobiliario->union($queryMaterial)->orderBy('fch_registro')->get();

        return view('Reportes.reportMobiliarioMaterial', compact('resultados'));
    }

    //reportes de accesorios adquiridos admin
    public function accesoriosadquirido(Request $request)
    {
        $query = DB::table('accesorio')
            ->select('accesorio.cod_accesorio', 'accesorio.nombre_acce', 'accesorio.fch_registro_acce', 'accesorio.ubicacion', 'accesorio.descripcion_acce', 'accesorio.observacion_ace', 'equipo.nombre_equi AS equipo')
            ->leftJoin('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
            ->leftJoin('equipo', 'equipo_has_accesorio.EQUIPO_cod_equipo', '=', 'equipo.cod_equipo');

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('accesorio.fch_registro_acce', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $resultados = $query->orderBy('accesorio.fch_registro_acce')->get();

        return view('Reportes.reportAccesoriosAdquirido', compact('resultados'));
    }

    //reportes de ambientes para admin
    public function reporteAmbientes(Request $request)
    {
        $query = DB::table('ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->select('ambiente.*', 'piso.numero_piso', 'edificio.nombre_edi', 'sede.nombre as sede_nombre', 'tipo_ambiente.nombre_amb');

        // Aplicar filtros
        if ($request->filled('sede_id')) {
            $query->where('sede.id_sede', $request->sede_id);
        }
        if ($request->filled('edificio_id')) {
            $query->where('edificio.id_edificio', $request->edificio_id);
        }
        if ($request->filled('piso_id')) {
            $query->where('piso.id_piso', $request->piso_id);
        }
        if ($request->filled('tipo_ambiente_id')) {
            $query->where('tipo_ambiente.id_tipoambiente', $request->tipo_ambiente_id);
        }
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('ambiente.fch_registro', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $ambientes = $query->get();

        $sedes = Sede::all();
        $edificios = Edificio::all();
        $pisos = Piso::all();
        $tiposAmbiente = TipoAmbiente::all();

        return view('Reportes.reportAmbientes', compact('ambientes', 'sedes', 'edificios', 'pisos', 'tiposAmbiente'));
    }

    //reporte por estado de equipos de los edificios
    public function estadoequipos(Request $request)
    {
        $estado_equi = $request->input('estado_equi');
        $edificio_id = $request->input('edificio');

        // Obtener lista de estados posibles (puedes definir esto en el controlador o en un archivo de configuración)
        $estados_equipos = ['activo', 'inactivo', 'mantenimiento'];

        // Obtener lista de edificios para el filtro
        $edificios = Edificio::all();

        // Consultar los equipos filtrados
        $equipos = Equipo::query()
            ->with(['ambiente.piso.edificios']) // Cargar relaciones necesarias
            ->when($estado_equi, function($query) use ($estado_equi) {
                return $query->where('estado_equi', $estado_equi);
            })
            ->when($edificio_id, function($query) use ($edificio_id) {
                return $query->whereHas('ambiente.piso.edificios', function($q) use ($edificio_id) {
                    $q->where('id_edificio', $edificio_id);
                });
            })
            ->get();

        return view('Reportes.reportEstadoEquipo', compact('equipos', 'estados_equipos', 'edificios'));
    }

}
