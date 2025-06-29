<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Ambiente;
use Illuminate\Support\Facades\Crypt;

class ContenidoMaterialController extends Controller
{
    //
    public function show($token)
    {
        try{
            // Desencriptar el ID del ambiente
            try {
                $id_ambiente = Crypt::decryptString($token);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'ID de ambiente no válido.']);
            }

            $materiales = Material::where('AMBIENTE_id_ambiente', $id_ambiente)->get();
            $ambiente = Ambiente::findOrFail($id_ambiente);

            return view('administrator.contenidoMaterial', compact('materiales', 'ambiente'));
        } catch (\Exception $e) {
            return redirect()->route('ambientes.index')->with('errordata', 'Error al obtener los datos de los materiales del ambiente: ');
        }   
    }
}
