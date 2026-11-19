<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Foto;
use App\Models\Accesorio;
use App\Services\InventarioImageService;

class AccesorioRController extends Controller
{
    protected InventarioImageService $imageService;

    public function __construct(InventarioImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function create()
    {
        $fotos = Foto::all();
        $fotosReutilizables = $this->imageService->getReusablePhotos('accesorios');
        $ultimaFotoId = session('ultima_foto_accesorios');

        return view('encargados.registroAccesorio', compact('fotos', 'fotosReutilizables', 'ultimaFotoId'));
    }

    public function storeAccesorio(Request $request)
    {
        $request->validate([
            'cod_accesorio' => 'required|string|max:50|unique:accesorio,cod_accesorio',
            'nombre_acce' => 'required|string|max:80',
            'descripcion_acce' => 'required|string',
            'observacion_ace' => 'nullable|string',
            'estado_acce' => 'required|string|in:Nuevo,Bueno,Regular,Para reponer,Para cambiar',
            'vida_util' => 'nullable|string|max:45',
            'ubicacion' => 'required|string|max:120',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120|required_without:existing_foto_id',
            'existing_foto_id' => 'nullable|integer|exists:foto,id_foto|required_without:foto',
        ], [
            'cod_accesorio.required' => 'El codigo del accesorio es obligatorio.',
            'cod_accesorio.unique' => 'El codigo del accesorio ya se encuentra registrado.',
            'cod_accesorio.max' => 'El codigo del accesorio no debe superar los 50 caracteres.',
            'nombre_acce.required' => 'El nombre del accesorio es obligatorio.',
            'nombre_acce.max' => 'El nombre del accesorio no debe superar los 80 caracteres.',
            'descripcion_acce.required' => 'La descripcion del accesorio es obligatoria.',
            'estado_acce.required' => 'El estado del accesorio es obligatorio.',
            'estado_acce.in' => 'El estado del accesorio debe ser una de las opciones validas.',
            'vida_util.max' => 'La vida util no debe superar los 45 caracteres.',
            'ubicacion.required' => 'La ubicacion del accesorio es obligatoria.',
            'ubicacion.max' => 'La ubicacion no debe superar los 120 caracteres.',
            'foto.required_without' => 'Debe subir una fotografia o seleccionar una existente.',
            'foto.image' => 'El archivo seleccionado debe ser una imagen.',
            'foto.mimes' => 'La imagen debe tener formato: jpeg, jpg, png o webp.',
            'foto.max' => 'La imagen no debe superar los 5MB de tamano.',
            'existing_foto_id.required_without' => 'Debe seleccionar una foto existente o subir una nueva.',
            'existing_foto_id.exists' => 'La foto seleccionada no existe en el sistema.',
        ]);

        try {
            $this->imageService->storeWithTransaction(
                $request->file('foto'),
                $request->filled('existing_foto_id') ? (int) $request->input('existing_foto_id') : null,
                'accesorios',
                function (Foto $foto) use ($request) {
                    $accesorio = new Accesorio();
                    $accesorio->cod_accesorio = $request->cod_accesorio;
                    $accesorio->nombre_acce = $request->nombre_acce;
                    $accesorio->descripcion_acce = $request->descripcion_acce;
                    $accesorio->observacion_ace = $request->observacion_ace;
                    $accesorio->estado_acce = $request->estado_acce;
                    $accesorio->vida_util = $request->vida_util;
                    $accesorio->ubicacion = $request->ubicacion;
                    $accesorio->fch_registro_acce = now();
                    $accesorio->FOTO_id_foto = $foto->id_foto;
                    $accesorio->save();

                    session()->put('ultima_foto_accesorios', $foto->id_foto);

                    return $accesorio;
                }
            );

            return redirect()->route('accesorio.create')->with('success', 'Accesorio registrado exitosamente');
        } catch (\Throwable $e) {
            Log::error('Error al registrar accesorio: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('errordata', 'No se pudo completar el registro del accesorio. Intente nuevamente.');
        }
    }

    public function cambiarFoto(Request $request, $cod_accesorio)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'foto.required' => 'Debe seleccionar una fotografia para actualizar.',
            'foto.image' => 'El archivo debe ser una imagen valida.',
            'foto.mimes' => 'La imagen debe ser de formato: jpeg, jpg, png o webp.',
            'foto.max' => 'La imagen no debe superar los 5MB.',
        ]);

        $edificioId = Auth::user()->EDIFICIO_id_edificio;

        $accesorio = Accesorio::with('equipos.ambiente.piso')->where('cod_accesorio', $cod_accesorio)->firstOrFail();

        // Validar que no pertenezca exclusivamente a un equipo de otro edificio
        $equiposOtroEdificio = $accesorio->equipos->filter(function ($eq) use ($edificioId) {
            return $eq->ambiente && $eq->ambiente->piso && $eq->ambiente->piso->EDIFICIO_id_edificio != $edificioId;
        });
        $equiposEsteEdificio = $accesorio->equipos->filter(function ($eq) use ($edificioId) {
            return $eq->ambiente && $eq->ambiente->piso && $eq->ambiente->piso->EDIFICIO_id_edificio == $edificioId;
        });

        if ($equiposOtroEdificio->isNotEmpty() && $equiposEsteEdificio->isEmpty()) {
            abort(403, 'No tiene permisos para modificar la fotografia de este accesorio.');
        }

        try {
            $this->imageService->replaceArticleFoto($accesorio, $request->file('foto'), 'accesorios');

            return redirect()->back()->with('success', 'Fotografia del accesorio actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al cambiar foto del accesorio: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('errordata', 'Error al actualizar la fotografia del accesorio.');
        }
    }
}
