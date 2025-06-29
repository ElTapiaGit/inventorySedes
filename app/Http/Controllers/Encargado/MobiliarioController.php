<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\Personal;
use App\Models\Ambiente;
use Illuminate\Http\Request;
use App\Models\Mobiliario;
use App\Models\Foto;
use App\Models\TipoMobiliario;

class MobiliarioController extends Controller
{
    // vista de la pagina
    public function create (){
        // Obtén el ID del usuario actualmente autenticado
        $userId = auth()->user()->id_personal; // Asegúrate de que esto corresponda con tu implementación de autenticación

        // Obtener el id del edificio donde el personal trabaja
        $edificioId = Personal::where('id_personal', $userId)->value('EDIFICIO_id_edificio');

        // Obtén los ambientes asociados a ese edificio
        $ambientes = Ambiente::whereHas('piso', function ($query) use ($edificioId) {
            $query->where('Edificio_id_edificio', $edificioId);
        })->get();

        $tipos_mobiliario = TipoMobiliario::all();
        $fotos = Foto::all();

        return view('encargados.regitroMobiliario', compact('tipos_mobiliario', 'ambientes', 'fotos'));
    }

    public function storeFoto(Request $request){
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Subir la imagen al servidor
        if ($request->hasFile('foto')) {
            // Obtén el archivo
            $file = $request->file('foto');

            // Define un nombre único para la foto, para evitar conflictos de nombres
            $filename = $file->getClientOriginalName();

            // Verificar si la ruta ya existe en la base de datos
            $existingFoto = Foto::where('ruta_foto', 'img/mobiliarios-img/' . $filename)->first();
            if ($existingFoto) {
                // Si la foto ya existe, redirige con un mensaje de que ya está registrada
                return redirect()->back()->with([
                    'aviso' => 'La foto ya está registrada.',
                    'foto_id' => $existingFoto->id_foto,
                    'ruta_foto' => $existingFoto->ruta_foto,
                ]);
            }

            $filePath = $file->storeAs('img/mobiliarios-img', $filename, 'public'); // Subir a la carpeta 'public/img/img/mobiliarios-img'

            // Mover la imagen a la carpeta correcta
            $file->move(public_path('img/mobiliarios-img'), $filename);

            // Guardar la ruta de la imagen en la base de datos
            $foto = new Foto();
            $foto->ruta_foto = $filePath; // Ruta almacenada será 'img/mabiliarios-img/nombre_de_archivo.ext'
            $foto->save();

            return redirect()->route('mobiliario.create')
            ->with('ruta_foto', $foto->ruta_foto)
            ->with('foto_id', $foto->id_foto);
        }

        return redirect()->back()->with('error', 'Error al subir la foto del Mobiliario.');
    }

    // registar tipo mobiliario
    public function storeTipoMobiliario(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'tipo_mueble' => 'required|string|max:80',
        ]);

        // Crear un nuevo registro en la base de datos
        TipoMobiliario::create([
            'tipo_mueble' => $request->input('tipo_mueble'),
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->back()->with('success', 'Tipo de mobiliario registrado con éxito.');
    }


    public function storeMobiliario(Request $request)
    {
        try{
            $request->validate([
                'cod_mueble' => 'required|max:50',
                'descripcion_mueb' => 'required',
                'estado_mueb' => 'required',
                'vida_util' => 'required',
                'TIPO_MOBILIARIO_id_tipo_mueb' => 'required',
                'AMBIENTE_id_ambiente' => 'required|integer',
                'FOTO_id_foto' => 'required|exists:foto,id_foto'
            ]);
    
            // Verificar si el código del equipo ya existe
            $existingEquipo = Mobiliario::where('cod_mueble', $request->cod_mueble)->first();
            if ($existingEquipo) {
                // Si el equipo ya existe, redirigir con un mensaje de información
                return redirect()->route('mobiliario.create')->with('info', 'El código del mobiliario ya existe.');
            }
    
            // Crear registro de mobiliario
            $mobiliario = new Mobiliario;
            $mobiliario->cod_mueble = $request->cod_mueble;
            $mobiliario->descripticion_mueb = $request->descripcion_mueb;
            $mobiliario->fch_registro = now();
            // Convertir la fecha a la zona horaria deseada
            //$mobiliario->fch_registro = Carbon::now('America/La_Paz');
            $mobiliario->estado_mueb = $request->estado_mueb;
            $mobiliario->observacion = $request->observacion;
            $mobiliario->vida_util = $request->vida_util;
            $mobiliario->TIPO_MOBILIARIO_id_tipo_mueb = $request->TIPO_MOBILIARIO_id_tipo_mueb;
            $mobiliario->AMBIENTE_id_ambiente = $request->AMBIENTE_id_ambiente;
            $mobiliario->FOTO_id_foto = $request->FOTO_id_foto;
            $mobiliario->save();
    
            return redirect()->route('mobiliario.create')->with('success', 'Mobiliario registrado exitosamente');

        }catch (\Exception $e) {
            return redirect()->route('mobiliario.create')->with('errordata', 'Problemas al registrar el Mobiliario: FAVOR DE REVISAR LOS CAMPOS AL LLENAR' );
        }
    }
}
