<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoAmbiente;
use App\Models\Ambiente;
use App\Models\Edificio;
use App\Models\UsoAmbiente;
use App\Models\FinalUso;
use App\Models\Usuario;
use App\Models\Personal;
use App\Models\TipoUsuario;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

//manejadores de error
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class MovimientoAmbienteController extends Controller
{
    //
    public function index(Request $request)
    {
        try{
            $currentDate = now()->format('d-m-Y'); // Fecha en formato DD-MM-YY
            $currentTime = now()->format('H:i:s');   // Hora en formato HH:MM:SS

            // Obtén el ID del usuario actualmente autenticado
            $userId = auth()->user()->id_personal;

            // Obtener el id del edificio donde el personal trabaja
            $edificio_id = Personal::where('id_personal', $userId)->value('EDIFICIO_id_edificio');

            // Verificar el tipo de edificio
            $edificio = Edificio::where('id_edificio', $edificio_id)->first();

            // Inicializar ambientes
            $ambientes = collect(); // Colección vacía para almacenar ambientes

            // Obtener los parámetros de búsqueda
            //$usuarioNombre = request('usuario_nombre');
            $usuarioNombre = $request->input('usuario_nombre');
            //$fechaUso = request('fecha_uso');
            $fechaUso = $request->input('fecha_uso');

            if (!$edificio) {
                return redirect()->route('encargado.inicio')->withErrors('El edificio del personal no se ha encontrado.');
            }

            // Filtro de registros de uso del ambiente
            $query = UsoAmbiente::with('finalUsos', 'ambiente', 'usuario', 'personalInicio')
                ->whereHas('ambiente.piso', function ($query) use ($edificio_id) {
                    $query->where('EDIFICIO_id_edificio', $edificio_id);
                });

            if ($usuarioNombre) {
                $query->whereHas('usuario', function ($q) use ($usuarioNombre) {
                    $q->whereRaw("CONCAT(nombre, ' ', apellidos) LIKE ?", ["%{$usuarioNombre}%"]);
                });
            }

            if ($fechaUso) {
                $query->whereDate('fch_uso', $fechaUso);
            }

            $usos = $query->orderBy('fch_uso', 'desc')
                          ->orderBy('hora_uso', 'desc')
                          ->paginate(10);

            // Determinar si es clínica odontológica
            $esClinica = in_array($edificio->nombre_edi, ['Clinica Odontologia', 'Clinica Odontologica']);
            $ambientes = $this->obtenerAmbientes($edificio_id, $esClinica);

            // Validar si se encontraron ambientes
            if ($ambientes->isEmpty()) {
                return redirect()->route('encargado.inicio')->withErrors('No hay ambientes registrados para este edificio.');
            }

            // Obtener usuario para el campo de selección
            $usuarios = Usuario::all();
            $tiposUsuario = TipoUsuario::all();
            
            return view('encargados.movimientoAmbiente', compact('currentDate', 'currentTime', 'usos', 'ambientes', 'usuarios', 'tiposUsuario'));
        } catch (QueryException $e) {
            // Manejo de excepciones para errores en la consulta SQL
            return redirect()->route('encargado.inicio')->withErrors('Error en la consulta de datos. ');

        } catch (Exception $e) {
            // Manejo de cualquier otra excepción general
            return redirect()->route('encargado.inicio')->withErrors('Ocurrió un error inesperado. Por favor, intente nuevamente.');
        }
    }

    /**
     * Obtener ambientes dependiendo si es clínica odontológica o no.
     */
    private function obtenerAmbientes($edificio_id, $esClinica) {
        if ($esClinica) {
            return Ambiente::whereHas('piso', function ($query) use ($edificio_id) {
                $query->where('EDIFICIO_id_edificio', $edificio_id);
            })->get();
        }

        $tipoLaboratorio = TipoAmbiente::where('nombre_amb', 'like', 'laboratorio%')->pluck('id_tipoambiente');
        return Ambiente::whereIn('TIPO_AMBIENTE_id_ambiente', $tipoLaboratorio)
            ->whereHas('piso', function ($query) use ($edificio_id) {
                $query->where('EDIFICIO_id_edificio', $edificio_id);
            })->get();
    }


    public function registrarUso(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'ambiente_id' => 'required|exists:ambiente,id_ambiente',
            'descripcion' => 'required|string',
            'semestre' => 'nullable|string',
            'usuario_id' => 'required|exists:usuario,id_usuario',
        ]);

        try {
            // Verificar si el ambiente aun esta en uso
            $ambiente_id = $request->input('ambiente_id');
            $existeUsoActivo = UsoAmbiente::where('AMBIENTE_id_ambiente', $ambiente_id)
                ->whereDoesntHave('finalUsos')   // Verifica si no tiene fecha de finalización
                ->exists();

            if ($existeUsoActivo) {
                return redirect()->back()->withErrors('El ambiente ya está en uso. Por favor, finalice el uso actual antes de registrar uno nuevo.');
            }

            // Obtener el personal actual
            $personal = Personal::where('id_personal', auth()->user()->id_personal)->firstOrFail();
            
            // Crear un nuevo registro de uso de ambiente
            UsoAmbiente::create([
                'AMBIENTE_id_ambiente' => $request->input('ambiente_id'),
                'USUARIO_id_usuario' => $request->input('usuario_id'),
                'descripcion' => $request->input('descripcion'),
                'semestre' => $request->input('semestre'),
                'fch_uso' => now()->format('Y-m-d'), // Usar la fecha actual
                'hora_uso' => now()->format('H:i:s'), // Usar la hora actual
                'PERSONAL_id_personal' => $personal->id_personal,
            ]);

            // Redirigir con mensaje de éxito
            return redirect()->route('movimiento.ambiente.index')->with('success', 'Uso registrado exitosamente.');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return redirect()->back()->withErrors('Error al registrar el uso del ambiente: ' . $e->getMessage());
        }
    }

    public function finalizarUso(Request $request)
    {
        try{
            $request->validate([
                'uso_ambiente_id' => 'required|exists:uso_ambiente,id_uso_ambiente',
                'fch_fin' => 'required|date',
                'hora_fin' => 'required|date_format:H:i',
            ]);
    
            $usoAmbiente = UsoAmbiente::find($request->input('uso_ambiente_id'));
    
            // Verificar si ya se ha registrado el final de uso
            if ($usoAmbiente->finalUsos()->exists()) {
                return redirect()->back()->withErrors('El ambiente laboratorio ya ha sido finalizado.');
            }

            // Obtener el personal actual
            $personal = Personal::where('id_personal', auth()->user()->id_personal)->firstOrFail();
    
            // Crear el registro de final de uso
            $finalUso = new FinalUso();
            $finalUso->fch_fin = now()->format('Y-m-d');
            $finalUso->hora_fin = now()->format('H:i:s');
            $finalUso->USO_AMBIENTE_id_uso_ambiente = $request->input('uso_ambiente_id');
            $finalUso->PERSONAL_id_personal = $personal->id_personal; 
            $finalUso->save();
    
            return redirect()->route('movimiento.ambiente.index')->with('success', 'Uso de ambiente finalizado correctamente.');
        }catch (\Exception $e) {
            // Manejo de excepciones
            return redirect()->back()->withErrors('Error al  final el uso del ambiente: ' . $e->getMessage());
        }
    }

    public function detalleMov($id_uso_ambiente)
    {
        try {
            $id_ambiente = Crypt::decrypt($id_uso_ambiente);
        } catch (DecryptException $e) {
            abort(404);
        }

        try{
            // Obtener el movimiento por el ID
            $movimiento = UsoAmbiente::with(['personalInicio', 'personalFin'])
            ->where('id_uso_ambiente', $id_ambiente)
            ->firstOrFail();

            // Obtener los detalles de finalización si existen
            $finalUso = FinalUso::where('USO_AMBIENTE_id_uso_ambiente', $id_ambiente)->first();

            // Obtener el personal de inicio y fin
            $personal_inicio = $movimiento->personalInicio;
            $personal_fin = $movimiento->fch_fin ? $movimiento->personalFin : null;

            return view('encargados.detallesUsoAmbiente', compact('movimiento', 'personal_inicio', 'personal_fin', 'finalUso'));

        } catch(\Exception $e) {
            // Manejo de errores
            return redirect()->back()->withErrors('errorNo se pudo encontrar el detalle del movimiento.');
        }
        
    }

    public function registraruser(Request $request)
    {
        try{
            $request->validate([
                'nombre' => 'required|string|max:50',
                'apellidos' => 'required|string|max:50',
                'celular' => 'required|string|max:50',
                'TIPO_USUARIO_id_tipo_usu' => 'required|exists:tipo_usuario,id_tipo_usu',
            ]);
        
            Usuario::create([
                'nombre' => $request->input('nombre'),
                'apellidos' => $request->input('apellidos'),
                'celular' => $request->input('celular'),
                'TIPO_USUARIO_id_tipo_usu' => $request->input('TIPO_USUARIO_id_tipo_usu'),
            ]);
        
            return redirect()->back()->with('success', 'Usuario registrado exitosamente.');
        }catch (\Exception $e) {
            // Manejo de excepciones
            return redirect()->back()->withErrors('Error al registrar el uso del ambiente: ' . $e->getMessage());
        }
    }
}
