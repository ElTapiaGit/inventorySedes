<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class DetallesLaboratorioController extends Controller
{
    //
    public function show($id)
    {
        try{
            // Desencriptar el ID del laboratorio
            $id = Crypt::decrypt($id);

            // Obtener los detalles del ambiente
            $ambiente = DB::table('ambiente')->where('id_ambiente', $id)->first();

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
            return view('coordinator.ambiente', compact('ambiente', 'equipos', 'materiales', 'mobiliarios', 'edificios'));
        }catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del laboratorio: ');
        }
    }
    
}
