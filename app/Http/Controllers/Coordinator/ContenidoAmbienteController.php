<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Edificio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class ContenidoAmbienteController extends Controller
{
    //metodo de la pagina contenido ambiente PARA LAS CLINICAS
    public function show($id_ambiente)
    {
        try{
            // Desencriptar el ID del ambiente
            try {
                $id = Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('coordinator.clinica.inicio')->withErrors('ID de ambiente no válido.');
            }

            // Obtener los detalles del ambiente
            $ambiente = DB::table('ambiente')
                        ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                        ->where('ambiente.id_ambiente', $id)
                        ->select(DB::raw("CONCAT(tipo_ambiente.nombre_amb, ' - ', ambiente.nombre) AS nombre_completo_ambiente", 'ambiente.id_ambiente'), 'ambiente.id_ambiente')
                        ->first();

            // Obtener los equipos del ambiente
            $equipos = DB::table('equipo')
                        ->where('AMBIENTE_id_ambiente', $id)
                        ->select('cod_equipo', 'nombre_equi', 'estado_equi', 'observaciones_equi', 'vida_util', 'fch_registro')
                        ->get();

            // Obtener los materiales del ambiente
            $materiales = DB::table('material')
                            ->where('AMBIENTE_id_ambiente', $id)
                            ->select('cod_mate', 'tipo_mate', 'estado_mate', 'observacion_mate', 'fch_registrada')
                            ->get();

            // Obtener los mobiliarios del ambiente
            $mobiliarios = DB::table('mobiliario')
                            ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                            ->where('AMBIENTE_id_ambiente', $id)
                            ->select('mobiliario.cod_mueble', 'tipo_mobiliario.tipo_mueble', 'mobiliario.estado_mueb', 'mobiliario.observacion', 'mobiliario.vida_util', 'mobiliario.fch_registro')
                            ->get();

            // Obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                            ->where('sede.nombre', 'Sede Central')
                            ->select('edificio.nombre_edi')
                            ->get();

            // Retornar la vista con los datos obtenidos
            return view('coordinator.clinicas.contenidoAmbiente', compact('ambiente', 'equipos', 'materiales', 'mobiliarios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos para la pagina ambientes: VERIFICAR CAMPOS EN LA BD');
        }
    }

    //metodo de la pagina contenido ambiente clinico especificos
    public function showEquipos($id_ambiente)
    {
        try{
            try {
                $id = Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('coordinator.clinica.inicio')->withErrors('ID de ambiente no válido.');
            }
    
            $ambiente = DB::table('ambiente')->where('id_ambiente', $id)->first();
            $equipos = DB::table('equipo')->where('AMBIENTE_id_ambiente', $id)->get();
    
            // Obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                            ->where('sede.nombre', 'Sede Central')
                            ->select('edificio.nombre_edi')
                            ->get();
    
            return view('coordinator.clinicas.equiposAmbiente', compact('ambiente', 'equipos','edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos para la pagina equipos del ambinete: VERIFICAR CAMPOS EN LA BD');
        }
    }

    public function showMateriales($id_ambiente)
    {
        try{
            try {
                $id = Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('coordinator.clinica.inicio')->withErrors('ID de ambiente no válido.');
            }
    
            $ambiente = DB::table('ambiente')->where('id_ambiente', $id)->first();
            $materiales = DB::table('material')->where('AMBIENTE_id_ambiente', $id)->get();
    
            // Obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                            ->where('sede.nombre', 'Sede Central')
                            ->select('edificio.nombre_edi')
                            ->get();
    
            return view('coordinator.clinicas.materialesAmbiente', compact('ambiente', 'materiales', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos para la pagina materiales del ambinete: VERIFICAR CAMPOS EN LA BD');
        }
    }

    public function showMobiliarios($id_ambiente)
    {
        try{
            try {
                $id = Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('coordinator.clinica.inicio')->withErrors('ID de ambiente no válido.');
            }
    
            $ambiente = DB::table('ambiente')->where('id_ambiente', $id)->first();
            $mobiliarios = DB::table('mobiliario')
                             ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                             ->where('AMBIENTE_id_ambiente', $id)
                             ->get();
    
            // Obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                            ->where('sede.nombre', 'Sede Central')
                            ->select('edificio.nombre_edi')
                            ->get();
    
            return view('coordinator.clinicas.mobiliariosAmbiente', compact('ambiente', 'mobiliarios', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos para la pagina mobiliario del ambinete ');
        }
    }

    /////////////////////////////////////////////////////////////////////////
    ////////METODO PARA LA PAGINA DE TODOS LOS AMBIENTES DE LA CLINICA///////
    public function index(Request $request)
    {
        try{
            $search = $request->input('search');
            $pisoSeleccionado = $request->input('piso');

            // Obtener los pisos del edificio 'Clinica Odontologia'
            $pisos = DB::table('piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->where('edificio.nombre_edi', 'Clinica Odontologia')//este nombre debe de ser igual en la tabla edificios
                ->select('piso.id_piso', 'piso.numero_piso')
                ->get();

            // Obtener los ambientes del piso con búsqueda
            $query = DB::table('sede')
                ->join('edificio', 'sede.id_sede', '=', 'edificio.SEDE_id_sede')
                ->join('piso', 'edificio.id_edificio', '=', 'piso.Edificio_id_edificio')
                ->join('ambiente', 'piso.id_piso', '=', 'ambiente.PISO_id_piso')
                ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                    ->where('edificio.nombre_edi', 'Clinica Odontologia')//este nombre debe de ser igual en la tabla edificios
                    ->select('piso.numero_piso', 'ambiente.id_ambiente', 'tipo_ambiente.nombre_amb as tipo_ambiente', 'ambiente.nombre as nombre_ambiente', 'ambiente.descripcion_amb as descripcion_ambiente');

            // Filtrar por búsqueda y piso seleccionado
            //buscar por nombre
            if ($search) {
                $query->where('ambiente.nombre', 'LIKE', "%$search%");
            }

            //buscar por piso
            if ($pisoSeleccionado) {
                $query->where('piso.numero_piso', $pisoSeleccionado);
            }

            $ambientes = $query->get();

            //no encotrado reesultado
            if ($ambientes->isEmpty()) {
                return redirect()->route('coordinator.clinica.ambientes')->with('info', 'No se encontraron resultados.');
            }

            // Obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.clinicas.ambientes', compact('pisos', 'ambientes', 'edificios', 'pisoSeleccionado'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Problemas al obtener los datos para la pagina ambientes: ');
        }
    }
}
