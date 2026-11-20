<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Mantenimiento;
use App\Models\FinalMantenimiento;
use App\Models\DetallesMantenimiento;
use App\Models\Tecnico;
use App\Models\Personal;
use App\Models\Equipo;
use App\Models\Mobiliario;
use App\Models\Material;
use App\Models\Accesorio;

class MantenimientopController extends Controller
{
    // Mostrar mantenimientos filtrados por edificio
    public function index()
    {
        $edificioId = Auth::user()->edificio_activo_id;

        $mantenimientos = Mantenimiento::with(['tecnico', 'personal', 'finalMantenimiento'])
            ->whereHas('personal', function ($q) use ($edificioId) {
                $q->where('EDIFICIO_id_edificio', $edificioId)
                  ->orWhereHas('edificiosAsignados', function ($sub) use ($edificioId) {
                      $sub->where('edificio.id_edificio', $edificioId);
                  });
            })
            ->latest('id_mantenimiento_ini')
            ->paginate(10);

        $tecnicos = Tecnico::all();
        $personales = Personal::where('EDIFICIO_id_edificio', $edificioId)
            ->orWhereHas('edificiosAsignados', function ($q) use ($edificioId) {
                $q->where('edificio.id_edificio', $edificioId);
            })->get();
        $equipos = Equipo::whereHas('ambiente.piso', function ($q) use ($edificioId) {
            $q->where('EDIFICIO_id_edificio', $edificioId);
        })->get();

        return view('encargados.mantenimientoPage', compact('mantenimientos', 'tecnicos', 'personales', 'equipos'));
    }

    // Registrar el inicio del mantenimiento con sus detalles
    public function store(Request $request)
    {
        $edificioId = Auth::user()->edificio_activo_id;

        // Validacion estricta fuera de bloques try-catch
        $request->validate([
            'informe_inicial' => 'required|string',
            'fch_inicio' => 'required|date',
            'TECNICO_id_tecnico' => 'required|exists:tecnico,id_tecnico',
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
        ]);

        try {
            $mantenimiento = Mantenimiento::create([
                'informe_inicial' => $request->informe_inicial,
                'fch_inicio' => $request->fch_inicio,
                'TECNICO_id_tecnico' => $request->TECNICO_id_tecnico,
                'PERSONAL_id_personal' => Auth::user()->id_personal,
            ]);

            // Guardar los articulos asociados al mantenimiento
            foreach ($request->cod_articulo as $codigo) {
                DetallesMantenimiento::create([
                    'cod_articulo' => $codigo,
                    'INICIO_MANTENIMIENTO_id_mantenimiento_ini' => $mantenimiento->id_mantenimiento_ini,
                ]);
            }

            return redirect()->route('encargado.mantenimiento')->with('success', 'Mantenimiento registrado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrio un error al registrar el mantenimiento. Intente nuevamente.');
        }
    }

    // Mostrar los detalles completos del mantenimiento verificando edificio
    public function show($id)
    {
        try {
            $idDesencriptado = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID invalido');
        }

        $edificioId = Auth::user()->edificio_activo_id;

        $mantenimiento = Mantenimiento::with([
            'tecnico',
            'personal',
            'finalMantenimiento',
            'detalleMantenimiento',
        ])
        ->where('id_mantenimiento_ini', $idDesencriptado)
        ->whereHas('personal', function ($q) use ($edificioId) {
            $q->where('EDIFICIO_id_edificio', $edificioId)
              ->orWhereHas('edificiosAsignados', function ($sub) use ($edificioId) {
                  $sub->where('edificio.id_edificio', $edificioId);
              });
        })
        ->firstOrFail();

        return view('encargados.detalleMantenimiento', compact('mantenimiento'));
    }

    // Registrar el final del mantenimiento verificando edificio
    public function finalizar(Request $request, $id)
    {
        // Validacion fuera de try-catch
        $request->validate([
            'informe_final' => 'required|string',
            'fch_final' => 'required|date',
        ]);

        try {
            $edificioId = Auth::user()->edificio_activo_id;

            // Verificar aislamiento territorial del mantenimiento antes de finalizar
            $mantenimiento = Mantenimiento::where('id_mantenimiento_ini', $id)
                ->whereHas('personal', function ($q) use ($edificioId) {
                    $q->where('EDIFICIO_id_edificio', $edificioId)
                      ->orWhereHas('edificiosAsignados', function ($sub) use ($edificioId) {
                          $sub->where('edificio.id_edificio', $edificioId);
                      });
                })
                ->firstOrFail();

            FinalMantenimiento::create([
                'informe_final' => $request->informe_final,
                'fch_final' => $request->fch_final,
                'INICIO_MANTENIMIENTO_id_mantenimiento_ini' => $mantenimiento->id_mantenimiento_ini,
            ]);

            return redirect()->route('encargado.mantenimiento')->with('success', 'Mantenimiento finalizado correctamente.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('encargado.mantenimiento')->with('error', 'No tiene permisos para finalizar un mantenimiento de otro edificio.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.mantenimiento')->with('error', 'Ocurrio un error al finalizar el mantenimiento.');
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
