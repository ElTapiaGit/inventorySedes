<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Services\CoordinatorScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class BuscarLaboController extends Controller
{
    protected CoordinatorScopeService $scopeService;

    public function __construct(CoordinatorScopeService $scopeService)
    {
        $this->scopeService = $scopeService;
    }

    public function index()
    {
        try {
            $edificios = $this->scopeService->getEdificiosPermitidos();
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioActivoId);

            $laboratorios = DB::table('ambiente')
                ->whereIn('id_ambiente', $ambientesIds)
                ->select('id_ambiente', 'nombre')
                ->orderBy('nombre')
                ->get();

            return view('coordinator.laboratorios', compact('edificios', 'laboratorios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Ocurrio un error al cargar los laboratorios.');
        }
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'Laboratorio' => 'required|string|max:45',
        ]);

        try {
            $nombre = $request->input('Laboratorio');
            $edificioActivoId = $this->scopeService->getEdificioActivoId();
            $ambientesIds = $this->scopeService->getAmbientesPermitidosIds($edificioActivoId);

            $laboratorio = DB::table('ambiente')
                ->whereIn('id_ambiente', $ambientesIds)
                ->where('nombre', $nombre)
                ->select('id_ambiente')
                ->first();

            if (!$laboratorio) {
                return redirect()->route('laboratorios.index')->with('errorbuscar', 'El laboratorio que busca no fue encontrado en los ambientes asignados.');
            }

            $encryptedId = Crypt::encrypt($laboratorio->id_ambiente);
            return redirect()->route('ambiente.show', ['id' => $encryptedId]);
        } catch (\Exception $e) {
            return redirect()->route('coordinator.inicio')->with('errordata', 'Ocurrio un error al buscar el laboratorio.');
        }
    }

    public function buscarRapido()
    {
        try {
            $edificios = $this->scopeService->getEdificiosPermitidos();
            return view('coordinator.niveles', compact('edificios'));
        } catch (\Exception $e) {
            return redirect()->route('coordinator.clinica.inicio')->with('errordata', 'Error al obtener los datos para la busqueda rapida.');
        }
    }

    /**
     * Metodo privado reutilizable para busquedas seguras restringidas a ambientes permitidos.
     */
    private function buscarElemento($tabla, $campoCodigo, $codigo, $rutaRedireccion, $encriptar = true)
    {
        try {
            $ambientesPermitidosIds = $this->scopeService->getAmbientesPermitidosIds();

            if ($tabla === 'accesorio') {
                $elemento = DB::table('accesorio')
                    ->leftJoin('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
                    ->leftJoin('equipo', 'equipo_has_accesorio.EQUIPO_cod_equipo', '=', 'equipo.cod_equipo')
                    ->where('accesorio.' . $campoCodigo, $codigo)
                    ->where(function ($q) use ($ambientesPermitidosIds) {
                        $q->whereIn('equipo.AMBIENTE_id_ambiente', $ambientesPermitidosIds)
                          ->orWhereNull('equipo.AMBIENTE_id_ambiente');
                    })
                    ->select('accesorio.cod_accesorio')
                    ->first();
            } else {
                $elemento = DB::table($tabla)
                    ->where($campoCodigo, $codigo)
                    ->whereIn('AMBIENTE_id_ambiente', $ambientesPermitidosIds)
                    ->first();
            }

            if ($elemento) {
                $param = $encriptar ? Crypt::encrypt($codigo) : $codigo;
                return redirect()->route($rutaRedireccion, ['token' => $param]);
            }

            return redirect()->back()->with('errorbuscar', 'El codigo ingresado no es valido o no existe en los ambientes asignados.');
        } catch (\Exception $e) {
            return redirect()->back()->with('errorbuscar', 'Hubo un error al procesar la busqueda. Verifique los campos o intente mas tarde.');
        }
    }

    public function buscarEquipo(Request $request)
    {
        return $this->buscarElemento('equipo', 'cod_equipo', $request->input('codigo'), 'clinica.equipo.detalles');
    }

    public function buscarMobiliario(Request $request)
    {
        return $this->buscarElemento('mobiliario', 'cod_mueble', $request->input('codigo'), 'clinica.mobiliario.detalles');
    }

    public function buscarMaterial(Request $request)
    {
        return $this->buscarElemento('material', 'cod_mate', $request->input('codigo'), 'clinica.material.detalles');
    }

    public function buscarAccesorio(Request $request)
    {
        $codigo = $request->input('codigo');
        return $this->buscarElemento('accesorio', 'cod_accesorio', $codigo, 'clinica.accesorios.show', false);
    }
}
