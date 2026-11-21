<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Prestamo;
use App\Models\DetallePrestamo;
use App\Models\Personal;
use App\Models\Ambiente;
use App\Models\Devolucion;
use App\Models\Equipo;
use App\Models\Mobiliario;
use App\Models\Material;
use App\Models\Accesorio;

class PrestamopController extends Controller
{
    public function index()
    {
        $edificioId = Auth::user()->edificio_activo_id;

        $prestamos = Prestamo::with(['personal', 'devolucion'])
            ->porEdificio($edificioId)
            ->latest('id_prestamo')
            ->paginate(10);

        $personales = Personal::where('EDIFICIO_id_edificio', $edificioId)
            ->orWhereHas('edificiosAsignados', function ($q) use ($edificioId) {
                $q->where('edificio.id_edificio', $edificioId);
            })->get();

        return view('encargados.prestamoPag', compact('prestamos', 'personales'));
    }

    public function store(Request $request)
    {
        $edificioId = Auth::user()->edificio_activo_id;

        // Validacion estricta fuera de bloques try-catch
        $request->validate([
            'nombre_solicitante' => 'required|string',
            'descripcion_prestamo' => 'required|string',
            'fch_prestamo' => 'required|date',
            'hora_prestamo' => 'required',
            'cod_articulo' => 'required|array',
            'cod_articulo.*' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($edificioId) {
                    $resultado = $this->validarArticuloEdificio($value, $edificioId);
                    if ($resultado !== true) {
                        $fail($resultado);
                    }
                },
            ],
            'observacion_detalle' => 'nullable|array',
        ]);

        try {
            $prestamo = Prestamo::create([
                'nombre_solicitante' => $request->nombre_solicitante,
                'descripcion_prestamo' => $request->descripcion_prestamo,
                'fch_prestamo' => $request->fch_prestamo,
                'hora_prestamo' => $request->hora_prestamo,
                'PERSONAL_id_personal' => Auth::user()->id_personal,
            ]);

            foreach ($request->cod_articulo as $index => $codigo) {
                DetallePrestamo::create([
                    'cod_articulo' => $codigo,
                    'observacion_detalle' => $request->observacion_detalle[$index] ?? 'Sin observaciones previas al prestamo',
                    'PRESTAMO_id_prestamo' => $prestamo->id_prestamo,
                ]);
            }

            return redirect()->route('encargado.prestamo')->with('success', 'Prestamo registrado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.prestamo')->with('error', 'Ocurrio un error al registrar el prestamo. Intente nuevamente.');
        }
    }

    public function show($id)
    {
        $idDesencriptado = Crypt::decrypt($id);
        $edificioId = Auth::user()->edificio_activo_id;

        $prestamo = Prestamo::with(['detallePrestamos', 'personal', 'devolucion'])
            ->where('id_prestamo', $idDesencriptado)
            ->porEdificio($edificioId)
            ->firstOrFail();

        return view('encargados.detallePrestamos', compact('prestamo'));
    }

    public function registrarDevolucion(Request $request, $id)
    {
        // Validacion fuera de try-catch
        $request->validate([
            'fch_devolucion' => 'required|date',
            'hora_devolucion' => 'required',
            'descripcion_devolucion' => 'nullable|string',
        ]);

        try {
            $edificioId = Auth::user()->edificio_activo_id;

            // Validar que el prestamo pertenezca al edificio activo
            $prestamo = Prestamo::where('id_prestamo', $id)
                ->porEdificio($edificioId)
                ->firstOrFail();

            Devolucion::create([
                'fch_devolucion' => $request->fch_devolucion,
                'hora_devolucion' => $request->hora_devolucion,
                'descripcion_devolucion' => $request->descripcion_devolucion,
                'PRESTAMO_id_prestamo' => $prestamo->id_prestamo,
                'PERSONAL_id_personal' => Auth::user()->id_personal,
            ]);

            return redirect()->route('encargado.prestamo')->with('success', 'Devolucion registrada correctamente.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('encargado.prestamo')->with('error', 'No tiene permisos para registrar la devolucion de un prestamo de otro edificio.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.prestamo')->with('error', 'Ocurrio un error al registrar la devolucion. Intente nuevamente mas tarde.');
        }
    }

    /**
     * Valida que un codigo de articulo exista en inventario y pertenezca al edificio asignado.
     *
     * @param string $codigo
     * @param int $edificioId
     * @return true|string Retorna true si es valido, o mensaje de error descriptivo.
     */
    private function validarArticuloEdificio(string $codigo, int $edificioId)
    {
        // 1. Verificar si es un Equipo
        $equipo = Equipo::with('ambiente.piso')->where('cod_equipo', $codigo)->first();
        if ($equipo) {
            if ($equipo->ambiente && $equipo->ambiente->piso && $equipo->ambiente->piso->EDIFICIO_id_edificio == $edificioId) {
                return true;
            }
            return "El articulo '{$codigo}' (Equipo) no pertenece a su edificio asignado.";
        }

        // 2. Verificar si es un Mobiliario
        $mobiliario = Mobiliario::with('ambiente.piso')->where('cod_mueble', $codigo)->first();
        if ($mobiliario) {
            if ($mobiliario->ambiente && $mobiliario->ambiente->piso && $mobiliario->ambiente->piso->EDIFICIO_id_edificio == $edificioId) {
                return true;
            }
            return "El articulo '{$codigo}' (Mobiliario) no pertenece a su edificio asignado.";
        }

        // 3. Verificar si es un Material
        $material = Material::with('ambiente.piso')->where('cod_mate', $codigo)->first();
        if ($material) {
            if ($material->ambiente && $material->ambiente->piso && $material->ambiente->piso->EDIFICIO_id_edificio == $edificioId) {
                return true;
            }
            return "El articulo '{$codigo}' (Material) no pertenece a su edificio asignado.";
        }

        // 4. Verificar si es un Accesorio
        $accesorio = Accesorio::with('equipos.ambiente.piso')->where('cod_accesorio', $codigo)->first();
        if ($accesorio) {
            $equiposOtroEdificio = $accesorio->equipos->filter(function ($eq) use ($edificioId) {
                return $eq->ambiente && $eq->ambiente->piso && $eq->ambiente->piso->EDIFICIO_id_edificio != $edificioId;
            });
            $equiposEsteEdificio = $accesorio->equipos->filter(function ($eq) use ($edificioId) {
                return $eq->ambiente && $eq->ambiente->piso && $eq->ambiente->piso->EDIFICIO_id_edificio == $edificioId;
            });

            if ($equiposOtroEdificio->isNotEmpty() && $equiposEsteEdificio->isEmpty()) {
                return "El articulo '{$codigo}' (Accesorio) esta vinculado exclusivamente a un equipo de otro edificio.";
            }

            return true;
        }

        return "El codigo de articulo '{$codigo}' no existe en el inventario.";
    }
}
