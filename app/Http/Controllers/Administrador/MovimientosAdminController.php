<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/*  es una biblioteca de PHP que se extiende sobre la clase DateTime de PHP y proporciona una interfaz 
    más amigable y métodos adicionales para manejar y manipular fechas y tiempos de manera más sencilla y expresiva. */
use Carbon\Carbon;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\UsoAmbiente;
use App\Models\Personal;
use Illuminate\Support\Facades\Crypt;

class MovimientosAdminController extends Controller
{
    //
    public function index(Request $request)
    {
        try{
            // Desencriptar la sede seleccionada (con un valor por defecto en caso de que no se seleccione ninguna)
            $sedeSeleccionada = $request->input('sede') ? Crypt::decryptString($request->input('sede')) : 1;

            // Obtener el nombre de la sede seleccionada
            $nombreSedeSeleccionada = Sede::find($sedeSeleccionada)->nombre;

            // Obtener los edificios de la sede seleccionada
            $edificios = Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get();

            // Verificar si hay edificios en la sede seleccionada
            if ($edificios->isEmpty()) {
                return redirect()->route('ambiente.index')->with('error', 'No hay edificios disponibles para la sede seleccionada.');
            }

            // Desencriptar el edificio seleccionado (con un valor por defecto en caso de que no se seleccione ninguno)
            $edificioSeleccionado = $request->input('edificio') ? Crypt::decryptString($request->input('edificio')) : $edificios->first()->id_edificio;

            // Obtener el nombre del edificio seleccionado
            $nombreEdificioSeleccionada = Edificio::find($edificioSeleccionado)->nombre_edi;

            // Obtener los movimientos filtrados por sede y edificio seleccionados
            $movimientos = UsoAmbiente::join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
            ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
            ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente') // Left join para obtener registros sin fecha de fin
            ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
            ->select(
                'ambiente.nombre as nombre_ambiente',
                DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),//para obtener nombre completo
                'uso_ambiente.descripcion',
                'uso_ambiente.semestre',
                'uso_ambiente.fch_uso',
                'uso_ambiente.hora_uso',
                'uso_ambiente.id_uso_ambiente',
                'final_uso.fch_fin',
            )
            ->where('edificio.id_edificio', $edificioSeleccionado)
            ->orderBy('uso_ambiente.fch_uso', 'desc') // Ordenar por fecha de uso en orden descendente
            ->paginate(10); // Paginación de 10 registros por página

            // Obtener todas las sedes
            $sedes = Sede::all();

            // Obtener todas las fechas y usuarios para los datalists
            $nombresUsuarios = Personal::select('nombre')->distinct()->get();
            $fechas = UsoAmbiente::select('fch_uso')->distinct()->get();

            return view('Administrator.movimientosAdmin', compact(
                'sedes', 
                'nombreSedeSeleccionada', 
                'sedeSeleccionada', 
                'nombreEdificioSeleccionada', 
                'edificios', 
                'edificioSeleccionado', 
                'movimientos', 
                'nombresUsuarios', 
                'fechas'
            ));
        }catch (\Exception $e) {
            return redirect()->route('movimientosAdmin.index')->with('errordata', 'Error al obtener los datos para los movimientos de ambientes: ');
        }
    }

    public function buscar(Request $request)
    {
        try{
            // Desencriptar la sede seleccionada (con un valor por defecto en caso de que no se seleccione ninguna)
            $sedeSeleccionada = $request->input('sede') ? Crypt::decryptString($request->input('sede')) : 1;

            // Obtener el nombre de la sede seleccionada
            $nombreSedeSeleccionada = Sede::find($sedeSeleccionada)->nombre;

            // Obtener los edificios de la sede seleccionada
            $edificios = Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get();

            // Desencriptar el edificio seleccionado (con un valor por defecto en caso de que no se seleccione ninguno)
            $edificioSeleccionado = $request->input('edificio') ? Crypt::decryptString($request->input('edificio')) : $edificios->first()->id_edificio;

            // Obtener el nombre del edificio seleccionado
            $nombreEdificioSeleccionada = Edificio::find($edificioSeleccionado)->nombre_edi;

            // Obtener todas las sedes
            $sedes = Sede::all();

            $query = UsoAmbiente::query()
                ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente')
                ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
                ->select(
                    'ambiente.nombre as nombre_ambiente',
                    DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"), // Para obtener nombre completo
                    'uso_ambiente.descripcion',
                    'uso_ambiente.semestre',
                    'uso_ambiente.fch_uso',
                    'uso_ambiente.hora_uso',
                    'uso_ambiente.id_uso_ambiente',
                    'final_uso.fch_fin'
                ); 

            if ($request->filled('nombre_usuario')) {
                $query->where(DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos)"), 'like', '%' . $request->input('nombre_usuario') . '%');
            }

            if ($request->filled('fecha_inicio')) {
                try {
                    $fechaInicio =  Carbon::createFromFormat('d-m-Y', $request->input('fecha_inicio'))->format('Y-m-d');
                    $query->where('uso_ambiente.fch_uso', $fechaInicio);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error_fecha', 'Formato de fecha incorrecto. Use dd-mm-aa.');
                }
            }

            $movimientos = $query->paginate(10);

            if ($movimientos->isEmpty()) {
                return redirect()->back()->with('error_usuario', 'No se encontraron resultados.');
            }

            // Obtener la sede y el edificio correspondientes a los resultados de la búsqueda, si están disponibles
            $sedeSeleccionada = $movimientos->first()->sede->id_sede ?? $sedeSeleccionada;
            $nombreSedeSeleccionada = Sede::find($sedeSeleccionada)->nombre;
            $edificioSeleccionado = $movimientos->first()->edificio->id_edificio ?? $edificioSeleccionado;
            $nombreEdificioSeleccionada = Edificio::find($edificioSeleccionado)->nombre_edi;

            return view('Administrator.movimientosAdmin', compact(
                'movimientos',
                'sedes',
                'edificios',
                'sedeSeleccionada',
                'nombreSedeSeleccionada',
                'edificioSeleccionado',
                'nombreEdificioSeleccionada'
            ));
        }catch (\Exception $e) {
            return redirect()->route('movimientosAdmin.index')->with('errordata', 'Error al obtener los datos para la busqueda: ');
        }
    }
}
