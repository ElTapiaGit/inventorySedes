<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class PrestamosController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    protected function getPrestamosQuery(array $edificiosIds)
    {
        return DB::table('prestamo as pr')
            ->join('personal as per', 'pr.PERSONAL_id_personal', '=', 'per.id_personal')
            ->join('edificio as edi', 'per.EDIFICIO_id_edificio', '=', 'edi.id_edificio')
            ->leftJoin('devolucion as dev', 'pr.id_prestamo', '=', 'dev.PRESTAMO_id_prestamo')
            ->whereIn('edi.id_edificio', $edificiosIds)
            ->select(
                'pr.id_prestamo',
                'pr.nombre_solicitante',
                'pr.descripcion_prestamo',
                'pr.fch_prestamo',
                DB::raw("CONCAT(per.nombre, ' ', per.ap_paterno) AS encargado"),
                'dev.fch_devolucion'
            );
    }

    public function index(Request $request)
    {
        try {
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $edificiosIds = [$edificioActivoId];

            $nombresSolicitantes = DB::table('prestamo')
                ->select(DB::raw("nombre_solicitante AS nombre_solicitante"))
                ->distinct()
                ->get();

            $fechasPrestamo = DB::table('prestamo')
                ->select('fch_prestamo')
                ->distinct()
                ->get();

            $query = $this->getPrestamosQuery($edificiosIds);

            if ($request->filled('usuario')) {
                $query->where('pr.nombre_solicitante', 'LIKE', '%' . $request->input('usuario') . '%');
            }

            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                try {
                    $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->format('Y-m-d');
                    $fechaFin = Carbon::parse($request->input('fecha_fin'))->format('Y-m-d');
                    $query->whereBetween('pr.fch_prestamo', [$fechaInicio, $fechaFin]);
                } catch (\Exception $e) {
                    return redirect()->route('prestamos.index')->with('error_fecha', 'Las fechas ingresadas no son validas.');
                }
            }

            $prestamos = $query->paginate(10);

            if ($request->filled('usuario') && $prestamos->isEmpty()) {
                return redirect()->route('prestamos.index')->with('error_usuario', 'El nombre del prestatario no esta registrado.');
            }

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.prestamos', compact('nombresSolicitantes', 'fechasPrestamo', 'prestamos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina prestamos.');
        }
    }

    public function detalles($token)
    {
        try {
            try {
                $id = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                abort(404, 'Prestamo no encontrado');
            }

            $prestamo = DB::table('prestamo as pr')
                ->join('personal as per', 'pr.PERSONAL_id_personal', '=', 'per.id_personal')
                ->leftJoin('devolucion as dev', 'pr.id_prestamo', '=', 'dev.PRESTAMO_id_prestamo')
                ->leftJoin('personal as per_dev', 'dev.PERSONAL_id_personal', '=', 'per_dev.id_personal')
                ->where('pr.id_prestamo', $id)
                ->select(
                    'pr.id_prestamo',
                    'pr.nombre_solicitante',
                    'pr.descripcion_prestamo',
                    'pr.fch_prestamo',
                    'pr.hora_prestamo',
                    'per.EDIFICIO_id_edificio',
                    DB::raw("CONCAT(per.nombre, ' ', per.ap_paterno, ' ', per.ap_materno) AS nombre_encargado"),
                    'dev.fch_devolucion',
                    'dev.hora_devolucion',
                    'dev.descripcion_devolucion as devolucion_descripcion',
                    DB::raw("CONCAT(per_dev.nombre, ' ', per_dev.ap_paterno, ' ', per_dev.ap_materno) AS nombre_encargado_devolucion")
                )
                ->first();

            if (!$prestamo) {
                abort(404, 'Prestamo no encontrado');
            }

            // Blindaje IDOR: verificar que el edificio del prestamo pertenezca a los permitidos
            $this->scopeService->validarAccesoEdificio((int) $prestamo->EDIFICIO_id_edificio);

            $detalle_prestamos = DB::table('detalle_prestamo as dp')
                ->select([
                    'dp.cod_articulo as cod_equipo',
                    DB::raw("CASE 
                                WHEN eq.nombre_equi IS NOT NULL THEN eq.nombre_equi
                                WHEN tm.tipo_mueble IS NOT NULL THEN tm.tipo_mueble
                                WHEN acc.nombre_acce IS NOT NULL THEN acc.nombre_acce
                                WHEN mat.tipo_mate IS NOT NULL THEN mat.tipo_mate
                                ELSE NULL 
                            END AS nombre_equipo"),
                    'dp.observacion_detalle as observacion_detalle',
                    DB::raw("CASE 
                                WHEN eq.estado_equi IS NOT NULL THEN eq.estado_equi
                                WHEN mob.estado_mueb IS NOT NULL THEN mob.estado_mueb
                                WHEN acc.estado_acce IS NOT NULL THEN acc.estado_acce
                                WHEN mat.estado_mate IS NOT NULL THEN mat.estado_mate
                                ELSE NULL 
                            END AS estado_equipo"),
                    'amb.nombre as ambiente_equipo'
                ])
                ->leftJoin('equipo as eq', 'dp.cod_articulo', '=', 'eq.Cod_equipo')
                ->leftJoin('mobiliario as mob', 'dp.cod_articulo', '=', 'mob.cod_mueble')
                ->leftJoin('accesorio as acc', 'dp.cod_articulo', '=', 'acc.cod_accesorio')
                ->leftJoin('material as mat', 'dp.cod_articulo', '=', 'mat.cod_mate')
                ->leftJoin('tipo_mobiliario as tm', 'mob.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tm.id_tipo_mueb')
                ->leftJoin('ambiente as amb', function ($join) {
                    $join->on('amb.id_ambiente', '=', 'eq.AMBIENTE_id_ambiente')
                        ->orOn('amb.id_ambiente', '=', 'mob.AMBIENTE_id_ambiente')
                        ->orOn('amb.id_ambiente', '=', 'mat.AMBIENTE_id_ambiente');
                })
                ->where('dp.PRESTAMO_id_prestamo', '=', $id)
                ->get();

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesPrestamo', compact('prestamo', 'detalle_prestamos', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('prestamos.index')->with('errordata', 'Error al obtener los detalles de prestamo.');
        }
    }

    // Metodos para contexto Clinica
    public function ObtenerPrestamosClinica(Request $request)
    {
        try {
            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
            $edificiosIds = [$edificioId];

            $nombresSolicitantes = DB::table('prestamo')
                ->select(DB::raw("nombre_solicitante AS nombre_solicitante"))
                ->distinct()
                ->get();

            $fechasPrestamo = DB::table('prestamo')
                ->select('fch_prestamo')
                ->distinct()
                ->get();

            $query = $this->getPrestamosQuery($edificiosIds);

            if ($request->filled('usuario')) {
                $query->where('pr.nombre_solicitante', 'LIKE', '%' . $request->input('usuario') . '%');
            }

            if ($request->filled('fecha')) {
                try {
                    $fecha = Carbon::parse($request->input('fecha'))->format('Y-m-d');
                    $query->whereDate('pr.fch_prestamo', $fecha);
                } catch (\Exception $e) {
                    // Correccion del bug de ruta mal escrita clinca -> clinica
                    return redirect()->route('clinica.prestamos.index')->with('error_fecha', 'La fecha ingresada no esta registrada.');
                }
            }

            $prestamos = $query->paginate(10);

            if ($request->filled('usuario') && $prestamos->isEmpty()) {
                return redirect()->route('clinica.prestamos.index')->with('error_usuario', 'El nombre del prestatario no esta registrado.');
            }

            $edificios = $edificiosPermitidos;

            return view('coordinator.clinicas.prestamos', compact('nombresSolicitantes', 'fechasPrestamo', 'prestamos', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina prestamos.');
        }
    }

    public function detallesPrestamos($token)
    {
        return $this->detalles($token);
    }
}
