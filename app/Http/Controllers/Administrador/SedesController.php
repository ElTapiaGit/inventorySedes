<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\Piso;
use App\Models\Carrera;

class SedesController extends Controller
{
    public function index()
    {
        try {
            $sedes = Sede::all();
            $sedeSeleccionada = session('sedeSeleccionada', $sedes->first()?->id_sede);
            $edificiosPorSede = $sedeSeleccionada ? Edificio::with('carrera')->where('SEDE_id_sede', $sedeSeleccionada)->get() : collect();
            $edificioSeleccionado = session('edificioSeleccionado', $edificiosPorSede->first()?->id_edificio);

            // Obtener los edificios con la carrera asociada y el numero de pisos
            $edificios = Edificio::with('carrera')->withCount('pisos')->get();
            $carreras = Carrera::orderBy('nombre_carrera')->get();

            return view('administrator.sedes', compact('sedes', 'sedeSeleccionada', 'edificios', 'edificioSeleccionado', 'carreras'));
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al cargar las sedes: ' . $e->getMessage());
        }
    }

    // Metodo para registrar nuevas sedes
    public function storeSede(Request $request)
    {
        $request->validate([
            // Validar que el nombre es unico ademas de los otros requisitos
            'nombre' => ['required', 'string', 'max:45', 'regex:/^[a-zA-ZÁÉÍÓÚÑáéíóúñ0-9\s\.,\-()]+$/']
        ], [
            'nombre.regex' => 'No se permiten esos carateres, solo se permite. , - ( )'
        ]);
        try {
            // Crear una nueva sede
            $sede = new Sede();
            $sede->nombre = $request->nombre;
            $sede->save();
    
            return redirect()->route('sedes.index')->with('success', 'Sede agregada exitosamente.');
        } catch (\Exception $e) {
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
        try {
            $sede = Sede::findOrFail($request->id_sede);
            $sede->update([
                'nombre' => $request->nombredit,
            ]);
    
            return redirect()->route('sedes.index')->with('success', 'Sede actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Error al actualizar la sede en la Base de Datos: ');
        }
    }

    // Metodo para eliminar una sede
    public function destroy($id)
    {
        try {
            $sede = Sede::findOrFail($id);
            $sede->delete();

            return redirect()->route('sedes.index')->with('success', 'Sede eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'ERROR!! No se puede eliminar... verifique que no haya nada que dependa de sede (edificios, pisos, ambientes,...)');
        }
    }

    public function show()
    {
        try {
            // Obtener los edificios de la sede seleccionada
            $edificios = Edificio::withCount('pisos')->get();
            
            return response()->json($edificios);

        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para los edificios: ');
        }
    }

    // Metodo para guardar nuevos edificios
    public function storeEdificio(Request $request)
    {
        // Validacion estricta fuera de try-catch
        $request->validate([
            'nombre_edi' => 'required|string|max:45',
            'direccion' => 'required|string|max:80',
            'SEDE_id_sede' => 'required|integer|exists:sede,id_sede',
            'CARRERA_id_carrera' => 'nullable|integer|exists:carrera,id_carrera',
        ], [
            'nombre_edi.required' => 'El nombre del edificio es obligatorio.',
            'nombre_edi.max' => 'El nombre del edificio no debe superar los 45 caracteres.',
            'direccion.required' => 'La direccion es obligatoria.',
            'direccion.max' => 'La direccion no debe superar los 80 caracteres.',
            'SEDE_id_sede.required' => 'Debe seleccionar una sede valida.',
            'SEDE_id_sede.exists' => 'La sede seleccionada no existe.',
            'CARRERA_id_carrera.exists' => 'La carrera seleccionada no existe.',
        ]);

        try {
            // Crear un nuevo edificio
            $edificio = new Edificio();
            $edificio->nombre_edi = trim($request->nombre_edi);
            $edificio->direccion = trim($request->direccion);
            $edificio->SEDE_id_sede = $request->SEDE_id_sede;
            $edificio->CARRERA_id_carrera = $request->filled('CARRERA_id_carrera') ? (int) $request->CARRERA_id_carrera : null;
            $edificio->save();

            return redirect()->route('sedes.index')->with('success', 'Edificio agregado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Error al guardar los datos para los edificios en la Base de Datos.');
        }
    }

    // Metodo para editar edificio
    public function updateEdificio(Request $request, $id_edificio)
    {
        // Validacion estricta fuera de try-catch
        $request->validate([
            'nombre_edi' => 'required|string|max:45',
            'direccion' => 'required|string|max:80',
            'CARRERA_id_carrera' => 'nullable|integer|exists:carrera,id_carrera',
        ], [
            'nombre_edi.required' => 'El nombre del edificio es obligatorio.',
            'nombre_edi.max' => 'El nombre del edificio no debe superar los 45 caracteres.',
            'direccion.required' => 'La direccion es obligatoria.',
            'direccion.max' => 'La direccion no debe superar los 80 caracteres.',
            'CARRERA_id_carrera.exists' => 'La carrera seleccionada no existe.',
        ]);

        try {
            $edificio = Edificio::findOrFail($id_edificio);
            $edificio->update([
                'nombre_edi' => trim($request->input('nombre_edi')),
                'direccion' => trim($request->input('direccion')),
                'CARRERA_id_carrera' => $request->filled('CARRERA_id_carrera') ? (int) $request->input('CARRERA_id_carrera') : null,
            ]);

            return redirect()->route('sedes.index')->with('success', 'Edificio actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Error al actualizar los datos del edificio en la Base de Datos.');
        }
    }

    // Metodo para eliminar edificio
    public function destroyEdificio($id_edificio)
    {
        try {
            $edificio = Edificio::findOrFail($id_edificio);
            $edificio->delete();

            return redirect()->route('sedes.index')->with('success', 'Edificio eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Error al eliminar el registro de edificio verifique no haya nada que dependa del edificio como pisos, ambientes...:');
        }
    }

    // Metodo para guardar nuevos pisos
    public function storePisos(Request $request)
    {
        $request->validate([
            'numero_piso.*' => 'required|string|max:50',
            'Edificio_id_edificio.*' => 'required|integer|exists:edificio,id_edificio',
        ]);

        // Verificar que ambos arrays tengan la misma longitud
        if (count($request->numero_piso) !== count($request->Edificio_id_edificio)) {
            return redirect()->back()->with('errordata', 'Datos de pisos inconsistentes: la cantidad de pisos no coincide con la cantidad de edificios.');
        }

        try {
            foreach ($request->numero_piso as $index => $numero_piso) {
                Piso::create([
                    'numero_piso' => $numero_piso,
                    'EDIFICIO_id_edificio' => $request->Edificio_id_edificio[$index],
                ]);
            }

            return redirect()->route('sedes.index')->with('success', 'Pisos agregados exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('sedes.index')->with('errordata', 'Problema al agregar pisos: ' . $e->getMessage());
        }
    }
}
