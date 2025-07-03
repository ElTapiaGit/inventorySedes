<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mantenimiento;
use App\Models\FinalMantenimiento;
use App\Models\DetallesMantenimiento;
use App\Models\Tecnico;
use App\Models\Personal;
use App\Models\Equipo;
use Illuminate\Support\Facades\Crypt;

class MantenimientopController extends Controller
{
    // Mostrar todos los mantenimientos
    public function index()
    {
        $mantenimientos = Mantenimiento::with(['tecnico', 'personal', 'finalMantenimiento'])
            ->latest('id_mantenimiento_ini')->paginate(10);

        $tecnicos = Tecnico::all();
        $personales = Personal::all();
        $equipos = Equipo::all();

        return view('encargados.mantenimientoPage', compact('mantenimientos', 'tecnicos', 'personales', 'equipos'));
    }

    // Registrar el inicio del mantenimiento con sus detalles
    public function store(Request $request)
    {
        $request->validate([
            'informe_inicial' => 'required|string',
            'fch_inicio' => 'required|date',
            'TECNICO_id_tecnico' => 'required|exists:tecnico,id_tecnico',
            'PERSONAL_id_personal' => 'required|exists:personal,id_personal',
            'cod_articulo' => 'required|array',
            'cod_articulo.*' => 'required|string'
        ]);

        $mantenimiento = Mantenimiento::create([
            'informe_inicial' => $request->informe_inicial,
            'fch_inicio' => $request->fch_inicio,
            'TECNICO_id_tecnico' => $request->TECNICO_id_tecnico,
            'PERSONAL_id_personal' => $request->PERSONAL_id_personal,
        ]);

        // Guardar los artículos asociados al mantenimiento
        foreach ($request->cod_articulo as $codigo) {
            DetallesMantenimiento::create([
                'cod_articulo' => $codigo,
                'INICIO_MANTENIMIENTO_id_mantenimiento_ini' => $mantenimiento->id_mantenimiento_ini
            ]);
        }

        return redirect()->route('encargado.mantenimiento')->with('success', 'Mantenimiento registrado correctamente.');
        return back()->with('error', 'Ocurrió un error al registrar el préstamo.');
    }

    // Mostrar los detalles completos del mantenimiento 
    public function show($id)
    {
        try {
            $idDesencriptado = Crypt::decrypt($id);
        } catch (\Exception $e) {
            abort(403, 'ID inválido');
        }

        $mantenimiento = Mantenimiento::with([
            'tecnico',
            'personal',
            'finalMantenimiento',
            'detalleMantenimiento'
        ])->findOrFail($idDesencriptado);

        return view('encargados.detalleMantenimiento', compact('mantenimiento'));
    }

    // Registrar el final del mantenimiento
    public function finalizar(Request $request, $id)
    {
        $request->validate([
            'informe_final' => 'required|string',
            'fch_final' => 'required|date'
        ]);

        FinalMantenimiento::create([
            'informe_final' => $request->informe_final,
            'fch_final' => $request->fch_final,
            'INICIO_MANTENIMIENTO_id_mantenimiento_ini' => $id
        ]);

        return redirect()->route('encargado.mantenimiento')->with('success', 'Mantenimiento finalizado correctamente.');
    }
}
