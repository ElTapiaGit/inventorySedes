<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteClinicaController extends Controller
{
    //
    public function index()
    {
        // Obtener los edificios para el menú desplegable
        $edificios = DB::table('edificio')
        ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
        ->where('sede.nombre', 'Sede Central')
        ->select('edificio.nombre_edi')
        ->get(); 

        return view('coordinator.reporte', compact('edificios'));
    }

    public function indexClinica()
    {
        // Obtener los edificios para el menú desplegable
        $edificios = DB::table('edificio')
        ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
        ->where('sede.nombre', 'Sede Central')
        ->select('edificio.nombre_edi')
        ->get();

        return view('coordinator.clinicas.reporteClinica', compact('edificios'));
    }
}
