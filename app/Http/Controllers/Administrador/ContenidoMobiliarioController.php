<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobiliario;
use App\Models\Ambiente;
use Illuminate\Support\Facades\Crypt;

class ContenidoMobiliarioController extends Controller
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

            // Obtener los mobiliarios filtrados por id_ambiente
            $mobiliarios = Mobiliario::with('tipoMobiliario')->where('AMBIENTE_id_ambiente', $id_ambiente)->get();

            // Obtener la información del ambiente
            $ambiente = Ambiente::findOrFail($id_ambiente);

            return view('administrator.contenidoMobiliario', compact('mobiliarios', 'ambiente'));
        }catch (\Exception $e) {
            return redirect()->route('ambiente.index')->with('error', 'Error al obtener los datos para los mobiliarios del ambiente: ');
        }
    }
}
