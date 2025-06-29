<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Accesorio;
use App\Models\HistorialAccesorio;

class AccesoriosController extends Controller
{
    //
    public function index(Request $request)
    {
        try{
            // Obtener accesorios que están 'para reponer' o 'para cambiar'
            $accesorios = Accesorio::whereIn('estado_acce', ['para reponer', 'para cambiar'])->paginate(10);

            // Recorrer cada accesorio para agregar información adicional
            foreach ($accesorios as $accesorio) {
                // Obtener el último cambio del accesorio
                $ultimoCambio = HistorialAccesorio::where('ACCESORIO_cod_accesorio', $accesorio->cod_accesorio)
                                                ->orderBy('fch_cambio', 'desc')
                                                ->first();
                // Asignar fecha de último cambio o "Sin cambios"
                $accesorio->fecha_ultimo_cambio = $ultimoCambio ? $ultimoCambio->fch_cambio : 'Sin cambios';

                // Verificar si el accesorio pertenece a un equipo
                // Obtener equipos asociados al accesorio
                $equipos = $accesorio->equipos;

                // Asignar código del equipo o "Sin equipo"
                $accesorio->equipo_codigo = $equipos->isEmpty() ? 'Sin equipo' : $equipos->pluck('cod_equipo')->implode(', ');
            }

            return view('administrator.accesorios', compact('accesorios'));
        }catch (\Exception $e) {
            // Manejo de excepciones
            return redirect()->route('admin.index')->with('errordata', 'broblemas para acceder a la pagina de accesorios.');
        }
    }

    public function accesoriosUnicos(Request $request)
    {
        try{
            // Filtros de búsqueda
            $codigo = $request->input('codigo');
            $nombre = $request->input('nombre');

            // Consulta básica
            $query = Accesorio::query();

            // Aplicar filtros si existen
            if ($codigo) {
                $query->where('cod_accesorio', 'like', '%' . $codigo . '%');
            }

            if ($nombre) {
                $query->where('nombre_acce', 'like', '%' . $nombre . '%');
            }

            // Filtrar los accesorios que no están asociados a ningún equipo
            $query->leftJoin('equipo_has_accesorio', 'accesorio.cod_accesorio', '=', 'equipo_has_accesorio.ACCESORIO_cod_accesorio')
                ->whereNull('equipo_has_accesorio.EQUIPO_cod_equipo')
                ->select('accesorio.*'); // Asegurar que solo se seleccionen columnas de la tabla "accesorio"

            
            // Obtener los accesorios únicos con paginación
            $accesorios = $query->paginate(10);

            return view('administrator.accesoriosUnicos', compact('accesorios', 'codigo', 'nombre'));
        }catch (\Exception $e) {
            // Manejo de excepciones
            return redirect()->route('admin.index')->with('errordata', 'No hay resultados, verifique los campos a buscar.');
        }
    }

    public function accesoriosConEquipo(Request $request)
    {
        try{
            // Filtros de búsqueda
            $codigo = $request->input('codigo');
            $nombre = $request->input('nombre');

            // Consulta básica con accesorios que tienen equipos asociados
            $query = Accesorio::whereHas('equipos');

            // Aplicar filtros si existen
            if ($codigo) {
                $query->where('cod_accesorio', 'like', '%' . $codigo . '%');
            }

            if ($nombre) {
                $query->where('nombre_acce', 'like', '%' . $nombre . '%');
            }

            // Obtener los accesorios con equipos con paginación
            $accesorios = $query->paginate(10);

            return view('administrator.accesoriosConEquipo', compact('accesorios', 'codigo', 'nombre'));
        }catch (\Exception $e) {
            // Manejo de excepciones
            return redirect()->route('admin.index')->with('errordata', 'No hay resultados, verifique los campos a buscar.');
        }
    }
    //para detalles de accesorios
    public function show($codigo)
    {
        try {
            // Desencriptar el código del accesorio
            $codigo = Crypt::decrypt($codigo);

            // Obtener el accesorio junto con el historial y los equipos
            $accesorio = Accesorio::with('equipos', 'foto')->findOrFail($codigo);

            // Obtener el historial del accesorio, ordenado por fecha de cambio descendente
            $historial = HistorialAccesorio::where('ACCESORIO_cod_accesorio', $codigo)
                                        ->orderBy('fch_cambio', 'desc')
                                        ->limit(5)//limita solo a los 5 ultimos registros
                                        ->get();

            // Obtener el equipo asociado si existe
            $equipo = $accesorio->equipos->first(); // usando el equipos del metodo para equipo_has_accesorio

            return view('administrator.detallesAccesorio', compact('accesorio', 'historial', 'equipo'));
        } catch (\Exception $e) {
            // Manejo de excepciones
            return redirect()->route('administrator.accesoriosUnicos')->with('error', 'Accesorio no encontrado o código inválido.');
        }
    }
}
