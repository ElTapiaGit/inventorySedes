<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\TipoPersonal;
use App\Models\Sede;
use App\Models\Edificio;


class InhabilitadosController extends Controller
{
    //
    public function index()
    {
        try{
            $personales = Personal::with(['tipoPersonal', 'edificio.sede'])
            ->where('estado', 0)
            ->get();

            return view('administrator.personalInhabilitado', compact('personales'));
        }catch(\Exception $e){
            return redirect()->route('admin.index')->with('errordata', 'Error al obtener los datos para la pagiana de personal inhabilitados:');
        }
    }

    public function reactivar($id)
    {
        TRY{
            $personal = Personal::findOrFail($id);
            $personal->estado = 1;
            $personal->save();

            return response()->json(['success' => true]);
        }catch(\Exception $e){
            return redirect()->route('admin.index')->with('Error', 'Error al reactivar al personal :');
        }
    }
}
