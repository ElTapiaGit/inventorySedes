<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accesorio;
use Illuminate\Support\Facades\DB;

/////////////////////////////////////////////////////////////////////
// CONTROLADOR PARA LOS ACCESORIOS TANTO DE LA CLINICA Y EDIFICIO////

class AccesorioController extends Controller
{
    //METODO PARA LA PAGINA ACCESORIOS PARA EL EDIFICIO CENTRAL
    public function index()
    {
        try{
            // Obtener los accesorios con paginación de 10 en 10
            $accesorios = Accesorio::paginate(10);
            // Obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina accesorios: ');
        }
    }
    
    public function accesoriosConEquipo()
    {
        try{
            $accesorios = DB::table('accesorio')
            ->join('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
            ->select('accesorio.*')
            ->paginate(10);

            $edificios = DB::table('edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('sede.nombre', 'Sede Central')
            ->select('edificio.nombre_edi')
            ->get();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error no se pueden obtener los datos para los accesorios con equipos: ');
        }
    }

    public function accesoriosUnicos()
    {
        try{
            $accesorios = DB::table('accesorio')
            ->leftJoin('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
            ->whereNull('equipo_has_accesorio.ACCESORIO_cod_accesorio')
            ->select('accesorio.*')
            ->paginate(10);
        
            $edificios = DB::table('edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('sede.nombre', 'Sede Central')
            ->select('edificio.nombre_edi')
            ->get();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error no se pueden obtener los datos para los accesorios sin equipos: ');
        }
    }

    public function buscarPorCodigo(Request $request)
    {
        try{
            $codigo = $request->input('codigo');
            $accesorios = DB::table('accesorio')
                ->where('cod_accesorio', 'LIKE', '%' . $codigo . '%')
                ->paginate(10);
            //para cuando no se encuntre la busqueda
            if ($accesorios->isEmpty()) {
                return redirect()->route('accesorios.index')->with('errorbuscar', 'El código ingresado no está registrado en la Base de Datos.');
            }

                $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error no se pueden obtener los datos para buscar por codigo de accesorio: ');
        }
    }

    public function buscarPorNombre(Request $request)
    {
        try{
            $nombre = $request->input('nombre');
            $accesorios = DB::table('accesorio')
                ->where('nombre_acce', 'LIKE', '%' . $nombre . '%')
                ->paginate(10);

            if ($accesorios->isEmpty()) {
                return redirect()->route('accesorios.index')->with('errorbuscar', 'El nombre a buscar no se encuentra en la Base de Datos.');
            }

            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error no se pueden obtener para buscar por nombre de accesorio: ');
        }
    }

    //funcion para mostrar los DETALLES DE ACCESORIO    
    public function show($cod_accesorio)
    {
       try{
            $accesorio = Accesorio::with('foto')->where('cod_accesorio', $cod_accesorio)->firstOrFail();
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();
            
            return view('coordinator.detallesAccesorio', compact('accesorio', 'edificios'));
       }catch(\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error no se pueden obtener para mostrar los detalles del accesorio: ');
        }
    }

    ////////////////////////////////////////////////////////////////////////
    //METODO PARA LA PAGINA ACCESORIOS PARA LA CLINICA ODONTOLOGIA
    public function obtenerAccesoriosClinica()
    {
        try{
            // Obtener los accesorios con paginación de 10 en 10
            $accesorios = Accesorio::paginate(10);
            // Obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina accesorios: ');
        }
    }
    
    public function accesoriosEquipo()
    {
        try{
            $accesorios = DB::table('accesorio')
            ->join('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
            ->select('accesorio.*')
            ->paginate(10);

            $edificios = DB::table('edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('sede.nombre', 'Sede Central')
            ->select('edificio.nombre_edi')
            ->get();

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error no se pueden obtener los datos para los accesorios con equipos: ');
        }
    }

    public function accesoriosUnicosClin()
    {
        try{
            $accesorios = DB::table('accesorio')
            ->leftJoin('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
            ->whereNull('equipo_has_accesorio.ACCESORIO_cod_accesorio')
            ->select('accesorio.*')
            ->paginate(10);
        
            $edificios = DB::table('edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('sede.nombre', 'Sede Central')
            ->select('edificio.nombre_edi')
            ->get();

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error no se pueden obtener los datos para los accesorios unicos: ');
        }
    }

    public function buscarCodigoCli(Request $request)
    {
        try{
            $codigo = $request->input('codigo');
            $accesorios = DB::table('accesorio')
                ->where('cod_accesorio', 'LIKE', '%' . $codigo . '%')
                ->paginate(10);
            //para cuando no se encuntre la busqueda
            if ($accesorios->isEmpty()) {
                return redirect()->route('clinica.accesorios.index')->with('errorbuscar', 'El código ingresado no está registrado en la base de datos.');
            }

                $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error no se pueden obtener los datos para buscar por cododigo accesorio: ');
        }
    }

    public function buscarNombreCli(Request $request)
    {
        try{
            $nombre = $request->input('nombre');
            $accesorios = DB::table('accesorio')
                ->where('nombre_acce', 'LIKE', '%' . $nombre . '%')
                ->paginate(10);

            if ($accesorios->isEmpty()) {
                return redirect()->route('clinica.accesorios.index')->with('errorbuscar', 'El nombre a buscar no se encuentra.');
            }

            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error no se pueden obtener los datos para buscar por nombre de accesorio: ');
        }
    }

    //funcion para mostrar los detalles del accesorio
    public function mostrar($cod_accesorio)
    {
       try{
            $accesorio = Accesorio::with('foto')->where('cod_accesorio', $cod_accesorio)->firstOrFail();
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();
            
            return view('coordinator.clinicas.detallesAccesorios', compact('accesorio', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error no se pueden obtener los datos para mostrar los detalles del accesorio: ');
        }
    }

}
