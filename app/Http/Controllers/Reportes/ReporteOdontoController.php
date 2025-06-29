<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Edificio;
use App\Models\Ambiente;
use App\Models\Equipo;
use App\Models\Material;
use App\Models\Mobiliario;
use App\Models\Descarte;
use App\Models\Accesorio;

class ReporteOdontoController extends Controller
{
    //reporte para los equipos de odontologi del edificio central
    public function reportEquiposOdont(Request $request)
    {
        // Obtener los filtros de fecha de adquisición
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Realizar la consulta con los filtros aplicados
        $equipos = Equipo::join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->where('tipo_ambiente.nombre_amb', 'Laboratorio Odontologia')
            ->where('edificio.nombre_edi', 'edificio central')
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('equipo.fch_registro', [$fechaInicio, $fechaFin]);
            })
            ->select('equipo.Cod_equipo', 'equipo.nombre_equi', 'equipo.estado_equi', 'equipo.fch_registro', 'ambiente.nombre AS nombre_ambiente')
            ->get();

        return view('Reportes.reportEquiposEdifCent', ['equipos' => $equipos]);
    }

    //reporte para los mobiliarios y materiales de los ambientes de odontologia del edificio central
    public function reportAmbienteOdontologia(Request $request)
    {
        // Obtener el nombre del ambiente desde el filtro
        $nombreAmbiente = $request->input('nombre_amb');
        $nombreArticulo = $request->input('nombre_equipo');

        // Obtener todos los ambientes relacionados con "Laboratorio Odontología"
        $ambientesOdontologia = Ambiente::join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->where('tipo_ambiente.nombre_amb', '=', 'Laboratorio Odontologia')
            ->select('ambiente.id_ambiente', 'ambiente.nombre')
            ->get();

        // Consultar todos los materiales de los ambientes relacionados con "Laboratorio Odontología"
        $queryMateriales = Material::join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->where('tipo_ambiente.nombre_amb', '=', 'Laboratorio Odontologia');

        // Consultar todos los mobiliarios de los ambientes relacionados con "Laboratorio Odontología"
        $queryMobiliarios = Mobiliario::join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
            ->where('tipo_ambiente.nombre_amb', '=', 'Laboratorio Odontologia');

        // Filtrar por ambiente específico si se selecciona uno
        if ($nombreAmbiente) {
            $queryMateriales->where('ambiente.nombre', '=', $nombreAmbiente);
            $queryMobiliarios->where('ambiente.nombre', '=', $nombreAmbiente);
        }
        // Filtrar por nombre de equipo si se proporciona
        if ($nombreArticulo) {
            $queryMateriales->where('material.tipo_mate', 'LIKE', '%' . $nombreArticulo . '%');
            $queryMobiliarios->where('tipo_mobiliario.tipo_mueble', 'LIKE', '%' . $nombreArticulo . '%');
        }

        // Obtener los materiales
        $materiales = $queryMateriales->select(
            'material.cod_mate AS codigo',
            'material.tipo_mate AS nombre',
            'material.estado_mate AS estado',
            'material.fch_registrada AS fecha',
            'ambiente.nombre AS nombre_ambiente'
        )->get();

        // Obtener los mobiliarios
        $mobiliarios = $queryMobiliarios->select(
            'mobiliario.cod_mueble AS codigo',
            'tipo_mobiliario.tipo_mueble AS nombre',
            'mobiliario.estado_mueb AS estado',
            'mobiliario.fch_registro AS fecha',
            'ambiente.nombre AS nombre_ambiente'
        )->get();

        // Combinar los resultados en una única colección
        $articulos = collect($materiales)->concat($mobiliarios);

        return view('Reportes.reportAmbienteOdontologia', compact('articulos', 'ambientesOdontologia', 'nombreAmbiente', 'nombreArticulo'));
    }

    //para desacartes
    public function reportEquipoDescartado(Request $request)
    {
        $query = Descarte::with('personal');
        
        //filtrar por rango de fechas de descarte
        if ($request->has('start_date') && $request->start_date) {
            $query->where('fch_descarte', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('fch_descarte', '<=', $request->end_date);
        }
        //filtrar por nombre de equipo descartador
        if ($request->has('name') && $request->name) {
            $query->where('nombre', 'like', '%' . $request->name . '%');
        }
    
        $descartes = $query->get();
    
        return view('coordinator.reportes.reportDescaterOdonto', compact('descartes'));
    }

    //reporte de accesorio que mas se cambian admin
    public function reportAccesorioCambio(Request $request)
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

    //reporte para estado de equipo odonotlogicos del edificio principal
    public function reportEstadoEquipos(Request $request)
    {
        $query = Equipo::query()
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            
            ->where('tipo_ambiente.nombre_amb', 'Laboratorio Odontologia')
            ->where('edificio.nombre_edi', 'Edificio Central');

        // Filtros
        if ($request->filled('estado_equi')) {
            $query->where('equipo.estado_equi', $request->input('estado_equi'));
        }
        if ($request->filled('nombre_equi')) {
            $query->where('equipo.nombre_equi', 'like', '%' . $request->input('nombre_equi') . '%');
        }

        $equipos = $query->select(
            'equipo.cod_equipo',
            'equipo.nombre_equi',
            'equipo.marca',
            'equipo.estado_equi',
            'ambiente.nombre',
            'piso.numero_piso'
        )->get();

        $estados_equipos = ['nueno', 'nuevo', 'para mantenimiento'];  // Ejemplo de estados, ajusta según tu BD
        $edificios = Edificio::all();  // Ajusta según la relación con Sede

        return view('Reportes.reporteEstadoEquipoOdonto', compact('equipos', 'estados_equipos', 'edificios'));
    }


    ////////////////////////////////////////////////////////////////////
    ///////// PARA LAS CLINICAS ODONTOLOGIA ////////////////////////////
    //reporte para los equipos de odontologi del edificio central
    public function reportEquiposClinica(Request $request)
    {
        // Obtener los filtros de fecha de adquisición
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Realizar la consulta con los filtros aplicados
        $equipos = Equipo::join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->where('edificio.nombre_edi', 'clinica odontologia')
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('equipo.fch_registro', [$fechaInicio, $fechaFin]);
            })
            ->select('equipo.cod_equipo', 'equipo.nombre_equi', 'equipo.estado_equi', 'equipo.fch_registro', 'ambiente.nombre AS nombre_ambiente', 'piso.numero_piso')
            ->get();

        return view('Coordinator.reportes.reportEquiposClinica', ['equipos' => $equipos]);
    }

    //reportes de mobiliarios y materiales adquiridos en la clinica odontologica
    public function reportMobiliarioMaterialClinica(Request $request)
    {
        // Obtener el nombre del ambiente desde el filtro
        $nombreAmbiente = $request->input('nombre_amb');
        //filtrar por nombre de articulo
        $nombre = $request->input('nombre');

        // Obtener todos los ambientes relacionados con "Laboratorio Odontología"
        $ambientesClinica = Ambiente::join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->where('edificio.nombre_edi', 'clinica odontologia')
            ->select('ambiente.id_ambiente', 'ambiente.nombre')
            ->get();

        // Consultar todos los materiales de los ambientes relacionados con "Laboratorio Odontología"
        $queryMateriales = Material::join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->where('edificio.nombre_edi', 'clinica odontologia');

        // Consultar todos los mobiliarios de los ambientes relacionados con "Laboratorio Odontología"
        $queryMobiliarios = Mobiliario::join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
            ->where('edificio.nombre_edi', 'clinica odontologia');

        // Filtrar por ambiente específico si se selecciona uno
        if ($nombreAmbiente) {
            $queryMateriales->where('ambiente.nombre', '=', $nombreAmbiente);
            $queryMobiliarios->where('ambiente.nombre', '=', $nombreAmbiente);
        }
        // Filtrar por nombre de articulo si se proporciona 
        if ($nombre) {
            $queryMateriales->where('material.tipo_mate', 'LIKE', "%$nombre%");
            $queryMobiliarios->where('tipo_mobiliario.tipo_mueble', 'LIKE', "%$nombre%");
        }

        // Obtener los materiales
        $materiales = $queryMateriales->select(
            'material.cod_mate AS codigo',
            'material.tipo_mate AS nombre',
            'material.estado_mate AS estado',
            'material.fch_registrada AS fecha',
            'ambiente.nombre AS nombre_ambiente'
        )->get();

        // Obtener los mobiliarios
        $mobiliarios = $queryMobiliarios->select(
            'mobiliario.cod_mueble AS codigo',
            'tipo_mobiliario.tipo_mueble AS nombre',
            'mobiliario.estado_mueb AS estado',
            'mobiliario.fch_registro AS fecha',
            'ambiente.nombre AS nombre_ambiente'
        )->get();

        // Combinar los resultados en una única colección
        $articulos = collect($materiales)->concat($mobiliarios);

        return view('Coordinator.reportes.reportContenidoClinica', compact('articulos', 'ambientesClinica', 'nombreAmbiente', 'nombre'));
    }

    //reporte para estado de equipo odonotlogicos de la clincia odontologica
    public function reportEstadoEquiClinica(Request $request)
    {
        $query = Equipo::query()
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            
            ->where('edificio.nombre_edi', 'Clinica Odontologia');

        // Filtros
        if ($request->filled('estado_equi')) {
            $query->where('equipo.estado_equi', $request->input('estado_equi'));
        }
        if ($request->filled('nombre_equi')) {
            $query->where('equipo.nombre_equi', 'like', '%' . $request->input('nombre_equi') . '%');
        }

        $equipos = $query->select(
            'equipo.cod_equipo',
            'equipo.nombre_equi',
            'equipo.marca',
            'equipo.estado_equi',
            'ambiente.nombre',
            'piso.numero_piso'
        )->get();

        $estados_equipos = ['nueno', 'nuevo', 'para mantenimiento'];  // Ejemplo de estados, ajusta según tu BD
        $edificios = Edificio::all();  // Ajusta según la relación con Sede

        return view('coordinator.reportes.reportEstadoEquipoClinica', compact('equipos', 'estados_equipos', 'edificios'));
    }

}
