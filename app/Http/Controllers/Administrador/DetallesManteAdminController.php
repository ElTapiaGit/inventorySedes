<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class DetallesManteAdminController extends Controller
{
    //
    public function detallesManteAdmin($id)
    {
        try{
            // Desencriptar el ID
            $id = Crypt::decrypt($id);

            // Obtener los detalles del mantenimiento
            $mantenimiento = DB::table('inicio_mantenimiento')
            ->leftJoin('final_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'final_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
            ->leftJoin('detalles_mantenimiento', 'inicio_mantenimiento.id_mantenimiento_ini', '=', 'detalles_mantenimiento.INICIO_MANTENIMIENTO_id_mantenimiento_ini')
            ->leftJoin('equipo', 'detalles_mantenimiento.cod_articulo', '=', 'equipo.cod_equipo')
            ->leftJoin('mobiliario', 'detalles_mantenimiento.cod_articulo', '=', 'mobiliario.cod_mueble')
            ->leftJoin('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
            ->leftJoin('material', 'detalles_mantenimiento.cod_articulo', '=', 'material.cod_mate')
            ->leftJoin('ambiente', function($join) {
                $join->on('equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                    ->orOn('mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                    ->orOn('material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente');
            })
            ->leftJoin('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->leftJoin('edificio', 'piso.EDIFICIO_id_edificio', '=', 'edificio.id_edificio')
            ->leftJoin('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->leftJoin('tecnico', 'inicio_mantenimiento.TECNICO_id_tecnico', '=', 'tecnico.id_tecnico')
            ->leftJoin('personal', 'inicio_mantenimiento.PERSONAL_id_personal', '=', 'personal.id_personal')
            ->leftJoin('foto', function($join) {
                $join->on('equipo.FOTO_id_foto', '=', 'foto.id_foto')
                    ->orOn('mobiliario.FOTO_id_foto', '=', 'foto.id_foto')
                    ->orOn('material.FOTO_id_foto', '=', 'foto.id_foto');
            })
            ->select(
                'inicio_mantenimiento.informe_inicial',
                'inicio_mantenimiento.fch_inicio',
                'final_mantenimiento.informe_final',
                'final_mantenimiento.fch_final',
                'equipo.cod_equipo as cod_equipo',
                'equipo.nombre_equi as nombre_equipo',
                'mobiliario.cod_mueble as cod_mueble',
                'mobiliario.descripticion_mueb as nombre_mobiliario',
                'material.cod_mate as cod_material',
                'material.descripcion_mate as nombre_material',
                'tipo_mobiliario.tipo_mueble',
                'tecnico.nombre as nombre_tecnico',
                'tecnico.celular as celular_tecnico',
                'tecnico.direccion as direccion_tecnico',
                'personal.nombre as nombre_personal',
                'personal.celular as celular_personal',
                'foto.ruta_foto'
            )
            ->where('inicio_mantenimiento.id_mantenimiento_ini', $id)
            ->first();

            if (!$mantenimiento) {
                abort(404);
            }

            // Pasar los datos a la vista
            return view('administrator.detallesManteAdmin', [
                'mantenimiento' => $mantenimiento
            ]);
        }catch (\Exception $e) {
            return redirect()->route('mantenimientosAdmin.index')->with('error', 'Error al obtener los datos del detalle de mantenimiento: ');
        }
    }
}
