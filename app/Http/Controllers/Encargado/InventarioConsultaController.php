<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Accesorio;
use App\Models\Ambiente;
use App\Models\Edificio;
use App\Models\Equipo;
use App\Models\Material;
use App\Models\Mobiliario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarioConsultaController extends Controller
{
    /**
     * Listado y filtros de articulos aislados por edificio.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $edificioId = Auth::user()->EDIFICIO_id_edificio;
        $edificio = Edificio::with('sede')->find($edificioId);

        $tab = $request->input('tab', 'equipos');
        if (!in_array($tab, ['equipos', 'mobiliarios', 'materiales', 'accesorios'])) {
            $tab = 'equipos';
        }

        // Ambientes pertenecientes estrictamente al edificio del usuario
        $ambientes = Ambiente::whereHas('piso', function ($q) use ($edificioId) {
            $q->where('EDIFICIO_id_edificio', $edificioId);
        })->orderBy('nombre')->get();

        // Conteos para los badges de las pestanas
        $countEquipos = Equipo::whereHas('ambiente.piso', function ($q) use ($edificioId) {
            $q->where('EDIFICIO_id_edificio', $edificioId);
        })->count();

        $countMobiliarios = Mobiliario::whereHas('ambiente.piso', function ($q) use ($edificioId) {
            $q->where('EDIFICIO_id_edificio', $edificioId);
        })->count();

        $countMateriales = Material::whereHas('ambiente.piso', function ($q) use ($edificioId) {
            $q->where('EDIFICIO_id_edificio', $edificioId);
        })->count();

        $countAccesorios = Accesorio::where(function ($query) use ($edificioId) {
            $query->whereHas('equipos.ambiente.piso', function ($q) use ($edificioId) {
                $q->where('EDIFICIO_id_edificio', $edificioId);
            })->orWhereDoesntHave('equipos');
        })->count();

        // Consulta segun la pestana activa
        switch ($tab) {
            case 'mobiliarios':
                $query = Mobiliario::with(['foto', 'ambiente', 'tipoMobiliario'])
                    ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                        $q->where('EDIFICIO_id_edificio', $edificioId);
                    });

                if ($request->filled('q')) {
                    $search = trim($request->input('q'));
                    $query->where(function ($q) use ($search) {
                        $q->where('cod_mueble', 'like', "%{$search}%")
                          ->orWhere('descripticion_mueb', 'like', "%{$search}%")
                          ->orWhereHas('tipoMobiliario', function ($tm) use ($search) {
                              $tm->where('tipo_mueble', 'like', "%{$search}%");
                          });
                    });
                }

                if ($request->filled('ambiente_id')) {
                    $query->where('AMBIENTE_id_ambiente', $request->input('ambiente_id'));
                }

                if ($request->filled('estado')) {
                    $query->where('estado_mueb', $request->input('estado'));
                }

                $items = $query->orderBy('cod_mueble')->paginate(12)->withQueryString();
                break;

            case 'materiales':
                $query = Material::with(['foto', 'ambiente'])
                    ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                        $q->where('EDIFICIO_id_edificio', $edificioId);
                    });

                if ($request->filled('q')) {
                    $search = trim($request->input('q'));
                    $query->where(function ($q) use ($search) {
                        $q->where('cod_mate', 'like', "%{$search}%")
                          ->orWhere('tipo_mate', 'like', "%{$search}%")
                          ->orWhere('descripcion_mate', 'like', "%{$search}%");
                    });
                }

                if ($request->filled('ambiente_id')) {
                    $query->where('AMBIENTE_id_ambiente', $request->input('ambiente_id'));
                }

                if ($request->filled('estado')) {
                    $query->where('estado_mate', $request->input('estado'));
                }

                $items = $query->orderBy('cod_mate')->paginate(12)->withQueryString();
                break;

            case 'accesorios':
                $query = Accesorio::with(['foto', 'equipos.ambiente'])
                    ->where(function ($query) use ($edificioId) {
                        $query->whereHas('equipos.ambiente.piso', function ($q) use ($edificioId) {
                            $q->where('EDIFICIO_id_edificio', $edificioId);
                        })->orWhereDoesntHave('equipos');
                    });

                if ($request->filled('q')) {
                    $search = trim($request->input('q'));
                    $query->where(function ($q) use ($search) {
                        $q->where('cod_accesorio', 'like', "%{$search}%")
                          ->orWhere('nombre_acce', 'like', "%{$search}%")
                          ->orWhere('descripcion_acce', 'like', "%{$search}%")
                          ->orWhere('ubicacion', 'like', "%{$search}%");
                    });
                }

                if ($request->filled('estado')) {
                    $query->where('estado_acce', $request->input('estado'));
                }

                $items = $query->orderBy('cod_accesorio')->paginate(12)->withQueryString();
                break;

            case 'equipos':
            default:
                $query = Equipo::with(['foto', 'ambiente'])
                    ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                        $q->where('EDIFICIO_id_edificio', $edificioId);
                    });

                if ($request->filled('q')) {
                    $search = trim($request->input('q'));
                    $query->where(function ($q) use ($search) {
                        $q->where('cod_equipo', 'like', "%{$search}%")
                          ->orWhere('nombre_equi', 'like', "%{$search}%")
                          ->orWhere('marca', 'like', "%{$search}%")
                          ->orWhere('modelo', 'like', "%{$search}%")
                          ->orWhere('descripcion_equi', 'like', "%{$search}%");
                    });
                }

                if ($request->filled('ambiente_id')) {
                    $query->where('AMBIENTE_id_ambiente', $request->input('ambiente_id'));
                }

                if ($request->filled('estado')) {
                    $query->where('estado_equi', $request->input('estado'));
                }

                $items = $query->orderBy('cod_equipo')->paginate(12)->withQueryString();
                break;
        }

        return view('encargados.inventario.index', compact(
            'tab',
            'items',
            'ambientes',
            'edificio',
            'countEquipos',
            'countMobiliarios',
            'countMateriales',
            'countAccesorios'
        ));
    }

    /**
     * Muestra la ficha detallada de un articulo verificando aislamiento territorial.
     *
     * @param string $tipo
     * @param string $codigo
     * @return \Illuminate\View\View
     */
    public function show(string $tipo, string $codigo)
    {
        $edificioId = Auth::user()->EDIFICIO_id_edificio;
        $edificio = Edificio::with('sede')->find($edificioId);

        if (!in_array($tipo, ['equipo', 'mobiliario', 'material', 'accesorio'])) {
            abort(404, 'Tipo de articulo no valido.');
        }

        switch ($tipo) {
            case 'equipo':
                $articulo = Equipo::with(['foto', 'ambiente.piso', 'componentes', 'accesorios.foto'])
                    ->where('cod_equipo', $codigo)
                    ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                        $q->where('EDIFICIO_id_edificio', $edificioId);
                    })
                    ->firstOrFail();

                $cambiarFotoRoute = route('equipo.cambiarFoto', $articulo->cod_equipo);
                break;

            case 'mobiliario':
                $articulo = Mobiliario::with(['foto', 'ambiente.piso', 'tipoMobiliario'])
                    ->where('cod_mueble', $codigo)
                    ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                        $q->where('EDIFICIO_id_edificio', $edificioId);
                    })
                    ->firstOrFail();

                $cambiarFotoRoute = route('mobiliario.cambiarFoto', $articulo->cod_mueble);
                break;

            case 'material':
                $articulo = Material::with(['foto', 'ambiente.piso'])
                    ->where('cod_mate', $codigo)
                    ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                        $q->where('EDIFICIO_id_edificio', $edificioId);
                    })
                    ->firstOrFail();

                $cambiarFotoRoute = route('material.cambiarFoto', $articulo->cod_mate);
                break;

            case 'accesorio':
                $articulo = Accesorio::with(['foto', 'equipos.ambiente.piso'])
                    ->where('cod_accesorio', $codigo)
                    ->firstOrFail();

                // Validar que no pertenezca exclusivamente a un equipo de otro edificio
                $equiposOtroEdificio = $articulo->equipos->filter(function ($eq) use ($edificioId) {
                    return $eq->ambiente && $eq->ambiente->piso && $eq->ambiente->piso->EDIFICIO_id_edificio != $edificioId;
                });
                $equiposEsteEdificio = $articulo->equipos->filter(function ($eq) use ($edificioId) {
                    return $eq->ambiente && $eq->ambiente->piso && $eq->ambiente->piso->EDIFICIO_id_edificio == $edificioId;
                });

                if ($equiposOtroEdificio->isNotEmpty() && $equiposEsteEdificio->isEmpty()) {
                    abort(403, 'No tiene permisos para consultar este accesorio.');
                }

                $cambiarFotoRoute = route('accesorio.cambiarFoto', $articulo->cod_accesorio);
                break;
        }

        return view('encargados.inventario.show', compact('tipo', 'articulo', 'cambiarFotoRoute', 'edificio'));
    }
}
