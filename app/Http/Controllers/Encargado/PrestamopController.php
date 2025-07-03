<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\DetallePrestamo;
use App\Models\Personal;
use App\Models\Ambiente;
use App\Models\Devolucion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PrestamopController extends Controller
{
    //
    public function index()
    {
        $prestamos = Prestamo::with(['personal', 'devolucion'])->latest('id_prestamo')->paginate(10);
        $personales = Personal::all();

        return view('encargados.prestamoPag', compact('prestamos', 'personales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_solicitante' => 'required|string',
            'descripcion_prestamo' => 'required|string',
            'fch_prestamo' => 'required|date',
            'hora_prestamo' => 'required',
            'PERSONAL_id_personal' => 'required|exists:personal,id_personal',
            'cod_articulo' => 'required|array',
            'cod_articulo.*' => 'required|string',
            'observacion_detalle' => 'nullable|array'
        ]);

        try {
            $prestamo = Prestamo::create([
                'nombre_solicitante' => $request->nombre_solicitante,
                'descripcion_prestamo' => $request->descripcion_prestamo,
                'fch_prestamo' => $request->fch_prestamo,
                'hora_prestamo' => $request->hora_prestamo,
                'PERSONAL_id_personal' => $request->PERSONAL_id_personal,
            ]);

            foreach ($request->cod_articulo as $index => $codigo) {
                DetallePrestamo::create([
                    'cod_articulo' => $codigo,
                    'observacion_detalle' => $request->observacion_detalle[$index] ?? 'Sin observaciones previas al préstamo', 
                    'PRESTAMO_id_prestamo' => $prestamo->id_prestamo,
                ]);
            }

            return redirect()->route('encargado.prestamo')->with('success', 'Préstamo registrado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.prestamo')->with('error', 'Ocurrió un error al registrar el préstamo. Intente nuevamente.');
        }
    }

    public function show($id)
    {
        $idDesencriptado = Crypt::decrypt($id);

        $prestamo = Prestamo::with(['detallePrestamos', 'personal', 'devolucion'])->findOrFail($idDesencriptado);

        return view('encargados.detallePrestamos', compact('prestamo'));
    }

    public function registrarDevolucion(Request $request, $id)
    {
        $request->validate([
            'fch_devolucion' => 'required|date',
            'hora_devolucion' => 'required',
            'descripcion_devolucion' => 'nullable|string',
            'PERSONAL_id_personal' => 'required|exists:personal,id_personal',
        ]);

        try{
            Devolucion::create([
                'fch_devolucion' => $request->fch_devolucion,
                'hora_devolucion' => $request->hora_devolucion,
                'descripcion_devolucion' => $request->descripcion_devolucion,
                'PRESTAMO_id_prestamo' => $id,
                'PERSONAL_id_personal' => $request->PERSONAL_id_personal,
            ]);

            return redirect()->route('encargado.prestamo')->with('success', 'Devolución registrada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('encargado.prestamo')->with('error', 'Ocurrió un error al registrar la devolucion. Intente nuevamente mas tarde.');
        }
    }

}
