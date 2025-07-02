<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\DetallePrestamo;
use App\Models\Personal;
use App\Models\Ambiente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PrestamopController extends Controller
{
    //
    public function index() {
        $prestamos = Prestamo::with('personal')->latest('id_prestamo')->get();
        $personal = Personal::all();

        return view('encargados.prestamoPag', compact('prestamos', 'personal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_solicitante' => 'required|string|max:100',
            'descripcion_prestamo' => 'required|string|max:255',
            'fch_prestamo' => 'required|date',
            'hora_prestamo' => 'required',
            'PERSONAL_id_personal' => 'required|exists:personal,id_personal',
        ]);

        Prestamo::create($request->all());

        return redirect()->route('encargado.prestamo')->with('success', 'Préstamo registrado correctamente.');
    }

    public function detalle($id)
    {
        $idDesencriptado = Crypt::decrypt($id);
        
        $prestamo = Prestamo::with('detallePrestamos')->findOrFail($idDesencriptado);
        $ambientes = Ambiente::all(); // si lo necesitas

        return view('encargado.prestamo_detalle', compact('prestamo', 'ambientes'));
    }

    public function detalleStore(Request $request, $id)
    {
        $idDesencriptado = Crypt::decrypt($id);

        $request->validate([
            'cod_articulo.*' => 'required|string',
            'AMBIENTE_id_ambiente.*' => 'required|exists:ambiente,id_ambiente',
            'observacion_detalle.*' => 'nullable|string',
        ]);

        $data = [];
        foreach ($request->cod_articulo as $i => $codigo) {
            $data[] = [
                'cod_articulo' => $codigo,
                'AMBIENTE_id_ambiente' => $request->AMBIENTE_id_ambiente[$i],
                'observacion_detalle' => $request->observacion_detalle[$i] ?? '',
                'PRESTAMO_id_prestamo' => $idDesencriptado
            ];
        }

        DetallePrestamo::insert($data);

        return redirect()->route('encargado.prestamo')->with('success', 'Detalles del préstamo registrados.');
    }

}
