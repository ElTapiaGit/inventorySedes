<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Personal;
use App\Models\Ambiente;
use App\Models\Foto;
use App\Models\Equipo;
use App\Models\Componente;
use App\Models\Accesorio;
use App\Models\EquipoHasAccesorio;
use App\Services\InventarioImageService;

class EquipoRController extends Controller
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
        $fotosReutilizables = $this->imageService->getReusablePhotos('equipos');
        $ultimaFotoId = session('ultima_foto_equipos');

        return view('encargados.registroEquipo', compact('ambientes', 'fotos', 'fotosReutilizables', 'ultimaFotoId'));
    }

    public function storeEquipo(Request $request)
    {
        $edificioId = Auth::user()->EDIFICIO_id_edificio;

        $request->validate([
            'cod_equipo' => 'required|string|max:50|unique:equipo,cod_equipo',
            'nombre_equi' => 'required|string|max:100',
            'marca' => 'nullable|string|max:60',
            'modelo' => 'nullable|string|max:50',
            'descripcion_equi' => 'required|string',
            'observaciones_equi' => 'nullable|string',
            'empotrado' => 'required|in:0,1',
            'estado_equi' => 'required|string|in:Nuevo,Bueno,Regular,Para mantenimiento,Para reparar,Para descarte',
            'vida_util' => 'nullable|string|max:50',
            'AMBIENTE_id_ambiente' => [
                'required',
                'integer',
                'exists:ambiente,id_ambiente',
                function ($attribute, $value, $fail) use ($edificioId) {
                    $pertenece = Ambiente::where('id_ambiente', $value)
                        ->whereHas('piso', function ($q) use ($edificioId) {
                            $q->where('EDIFICIO_id_edificio', $edificioId);
                        })->exists();
                    if (!$pertenece) {
                        $fail('El ambiente seleccionado no pertenece a su edificio asignado.');
                    }
                },
            ],
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120|required_without:existing_foto_id',
            'existing_foto_id' => 'nullable|integer|exists:foto,id_foto|required_without:foto',
        ], [
            'cod_equipo.required' => 'El codigo del equipo es obligatorio.',
            'cod_equipo.unique' => 'El codigo del equipo ya se encuentra registrado.',
            'cod_equipo.max' => 'El codigo del equipo no debe superar los 50 caracteres.',
            'nombre_equi.required' => 'El nombre del equipo es obligatorio.',
            'nombre_equi.max' => 'El nombre del equipo no debe superar los 100 caracteres.',
            'marca.max' => 'La marca no debe superar los 60 caracteres.',
            'modelo.max' => 'El modelo no debe superar los 50 caracteres.',
            'descripcion_equi.required' => 'La descripcion del equipo es obligatoria.',
            'empotrado.required' => 'Debe indicar si el equipo esta empotrado o no.',
            'estado_equi.required' => 'El estado del equipo es obligatorio.',
            'estado_equi.in' => 'El estado del equipo debe ser una de las opciones validas.',
            'vida_util.max' => 'La vida util no debe superar los 50 caracteres.',
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
            $equipo = $this->imageService->storeWithTransaction(
                $request->file('foto'),
                $request->filled('existing_foto_id') ? (int) $request->input('existing_foto_id') : null,
                'equipos',
                function (Foto $foto) use ($request) {
                    $equipo = new Equipo();
                    $equipo->cod_equipo = $request->cod_equipo;
                    $equipo->nombre_equi = $request->nombre_equi;
                    $equipo->marca = $request->marca;
                    $equipo->modelo = $request->modelo;
                    $equipo->descripcion_equi = $request->descripcion_equi;
                    $equipo->empotrado = $request->empotrado;
                    $equipo->estado_equi = $request->estado_equi;
                    $equipo->observaciones_equi = $request->observaciones_equi;
                    $equipo->vida_util = $request->vida_util;
                    $equipo->fch_registro = now();
                    $equipo->AMBIENTE_id_ambiente = $request->AMBIENTE_id_ambiente;
                    $equipo->FOTO_id_foto = $foto->id_foto;
                    $equipo->save();

                    session()->put('ultima_foto_equipos', $foto->id_foto);

                    return $equipo;
                }
            );

            return redirect()->route('equipo.show', ['cod_equipo' => $equipo->cod_equipo])
                ->with('success', 'Equipo registrado correctamente');
        } catch (\Throwable $e) {
            Log::error('Error al registrar equipo: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->with('errordata', 'No se pudo completar el registro del equipo. Intente nuevamente.');
        }
    }

    public function show($cod_equipo)
    {
        $edificioId = Auth::user()->EDIFICIO_id_edificio;

        $equipo = Equipo::with('accesorios')
            ->where('cod_equipo', $cod_equipo)
            ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                $q->where('EDIFICIO_id_edificio', $edificioId);
            })
            ->firstOrFail();

        $componentes = Componente::where('EQUIPO_cod_equipo', $cod_equipo)->get();
        $accesorios = Accesorio::where(function ($query) use ($edificioId) {
            $query->whereHas('equipos.ambiente.piso', function ($q) use ($edificioId) {
                $q->where('EDIFICIO_id_edificio', $edificioId);
            })->orWhereDoesntHave('equipos');
        })->get();

        $fotos = Foto::all();
        $fotosReutilizables = $this->imageService->getReusablePhotos('accesorios');
        $ultimaFotoId = session('ultima_foto_accesorios');

        return view('encargados.registrarComponenteAccesorio', compact('equipo', 'componentes', 'accesorios', 'cod_equipo', 'fotos', 'fotosReutilizables', 'ultimaFotoId'));
    }

    public function storeComponente(Request $request)
    {
        $edificioId = Auth::user()->EDIFICIO_id_edificio;

        $request->validate([
            'cod_equipo' => [
                'required',
                'string',
                'max:50',
                'exists:equipo,cod_equipo',
                function ($attribute, $value, $fail) use ($edificioId) {
                    $pertenece = Equipo::where('cod_equipo', $value)
                        ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                            $q->where('EDIFICIO_id_edificio', $edificioId);
                        })->exists();
                    if (!$pertenece) {
                        $fail('El equipo especificado no pertenece a su edificio asignado.');
                    }
                },
            ],
            'nombre_compo' => 'required|string|max:50',
            'descripcion_compo' => 'required|string',
            'estado_compo' => 'required|string|in:Nuevo,Bueno,Regular,Para mantenimiento,Para reparar,Para descarte',
        ], [
            'nombre_compo.required' => 'El nombre del componente es obligatorio.',
            'nombre_compo.max' => 'El nombre del componente no debe superar los 50 caracteres.',
            'descripcion_compo.required' => 'La descripcion del componente es obligatoria.',
            'estado_compo.required' => 'El estado del componente es obligatorio.',
            'estado_compo.in' => 'El estado del componente debe ser una de las opciones validas.',
            'cod_equipo.required' => 'El codigo del equipo es obligatorio.',
            'cod_equipo.exists' => 'El equipo especificado no existe.',
        ]);

        try {
            $componente = new Componente();
            $componente->nombre_compo = $request->nombre_compo;
            $componente->descripcion_compo = $request->descripcion_compo;
            $componente->estado_compo = $request->estado_compo;
            $componente->EQUIPO_cod_equipo = $request->cod_equipo;
            $componente->save();

            return redirect()->route('equipo.show', ['cod_equipo' => $request->cod_equipo])
                ->with('success', 'Componente registrado correctamente');
        } catch (\Throwable $e) {
            Log::error('Error al registrar componente: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('equipo.show', ['cod_equipo' => $request->cod_equipo])
                ->withInput()
                ->with('errordata', 'Error al registrar el componente. Intente nuevamente.');
        }
    }

    public function storeAccesorioEquipo(Request $request)
    {
        $edificioId = Auth::user()->EDIFICIO_id_edificio;

        $request->validate([
            'cod_accesorio' => 'required|string|max:50|unique:accesorio,cod_accesorio',
            'nombre_acce' => 'required|string|max:80',
            'descripcion_acce' => 'required|string',
            'observacion_ace' => 'nullable|string',
            'estado_acce' => 'required|string|in:Nuevo,Bueno,Regular,Para reponer,Para cambiar',
            'vida_util' => 'nullable|string|max:45',
            'ubicacion' => 'required|string|max:120',
            'cod_equipo' => [
                'required',
                'string',
                'max:50',
                'exists:equipo,cod_equipo',
                function ($attribute, $value, $fail) use ($edificioId) {
                    $pertenece = Equipo::where('cod_equipo', $value)
                        ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                            $q->where('EDIFICIO_id_edificio', $edificioId);
                        })->exists();
                    if (!$pertenece) {
                        $fail('El equipo indicado no pertenece a su edificio asignado.');
                    }
                },
            ],
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
            'cod_equipo.required' => 'El codigo del equipo es obligatorio.',
            'cod_equipo.exists' => 'El equipo indicado no existe en el sistema.',
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

                    // Asociar con el equipo a traves de equipo_has_accesorio
                    $equipo = Equipo::where('cod_equipo', $request->cod_equipo)->firstOrFail();
                    $equipo->accesorios()->syncWithoutDetaching([$accesorio->cod_accesorio]);

                    session()->put('ultima_foto_accesorios', $foto->id_foto);

                    return $accesorio;
                }
            );

            return redirect()->route('equipo.show', ['cod_equipo' => $request->cod_equipo])
                ->with('success', 'Accesorio registrado y vinculado al equipo correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar accesorio de equipo: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('equipo.show', ['cod_equipo' => $request->cod_equipo])
                ->withInput()
                ->with('errordata', 'No se pudo registrar el accesorio del equipo. Intente nuevamente.');
        }
    }

    public function cambiarFoto(Request $request, $cod_equipo)
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
            $edificioId = Auth::user()->EDIFICIO_id_edificio;

            $equipo = Equipo::where('cod_equipo', $cod_equipo)
                ->whereHas('ambiente.piso', function ($q) use ($edificioId) {
                    $q->where('EDIFICIO_id_edificio', $edificioId);
                })
                ->firstOrFail();

            $this->imageService->replaceArticleFoto($equipo, $request->file('foto'), 'equipos');

            return redirect()->back()->with('success', 'Fotografia del equipo actualizada correctamente.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('errordata', 'No tiene permisos para modificar la fotografia de un equipo de otro edificio.');
        } catch (\Throwable $e) {
            Log::error('Error al cambiar foto del equipo: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('errordata', 'Error al actualizar la fotografia del equipo.');
        }
    }
}
