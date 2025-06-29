<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\UsoAmbiente;
use App\Models\FinalUso;

class DetallesMovAdminController extends Controller
{
    //
    public function detalleMivimientoAdmin($id_uso_ambiente)
    {
        try {
            $id_ambiente = Crypt::decrypt($id_uso_ambiente);
        } catch (DecryptException $e) {
            abort(404);
        }

        try{
            // Obtener el movimiento por el ID
            $movimiento = UsoAmbiente::with(['personalInicio', 'personalFin'])
            ->where('id_uso_ambiente', $id_ambiente)
            ->firstOrFail();

            // Obtener los detalles de finalización si existen
            $finalUso = FinalUso::where('USO_AMBIENTE_id_uso_ambiente', $id_ambiente)->first();

            // Obtener el personal de inicio y fin
            $personal_inicio = $movimiento->personalInicio;
            $personal_fin = $movimiento->fch_fin ? $movimiento->personalFin : null;

            return view('Administrator.detallesMoviAdmin', compact('movimiento', 'personal_inicio', 'personal_fin', 'finalUso'));

        } catch(\Exception $e) {
            // Manejo de errores
            return redirect()->back()->withErrors(['error' => 'No se pudo encontrar el detalle del movimiento.']);
        }
        
    }
}
