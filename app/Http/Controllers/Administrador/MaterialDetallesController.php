<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Models\Material;
use Exception;//manejo errores clase base en PHP para todos los errores y excepciones.
use Illuminate\Support\Facades\Log;//es una fachada proporcionada por Laravel para registrar mensajes y errores que ocurren en la aplicación.

class MaterialDetallesController extends Controller
{
    //
    public function detallesMaterialAdmin($token)
    {
        try{
            // Desencriptar el código del material
            try {
                $cod_mate = Crypt::decryptString($token);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Código de material no válido.']);
            }

            $material = Material::with('foto')->where('cod_mate', $cod_mate)->firstOrFail();
            
            return view('administrator.materialAdminDetalles', compact('material'));

        }catch(Exception $e) {
            Log::error('Error al obtener los detalles del material: ');

            return redirect()->back()->withErrors(['error' => 'No se pudo obtener los detalles del material.']);
        }
        
    }
}
