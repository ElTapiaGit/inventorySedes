<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\UsoAmbiente;
use App\Models\Personal;
use Illuminate\Support\Facades\Crypt;

class MovimientosAdminController extends Controller
{
    public function index(Request $request)
    {
        try {
            $sedes = Sede::all();

            // Desencriptar la sede seleccionada (con un valor por defecto en caso de que no se seleccione ninguna)
            $sedeIdParam = $request->input('sede') ? Crypt::decryptString($request->input('sede')) : null;
            $sede = $sedeIdParam ? Sede::find($sedeIdParam) : (Sede::find(1) ?? $sedes->first());
            $sedeSeleccionada = $sede?->id_sede;
            $nombreSedeSeleccionada = $sede?->nombre ?? 'Sin Sede';

            // Obtener los edificios de la sede seleccionada
            $edificios = $sedeSeleccionada ? Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get() : collect();

            // Desencriptar el edificio seleccionado (con un valor por defecto en caso de que no se seleccione ninguno)
            $edificioSeleccionado = null;
            if ($request->input('edificio')) {
                $edificioSeleccionado = Crypt::decryptString($request->input('edificio'));
            } elseif ($edificios->isNotEmpty()) {
                $edificioSeleccionado = $edificios->first()?->id_edificio;
            }

            // Obtener el nombre del edificio seleccionado
            $edificio = $edificioSeleccionado ? Edificio::find($edificioSeleccionado) : null;
            $nombreEdificioSeleccionada = $edificio?->nombre_edi ?? 'Sin Edificio';

            // Obtener los movimientos filtrados por edificio seleccionado si existe
            if ($edificioSeleccionado) {
                $movimientos = UsoAmbiente::join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                    ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                    ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                    ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                    ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente')
                    ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
                    ->select(
                        'ambiente.nombre as nombre_ambiente',
                        DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
                        'uso_ambiente.descripcion',
                        'uso_ambiente.semestre',
                        'uso_ambiente.fch_uso',
                        'uso_ambiente.hora_uso',
                        'uso_ambiente.id_uso_ambiente',
                        'final_uso.fch_fin'
                    )
                    ->where('edificio.id_edificio', $edificioSeleccionado)
                    ->orderBy('uso_ambiente.fch_uso', 'desc')
                    ->paginate(10);
            } else {
                $movimientos = UsoAmbiente::whereRaw('1 = 0')->paginate(10);
            }

            // Obtener todas las fechas y usuarios para los datalists
            $nombresUsuarios = Personal::select('nombre')->distinct()->get();
            $fechas = UsoAmbiente::select('fch_uso')->distinct()->get();

            return view('administrator.movimientosAdmin', compact(
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
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para los movimientos de ambientes: ');
        }
    }

    public function buscar(Request $request)
    {
        try {
            $sedes = Sede::all();

            // Desencriptar la sede seleccionada (con un valor por defecto en caso de que no se seleccione ninguna)
            $sedeIdParam = $request->input('sede') ? Crypt::decryptString($request->input('sede')) : null;
            $sede = $sedeIdParam ? Sede::find($sedeIdParam) : (Sede::find(1) ?? $sedes->first());
            $sedeSeleccionada = $sede?->id_sede;
            $nombreSedeSeleccionada = $sede?->nombre ?? 'Sin Sede';

            // Obtener los edificios de la sede seleccionada
            $edificios = $sedeSeleccionada ? Edificio::where('SEDE_id_sede', $sedeSeleccionada)->get() : collect();

            // Desencriptar el edificio seleccionado (con un valor por defecto en caso de que no se seleccione ninguno)
            $edificioSeleccionado = null;
            if ($request->input('edificio')) {
                $edificioSeleccionado = Crypt::decryptString($request->input('edificio'));
            } elseif ($edificios->isNotEmpty()) {
                $edificioSeleccionado = $edificios->first()?->id_edificio;
            }

            // Obtener el nombre del edificio seleccionado
            $edificio = $edificioSeleccionado ? Edificio::find($edificioSeleccionado) : null;
            $nombreEdificioSeleccionada = $edificio?->nombre_edi ?? 'Sin Edificio';

            $query = UsoAmbiente::query()
                ->join('ambiente', 'uso_ambiente.AMBIENTE_id_ambiente', '=', 'ambiente.id_ambiente')
                ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                ->join('edificio', 'piso.Edificio_id_edificio', '=', 'edificio.id_edificio')
                ->join('sede', 'edificio.SEDE_id_sede', '=', 'sede.id_sede')
                ->leftJoin('final_uso', 'uso_ambiente.id_uso_ambiente', '=', 'final_uso.USO_AMBIENTE_id_uso_ambiente')
                ->join('usuario', 'uso_ambiente.USUARIO_id_usuario', '=', 'usuario.id_usuario')
                ->select(
                    'ambiente.nombre as nombre_ambiente',
                    DB::raw("CONCAT(usuario.nombre, ' ', usuario.apellidos) AS nombre_usuario"),
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
                    $fechaInicio = Carbon::createFromFormat('d-m-Y', $request->input('fecha_inicio'))->format('Y-m-d');
                    $query->where('uso_ambiente.fch_uso', $fechaInicio);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error_fecha', 'Formato de fecha incorrecto. Use dd-mm-aa.');
                }
            }

            $movimientos = $query->paginate(10);

            if ($movimientos->isEmpty()) {
                return redirect()->back()->with('error_usuario', 'No se encontraron resultados.');
            }

            // Obtener la sede y el edificio correspondientes a los resultados de la busqueda, si estan disponibles
            $sedeSeleccionada = $movimientos->first()->sede->id_sede ?? $sedeSeleccionada;
            $sedeEncontrada = Sede::find($sedeSeleccionada);
            $nombreSedeSeleccionada = $sedeEncontrada?->nombre ?? $nombreSedeSeleccionada;

            $edificioSeleccionado = $movimientos->first()->edificio->id_edificio ?? $edificioSeleccionado;
            $edificioEncontrado = Edificio::find($edificioSeleccionado);
            $nombreEdificioSeleccionada = $edificioEncontrado?->nombre_edi ?? $nombreEdificioSeleccionada;

            return view('administrator.movimientosAdmin', compact(
                'movimientos',
                'sedes',
                'edificios',
                'sedeSeleccionada',
                'nombreSedeSeleccionada',
                'edificioSeleccionado',
                'nombreEdificioSeleccionada'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para la busqueda: ');
        }
    }
}
