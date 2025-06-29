<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\Mobiliario;
use App\Models\Material;

class MantenimientosAdminController extends Controller
{
    //
    public function index()
    {
        try{
            // Obtener los equipos para mantenimiento
            $equipos = Equipo::select('equipo.*', 'ambiente.nombre', 'edificio.nombre_edi', 'sede.nombre as nombre_sede')
            ->join('ambiente', 'equipo.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('equipo.estado_equi', 'para reparar')
            ->orWhere('equipo.estado_equi', 'para mantenimiento')
            ->paginate(10);

            // Obtener los mobiliarios para mantenimiento
            $mobiliarios = Mobiliario::select('mobiliario.*', 'tipo_mobiliario.tipo_mueble', 'ambiente.nombre', 'edificio.nombre_edi', 'sede.nombre as nombre_sede')
            ->join('tipo_mobiliario', 'mobiliario.TIPO_MOBILIARIO_id_tipo_mueb', '=', 'tipo_mobiliario.id_tipo_mueb')
            ->join('ambiente', 'mobiliario.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('mobiliario.estado_mueb', 'para reparar')
            ->orWhere('mobiliario.estado_mueb', 'para mantenimiento')
            ->paginate(10);

            // Obtener los materiales para mantenimiento
            $materiales = Material::select('material.*', 'ambiente.nombre', 'edificio.nombre_edi', 'sede.nombre as nombre_sede')
            ->join('ambiente', 'material.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->where('material.estado_mate', 'para reparar')
            ->orWhere('material.estado_mate', 'para mantenimiento')
            ->paginate(10);

            return view('administrator.mantenimiento', compact('equipos', 'mobiliarios', 'materiales'));
            
        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos de los AArticulos para Mantenimiento: ');
        }
    }
}
