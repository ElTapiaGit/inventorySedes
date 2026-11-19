<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Ambiente;
use App\Models\Equipo;
use App\Models\Material;
use App\Models\Mobiliario;
use App\Models\Descarte;
use App\Models\Accesorio;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;

class ReporteOdontoController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    // Reporte para equipos del edificio principal o activo
    public function reportEquiposOdont(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $edificioActivoId = $this->scopeService->getEdificioActivoId();
        $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioActivoId);

        $equipos = Equipo::join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->whereIn('equipo.AMBIENTE_id_ambiente', $ambientesIds)
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('equipo.fch_registro', [$fechaInicio, $fechaFin]);
            })
            ->select('equipo.Cod_equipo', 'equipo.nombre_equi', 'equipo.estado_equi', 'equipo.fch_registro', 'ambiente.nombre AS nombre_ambiente')
            ->get();

        return view('Reportes.reportEquiposEdifCent', ['equipos' => $equipos]);
    }

    // Reporte para mobiliarios y materiales de los ambientes permitidos
    public function reportAmbienteOdontologia(Request $request)
    {
        $nombreAmbiente = $request->input('nombre_amb');
        $nombreArticulo = $request->input('nombre_equipo');

        $ambientesPermitidosIds = $this->scopeService->getAmbientesPermitidosIds();

        $ambientesOdontologia = Ambiente::whereIn('id_ambiente', $ambientesPermitidosIds)
            ->select('id_ambiente', 'nombre')
            ->orderBy('nombre')
            ->get();

        $queryMateriales = Material::join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->whereIn('material.AMBIENTE_id_ambiente', $ambientesPermitidosIds);

        $queryMobiliarios = Mobiliario::join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
            ->whereIn('mobiliario.AMBIENTE_id_ambiente', $ambientesPermitidosIds);

        if ($nombreAmbiente) {
            $queryMateriales->where('ambiente.nombre', '=', $nombreAmbiente);
            $queryMobiliarios->where('ambiente.nombre', '=', $nombreAmbiente);
        }

        if ($nombreArticulo) {
            $queryMateriales->where('material.tipo_mate', 'LIKE', '%' . $nombreArticulo . '%');
            $queryMobiliarios->where('tipo_mobiliario.tipo_mueble', 'LIKE', '%' . $nombreArticulo . '%');
        }

        $materiales = $queryMateriales->select(
            'material.cod_mate AS codigo',
            'material.tipo_mate AS nombre',
            'material.estado_mate AS estado',
            'material.fch_registrada AS fecha',
            'ambiente.nombre AS nombre_ambiente'
        )->get();

        $mobiliarios = $queryMobiliarios->select(
            'mobiliario.cod_mueble AS codigo',
            'tipo_mobiliario.tipo_mueble AS nombre',
            'mobiliario.estado_mueb AS estado',
            'mobiliario.fch_registro AS fecha',
            'ambiente.nombre AS nombre_ambiente'
        )->get();

        $articulos = collect($materiales)->concat($mobiliarios);

        return view('Reportes.reportAmbienteOdontologia', compact('articulos', 'ambientesOdontologia', 'nombreAmbiente', 'nombreArticulo'));
    }

    // Reporte de descartes
    public function reportEquipoDescartado(Request $request)
    {
        $edificiosIds = $this->scopeService->getEdificiosPermitidosIds();

        $query = Descarte::with('personal')
            ->whereHas('personal', function ($q) use ($edificiosIds) {
                $q->whereIn('EDIFICIO_id_edificio', $edificiosIds);
            });

        if ($request->has('start_date') && $request->start_date) {
            $query->where('fch_descarte', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('fch_descarte', '<=', $request->end_date);
        }
        if ($request->has('name') && $request->name) {
            $query->where('nombre', 'like', '%' . $request->name . '%');
        }

        $descartes = $query->get();

        return view('coordinator.reportes.reportDescaterOdonto', compact('descartes'));
    }

    // Reporte de accesorios con cambios
    public function reportAccesorioCambio(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $ambientesIds = $this->scopeService->getAmbientesPermitidosIds();

        $accesorios = Accesorio::where(function ($q) use ($ambientesIds) {
                $q->whereHas('equipos', fn($eq) => $eq->whereIn('AMBIENTE_id_ambiente', $ambientesIds))
                  ->orWhereDoesntHave('equipos');
            })
            ->whereHas('historial', function ($query) use ($fechaInicio, $fechaFin) {
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fch_cambio', [$fechaInicio, $fechaFin]);
                }
            })
            ->with(['historial' => function ($query) use ($fechaInicio, $fechaFin) {
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fch_cambio', [$fechaInicio, $fechaFin]);
                }
                $query->orderBy('fch_cambio', 'desc');
            }])
            ->get();

        return view('Reportes.reportAccesorio', ['accesorios' => $accesorios]);
    }

    // Reporte de estado de equipos
    public function reportEstadoEquipos(Request $request)
    {
        $ambientesIds = $this->scopeService->getAmbientesPermitidosIds();

        $query = Equipo::query()
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->whereIn('equipo.AMBIENTE_id_ambiente', $ambientesIds);

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

        $estados_equipos = ['Nuevo', 'Bueno', 'Regular', 'Para mantenimiento', 'Para reparar', 'Para descarte'];
        $edificios = $this->scopeService->getEdificiosPermitidos();

        return view('Reportes.reporteEstadoEquipoOdonto', compact('equipos', 'estados_equipos', 'edificios'));
    }

    // Reportes para Clinica
    public function reportEquiposClinica(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
        $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
        $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
        $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

        $equipos = Equipo::join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->whereIn('equipo.AMBIENTE_id_ambiente', $ambientesIds)
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('equipo.fch_registro', [$fechaInicio, $fechaFin]);
            })
            ->select('equipo.cod_equipo', 'equipo.nombre_equi', 'equipo.estado_equi', 'equipo.fch_registro', 'ambiente.nombre AS nombre_ambiente', 'piso.numero_piso')
            ->get();

        return view('Coordinator.reportes.reportEquiposClinica', ['equipos' => $equipos]);
    }

    public function reportMobiliarioMaterialClinica(Request $request)
    {
        $nombreAmbiente = $request->input('nombre_amb');
        $nombre = $request->input('nombre');

        $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
        $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
        $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
        $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

        $ambientesClinica = Ambiente::whereIn('id_ambiente', $ambientesIds)
            ->select('id_ambiente', 'nombre')
            ->orderBy('nombre')
            ->get();

        $queryMateriales = Material::join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->whereIn('material.AMBIENTE_id_ambiente', $ambientesIds);

        $queryMobiliarios = Mobiliario::join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
            ->whereIn('mobiliario.AMBIENTE_id_ambiente', $ambientesIds);

        if ($nombreAmbiente) {
            $queryMateriales->where('ambiente.nombre', '=', $nombreAmbiente);
            $queryMobiliarios->where('ambiente.nombre', '=', $nombreAmbiente);
        }

        if ($nombre) {
            $queryMateriales->where('material.tipo_mate', 'LIKE', "%$nombre%");
            $queryMobiliarios->where('tipo_mobiliario.tipo_mueble', 'LIKE', "%$nombre%");
        }

        $materiales = $queryMateriales->select(
            'material.cod_mate AS codigo',
            'material.tipo_mate AS nombre',
            'material.estado_mate AS estado',
            'material.fch_registrada AS fecha',
            'ambiente.nombre AS nombre_ambiente'
        )->get();

        $mobiliarios = $queryMobiliarios->select(
            'mobiliario.cod_mueble AS codigo',
            'tipo_mobiliario.tipo_mueble AS nombre',
            'mobiliario.estado_mueb AS estado',
            'mobiliario.fch_registro AS fecha',
            'ambiente.nombre AS nombre_ambiente'
        )->get();

        $articulos = collect($materiales)->concat($mobiliarios);

        return view('Coordinator.reportes.reportContenidoClinica', compact('articulos', 'ambientesClinica', 'nombreAmbiente', 'nombre'));
    }

    public function reportEstadoEquiClinica(Request $request)
    {
        $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
        $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
        $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
        $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

        $query = Equipo::query()
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->whereIn('equipo.AMBIENTE_id_ambiente', $ambientesIds);

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

        $estados_equipos = ['Nuevo', 'Bueno', 'Regular', 'Para mantenimiento', 'Para reparar', 'Para descarte'];
        $edificios = $edificiosPermitidos;

        return view('coordinator.reportes.reportEstadoEquipoClinica', compact('equipos', 'estados_equipos', 'edificios'));
    }
}
