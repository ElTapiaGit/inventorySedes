<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Edificio;
use App\Models\Piso;
use App\Models\Ambiente;
use Illuminate\Support\Facades\Crypt;

class AmbientesController extends Controller
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

            // Obtener los pisos del edificio seleccionado
            $pisos = $edificioSeleccionado ? Piso::where('Edificio_id_edificio', $edificioSeleccionado)->get() : collect();

            // Obtener el termino de busqueda por nombre y numero de piso, si existen
            $search = $request->input('search');
            $pisoSeleccionado = $request->input('piso');

            // Construir la consulta para obtener los ambientes si hay pisos
            if ($pisos->isNotEmpty()) {
                $query = Ambiente::join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
                    ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
                    ->whereIn('ambiente.PISO_id_piso', $pisos->pluck('id_piso'));

                if ($search) {
                    $query->where('ambiente.nombre', 'like', "%{$search}%");
                }
                
                if ($pisoSeleccionado) {
                    $query->where('piso.numero_piso', $pisoSeleccionado);
                }

                $ambientes = $query->select('id_ambiente', 'tipo_ambiente.nombre_amb', 'ambiente.nombre', 'ambiente.descripcion_amb', 'piso.numero_piso')->get();
            } else {
                $ambientes = collect();
            }

            // Verificar si no hay ambientes
            $noHayAmbientes = $ambientes->isEmpty();
            $noAmbientesEncontrados = $search && $ambientes->isEmpty();

            return view('administrator.ambiente', compact(
                'sedes', 
                'nombreSedeSeleccionada', 
                'sedeSeleccionada', 
                'nombreEdificioSeleccionada', 
                'edificios', 
                'edificioSeleccionado', 
                'ambientes', 
                'noHayAmbientes', 
                'search', 
                'noAmbientesEncontrados',
                'pisos', 
                'pisoSeleccionado'
            ));
       } catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para la pagina ambientes: ');
       }
    }
}
