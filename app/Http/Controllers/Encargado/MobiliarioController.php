<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Personal;
use App\Models\Ambiente;
use App\Models\Mobiliario;
use App\Models\Foto;
use App\Models\TipoMobiliario;
use App\Services\InventarioImageService;

class MobiliarioController extends Controller
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

        $tipos_mobiliario = TipoMobiliario::all();
        $fotos = Foto::all();
        $fotosReutilizables = $this->imageService->getReusablePhotos('mobiliarios');
        $ultimaFotoId = session('ultima_foto_mobiliarios');

        return view('encargados.regitroMobiliario', compact('tipos_mobiliario', 'ambientes', 'fotos', 'fotosReutilizables', 'ultimaFotoId'));
    }

    public function storeTipoMobiliario(Request $request)
    {
        $request->validate([
            'tipo_mueble' => 'required|string|max:80',
        ], [
            'tipo_mueble.required' => 'El tipo de mueble es obligatorio.',
            'tipo_mueble.max' => 'El tipo de mueble no debe superar los 80 caracteres.',
        ]);

        TipoMobiliario::create([
            'tipo_mueble' => $request->input('tipo_mueble'),
        ]);

        return redirect()->back()->with('success', 'Tipo de mobiliario registrado con éxito.');
    }

    public function storeMobiliario(Request $request)
    {
        $request->validate([
            'cod_mueble' => 'required|string|max:50|unique:mobiliario,cod_mueble',
            'descripticion_mueb' => 'required_without:descripcion_mueb|nullable|string',
            'descripcion_mueb' => 'required_without:descripticion_mueb|nullable|string',
            'estado_mueb' => 'required|string|in:Nuevo,Bueno,Regular,Para mantenimiento,Para reparar,Para descarte',
            'observacion' => 'nullable|string',
            'vida_util' => 'required|string|max:50',
            'TIPO_MOBILIARIO_id_tipo_mueb' => 'required|integer|exists:tipo_mobiliario,id_tipo_mueb',
            'AMBIENTE_id_ambiente' => 'required|integer|exists:ambiente,id_ambiente',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120|required_without:existing_foto_id',
            'existing_foto_id' => 'nullable|integer|exists:foto,id_foto|required_without:foto',
        ], [
            'cod_mueble.required' => 'El codigo del mobiliario es obligatorio.',
            'cod_mueble.unique' => 'El codigo del mobiliario ya se encuentra registrado.',
            'cod_mueble.max' => 'El codigo del mobiliario no debe superar los 50 caracteres.',
            'descripticion_mueb.required_without' => 'La descripcion del mobiliario es obligatoria.',
            'descripcion_mueb.required_without' => 'La descripcion del mobiliario es obligatoria.',
            'estado_mueb.required' => 'El estado del mobiliario es obligatorio.',
            'estado_mueb.in' => 'El estado del mobiliario debe ser una de las opciones validas.',
            'vida_util.required' => 'La vida util es obligatoria.',
            'TIPO_MOBILIARIO_id_tipo_mueb.required' => 'Debe seleccionar un tipo de mobiliario.',
            'TIPO_MOBILIARIO_id_tipo_mueb.exists' => 'El tipo de mobiliario seleccionado no es valido.',
            'AMBIENTE_id_ambiente.required' => 'Debe seleccionar un ambiente valido.',
            'AMBIENTE_id_ambiente.exists' => 'El ambiente seleccionado no existe en el sistema.',
            'foto.required_without' => 'Debe subir una fotografia o seleccionar una existente.',
            'foto.image' => 'El archivo seleccionado debe ser una imagen.',
            'foto.mimes' => 'La imagen debe tener formato: jpeg, jpg, png o webp.',
            'foto.max' => 'La imagen no debe superar los 5MB de tamano.',
            'existing_foto_id.required_without' => 'Debe seleccionar una foto existente o subir una nueva.',
            'existing_foto_id.exists' => 'La foto seleccionada no existe en el sistema.',
        ]);

        $descripcion = $request->input('descripticion_mueb') ?? $request->input('descripcion_mueb');

        try {
            $this->imageService->storeWithTransaction(
                $request->file('foto'),
                $request->filled('existing_foto_id') ? (int) $request->input('existing_foto_id') : null,
                'mobiliarios',
                function (Foto $foto) use ($request, $descripcion) {
                    $mobiliario = new Mobiliario();
                    $mobiliario->cod_mueble = $request->cod_mueble;
                    $mobiliario->descripticion_mueb = $descripcion;
                    $mobiliario->fch_registro = now();
                    $mobiliario->estado_mueb = $request->estado_mueb;
                    $mobiliario->observacion = $request->observacion;
                    $mobiliario->vida_util = $request->vida_util;
                    $mobiliario->TIPO_MOBILIARIO_id_tipo_mueb = $request->TIPO_MOBILIARIO_id_tipo_mueb;
                    $mobiliario->AMBIENTE_id_ambiente = $request->AMBIENTE_id_ambiente;
                    $mobiliario->FOTO_id_foto = $foto->id_foto;
                    $mobiliario->save();

                    session()->put('ultima_foto_mobiliarios', $foto->id_foto);

                    return $mobiliario;
                }
            );

            return redirect()->route('mobiliario.create')->with('success', 'Mobiliario registrado exitosamente');
        } catch (\Throwable $e) {
            Log::error('Error al registrar mobiliario: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('errordata', 'No se pudo completar el registro del mobiliario. Intente nuevamente.');
        }
    }

    public function cambiarFoto(Request $request, $cod_mueble)
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
            $mobiliario = Mobiliario::where('cod_mueble', $cod_mueble)->firstOrFail();
            $this->imageService->replaceArticleFoto($mobiliario, $request->file('foto'), 'mobiliarios');

            return redirect()->back()->with('success', 'Fotografia del mobiliario actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al cambiar foto del mobiliario: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('errordata', 'Error al actualizar la fotografia del mobiliario.');
        }
    }
}
