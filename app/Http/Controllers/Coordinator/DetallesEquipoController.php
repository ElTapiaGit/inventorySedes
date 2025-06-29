<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
////////////////////////////////////////////////////////////////////////////////
///////// CONTROLADOR PARA LAS PAGINAS DE EDIFICIO CENTRAL Y LA CLINICA ODONTOLOGICA
class DetallesEquipoController extends Controller
{
    //Para las paginas del edificio central
    public function index($encryptedId)
    {
        try{
            try {
                $id_ambiente = Crypt::decrypt($encryptedId);
            } catch (DecryptException $e) {
                abort(404);
            }
    
            $equipo = DB::table('equipo')
            ->join('foto', 'equipo.FOTO_id_foto', '=', 'foto.id_foto')
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->where('equipo.cod_equipo', $id_ambiente)
            ->select(
                'foto.ruta_foto',
                'equipo.cod_equipo',
                'equipo.nombre_equi',
                'equipo.marca',
                'equipo.modelo',
                'equipo.descripcion_equi',
                'equipo.empotrado',
                'equipo.estado_equi',
                'equipo.observaciones_equi',
                'equipo.vida_util',
                'equipo.fch_registro',
                'ambiente.nombre'
            )
            ->first();
    
            $componentes = DB::table('componente')
                ->join('equipo', 'componente.EQUIPO_cod_equipo', '=', 'equipo.cod_equipo')
                ->where('componente.EQUIPO_cod_equipo', $id_ambiente)
                ->select('componente.nombre_compo', 'componente.descripcion_compo', 'componente.estado_compo')
                ->get();
    
            $accesorios = DB::table('accesorio')
            ->join('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
            ->join('equipo', 'equipo.cod_equipo', '=', 'equipo_has_accesorio.EQUIPO_cod_equipo')
            ->join('foto', 'accesorio.FOTO_id_foto', '=', 'foto.id_foto')
            ->where('equipo.cod_equipo', $id_ambiente)
            ->select(
                'accesorio.cod_accesorio',
                'accesorio.nombre_acce',
                'accesorio.descripcion_acce',
                'accesorio.observacion_ace',
                'accesorio.estado_acce',
                'accesorio.vida_util',
                'accesorio.ubicacion',
                'accesorio.fch_registro_acce',
                'accesorio.FOTO_id_foto'
            )
            ->get();
    
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();
    
            return view('coordinator.detallesEquipo', [
                'equipo' => $equipo,
                'componentes' => $componentes,
                'accesorios' => $accesorios,
                'edificios' => $edificios,
            ]);
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del equipo: ');
        }
    }
    public function fotoaccesorioEdiCen(Request $request){
        try{
            $id_foto = $request->input('cod_accesorio');
            $fotos = DB::table('foto')
                ->where('id_foto', $id_foto)
                ->select('foto.ruta_foto')
                ->first(); // Usa first() en lugar de get() si esperas un solo resultado

                $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')//este campos debe de ser igual en la base de datos
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.fotoAccess', compact('fotos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los detalles del equipo: ');
        }
    }


    //Para las paginas de la clinica odontologica
    public function detalleEquipo($encryptedId)
    {
        try{
            try {
                $id_ambiente = Crypt::decrypt($encryptedId);
            } catch (DecryptException $e) {
                abort(404);
            }
    
            $equipo = DB::table('equipo')
                ->join('foto', 'equipo.FOTO_id_foto', '=', 'foto.id_foto')
                ->where('equipo.cod_equipo', $id_ambiente)
                ->select(
                    'foto.ruta_foto',
                    'equipo.cod_equipo',
                    'equipo.nombre_equi',
                    'equipo.marca',
                    'equipo.modelo',
                    'equipo.descripcion_equi',
                    'equipo.empotrado',
                    'equipo.estado_equi',
                    'equipo.observaciones_equi',
                    'equipo.vida_util',
                    'equipo.fch_registro'
                )
                ->first();
    
            $componentes = DB::table('componente')
                ->join('equipo', 'componente.EQUIPO_cod_equipo', '=', 'equipo.cod_equipo')
                ->where('componente.EQUIPO_cod_equipo', $id_ambiente)
                ->select('componente.nombre_compo', 'componente.descripcion_compo', 'componente.estado_compo')
                ->get();
    
            $accesorios = DB::table('accesorio')
                ->join('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
                ->join('equipo', 'equipo.cod_equipo', '=', 'equipo_has_accesorio.EQUIPO_cod_equipo')
                ->where('equipo.cod_equipo', $id_ambiente)
                ->select(
                    'accesorio.cod_accesorio',
                    'accesorio.nombre_acce',
                    'accesorio.descripcion_acce',
                    'accesorio.observacion_ace',
                    'accesorio.estado_acce',
                    'accesorio.vida_util',
                    'accesorio.ubicacion',
                    'accesorio.fch_registro_acce',
                    'accesorio.FOTO_id_foto'
                )
                ->get();
    
            $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();
    
            return view('coordinator.clinicas.detallesEquipos', [
                'equipo' => $equipo,
                'componentes' => $componentes,
                'accesorios' => $accesorios,
                'edificios' => $edificios,
            ]);
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los detalles del equipo para la clinica: ');
        }
    }

    public function fotoaccesorio(Request $request){
        try{
            $id_foto = $request->input('cod_accesorio');
            $fotos = DB::table('foto')
                ->where('id_foto', $id_foto)
                ->select('foto.ruta_foto')
                ->first(); // Usa first() en lugar de get() si esperas un solo resultado

                $edificios = DB::table('edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->where('sede.nombre', 'Sede Central')
                ->select('edificio.nombre_edi')
                ->get();

            return view('coordinator.fotosAccesorio', compact('fotos', 'edificios'));
        }catch(\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener la foto ');
        }
    }
}
