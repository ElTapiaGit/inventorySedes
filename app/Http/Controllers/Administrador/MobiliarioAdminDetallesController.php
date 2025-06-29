<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobiliario;
use Exception;//manejo errores
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class MobiliarioAdminDetallesController extends Controller
{
    //
    public function detallesMobiliarioAdmin($cod_mueble)
    {


        try {
            // Desencriptar el código del mueble
            try {
                $cod_mueble = Crypt::decryptString($cod_mueble);
            } catch (\Exception $e) {
                return redirect()->route('admin.index')->withErrors('ID de ambiente no válido.');
            }

            $mobiliario = Mobiliario::with('foto') // Asegúrate de que la relación esté definida en el modelo
                ->where('cod_mueble', $cod_mueble)
                ->firstOrFail();
            
            return view('administrator.mobiliarioAdminDetalles', compact('mobiliario'));

        } catch (Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para los detalles de material: ');
        }
    }
}
