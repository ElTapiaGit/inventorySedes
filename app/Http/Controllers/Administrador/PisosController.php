<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Edificio;
use App\Models\Piso;

class PisosController extends Controller
{
    //
    public function index(Request $request)
    {
        try{
            $edificioId = $request->input('edificio_id');
            $edificios = Edificio::all();
            $edificioIdDecrypted = null;

            // Desencriptar el id del edificio si existe
            if ($edificioId) {
                try {
                    $edificioIdDecrypted = Crypt::decrypt($edificioId);
                } catch (\Exception $e) {
                    // Manejar error de desencriptación
                    return redirect()->route('pisos.index')->withErrors('Identificador de edificio no válido.');
                }
            }

            // Consultar los pisos filtrados por el edificio desencriptado
            $pisos = $edificioIdDecrypted 
                ? Piso::where('Edificio_id_edificio', $edificioIdDecrypted)->get()
                : Piso::all();

            return view('administrator.pisos', compact('edificios', 'pisos'));
        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los dato para la pagina pisos: ');
        }  
    }

    public function edit(Request $request)
    {
        try{
            $request->validate([
                'piso_id' => 'required|exists:piso,id_piso',
                'numero_piso' => 'required|string|max:50',
            ]);
    
            $piso = Piso::findOrFail($request->input('piso_id'));
            $piso->numero_piso = $request->input('numero_piso');
            $piso->save();
    
            return redirect()->back()->with('successregister', 'Piso actualizado exitosamente.');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'No se pudo editar el piso.');
        }
    }

    public function destroy($id)
    {
        try{
            //encontrar el piso a eliminar
            $piso = Piso::findOrFail($id);
            $piso->delete();

            return redirect()->back()->with('success', 'Piso eliminado exitosamente.');
        }catch (\Exception $e){
            return redirect()->back()->with('error', 'Verifique que no halla registros que pertenescan al piso.');
        }
    }
}
