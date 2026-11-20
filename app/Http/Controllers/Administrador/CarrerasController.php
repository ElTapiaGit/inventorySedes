<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarrerasController extends Controller
{
    /**
     * Muestra la lista de carreras con conteo de relaciones asociadas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $carreras = Carrera::withCount(['edificios', 'ambientes', 'personal'])
            ->orderBy('nombre_carrera')
            ->get();

        return view('administrator.carreras', compact('carreras'));
    }

    /**
     * Almacena una nueva carrera en la base de datos.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validacion estricta fuera de bloques try-catch
        $request->validate([
            'nombre_carrera' => 'required|string|max:60|unique:carrera,nombre_carrera',
            'codigo' => 'nullable|string|max:10',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ], [
            'nombre_carrera.required' => 'El nombre de la carrera es obligatorio.',
            'nombre_carrera.unique' => 'La carrera ya se encuentra registrada.',
            'nombre_carrera.max' => 'El nombre no debe superar los 60 caracteres.',
            'codigo.max' => 'El codigo no debe superar los 10 caracteres.',
            'logo.image' => 'El archivo debe ser una imagen valida.',
            'logo.mimes' => 'El logo debe ser de formato: png, jpg, jpeg o webp.',
            'logo.max' => 'El logo no debe superar los 2MB de tamano.',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = Storage::disk('public')->putFile('inventario/carreras', $request->file('logo'));
        }

        Carrera::create([
            'nombre_carrera' => trim($request->input('nombre_carrera')),
            'codigo' => $request->filled('codigo') ? trim(mb_strtoupper($request->input('codigo'))) : null,
            'logo' => $logoPath,
        ]);

        return redirect()->route('admin.carreras.index')->with('success', 'Carrera registrada exitosamente.');
    }

    /**
     * Actualiza los datos de una carrera existente.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $carrera = Carrera::findOrFail($id);

        // Validacion estricta fuera de bloques try-catch
        $request->validate([
            'nombre_carrera' => 'required|string|max:60|unique:carrera,nombre_carrera,' . $id . ',id_carrera',
            'codigo' => 'nullable|string|max:10',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ], [
            'nombre_carrera.required' => 'El nombre de la carrera es obligatorio.',
            'nombre_carrera.unique' => 'Ya existe otra carrera con este nombre.',
            'nombre_carrera.max' => 'El nombre no debe superar los 60 caracteres.',
            'codigo.max' => 'El codigo no debe superar los 10 caracteres.',
            'logo.image' => 'El archivo debe ser una imagen valida.',
            'logo.mimes' => 'El logo debe ser de formato: png, jpg, jpeg o webp.',
            'logo.max' => 'El logo no debe superar los 2MB de tamano.',
        ]);

        $carrera->nombre_carrera = trim($request->input('nombre_carrera'));
        $carrera->codigo = $request->filled('codigo') ? trim(mb_strtoupper($request->input('codigo'))) : null;

        if ($request->hasFile('logo')) {
            // Eliminar logo anterior solo si estaba alojado en storage de carreras
            if ($carrera->logo && str_starts_with($carrera->logo, 'inventario/carreras/') && Storage::disk('public')->exists($carrera->logo)) {
                Storage::disk('public')->delete($carrera->logo);
            }
            $carrera->logo = Storage::disk('public')->putFile('inventario/carreras', $request->file('logo'));
        }

        $carrera->save();

        return redirect()->route('admin.carreras.index')->with('success', 'Carrera actualizada correctamente.');
    }

    /**
     * Elimina una carrera si no tiene dependencias asociadas.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $carrera = Carrera::findOrFail($id);

        $carrera->loadCount(['edificios', 'ambientes', 'personal']);

        if ($carrera->edificios_count > 0 || $carrera->ambientes_count > 0 || $carrera->personal_count > 0) {
            $detalles = [];
            if ($carrera->edificios_count > 0) {
                $detalles[] = $carrera->edificios_count . ' edificio(s)';
            }
            if ($carrera->ambientes_count > 0) {
                $detalles[] = $carrera->ambientes_count . ' ambiente(s)';
            }
            if ($carrera->personal_count > 0) {
                $detalles[] = $carrera->personal_count . ' personal(es)';
            }

            return redirect()->route('admin.carreras.index')
                ->with('errordata', 'No se puede eliminar la carrera porque tiene dependencias asociadas: ' . implode(', ', $detalles) . '. Reasigne o desvincule estos registros primero.');
        }

        if ($carrera->logo && str_starts_with($carrera->logo, 'inventario/carreras/') && Storage::disk('public')->exists($carrera->logo)) {
            Storage::disk('public')->delete($carrera->logo);
        }

        $carrera->delete();

        return redirect()->route('admin.carreras.index')->with('success', 'Carrera eliminada exitosamente.');
    }
}
