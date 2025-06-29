<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Descarte;
use App\Models\Personal;

class DescartesController extends Controller
{
    //
    public function index(Request $request)
    {
        try{
            // Obtener filtros de la solicitud
            $codigo = $request->input('codigo');
            $nombre = $request->input('nombre');
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            // Construir la consulta
            $descartes = Descarte::with('personal')
                ->when($codigo, function ($query, $codigo) {
                    return $query->where('codigo', 'like', '%' . $codigo . '%');
                })
                ->when($nombre, function ($query, $nombre) {
                    return $query->where('nombre', 'like', '%' . $nombre . '%');
                })
                ->when($fechaInicio, function ($query, $fechaInicio) {
                    return $query->where('fch_descarte', '>=', $fechaInicio);
                })
                ->when($fechaFin, function ($query, $fechaFin) {
                    return $query->where('fch_descarte', '<=', $fechaFin);
                })
                ->paginate(10); // Paginación de 10 registros

            return view('administrator.descartes', compact('descartes', 'codigo', 'nombre', 'fechaInicio', 'fechaFin'));
        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los dato para la pagina descartes: ');
        }  
    }
}
