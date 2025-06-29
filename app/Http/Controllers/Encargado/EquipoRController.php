<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\Ambiente;
use App\Models\Foto;
use App\Models\Equipo;
use App\Models\Componente;
use App\Models\Accesorio;
use App\Models\EquipoHasAccesorio;

class EquipoRController extends Controller
{
    //
    public function create(){
        // Obtén el ID del usuario actualmente autenticado
        $userId = auth()->user()->id_personal; // Asegúrate de que esto corresponda con tu implementación de autenticación

        // Obtener el id del edificio donde el personal trabaja
        $edificioId = Personal::where('id_personal', $userId)->value('EDIFICIO_id_edificio');

        // Obtén los ambientes asociados a ese edificio
        $ambientes = Ambiente::whereHas('piso', function ($query) use ($edificioId) {
            $query->where('Edificio_id_edificio', $edificioId);
        })->get();

        $fotos = Foto::all();

        return view('encargados.registroEquipo', compact('ambientes', 'fotos'));
    }

    //registrar foto
    public function store(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048' // Validar que sea una imagen
        ]);

        // Subir la imagen al servidor
        if ($request->hasFile('foto')) {
            // Obtén el archivo
            $file = $request->file('foto');
            // Define un nombre único para la foto, para evitar conflictos de nombres
            $filename = $file->getClientOriginalName();

            // Verificar si la ruta ya existe en la base de datos
            $existingFoto = Foto::where('ruta_foto', 'img/equipos-img/' . $filename)->first();
            if ($existingFoto) {
                // Si la foto ya existe, redirige con un mensaje de que ya está registrada
                return redirect()->back()->with([
                    'aviso' => 'La foto ya está registrada.',
                    'foto_id' => $existingFoto->id_foto,
                    'ruta_foto' => $existingFoto->ruta_foto,
                ]);
            }

            $filePath = $file->storeAs('img/equipos-img', $filename, 'public'); // Subir a la carpeta 'public/img/equipos-img'

            // Mover la imagen a la carpeta correcta
            $file->move(public_path('img/equipos-img'), $filename);

            // Guardar la ruta de la imagen en la base de datos
            $foto = new Foto();
            $foto->ruta_foto = $filePath; // Ruta almacenada será 'img/equipos-img/nombre_de_archivo.ext'
            $foto->save();

            return redirect()->route('equipo.create')
            ->with('ruta_foto', $foto->ruta_foto)
            ->with('foto_id', $foto->id_foto);
        }

        return redirect()->back()->with('error', 'Error al subir la foto.');
    }

    //registrar equipoo
    public function storeEquipo(Request $request)
    {
        try{
            $request->validate([
                'cod_equipo' => 'required|string|max:50',
                'nombre_equi' => 'required|string|max:45',
                'marca' => 'required|string|max:20',
                'modelo' => 'required|string|max:50',
                'descripcion_equi' => 'required|string',
                'empotrado' => 'required|in:0,1',
                'estado_equi' => 'required|string|max:50',
                'vida_util' => 'required|string|max:50',
                'AMBIENTE_id_ambiente' => 'required|integer',
                'FOTO_id_foto' => 'required|integer',
            ]);

             // Verificar si el código del equipo ya existe
            $existingEquipo = Equipo::where('cod_equipo', $request->cod_equipo)->first();
            if ($existingEquipo) {
                // Si el equipo ya existe, redirigir con un mensaje de información
                return redirect()->route('equipo.create')->with('info', 'El código del equipo ya existe.');
            }
    
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
            $equipo->FOTO_id_foto = $request->FOTO_id_foto;
            $equipo->save();
    
            // Redirigir a una página donde se pueden registrar componentes y accesorios
            return redirect()->route('equipo.show', $request->cod_equipo)
            ->with('success', 'Equipo registrado correctamente');

        }catch (\Exception $e) {
            return redirect()->route('equipo.create')->with('errordata', 'Error al registrar el equipo: ' . $e->getMessage());
        }
    }


    // vista para la pagina componentes
    public function show($cod_equipo)
    {
        $equipo = Equipo::with('accesorios')->where('cod_equipo', $cod_equipo)->firstOrFail();
        $componentes = Componente::where('EQUIPO_cod_equipo', $cod_equipo)->get();
        $accesorios = Accesorio::all();
        $fotos = Foto::all();

        return view('encargados.registrarComponenteAccesorio', compact('equipo', 'componentes', 'accesorios', 'cod_equipo', 'fotos'));
    }

    //registrar componentes y accesorios
    public function storeComponente(Request $request)
    {
        try {
            $request->validate([
                'cod_equipo' => 'required|string|max:50|exists:equipo,cod_equipo',
                'nombre_compo' => 'required|string|max:50',
                'descripcion_compo' => 'required|string',
                'estado_compo' => 'required|string|max:50',
            ]);

            $componente = new Componente();
            $componente->nombre_compo = $request->nombre_compo;
            $componente->descripcion_compo = $request->descripcion_compo;
            $componente->estado_compo = $request->estado_compo;
            $componente->EQUIPO_cod_equipo = $request->cod_equipo;
            $componente->save();

            return redirect()->route('equipo.show', $request->cod_equipo)
                            ->with('success', 'Componente registrado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('equipo.show', $request->cod_equipo)
                            ->with('errordata', 'Error al registrar el componente: ' . $e->getMessage());
        }
    }

    //registrar foto accesorio
    public function storeFotoAccesorio(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048' // Validar que sea una imagen
        ]);

        // Subir la imagen al servidor
        if ($request->hasFile('foto')) {
            // Obtén el archivo
            $file = $request->file('foto');
            // Define un nombre único para la foto, para evitar conflictos de nombres
            $filename = $file->getClientOriginalName();

            // Verificar si la ruta ya existe en la base de datos
            $existingFoto = Foto::where('ruta_foto', 'img/accesorios-img/' . $filename)->first();
            if ($existingFoto) {
                // Si la foto ya existe, redirige con un mensaje de que ya está registrada
                return redirect()->back()->with([
                    'aviso' => 'La foto ya está registrada.',
                    'foto_id' => $existingFoto->id_foto,
                    'ruta_foto' => $existingFoto->ruta_foto,
                ]);
            }

            $filePath = $file->storeAs('img/accesorios-img', $filename, 'public'); // Subir a la carpeta 'public/img/accesorios-img'

            // Mover la imagen a la carpeta correcta
            $file->move(public_path('img/accesorios-img'), $filename);

            // Guardar la ruta de la imagen en la base de datos
            $foto = new Foto();
            $foto->ruta_foto = $filePath; // Ruta almacenada será 'img/accesorios-img/nombre_de_archivo.ext'
            $foto->save();

            return redirect()->back()
            ->with('success', 'Registro de foto correctamente');
        }

        return redirect()->back()->with('error', 'Error al subir la foto.');
    }

    public function storeAccesorioEquipo(Request $request)
    {
        $request->validate([
            'cod_accesorio' => 'required|string|max:50|unique:accesorio,cod_accesorio',
            'nombre_acce' => 'required|string|max:50',
            'descripcion_acce' => 'nullable|string',
            'estado_acce' => 'required|string|max:50',
            'vida_util' => 'required|string|max:50',
            'ubicacion' => 'required|string|max:50',
            'cod_equipo' => 'nullable|string|max:50|exists:equipo,cod_equipo', // Solo si se incluye
        ]);

        try {
            // Crear nuevo accesorio
            $accesorio = new Accesorio();
            $accesorio->cod_accesorio = $request->cod_accesorio;
            $accesorio->nombre_acce = $request->nombre_acce;
            $accesorio->descripcion_acce = $request->descripcion_acce;
            $accesorio->observacion_ace = $request->observacion_ace;
            $accesorio->estado_acce = $request->estado_acce;
            $accesorio->vida_util = $request->vida_util;
            $accesorio->ubicacion = $request->ubicacion;
            $accesorio->fch_registro_acce = now();
            $accesorio->FOTO_id_foto = $request->FOTO_id_foto;
            $accesorio->save();

            // Asociar el accesorio con el equipo
            $equipoHasAccesorio = new EquipoHasAccesorio();
            $equipoHasAccesorio->EQUIPO_cod_equipo = $request->cod_equipo;
            $equipoHasAccesorio->ACCESORIO_cod_accesorio = $request->cod_accesorio;
            $equipoHasAccesorio->save();

            // Asociar el accesorio con el equipo si se proporciona el código del equipo
            if ($request->has('cod_equipo')) {
                $equipo = Equipo::where('cod_equipo', $request->cod_equipo)->firstOrFail();
                $equipo->accesorios()->attach($accesorio->cod_accesorio);
            }

            return redirect()->return('equipo.show')->with('success', 'Accesorio registrado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('errordata', 'Problemas al registrar el accesorio del equipo: FAVOR REVISE LOS CAMPOS AL LLENAR ');
        }
    }
}
