<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\Piso;

class SedesController extends Controller
{
    //
    public function index()
    {
        try{
            $sedes = Sede::all();
            $sedeSeleccionada = session('sedeSeleccionada', $sedes->first()->id_sede);
            $edificios = Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get();
            $edificioSeleccionado = session('edificioSeleccionado', $edificios->first()->id_edificio);

            // Obtener los edificios con el número de pisos para cada sede
            $edificios = Edificio::withCount('pisos')->get();


            return view('administrator.sedes', compact('sedes', 'sedeSeleccionada', 'edificios', 'edificioSeleccionado'));
        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al agregar el nuevo personal: ');
        }
    }
    //Metodo para registrar nuevas sedes
    public function storeSede(Request $request)
    {
        $request->validate([
            // Validar que el nombre es único además de los otros requisitos
            'nombre' => ['required', 'string', 'max:45', 'regex:/^[a-zA-ZÁÉÍÓÚÑáéíóúñ0-9\s\.,\-()]+$/']
        ], [
            'nombre.regex' => 'No se permiten esos carateres, solo se permite. , - ( )'
        ]);
        try{
            
            // Crear una nueva sede
            $sede = new Sede();
            $sede->nombre = $request->nombre;
            $sede->save();
    
            return redirect()->route('sedes.index')->with('success', 'Sede agregada exitosamente.');
        }catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Ocurrió un error inesperado al guardar la sede. Por favor intenta nuevamente.');
        }
    }
    // Metodo para actualizar los datos de la sede
    public function update(Request $request)
    {
        $request->validate([
            'id_sede' => 'required|integer|exists:sede,id_sede',
            'nombredit' => ['required', 'string', 'max:45', 'regex:/^[a-zA-ZÁÉÍÓÚÑáéíóúñ0-9\s\.,\-()]+$/']
        ], [
            'nombredit.regex' => 'Los caracteres no son permitidos'
        ]);
        try{
    
            $sede = Sede::findOrFail($request->id_sede);
            $sede->update([
                'nombre' => $request->nombredit,
            ]);
    
            return redirect()->route('sedes.index')->with('success', 'Sede actualizada exitosamente.');

        }catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Error al actualizar la sede en la Base de Datos: ');
        }
    }
    //Metodo para eliminar una sede
    public function destroy($id)
    {
        try{
            $sede = Sede::findOrFail($id);
            $sede->delete();

            return redirect()->route('sedes.index')->with('success', 'Sede eliminada exitosamente.');
        }catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'ERROR!! No se puede eliminar... verifique que no haya nada que dependa de sede (edificios, pisos, ambientes,...)');
        }
    }

    public function show()
    {
        try{
            // Obtener los edificios de la sede seleccionada
            $edificios = Edificio::withCount('pisos')->get(); // Utilizar withCount para contar los pisos relacionados
            
            return response()->json($edificios);

        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para los edificios: ');
        }
    }

    // Metodo para guardar nuevos edificios
    public function storeEdificio(Request $request)
    {
        try{
            // Validar los datos
            $request->validate([
                'nombre_edi' => 'required|string|max:45',
                'direccion' => 'required|string|max:80',
                'SEDE_id_sede' => 'required|integer|exists:sede,id_sede',
            ]);

            // Crear un nuevo edificio
            $edificio = new Edificio();
            $edificio->nombre_edi = $request->nombre_edi;
            $edificio->direccion = $request->direccion;
            $edificio->SEDE_id_sede = $request->SEDE_id_sede;
            $edificio->save();

            return redirect()->route('sedes.index')->with('success', 'Edificio agregado exitosamente.');

        }catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Error al guardar los datos para los edificios en la Base de Datos: ');
        }
    }
    // Metodo para editar edificio
    public function updateEdificio(Request $request, $id_edificio)
    {
        try{
            $request->validate([
                'nombre_edi' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
            ]);
    
            $edificio = Edificio::findOrFail($id_edificio);
            $edificio->update($request->all());
    
            return redirect()->route('sedes.index')->with('success', 'Edificio actualizado correctamente.');
        }catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Error al actualizar los datos del edificio en la Base de Datos :');
        }
    }
    // metodo para eliminar edificio
    public function destroyEdificio($id_edificio)
    {
        try{
            $edificio = Edificio::findOrFail($id_edificio);
            $edificio->delete();

            return redirect()->route('sedes.index')->with('success', 'Edificio eliminado correctamente.');
        }catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Error al eliminar el registro de edificio verifique no haya nada que dependa del edificio como pisos, ambientes...:');
        }
    }


    // Metodo para guardar nuevos pisos
    public function storePisos(Request $request)
    {
        // Agregar manejo de errores para depurar
        try {
            $request->validate([ //validar datos de entrada
                'numero_piso.*' => 'required|string|max:50',
                'Edificio_id_edificio.*' => 'required|integer|exists:edificio,id_edificio',
            ]);

            // Verificar que ambos arrays tengan la misma longitud si sale error borrar esto
            if (count($request->numero_piso) !== count($request->Edificio_id_edificio)) {
                return redirect()->back()->with('errordata', 'Datos de pisos inconsistentes: la cantidad de pisos no coincide con la cantidad de edificios.');
            }

            foreach ($request->numero_piso as $index => $numero_piso) {
                Piso::create([ //instar los datos en los campos de la base de datos
                    'numero_piso' => $numero_piso,
                    'EDIFICIO_id_edificio' => $request->Edificio_id_edificio[$index],
                ]);
            }

            return redirect()->route('sedes.index')->with('success', 'Pisos agregados exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Problema al agregar pisos: ');
        }
    }

}
