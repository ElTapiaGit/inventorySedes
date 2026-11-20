<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\Piso;
use App\Models\Ambiente;
use App\Models\TipoAmbiente;
use App\Models\Carrera;
use Illuminate\Support\Facades\Crypt;

class AmbienteController extends Controller
{
    // Para la pagina de solo ambientes y agregar ambientes
    public function index(Request $request)
    {
        try {
            $sedes = Sede::all();

            // Obtener la sede seleccionada, por defecto la sede central o la primera disponible
            $sedeIdParam = $request->input('sede') ? Crypt::decryptString($request->input('sede')) : null;
            $sede = $sedeIdParam ? Sede::find($sedeIdParam) : (Sede::find(1) ?? $sedes->first());
            $sedeSeleccionada = $sede?->id_sede;
            $nombreSedeSeleccionada = $sede?->nombre ?? 'Sin Sede';

            // Obtener los edificios de la sede seleccionada
            $edificios = $sedeSeleccionada ? Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get() : collect();

            // Desencriptar el edificio seleccionado (con un valor por defecto en caso de que no se seleccione ninguno)
            $edificioSeleccionado = null;
            if ($request->input('edificio')) {
                $edificioSeleccionado = Crypt::decryptString($request->input('edificio'));
            } elseif ($edificios->isNotEmpty()) {
                $edificioSeleccionado = $edificios->first()?->id_edificio;
            }

            $edificio = $edificioSeleccionado ? Edificio::find($edificioSeleccionado) : null;
            $nombreEdificioSeleccionada = $edificio?->nombre_edi ?? 'Sin Edificio';

            // Obtener los pisos del edificio seleccionado
            $pisos = $edificioSeleccionado ? Piso::where('EDIFICIO_id_edificio', $edificioSeleccionado)->get() : collect();

            // Obtener los ambientes del edificio seleccionado con su carrera
            $ambientes = $edificioSeleccionado ? Ambiente::with('carrera')
                ->join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->where('piso.EDIFICIO_id_edificio', $edificioSeleccionado)
                ->select('ambiente.*', 'tipo_ambiente.nombre_amb as tipo_ambiente', 'piso.numero_piso')
                ->get() : collect();

            // Verificar si no hay ambientes
            $noHayAmbientes = $ambientes->isEmpty();

            // Obtener los tipos de ambiente y carreras
            $tiposAmbiente = TipoAmbiente::all();
            $carreras = Carrera::orderBy('nombre_carrera')->get();

            return view('administrator.ambientes', [
                'sedes' => $sedes,
                'edificios' => $edificios,
                'pisos' => $pisos,
                'tiposAmbiente' => $tiposAmbiente,
                'carreras' => $carreras,
                'ambientes' => $ambientes,
                'sedeSeleccionada' => $sedeSeleccionada,
                'nombreSedeSeleccionada' => $nombreSedeSeleccionada,
                'edificioSeleccionado' => $edificioSeleccionado,
                'nombreEdificioSeleccionada' => $nombreEdificioSeleccionada,
                'noHayAmbientes' => $noHayAmbientes
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para la pagina ambiente: ' . $e->getMessage());
        }   
    }

    public function store(Request $request)
    {
        // Validacion estricta fuera de try-catch
        $request->validate([
            'nombre' => 'required|string|max:45',
            'descripcion' => 'required|string',
            'tipo_ambiente' => 'required|integer|exists:tipo_ambiente,id_tipoambiente',
            'piso' => 'required|integer|exists:piso,id_piso',
            'CARRERA_id_carrera' => 'nullable|integer|exists:carrera,id_carrera',
        ], [
            'nombre.required' => 'El nombre del ambiente es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 45 caracteres.',
            'descripcion.required' => 'La descripcion es obligatoria.',
            'tipo_ambiente.required' => 'Debe seleccionar un tipo de ambiente.',
            'tipo_ambiente.exists' => 'El tipo de ambiente seleccionado no existe.',
            'piso.required' => 'Debe seleccionar un piso.',
            'piso.exists' => 'El piso seleccionado no existe.',
            'CARRERA_id_carrera.exists' => 'La carrera seleccionada no existe.',
        ]);

        try {
            // Crear un nuevo ambiente
            Ambiente::create([
                'nombre' => trim($request->input('nombre')),
                'descripcion_amb' => trim($request->input('descripcion')),
                'TIPO_AMBIENTE_id_ambiente' => $request->input('tipo_ambiente'),
                'PISO_id_piso' => $request->input('piso'),
                'CARRERA_id_carrera' => $request->filled('CARRERA_id_carrera') ? (int) $request->input('CARRERA_id_carrera') : null,
            ]);

            return redirect()->route('ambientes.index')->with('successregister', 'Ambiente agregado exitosamente en la BD.');

        } catch (\Exception $e) {
            return redirect()->route('ambientes.index')->with('errorregister', 'Error al agregar Ambiente a la BD.');
        }   
    }

    // Para editar un registro
    public function update(Request $request)
    {
        // Validacion estricta fuera de try-catch
        $request->validate([
            'id' => 'required|integer|exists:ambiente,id_ambiente',
            'nombre' => 'required|string|max:45',
            'descripcion' => 'required|string',
            'tipo_ambiente' => 'required|integer|exists:tipo_ambiente,id_tipoambiente',
            'piso' => 'required|integer|exists:piso,id_piso',
            'CARRERA_id_carrera' => 'nullable|integer|exists:carrera,id_carrera',
        ], [
            'id.required' => 'El identificador del ambiente es obligatorio.',
            'id.exists' => 'El ambiente especificado no existe.',
            'nombre.required' => 'El nombre del ambiente es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 45 caracteres.',
            'descripcion.required' => 'La descripcion es obligatoria.',
            'tipo_ambiente.required' => 'Debe seleccionar un tipo de ambiente.',
            'tipo_ambiente.exists' => 'El tipo de ambiente seleccionado no existe.',
            'piso.required' => 'Debe seleccionar un piso.',
            'piso.exists' => 'El piso seleccionado no existe.',
            'CARRERA_id_carrera.exists' => 'La carrera seleccionada no existe.',
        ]);

        try {
            // Encontrar el ambiente por id
            $ambiente = Ambiente::findOrFail($request->input('id'));

            // Actualizar el ambiente
            $ambiente->update([
                'nombre' => trim($request->input('nombre')),
                'descripcion_amb' => trim($request->input('descripcion')),
                'TIPO_AMBIENTE_id_ambiente' => $request->input('tipo_ambiente'),
                'PISO_id_piso' => $request->input('piso'),
                'CARRERA_id_carrera' => $request->filled('CARRERA_id_carrera') ? (int) $request->input('CARRERA_id_carrera') : null,
            ]);

            return redirect()->route('ambientes.index')->with('success', 'Ambiente actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('ambientes.index')->with('error', 'Error al actualizar Ambiente: ');
        }
    }

    // Para eliminar un registro
    public function destroy($id)
    {
        try {
            // Encontrar el ambiente por su ID
            $ambiente = Ambiente::findOrFail($id);

            // Eliminar el ambiente
            $ambiente->delete();

            // Retornar una respuesta indicando exito
            return redirect()->route('ambientes.index')->with('successdelete', 'Ambiente eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('ambientes.index')->with('errordelete', 'No se puede eliminar.. Verifique que no haya registros que dependan del ambiente');
        }
    }
}
