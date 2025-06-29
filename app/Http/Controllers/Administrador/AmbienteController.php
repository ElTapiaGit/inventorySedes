<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\Piso;
use App\Models\Ambiente;
use App\Models\TipoAmbiente;
use Illuminate\Support\Facades\Crypt;

class AmbienteController extends Controller
{
    //para la pagina de solo ambientes y agregar ambientes
    public function index(Request $request)
    {
        try{
            // Obtener la sede seleccionada, por defecto la sede central
            $sedeSeleccionada = $request->input('sede') ? Crypt::decryptString($request->input('sede')) : 1;

            // Obtener el nombre de la sede seleccionada
            $nombreSedeSeleccionada = Sede::find($sedeSeleccionada)->nombre;

            // Obtener los edificios de la sede seleccionada
            $edificios = Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get();

            // Desencriptar el edificio seleccionado (con un valor por defecto en caso de que no se seleccione ninguno)
            $edificioSeleccionado = $request->input('edificio') ? Crypt::decryptString($request->input('edificio')) : $edificios->first()->id_edificio;
            $nombreEdificioSeleccionada = Edificio::find($edificioSeleccionado)->nombre_edi;

            // Obtener los pisos del edificio seleccionado
            $pisos = Piso::where('EDIFICIO_id_edificio', $edificioSeleccionado)->get();

            // Obtener los ambientes del edificio seleccionado
            $ambientes = Ambiente::join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->where('piso.EDIFICIO_id_edificio', $edificioSeleccionado)
                ->select('ambiente.*', 'tipo_ambiente.nombre_amb as tipo_ambiente', 'piso.numero_piso')
                ->get();

            // Verificar si no hay ambientes
            $noHayAmbientes = $ambientes->isEmpty();

            // Obtener los tipos de ambiente 
            $tiposAmbiente = TipoAmbiente::all();

            return view('administrator.ambientes', [
                'sedes' => Sede::all(),
                'edificios' => $edificios,
                'pisos' => $pisos,
                'tiposAmbiente' => $tiposAmbiente,
                'ambientes' => $ambientes,
                'sedeSeleccionada' => $sedeSeleccionada,
                'nombreSedeSeleccionada' => $nombreSedeSeleccionada,
                'edificioSeleccionado' => $edificioSeleccionado,
                'nombreEdificioSeleccionada' => $nombreEdificioSeleccionada,
                'noHayAmbientes' => $noHayAmbientes
            ]);
        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los dato para la pagina ambiente: ' );//. $e->getMessage()
        }   
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:45',
                'descripcion' => 'required|string',
                'tipo_ambiente' => 'required|integer|exists:tipo_ambiente,id_tipoambiente',//para validar llaves foraneas no dejas espacios
                'piso' => 'required|integer|exists:piso,id_piso',//para validar llaves foraneas no dejas espacios
            ]);

            // Crear un nuevo ambiente
            Ambiente::create([
                'nombre' => $request->input('nombre'),
                'descripcion_amb' => $request->input('descripcion'),
                'TIPO_AMBIENTE_id_ambiente' => $request->input('tipo_ambiente'),
                'PISO_id_piso' => $request->input('piso'),
            ]);

            return redirect()->route('ambientes.index')->with('successregister', 'Ambiente agregados exitosamente en la BD.');

        } catch (\Exception $e) {
            return redirect()->route('ambientes.index')->with('errorregister', 'Error al agregar Ambiente a la BD: ');
        }   
    }
    //para editar un registro
    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:ambiente,id_ambiente',
                'nombre' => 'required|string|max:45',
                'descripcion' => 'required|string',
                'tipo_ambiente' => 'required|integer|exists:tipo_ambiente,id_tipoambiente',
                'piso' => 'required|integer|exists:piso,id_piso',
            ]);

            // Encontrar el ambiente por id
            $ambiente = Ambiente::find($request->input('id'));

            // Actualizar el ambiente
            $ambiente->update([
                'nombre' => $request->input('nombre'),
                'descripcion_amb' => $request->input('descripcion'),
                'TIPO_AMBIENTE_id_ambiente' => $request->input('tipo_ambiente'),
                'PISO_id_piso' => $request->input('piso'),
            ]);

            return redirect()->route('ambientes.index')->with('success', 'Ambiente actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('ambientes.index')->with('error', 'Error al actualizar Ambiente: ');
        }
    }

    //para elimanar un registro
    public function destroy($id)
    {
        try {
            // Encontrar el ambiente por su ID
            $ambiente = Ambiente::findOrFail($id);

            // Eliminar el ambiente
            $ambiente->delete();

            // Retornar una respuesta indicando éxito
            return redirect()->route('ambientes.index')->with('successdelete', 'Ambiente actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('ambientes.index')->with('errordelete', 'No se puede eliminar.. Verifique que no halla registros que dependan del ambiente');
        }
    }

}
