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

            // Obtener los detalles del ambiente junto con su jerarquia de ubicacion
            $ambiente = Ambiente::with(['tipoAmbiente', 'piso.edificios.sede'])->findOrFail($id);
    
            // Obtener los equipos, materiales y mobiliarios del ambiente
            $equipos = Equipo::where('AMBIENTE_id_ambiente', $id)->get();
            $materiales = Material::where('AMBIENTE_id_ambiente', $id)->get();
            $mobiliarios = Mobiliario::with('tipoMobiliario')->where('AMBIENTE_id_ambiente', $id)->get();
    
            // Obtener dinamicamente los edificios de la sede a la que pertenece el ambiente (o todos en contexto global)
            $sedeId = $ambiente->piso?->edificios?->SEDE_id_sede;
            $edificios = $sedeId 
                ? Edificio::where('SEDE_id_sede', $sedeId)->orderBy('nombre_edi')->get()
                : Edificio::orderBy('nombre_edi')->get();
    
            return view('administrator.ContenidAmbiente', compact('ambiente', 'equipos', 'materiales', 'mobiliarios', 'edificios'));

        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para la pagina contenido de ambiente: ');
        }
    }

}
