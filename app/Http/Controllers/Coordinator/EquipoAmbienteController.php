<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EquipoAmbienteController extends Controller
{
    //
    public function show($token)
    {
        try{
            try {
                $id_ambiente = Crypt::decryptString($token);
            } catch (DecryptException $e) {
                abort(404, 'Ambiente no encontrado');
            }
            // Obtener los detalles del ambiente
            // Consulta para obtener el ambiente específico por el id_ambiente
            $ambiente = DB::table('ambiente')->where('id_ambiente', $id_ambiente)->first();
    
            if (!$ambiente) {
                abort(404, 'Ambiente no encontrado');
            }
    
            // Obtener los equipos del ambiente
            $equipos = DB::table('equipo')
                        ->where('AMBIENTE_id_ambiente', $id_ambiente)
                        ->get();
    
            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                          ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                          ->where('sede.nombre', 'Sede Central')
                          ->select('edificio.nombre_edi')
                          ->get();
    
            return view('coordinator.ambienteEquipo', compact('ambiente', 'equipos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los equipos del laboratorio: ');
        }
    }
}
