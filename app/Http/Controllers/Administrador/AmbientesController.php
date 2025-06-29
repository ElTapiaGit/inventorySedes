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
    
            // Obtener el nombre de la sede seleccionada
            $nombreEdificioSeleccionada = Edificio::find($edificioSeleccionado)->nombre_edi;
    
            // Obtener los pisos del edificio seleccionado
            $pisos = Piso::where('Edificio_id_edificio', $edificioSeleccionado)->get();
    
            // Obtener el término de búsqueda por nombre y número de piso, si existen
            $search = $request->input('search');
            $pisoSeleccionado = $request->input('piso');
    
            // Construir la consulta para obtener los ambientes
            $query = Ambiente::join('tipo_ambiente', 'ambiente.TIPO_AMBIENTE_id_ambiente', '=', 'tipo_ambiente.id_tipoambiente')
            ->join('piso', 'ambiente.PISO_id_piso', '=', 'piso.id_piso')
            ->whereIn('ambiente.PISO_id_piso', $pisos->pluck('id_piso'));
    
            if ($search) {//para buscar por nombre no importa se es mayuscula o miniscula
                $query->where('ambiente.nombre', 'like', "%{$search}%");
            }
            
            if ($pisoSeleccionado) {
                $query->where('piso.numero_piso', $pisoSeleccionado);
            }
    
            $ambientes = $query->select('id_ambiente', 'tipo_ambiente.nombre_amb', 'ambiente.nombre', 'ambiente.descripcion_amb', 'piso.numero_piso')->get();
    
            // Verificar si no hay ambientes
            $noHayAmbientes = $ambientes->isEmpty();
            $noAmbientesEncontrados = $search && $ambientes->isEmpty();
    
            // Obtener todas las sedes
            $sedes = Sede::all();
    
            return view('Administrator.ambiente', compact(
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
       }catch (\Exception $e) {
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para la pagina ambientes: ');
        }
    }
}
