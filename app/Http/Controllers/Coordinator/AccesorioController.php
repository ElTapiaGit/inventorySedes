<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Accesorio;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccesorioController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    protected function getAccesoriosQuery(?int $edificioId = null)
    {
        $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

        return Accesorio::with(['foto', 'equipos'])
            ->where(function ($q) use ($ambientesIds) {
                $q->whereHas('equipos', function ($eq) use ($ambientesIds) {
                    $eq->whereIn('AMBIENTE_id_ambiente', $ambientesIds);
                })->orWhereDoesntHave('equipos');
            });
    }

    public function index()
    {
        try {
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $accesorios = $this->getAccesoriosQuery($edificioActivoId)->paginate(10);
            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Error al obtener los datos para la pagina accesorios.');
        }
    }

    public function accesoriosConEquipo()
    {
        try {
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioActivoId);

            $accesorios = Accesorio::with(['foto', 'equipos'])
                ->whereHas('equipos', function ($eq) use ($ambientesIds) {
                    $eq->whereIn('AMBIENTE_id_ambiente', $ambientesIds);
                })
                ->paginate(10);

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error no se pueden obtener los datos para los accesorios con equipos.');
        }
    }

    public function accesoriosUnicos()
    {
        try {
            $accesorios = Accesorio::with(['foto', 'equipos'])
                ->whereDoesntHave('equipos')
                ->paginate(10);

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error no se pueden obtener los datos para los accesorios sin equipos.');
        }
    }

    public function buscarPorCodigo(Request $request)
    {
        try {
            $codigo = $request->input('codigo');
            $edificioActivoId = $this->scopeService->getEdificioActivoId();

            $accesorios = $this->getAccesoriosQuery($edificioActivoId)
                ->where('cod_accesorio', 'LIKE', '%' . $codigo . '%')
                ->paginate(10);

            if ($accesorios->isEmpty()) {
                return redirect()->route('accesorios.index')->with('errorbuscar', 'El codigo ingresado no esta registrado en sus ambientes asignados.');
            }

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error al buscar por codigo de accesorio.');
        }
    }

    public function buscarPorNombre(Request $request)
    {
        try {
            $nombre = $request->input('nombre');
            $edificioActivoId = $this->scopeService->getEdificioActivoId();

            $accesorios = $this->getAccesoriosQuery($edificioActivoId)
                ->where('nombre_acce', 'LIKE', '%' . $nombre . '%')
                ->paginate(10);

            if ($accesorios->isEmpty()) {
                return redirect()->route('accesorios.index')->with('errorbuscar', 'El nombre a buscar no se encuentra en sus ambientes asignados.');
            }

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.accesorio', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('accesorios.index')->with('error', 'Error al buscar por nombre de accesorio.');
        }
    }

    public function show($cod_accesorio)
    {
        try {
            $accesorio = Accesorio::with(['foto', 'equipos'])->where('cod_accesorio', $cod_accesorio)->firstOrFail();

            // Blindaje IDOR: si el accesorio esta asignado a equipos, verificar que al menos uno pertenezca al coordinador
            $ambientesPermitidos = $this->scopeService->getAmbientesPermitidosIds();
            if ($accesorio->equipos->isNotEmpty()) {
                $hasPermitido = $accesorio->equipos->contains(function ($eq) use ($ambientesPermitidos) {
                    return in_array((int) $eq->AMBIENTE_id_ambiente, $ambientesPermitidos, true);
                });
                if (!$hasPermitido) {
                    abort(403, 'Acceso denegado a este accesorio.');
                }
            }

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.detallesAccesorio', compact('accesorio', 'edificios'));
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            return redirect()->route('accesorios.index')->with('error', 'Error al mostrar los detalles del accesorio.');
        }
    }

    // Metodos para contexto Clinica
    public function obtenerAccesoriosClinica()
    {
        try {
            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();

            $accesorios = $this->getAccesoriosQuery($edificioId)->paginate(10);
            $edificios = $edificiosPermitidos;

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la pagina accesorios.');
        }
    }

    public function accesoriosEquipo()
    {
        try {
            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioId);

            $accesorios = Accesorio::with(['foto', 'equipos'])
                ->whereHas('equipos', function ($eq) use ($ambientesIds) {
                    $eq->whereIn('AMBIENTE_id_ambiente', $ambientesIds);
                })
                ->paginate(10);

            $edificios = $edificiosPermitidos;

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error al obtener los accesorios con equipos.');
        }
    }

    public function accesoriosUnicosClin()
    {
        try {
            $accesorios = Accesorio::with(['foto', 'equipos'])
                ->whereDoesntHave('equipos')
                ->paginate(10);

            $edificios = $this->scopeService->getEdificiosPermitidos();

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error al obtener los accesorios unicos.');
        }
    }

    public function buscarCodigoCli(Request $request)
    {
        try {
            $codigo = $request->input('codigo');
            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();

            $accesorios = $this->getAccesoriosQuery($edificioId)
                ->where('cod_accesorio', 'LIKE', '%' . $codigo . '%')
                ->paginate(10);

            if ($accesorios->isEmpty()) {
                return redirect()->route('clinica.accesorios.index')->with('errorbuscar', 'El codigo ingresado no esta registrado en sus ambientes asignados.');
            }

            $edificios = $edificiosPermitidos;

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error al buscar por codigo de accesorio.');
        }
    }

    public function buscarNombreCli(Request $request)
    {
        try {
            $nombre = $request->input('nombre');
            $edificiosPermitidos = $this->scopeService->getEdificiosPermitidos();
            $edificioClinica = $edificiosPermitidos->first(fn($e) => str_contains(mb_strtolower($e->nombre_edi), 'clinica'));
            $edificioId = $edificioClinica ? (int) $edificioClinica->id_edificio : $this->scopeService->getEdificioActivoId();

            $accesorios = $this->getAccesoriosQuery($edificioId)
                ->where('nombre_acce', 'LIKE', '%' . $nombre . '%')
                ->paginate(10);

            if ($accesorios->isEmpty()) {
                return redirect()->route('clinica.accesorios.index')->with('errorbuscar', 'El nombre a buscar no se encuentra.');
            }

            $edificios = $edificiosPermitidos;

            return view('coordinator.clinicas.accesorios', compact('accesorios', 'edificios'));
        } catch (\Exception $e) {
            return redirect()->route('clinica.accesorios.index')->with('error', 'Error al buscar por nombre de accesorio.');
        }
    }

    public function mostrar($cod_accesorio)
    {
        return $this->show($cod_accesorio);
    }
}
