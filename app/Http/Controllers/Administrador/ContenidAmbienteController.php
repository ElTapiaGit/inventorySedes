<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Edificio;
use App\Models\Ambiente;
use App\Models\TipoAmbiente;
use App\Models\Equipo;
use App\Models\Material;
use App\Models\Mobiliario;
use Illuminate\Support\Facades\Crypt;

class ContenidAmbienteController extends Controller
{
    //
    public function show($id_ambiente)
    {
        try{
            // Desencriptar el ID del ambiente
            try {
                $id = Crypt::decryptString($id_ambiente);
            } catch (\Exception $e) {
                return redirect()->route('admin.index')->withErrors('ID de ambiente no válido.');
            }
            
            //$id_ambiente = Crypt::decryptString($request->input('id_ambiente'));

            // Obtener los detalles del ambiente junto con el tipo de ambiente
            $ambiente = Ambiente::with('tipoAmbiente')->findOrFail($id);
    
            // Obtener los equipos, materiales y mobiliarios del ambiente
            $equipos = Equipo::where('AMBIENTE_id_ambiente', $id)->get();
            $materiales = Material::where('AMBIENTE_id_ambiente', $id)->get();
            $mobiliarios = Mobiliario::with('tipoMobiliario')->where('AMBIENTE_id_ambiente', $id)->get();
    
            // Obtener los edificios de la sede central
            $edificios = Edificio::where('SEDE_id_sede', 1)->get();
    
            return view('administrator.contenidAmbiente', compact('ambiente', 'equipos', 'materiales', 'mobiliarios', 'edificios'));

        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para la pagina contenido de ambiente: ');
        }
    }

}
