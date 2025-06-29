<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class BuscarLaboController extends Controller
{
    public function index()
    {
        try{
            // Consulta SQL para obtener los edificios de la sede central
            $edificios = DB::table('edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('sede.nombre', 'Sede Central')//campo de la tabla sede debe estar con este nombre
            ->select('edificio.id_edificio', 'edificio.nombre_edi')
            ->get();

            // Consulta SQL para obtener los laboratorios
            $laboratorios = DB::table('ambiente')
                ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('tipo_ambiente.nombre_amb', 'Laboratorio Odontologia')
                ->where('sede.id_sede', 1)//campo de la sede debe de ser id=1
                ->where('edificio.nombre_edi', 'Edificio Central')
                ->select('ambiente.id_ambiente', 'ambiente.nombre')
                ->get();
                

            return view('coordinator.laboratorios', compact('edificios', 'laboratorios'));
        }catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los para los laboratorios del edificio central: ');
        }
    }

    //metodo para filtrar los ambientes que son laboratorios de odontoligia
    public function buscar(Request $request)
    {
        try{
            $nombre = $request->input('Laboratorio');//lo que se optenga al escribir en el input

            // Consulta para obtener el ID del laboratorio
            $laboratorios = DB::table('ambiente')
            ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('tipo_ambiente.nombre_amb', 'Laboratorio Odontologia')
            ->where('sede.id_sede', 1)
            ->where('ambiente.nombre', $nombre)
            ->select('ambiente.id_ambiente')
            ->first();
        
            if (!$laboratorios) {
                return redirect()->route('laboratorios.index')->with('errorbuscar', 'El Laboratorio que busca no se encontrado en la Base de Datos.');
            }

            if ($laboratorios) {
                // Encriptar el ID del piso
                $encryptedId = Crypt::encrypt($laboratorios->id_ambiente);
                return redirect()->route('ambiente.show', ['id' => $encryptedId]);
            }

            return redirect()->back()->with('error', 'Laboratorio no encontrado.');
        }catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los para los datos de los ambientes laboratorios odontologicos: ');
        }
    }
    
    ///////////////////////////////////////////////////////////////////////
    //metodo para las paginas de clinica odontologia
    public function buscarRapido(){
        try{
            // Consulta SQL para obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.id_sede', 1)
                ->select('edificio.id_edificio', 'edificio.nombre_edi')
                ->get();

            return view('coordinator.clinicas.niveles', compact('edificios'));
        }catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los para los datos de los ambientes laboratorios para la clinica: ');
        }
    }

    /////////////////////////////////////////////////////////
    //////////// PARA PAGINA DE BUSQUEDAS RAPIDAS DE CLINICA ////////
    public function buscarEquipo(Request $request)
    {
        $codigo = $request->input('codigo');

        try {
            // Verificar si el código existe en la tabla equipo
            $equipo = DB::table('equipo')->where('cod_equipo', $codigo)->first();

            if ($equipo) {
                // Encriptar el código y redirigir a la página de detalles
                $encryptedCodigo = Crypt::encrypt($codigo);
                return redirect()->route('clinica.equipo.detalles', ['token' => $encryptedCodigo]);
            }

            // Si no se encuentra el código, redirigir de vuelta con un mensaje de error
            return redirect()->back()->with('errorbuscar', 'El código ingresado es inválido o no existe en la base de datos.');
        } catch (\Exception $e) {
            return redirect()->back()->with('errorbuscar', 'Hubo un error al procesar la búsqueda: ' );//para saber el error agregar al final: . $e->getMessage()
        }
    }

    public function buscarMobiliario(Request $request)
    {
        $codigo = $request->input('codigo');

        try {
            // Verificar si el código existe en la tabla equipo
            $equipo = DB::table('mobiliario')->where('cod_mueble', $codigo)->first();

            if ($equipo) {
                // Encriptar el código y redirigir a la página de detalles
                $encryptedCodigo = Crypt::encrypt($codigo);
                return redirect()->route('clinica.mobiliario.detalles', ['token' => $encryptedCodigo]);
            }

            // Si no se encuentra el código, redirigir de vuelta con un mensaje de error
            return redirect()->back()->with('errorbuscar', 'El código ingresado es inválido o no existe en la base de datos.');
        } catch (\Exception $e) {
            return redirect()->back()->with('errorbuscar', 'Hubo un error al procesar la búsqueda: VERIFICAR CONFIGURACION DE CAMPOS EN LA BD' );//. $e->getMessage()
        }
    }

    public function buscarMaterial(Request $request)
    {
        $codigo = $request->input('codigo');

        try {
            // Verificar si el código existe en la tabla equipo
            $equipo = DB::table('material')->where('cod_mate', $codigo)->first();

            if ($equipo) {
                // Encriptar el código y redirigir a la página de detalles
                $encryptedCodigo = Crypt::encrypt($codigo);
                return redirect()->route('clinica.material.detalles', ['token' => $encryptedCodigo]);
            }

            // Si no se encuentra el código, redirigir de vuelta con un mensaje de error
            return redirect()->back()->with('errorbuscar', 'El código ingresado es inválido o no existe en la base de datos.');
        } catch (\Exception $e) {
            return redirect()->back()->with('errorbuscar', 'Hubo un error al procesar la búsqueda: VERIFICAR CONFIGURACION DE CAMPOS EN LA BD ' );//. $e->getMessage()
        }
    }

    public function buscarAccesorio(Request $request)
    {
        $codigo = $request->input('codigo');

        try {
            // Verificar si el código existe en la tabla equipo
            $equipo = DB::table('accesorio')->where('cod_accesorio', $codigo)->first();

            if ($equipo) {
                // Encriptar el código y redirigir a la página de detalles
                $encryptedCodigo = Crypt::encrypt($codigo);
                return redirect()->route('clinica.accesorios.show', [$codigo]);
            }

            // Si no se encuentra el código, redirigir de vuelta con un mensaje de error
            return redirect()->back()->with('errorbuscar', 'El código ingresado es inválido o no existe en la base de datos.');
        } catch (\Exception $e) {
            return redirect()->back()->with('errorbuscar', 'Hubo un error al procesar la búsqueda: VERIFICAR CAMPOS EN LA BD' );//. $e->getMessage()
        }
    }
}
