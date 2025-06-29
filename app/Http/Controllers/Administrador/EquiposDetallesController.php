<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Equipo;
use App\Models\Componente;
use App\Models\Accesorio;
use Exception;//manejo errores clase base en PHP para todos los errores y excepciones.
use Illuminate\Support\Facades\Log;//es una fachada proporcionada por Laravel para registrar mensajes y errores que ocurren en la aplicación.


class EquiposDetallesController extends Controller
{
    //
    public function show($token)
    {
        try{
            // Desencriptar el código del equipo
            try {
                $cod_equipo = Crypt::decryptString($token);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Código de equipo no válido.']);
            }

            // Obtener el equipo con su foto
            // Obtener el equipo con sus componentes y accesorios
            $equipo = Equipo::with(['foto', 'componentes', 'accesorios.foto'])
                ->where('cod_equipo', $cod_equipo)
                ->firstOrFail();

            // Obtener los componentes del equipo
            $componentes = Componente::where('EQUIPO_cod_equipo', $cod_equipo)->get();

            // Obtener los accesorios del equipo a través de la relación many-to-many
            $accesorios = $equipo->accesorios;
            // Obtener el equipo con su foto

            return view('administrator.equipoAdminDetalles', compact('equipo', 'componentes', 'accesorios'));

        }catch(Exception $e) {
            Log::error('Error al obtener los detalles del equipo: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'No se pudo obtener los detalles del equipo.']);
        }
    }

    public function mostrarFotoAccesorio(Request $request)
    {
        $request->validate([
            'cod_accesorio' => 'required|string'
        ]);
    
        try{
            $cod_accesorio = $request->input('cod_accesorio');
            $accesorio = Accesorio::with('foto')->where('cod_accesorio', $cod_accesorio)->firstOrFail();
            $ruta_foto = $accesorio->foto ? asset($accesorio->foto->ruta_foto) : '';
    
            return view('administrator.fotoAccesorio', compact('ruta_foto'));

        }catch(Exception $e) {
            Log::error('Error al obtener la foto del aAccesorio: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'No se pudo obtener los datos de la ruta de la Foto.']);
        }

    }
}
