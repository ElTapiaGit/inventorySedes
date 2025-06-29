<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\TipoAmbiente;
use App\Models\TipoPersonal;
use App\Models\TipoUsuario;

class TipoAmbientesController extends Controller
{
    //para la pagina principal
    public function index()
    {
        try{
            $sedes = Sede::all();
            $sedeSeleccionada = session('sedeSeleccionada', $sedes->first()?->id_sede);
            $edificios = Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get();
            $edificioSeleccionado = session('edificioSeleccionado', $edificios->first()->id_edificio);
    
            $tipoAmbientes = TipoAmbiente::all();//para obtener todo los tipos de ambientes
    
            return view('administrator.tipoAmbientes', compact('sedes', 'sedeSeleccionada', 'edificios', 'edificioSeleccionado', 'tipoAmbientes'));
            
        }catch (\Exception $e) {
            return redirect()->route('tipoambiente.index')->with('error', 'Error al registrar el tipo de ambiente.');
        }
    }

    //para poder registrar nuevos tipos de ambientes
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => [
                'required',
                'string',
                'max:60',
                'regex:/^[a-zA-ZÁÉÍÓÚÑáéíóúñ0-9\s\.,\-()]+$/'
            ]
        ], [
            'tipo.regex' => 'No se permiten esos caracteres'
        ]);
        try {

            // Verificar si el tipo ambinete ya existe
            $ambienteExistente = TipoAmbiente::where('nombre_amb', $request->input('tipo'))->first();

            if ($ambienteExistente) {
                return redirect()->route('tipoambiente.index')->with('errorregister', 'El Tipo de Ambiente ya existe.');
            }

            TipoAmbiente::create([
                'nombre_amb' => $request->input('tipo'),
            ]);

            return redirect()->route('tipoambiente.index')->with('successregister', 'Tipo de ambiente registrado exitosamente.');
            
        } catch (\Exception $e) {
            return redirect()->route('tipoambiente.index')->with('error', 'Error al registrar el tipo de ambiente.');
        }
    }
    //para editar los datos de tipo de ambiente
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'tipo' => 'required|string|max:60',
            ]);

            $tipoAmbiente = TipoAmbiente::findOrFail($id);
            $tipoAmbiente->update([
                'nombre_amb' => $request->input('tipo'),
            ]);

            return redirect()->route('tipoambiente.index')->with('success', 'Tipo de ambiente actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('tipoambiente.index')->with('error', 'Error al actualizar el tipo de ambiente.');
        }
    }

    //para borrar el registro de la tabla tipo_ambiente
    public function destroy($id)
    {
        try {
            $realId = Crypt::decryptString($id); //desincriptar
            $tipoAmbiente = TipoAmbiente::findOrFail($realId);
            $tipoAmbiente->delete();

            return redirect()->route('tipoambiente.index')->with('success', 'Tipo de ambiente eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('tipoambiente.index')->with('error', 'Error al eliminar el tipo de ambiente.');
        }
    }
}
