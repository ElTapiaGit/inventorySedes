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
    //
    public function index(Request $request)
    {
        try{
           // Obtener todas las sedes
            $sedes = Sede::all();

            // Obtener la sede seleccionada, por defecto la sede central (id_sede = 1)
            $sedeSeleccionada = $request->input('sede', 1);

            // Obtener el nombre de la sede seleccionada
            $nombreSedeSeleccionada = Sede::find($sedeSeleccionada)->nombre;

            // Obtener los edificios de la sede seleccionada
            $edificios = Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get();

            // Si no hay edificios, mostrar mensaje y redirigir
            if ($edificios->isEmpty()) {
                return redirect()->route('prestamosAdmin.index')->with('info', 'La sede seleccionada no tiene edificios con préstamos.');
            }

            // Obtener el edificio seleccionado, por defecto el primer edificio de la sede
            $edificioSeleccionado = $request->input('edificio', $edificios->first()->id_edificio);

            // Obtener el nombre del edificio seleccionado
            $nombreEdificioSeleccionada = Edificio::find($edificioSeleccionado)->nombre_edi;

            // Obtener los nombres de los solicitantes
            $nombresSolicitantes = Prestamo::select('nombre_solicitante')->distinct()->get();

            // Obtener los nombres completos de los encargados (personal)
            $encargado = Personal::all()->pluck('nombre_completo');

            // Obtener las fechas de préstamo y de devolución
            $prestamos = Prestamo::with(['personal', 'devolucion'])//para obtener la relaciones de la tabla
                ->whereHas('personal', function ($query) use ($edificioSeleccionado) {
                    $query->where('EDIFICIO_id_edificio', $edificioSeleccionado);
                })
                ->orderBy('prestamo.fch_prestamo', 'desc') // Ordenar por fecha de uso en orden descendente
                ->paginate(10);


            return view('Administrator.prestamosAdmin', compact(
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
        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener datos para la pagina prestamos o No hay datos de prestamos en la BD ');
        }
    }

    public function buscar(Request $request)
    {
        try{
            // Obtener todas las sedes
            $sedes = Sede::all();

            // Obtener la sede central
            $sedeSeleccionada = 1;

            // Obtener el nombre de la sede seleccionada
            $nombreSedeSeleccionada = Sede::find($sedeSeleccionada)->nombre;

            // Obtener los edificios de la sede seleccionada
            $edificios = Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get();

            // Obtener el edificio seleccionado, por defecto el primer edificio de la sede
            $edificioSeleccionado = $request->input('edificio', $edificios->first()->id_edificio);

            // Obtener el nombre del edificio seleccionado
            $nombreEdificioSeleccionada = Edificio::find($edificioSeleccionado)->nombre_edi;

            // Construir la consulta de búsqueda
            $query = Prestamo::with(['personal', 'devolucion'])
                ->whereHas('personal', function ($query) use ($edificioSeleccionado) {
                    $query->where('EDIFICIO_id_edificio', $edificioSeleccionado);
                });

            // Filtrar por nombre de usuario
            if ($request->filled('usuario')) {
                $query->where('nombre_solicitante', 'like', '%' . $request->input('usuario') . '%');
            }

            // Filtrar por rango de fechas (año y mes)
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

            return view('Administrator.prestamosAdmin', compact(
                'prestamos',
                'nombreSedeSeleccionada',
                'nombreEdificioSeleccionada',
                'sedes',
                'sedeSeleccionada',
                'edificios',
                'edificioSeleccionado'
            ));
        }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener datos para la pagina prestamos: ');
        }
    }
}
