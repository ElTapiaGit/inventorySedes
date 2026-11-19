<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Foto;
use App\Models\Personal;
use App\Models\Ambiente;
use App\Models\Material;
use App\Services\InventarioImageService;

class MaterialController extends Controller
{
    protected InventarioImageService $imageService;

    public function __construct(InventarioImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function create()
    {
        $userId = auth()->user()->id_personal;
        $edificioId = Personal::where('id_personal', $userId)->value('EDIFICIO_id_edificio');

        $ambientes = Ambiente::whereHas('piso', function ($query) use ($edificioId) {
            $query->where('Edificio_id_edificio', $edificioId);
        })->get();

        $fotos = Foto::all();
        $fotosReutilizables = $this->imageService->getReusablePhotos('materiales');
        $ultimaFotoId = session('ultima_foto_materiales');

        return view('encargados.registrarMaterial', compact('ambientes', 'fotos', 'fotosReutilizables', 'ultimaFotoId'));
    }

    public function storeMaterial(Request $request)
    {
        $request->validate([
            'cod_mate' => 'required|string|max:50|unique:material,cod_mate',
            'tipo_mate' => 'required|string|max:50',
            'descripcion_mate' => 'required|string',
            'estado_mate' => 'required|string|max:45',
            'observacion_mate' => 'nullable|string',
            'AMBIENTE_id_ambiente' => 'required|integer|exists:ambiente,id_ambiente',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120|required_without:existing_foto_id',
            'existing_foto_id' => 'nullable|integer|exists:foto,id_foto|required_without:foto',
        ], [
            'cod_mate.required' => 'El codigo del material es obligatorio.',
            'cod_mate.unique' => 'El codigo del material ya se encuentra registrado.',
            'cod_mate.max' => 'El codigo del material no debe superar los 50 caracteres.',
            'tipo_mate.required' => 'El tipo de material es obligatorio.',
            'tipo_mate.max' => 'El tipo de material no debe superar los 50 caracteres.',
            'descripcion_mate.required' => 'La descripcion del material es obligatoria.',
            'estado_mate.required' => 'El estado del material es obligatorio.',
            'AMBIENTE_id_ambiente.required' => 'Debe seleccionar un ambiente valido.',
            'AMBIENTE_id_ambiente.exists' => 'El ambiente seleccionado no existe en el sistema.',
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
                'materiales',
                function (Foto $foto) use ($request) {
                    $material = new Material();
                    $material->cod_mate = $request->cod_mate;
                    $material->tipo_mate = $request->tipo_mate;
                    $material->descripcion_mate = $request->descripcion_mate;
                    $material->estado_mate = $request->estado_mate;
                    $material->observacion_mate = $request->observacion_mate;
                    $material->AMBIENTE_id_ambiente = $request->AMBIENTE_id_ambiente;
                    $material->FOTO_id_foto = $foto->id_foto;
                    $material->fch_registrada = now();
                    $material->save();

                    session()->put('ultima_foto_materiales', $foto->id_foto);

                    return $material;
                }
            );

            return redirect()->route('material.create')->with('success', 'Material registrado exitosamente');
        } catch (\Throwable $e) {
            Log::error('Error al registrar material: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('errordata', 'No se pudo completar el registro del material. Intente nuevamente.');
        }
    }

    public function cambiarFoto(Request $request, $cod_mate)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'foto.required' => 'Debe seleccionar una fotografia para actualizar.',
            'foto.image' => 'El archivo debe ser una imagen valida.',
            'foto.mimes' => 'La imagen debe ser de formato: jpeg, jpg, png o webp.',
            'foto.max' => 'La imagen no debe superar los 5MB.',
        ]);

        try {
            $material = Material::where('cod_mate', $cod_mate)->firstOrFail();
            $this->imageService->replaceArticleFoto($material, $request->file('foto'), 'materiales');

            return redirect()->back()->with('success', 'Fotografia del material actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al cambiar foto del material: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('errordata', 'Error al actualizar la fotografia del material.');
        }
    }
}
