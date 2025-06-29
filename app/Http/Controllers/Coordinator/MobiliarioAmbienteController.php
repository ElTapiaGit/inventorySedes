<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
class MobiliarioAmbienteController extends Controller
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
            // Obtener información del ambiente
            $ambiente = DB::table('ambiente')
                            ->where('id_ambiente', $id_ambiente)
                            ->first();
    
            // Obtener mobiliario del ambiente
            $mobiliarios = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->where('AMBIENTE_id_ambiente', $id_ambiente)
                ->select('mobiliario.cod_mueble', 'tipo_mobiliario.tipo_mueble', 'mobiliario.estado_mueb', 'mobiliario.observacion', 'mobiliario.vida_util', 'mobiliario.fch_registro')
                ->get();
    
            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                          ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                          ->where('sede.nombre', 'Sede Central')
                          ->select('edificio.nombre_edi')
                          ->get();
    
            return view('coordinator.ambienteMobiliario', [
                'ambiente' => $ambiente,
                'mobiliarios' => $mobiliarios,
                'edificios' => $edificios
            ]);
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los mobiliarios del laboratorio: ');
        }
    }
}
