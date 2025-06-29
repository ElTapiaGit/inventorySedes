<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class AmbienteClinicaController extends Controller
{
    //Metodo para la pagina ambienteclinicas
    public function show($id)
    {
        try{
            // Desencriptar el ID del piso
            $id = Crypt::decrypt($id);

            //consulta para el nombre del piso
            $pisos = DB::table('piso')
                ->where('id_piso', $id)
                        ->first();

            // Consulta SQL para obtener los edificios de la sede central
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.id_sede', 1)//campo de la sede siempre empezara con id=1
                ->select('edificio.id_edificio', 'edificio.nombre_edi')
                ->get(); 

            // Ejecuta la consulta para obtener los ambientes
            $ambientes = DB::table('tipo_ambiente')
                ->join('ambiente', 'tipo_ambiente.id_tipoambiente', '=', 'ambiente.TIPO_AMBIENTE_id_ambiente')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->where('piso.id_piso', $id)
                ->select('ambiente.id_ambiente', 'tipo_ambiente.nombre_amb as tipo_ambiente', 'ambiente.nombre as nombre_ambiente', 'ambiente.descripcion_amb as descripcion_ambiente')
                ->get();

            // Pasa los datos a la vista
            return view('coordinator.clinicas.ambienteClinica', ['ambientes' => $ambientes, 'edificios' => $edificios, 'pisos' =>$pisos]);
            
        }catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al Obtener los datos para la pagina Laboratorios: ');
        }
    }
}
