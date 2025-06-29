<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
/////////////////////////////////////////////////////////////////////////
/////////// CONTROLADOR PARA LAS PAGINAS DEL EDIFICIO CENTRAL Y LA CLINICA
class DetallesMaterialController extends Controller
{
    //Para las paginas del edificio central
    public function show($token){
        try{
            try {
                $materialId = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                return redirect()->back()->withErrors('Invalid encrypted ID.');
            }
    
            $material = DB::table('material')
                ->join('foto', 'material.FOTO_id_foto', '=', 'foto.id_foto')
                ->join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->where('material.cod_mate', $materialId)
                ->select(
                    'foto.ruta_foto',
                    'material.cod_mate',
                    'ambiente.nombre',
                    'material.tipo_mate',
                    'material.descripcion_mate',
                    'material.estado_mate',
                    'material.observacion_mate',
                    'material.fch_registrada',
                )
                ->first();
    
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();
            
            return view('coordinator.detallesMaterial', [
               'material' => $material,
               'edificios' => $edificios
            ]);
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del material: ');
        }
    }

    public function detalleMaterial($token){
        try{
            try {
                $materialId = Crypt::decrypt($token);
            } catch (DecryptException $e) {
                return redirect()->back()->withErrors('Invalid encrypted ID.');
            }
    
            $material = DB::table('material')
                ->join('foto', 'material.FOTO_id_foto', '=', 'foto.id_foto')
                ->where('material.cod_mate', $materialId)
                ->select(
                    'foto.ruta_foto',
                    'material.cod_mate',
                    'material.tipo_mate',
                    'material.descripcion_mate',
                    'material.estado_mate',
                    'material.observacion_mate',
                    'material.fch_registrada',
                )
                ->first();
    
                    
    
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();
            
            return view('coordinator.clinicas.detallesMateriales', [
               'material' => $material,
               'edificios' => $edificios
            ]);
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los detalles del material para la clinica: ');
        }
    }
}
