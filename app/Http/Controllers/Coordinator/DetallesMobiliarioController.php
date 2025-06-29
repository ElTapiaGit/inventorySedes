<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
/////////////////////////////////////////////////////////////////////////
/////////// CONTROLADOR PARA LAS PAGINAS DEL EDIFICIO CENTRAL Y LA CLINICA
class DetallesMobiliarioController extends Controller
{
    //Para las paginas del edificio central
    public function show($token){
        try{
            try {
                $mobiliarioId = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                return redirect()->back()->withErrors('Invalid encrypted ID.');
            }
    
            $mobiliario = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->join('foto', 'mobiliario.FOTO_id_foto', '=', 'foto.id_foto')
                ->join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->where('mobiliario.cod_mueble', $mobiliarioId)
                ->select(
                    'mobiliario.cod_mueble',
                    'ambiente.nombre',
                    'tipo_mobiliario.tipo_mueble',
                    'mobiliario.descripticion_mueb',
                    'mobiliario.observacion',
                    'mobiliario.estado_mueb',
                    'mobiliario.vida_util',
                    'mobiliario.fch_registro',
                    'foto.ruta_foto',
                )
                ->first();
                if (!$mobiliario) {
                    return redirect()->back()->withErrors('Mobiliario no encontrado.');
                }
    
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();
    
            return view('coordinator.detallesMobiliario', [
                'mobiliario' => $mobiliario,
                'edificios' => $edificios
            ]);
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del mobiliario: ');
        }
    }


    //Para las paginas de la clinica odontologica
    public function detalleMobiliario($token){
        try{
            try {
                $mobiliarioId = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                return redirect()->back()->withErrors('Invalid encrypted ID.');
            }
    
            $mobiliario = DB::table('mobiliario')
                ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
                ->join('foto', 'mobiliario.FOTO_id_foto', '=', 'foto.id_foto')
                ->where('mobiliario.cod_mueble', $mobiliarioId)
                ->select(
                    'mobiliario.cod_mueble',
                    'tipo_mobiliario.tipo_mueble',
                    'mobiliario.descripticion_mueb',
                    'mobiliario.observacion',
                    'mobiliario.estado_mueb',
                    'mobiliario.vida_util',
                    'mobiliario.fch_registro',
                    'foto.ruta_foto',
                )
                ->first();
                if (!$mobiliario) {
                    return redirect()->back()->withErrors('Mobiliario no encontrado.');
                }
    
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();
    
            return view('coordinator.clinicas.detallesMobiliarios', [
                'mobiliario' => $mobiliario,
                'edificios' => $edificios
            ]);
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los detalles del mobiliario para la clinica: ');
        }
    }
}
