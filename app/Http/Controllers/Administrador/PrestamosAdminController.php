<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Prestamo;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\Usuario;
use App\Models\Personal;
use App\Models\Devolucion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PrestamosAdminController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Obtener todas las sedes
            $sedes = Sede::all();

            // Obtener la sede seleccionada, por defecto la sede central (id_sede = 1) o la primera disponible
            $sedeIdParam = $request->input('sede');
            $sede = $sedeIdParam ? Sede::find($sedeIdParam) : (Sede::find(1) ?? $sedes->first());
            $sedeSeleccionada = $sede?->id_sede;
            $nombreSedeSeleccionada = $sede?->nombre ?? 'Sin Sede';

            // Obtener los edificios de la sede seleccionada
            $edificios = $sedeSeleccionada ? Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get() : collect();

            // Obtener el edificio seleccionado
            $edificioSeleccionado = $request->input('edificio');
            if (!$edificioSeleccionado && $edificios->isNotEmpty()) {
                $edificioSeleccionado = $edificios->first()?->id_edificio;
            }

            // Obtener el nombre del edificio seleccionado
            $edificio = $edificioSeleccionado ? Edificio::find($edificioSeleccionado) : null;
            $nombreEdificioSeleccionada = $edificio?->nombre_edi ?? 'Sin Edificio';

            // Obtener los nombres de los solicitantes
            $nombresSolicitantes = Prestamo::select('nombre_solicitante')->distinct()->get();

            // Obtener los nombres completos de los encargados (personal)
            $encargado = Personal::all()->pluck('nombre_completo');

            // Obtener las fechas de prestamo y de devolucion
            if ($edificioSeleccionado) {
                $prestamos = Prestamo::with(['personal', 'devolucion'])
                    ->porEdificio((int) $edificioSeleccionado)
                    ->orderBy('prestamo.fch_prestamo', 'desc')
                    ->paginate(10);
            } else {
                $prestamos = Prestamo::whereRaw('1 = 0')->paginate(10);
            }

            return view('administrator.prestamosAdmin', compact(
                'prestamos',
                'sedes',
                'nombreSedeSeleccionada',
                'edificios',
                'edificioSeleccionado',
                'sedeSeleccionada',
                'encargado',
                'nombreEdificioSeleccionada',
                'nombresSolicitantes'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener datos para la pagina prestamos o No hay datos de prestamos en la BD ');
        }
    }

    public function buscar(Request $request)
    {
        try {
            // Obtener todas las sedes
            $sedes = Sede::all();

            // Obtener la sede central o primera disponible
            $sede = Sede::find(1) ?? $sedes->first();
            $sedeSeleccionada = $sede?->id_sede;
            $nombreSedeSeleccionada = $sede?->nombre ?? 'Sin Sede';

            // Obtener los edificios de la sede seleccionada
            $edificios = $sedeSeleccionada ? Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get() : collect();

            // Obtener el edificio seleccionado
            $edificioSeleccionado = $request->input('edificio');
            if (!$edificioSeleccionado && $edificios->isNotEmpty()) {
                $edificioSeleccionado = $edificios->first()?->id_edificio;
            }

            // Obtener el nombre del edificio seleccionado
            $edificio = $edificioSeleccionado ? Edificio::find($edificioSeleccionado) : null;
            $nombreEdificioSeleccionada = $edificio?->nombre_edi ?? 'Sin Edificio';

            // Construir la consulta de busqueda
            $query = Prestamo::with(['personal', 'devolucion']);
            
            if ($edificioSeleccionado) {
                $query->porEdificio((int) $edificioSeleccionado);
            }

            // Filtrar por nombre de usuario
            if ($request->filled('usuario')) {
                $query->where('nombre_solicitante', 'like', '%' . $request->input('usuario') . '%');
            }

            // Filtrar por rango de fechas (ano y mes)
            if ($request->filled('fecha')) {
                try {
                    $fecha = Carbon::createFromFormat('Y-m-d', $request->input('fecha'));
                    $inicioMes = $fecha->startOfMonth()->format('Y-m-d');
                    $finMes = $fecha->endOfMonth()->format('Y-m-d');
                    $query->whereBetween('fch_prestamo', [$inicioMes, $finMes]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('errorbuscar', 'Formato de fecha incorrecto. Use yy-mm-dd.');
                }
            }

            // Ejecutar la consulta y obtener los resultados paginados
            $prestamos = $query->paginate(10);

            if ($prestamos->isEmpty()) {
                return redirect()->back()->with('errorbuscar', 'No se encontraron resultados.');
            }

            return view('administrator.prestamosAdmin', compact(
                'prestamos',
                'nombreSedeSeleccionada',
                'nombreEdificioSeleccionada',
                'sedes',
                'sedeSeleccionada',
                'edificios',
                'edificioSeleccionado'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener datos para la pagina prestamos: ');
        }
    }
}
