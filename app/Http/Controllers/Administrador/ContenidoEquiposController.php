<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Ambiente;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Exception;

class ContenidoEquiposController extends Controller
{
    //
    public function index($token)
    {
        try{
            // Desencriptar el ID del ambiente
            try {
                $id_ambiente = Crypt::decryptString($token);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'ID de ambiente no válido.']);
            }

            // Obtener los equipos filtrados por id_ambiente
            $equipos = Equipo::where('AMBIENTE_id_ambiente', $id_ambiente)->get();

            // Obtener la información del ambiente
            $ambiente = Ambiente::findOrFail($id_ambiente);

            return view('administrator.contenidoEquipos', compact('equipos', 'ambiente'));

        }catch (\Exception $e) {
            return redirect()->route('ambiente.index')->with('errordata', 'Error al obtener los datos para los equipos del ambiente: ');
        }
    }
}
