<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
////////////////////////////////////////////////////////////
/// CONTROLADORES PARA LOS PRESTAMOS DEL EDIFICIO CENTRAL Y LA CLINICA 
class PrestamosController extends Controller
{
    //Para las paginas del edificio central
    public function index(Request $request)
    {
        try{
            // Obtener los nombres de los usuarios para el datalist
            $nombresSolicitantes = DB::table('prestamo')
            ->select(DB::raw("nombre_solicitante AS nombre_solicitante"))
            ->get();

            // Obtener las fechas de préstamo
            $fechasPrestamo = DB::table('prestamo')
                ->select('fch_prestamo')
                ->distinct()
                ->get();

            // Obtener todos los préstamos iniciales con paginación
            $query = DB::table('prestamo as pr')
            ->join('personal as per', 'pr.PERSONAL_id_personal', '=', 'per.id_personal')
            ->join('edificio as edi', 'per.EDIFICIO_id_edificio', '=', 'edi.id_edificio')
            ->leftJoin('devolucion as dev', 'pr.id_prestamo', '=', 'dev.PRESTAMO_id_prestamo')
            ->where('edi.nombre_edi', 'Edificio Central')//camniar segun el edificio
            ->select(
                'pr.id_prestamo',
                'pr.nombre_solicitante',
                'pr.descripcion_prestamo',
                'pr.fch_prestamo',
                DB::raw("CONCAT(per.nombre, ' ', per.ap_paterno) AS encargado"),
                'dev.fch_devolucion'
            );

            // Filtros de búsqueda
            if ($request->has('usuario') && !empty($request->input('usuario'))) {
                $query->where('pr.nombre_solicitante', 'LIKE', '%' . $request->input('usuario') . '%');
            }

            if ($request->has('fecha_inicio') && !empty($request->input('fecha_inicio')) &&
            $request->has('fecha_fin') && !empty($request->input('fecha_fin'))) {
                try {
                    $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->format('Y-m-d');
                    $fechaFin = Carbon::parse($request->input('fecha_fin'))->format('Y-m-d');
                    $query->whereBetween('pr.fch_prestamo', [$fechaInicio, $fechaFin]);
                } catch (\Exception $e) {
                    return redirect()->route('prestamos.index')->with('error_fecha', 'Las fechas ingresadas no son válidas.');
                }  
            }

            //paginacion
            $prestamos = $query->paginate(10);

            // Redirección con mensaje de error si no se encuentran registros
            if ($request->has('usuario') && !empty($request->input('usuario')) && $prestamos->isEmpty()) {
                return redirect()->route('prestamos.index')->with('error_usuario', 'El nombre del prestatario no está registrado.');
            }

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                        ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                        ->where('sede.nombre', 'Sede Central')
                        ->select('edificio.nombre_edi')
                        ->get();

            return view('coordinator.prestamos', compact('nombresSolicitantes', 'fechasPrestamo', 'prestamos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina prestamos: ');
        }
    }

    public function detalles($token)
    {
        try {
            $id = Crypt::decrypt($token);
        } catch (DecryptException $e) {
            abort(404);
        }

        try{
            $prestamo = DB::table('prestamo as pr')
            ->join('personal as per', 'pr.PERSONAL_id_personal', '=', 'per.id_personal')  // Personal que realizó el préstamo
            ->leftJoin('devolucion as dev', 'pr.id_prestamo', '=', 'dev.PRESTAMO_id_prestamo')
            ->leftJoin('personal as per_dev', 'dev.PERSONAL_id_personal', '=', 'per_dev.id_personal') // Personal que recibió la devolución
            ->where('pr.id_prestamo', $id)
            ->select(
                'pr.id_prestamo',
                'pr.nombre_solicitante',
                'pr.descripcion_prestamo',
                'pr.fch_prestamo',
                'pr.hora_prestamo',
                DB::raw("CONCAT(per.nombre, ' ', per.ap_paterno, ' ', per.ap_materno) AS nombre_encargado"),
                'dev.fch_devolucion',
                'dev.hora_devolucion',
                'dev.descripcion_devolucion as devolucion_descripcion',
                DB::raw("CONCAT(per_dev.nombre, ' ', per_dev.ap_paterno, ' ', per_dev.ap_materno) AS nombre_encargado_devolucion") // Nombre del personal que recibió la devolución
            )
            ->first();

            // Obtener los detalles de los equipos prestados
            $detalle_prestamos = DB::table('detalle_prestamo as dp')
                ->select([
                    'dp.cod_articulo as cod_equipo',
                    //Utiliza expresiones crudas para construir campos calculados en la consulta SQL, como el nombre y el estado del equipo.
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
                //leftJoin Se usa para unir las tablas relacionadas (equipos, mobiliario, accesorios, materiales) con la tabla detalle_prestamo.
                ->leftJoin('equipo as eq', 'dp.cod_articulo', '=', 'eq.Cod_equipo')
                ->leftJoin('mobiliario as mob', 'dp.cod_articulo', '=', 'mob.cod_mueble')
                ->leftJoin('accesorio as acc', 'dp.cod_articulo', '=', 'acc.cod_accesorio')
                ->leftJoin('material as mat', 'dp.cod_articulo', '=', 'mat.cod_mate')
                ->leftJoin('tipo_mobiliario as tm', 'mob.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tm.id_tipo_mueb')
                //joinSe usa para unir la tabla ambiente
                ->join('ambiente as amb', function($join) {
                    $join->on('amb.id_ambiente', '=', 'eq.AMBIENTE_id_ambiente')
                        ->orOn('amb.id_ambiente', '=', 'mob.AMBIENTE_id_ambiente')
                        ->orOn('amb.id_ambiente', '=', 'mat.AMBIENTE_id_ambiente');
                })
                ->where('dp.PRESTAMO_id_prestamo', '=', $id)
                ->get();


                // Obtener los edificios para el menú desplegable
                $edificios = DB::table('edificio')
                    ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                    ->where('sede.nombre', 'Sede Central')
                    ->select('edificio.nombre_edi')
                    ->get();

            return view('coordinator.detallesPrestamo', compact('prestamo', 'detalle_prestamos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('prestamos.index')->with('errordata', 'Error al obtener los datos para los detalles de prestamo: ');
        }
    }

    //para las paginas de la clinica odontologica
    public function ObtenerPrestamosClinica(Request $request)
    {
        try{
            // Obtener los nombres de los usuarios para el datalist
            $nombresSolicitantes = DB::table('prestamo')
            ->select(DB::raw("nombre_solicitante AS nombre_solicitante"))
            ->get();

            // Obtener las fechas de préstamo
            $fechasPrestamo = DB::table('prestamo')
                ->select('fch_prestamo')
                ->distinct()
                ->get();

            // Obtener todos los préstamos iniciales con paginación
            $query = DB::table('prestamo as pr')
                ->join('personal as per', 'pr.PERSONAL_id_personal', '=', 'per.id_personal')
                ->join('edificio as edi', 'per.EDIFICIO_id_edificio', '=', 'edi.id_edificio')
                ->leftJoin('devolucion as dev', 'pr.id_prestamo', '=', 'dev.PRESTAMO_id_prestamo')
                ->where('edi.nombre_edi', 'Clinica Odontologia')//prestamos realizados en la clinica odontologica
                ->select(
                'pr.id_prestamo',
                'pr.nombre_solicitante',
                'pr.descripcion_prestamo',
                'pr.fch_prestamo',
                DB::raw("CONCAT(per.nombre, ' ', per.ap_paterno) AS encargado"),
                'dev.fch_devolucion'
            );

            // Filtros de búsqueda
            if ($request->has('usuario') && !empty($request->input('usuario'))) {
                $query->where('pr.nombre_solicitante', 'LIKE', '%' . $request->input('usuario') . '%');
            }

            if ($request->has('fecha') && !empty($request->input('fecha'))) {
                try {
                    $fecha = Carbon::parse($request->input('fecha'))->format('Y-m-d');
                    $query->whereDate('pr.fch_prestamo', $fecha);
                } catch (\Exception $e) {
                    return redirect()->route('clinca.prestamos.index')->with('error_fecha', 'La fecha ingresada no está registrada.');
                }
            }

            //paginacion
            $prestamos = $query->paginate(10);

            // Redirección con mensaje de error si no se encuentran registros
            if ($request->has('usuario') && !empty($request->input('usuario')) && $prestamos->isEmpty()) {
                return redirect()->route('clinica.prestamos.index')->with('error_usuario', 'El nombre del prestatario no está registrado.');
            }

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                        ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                        ->where('sede.nombre', 'Sede Central')
                        ->select('edificio.nombre_edi')
                        ->get();

            return view('coordinator.clinicas.prestamos', compact('nombresSolicitantes', 'fechasPrestamo', 'prestamos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina prestamos: ');
        }
    }

    public function detallesPrestamos($token)
    {
        try {
            $id = Crypt::decrypt($token);
        } catch (DecryptException $e) {
            abort(404);
        }
        try{
            $prestamo = DB::table('prestamo as pr')
                ->join('personal as per', 'pr.PERSONAL_id_personal', '=', 'per.id_personal')
                ->leftJoin('devolucion as dev', 'pr.id_prestamo', '=', 'dev.PRESTAMO_id_prestamo')
                ->where('pr.id_prestamo', $id)
                ->select(
                    'pr.id_prestamo',
                    'pr.nombre_solicitante',
                    'pr.descripcion_prestamo',
                    'pr.fch_prestamo',
                    'pr.hora_prestamo',
                    DB::raw("CONCAT(per.nombre, ' ', per.ap_paterno, ' ', per.ap_materno) AS nombre_encargado"),
                    'dev.fch_devolucion',
                    'dev.hora_devolucion',
                    'dev.descripcion_devolucion as devolucion_descripcion',
                    DB::raw("CONCAT(per.nombre, ' ', per.ap_paterno, ' ', per.ap_materno) AS nombre_encargado_devolucion")
                )
                ->first();

            // Obtener los detalles de los equipos prestados
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
                ->join('ambiente as amb', function($join) {
                    $join->on('amb.id_ambiente', '=', 'eq.AMBIENTE_id_ambiente')
                        ->orOn('amb.id_ambiente', '=', 'mob.AMBIENTE_id_ambiente')
                        ->orOn('amb.id_ambiente', '=', 'mat.AMBIENTE_id_ambiente');
                })
                ->where('dp.PRESTAMO_id_prestamo', '=', $id)
                ->get();

                // Obtener los edificios para el menú desplegable
                $edificios = DB::table('edificio')
                    ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                    ->where('sede.nombre', 'Sede Central')
                    ->select('edificio.nombre_edi')
                    ->get();
                    
            return view('coordinator.clinicas.detallesPrestamos', compact('prestamo', 'detalle_prestamos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina detalles prestamo: ');
        }
    }
}
