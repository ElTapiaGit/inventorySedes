<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Tecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TecnicoController extends Controller
{
    /**
     * Registrar un nuevo tecnico en el sistema.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validacion fuera de try-catch para respuesta automatica 422 JSON en AJAX
        $validated = $request->validate([
            'nombre' => 'required|string|max:60',
            'ap_paterno' => 'required|string|max:60',
            'ap_materno' => 'nullable|string|max:60',
            'celular' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:150',
        ], [
            'nombre.required' => 'El nombre del tecnico es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 60 caracteres.',
            'ap_paterno.required' => 'El apellido paterno es obligatorio.',
            'ap_paterno.max' => 'El apellido paterno no debe superar los 60 caracteres.',
            'ap_materno.max' => 'El apellido materno no debe superar los 60 caracteres.',
            'celular.required' => 'El numero de celular es obligatorio.',
            'celular.max' => 'El numero de celular no debe superar los 20 caracteres.',
            'direccion.max' => 'La direccion no debe superar los 150 caracteres.',
        ]);

        try {
            $tecnico = Tecnico::create($validated);
            $nombreCompleto = trim($tecnico->nombre . ' ' . $tecnico->ap_paterno . ' ' . ($tecnico->ap_materno ?? ''));

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tecnico registrado correctamente.',
                    'tecnico' => [
                        'id_tecnico' => $tecnico->id_tecnico,
                        'nombre_completo' => $nombreCompleto,
                    ]
                ], 201);
            }

            return redirect()->back()->with('success', 'Tecnico registrado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar tecnico: ' . $e->getMessage(), ['exception' => $e]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno al registrar el tecnico. Intente nuevamente.'
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al registrar el tecnico. Intente nuevamente.');
        }
    }
}
