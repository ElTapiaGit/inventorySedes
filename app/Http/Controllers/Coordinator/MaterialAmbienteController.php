<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

class MaterialAmbienteController extends Controller
{
    //
    public function mostrarMateriales($token)
    {
       try{
            try {
                $id_ambiente = Crypt::decryptString($token);
            } catch (DecryptException $e) {
                abort(404, 'Ambiente no encontrado');
            }

            $ambiente = DB::table('ambiente')
                            ->where('id_ambiente', $id_ambiente)
                            ->first();

            $materiales = DB::table('material')
                            ->where('AMBIENTE_id_ambiente', $id_ambiente)
                            ->get();

            // Obtener los edificios para el menú desplegable
            $edificios = DB::table('edificio')
                        ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                        ->where('sede.nombre', 'Sede Central')
                        ->select('edificio.nombre_edi')
                        ->get();

            return view('coordinator.ambienteMaterial', ['ambiente' => $ambiente, 'materiales' => $materiales, 'edificios' =>$edificios]);
        }catch(\Exception $e) {
        return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los emateriales del laboratorio: ');
    }
    }
}
